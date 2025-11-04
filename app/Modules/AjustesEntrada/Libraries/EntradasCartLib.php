<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of EntradasCartLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 8 oct 2025
 * @time 9:48:36 a.m.
 */

namespace Modules\AjustesEntrada\Libraries;

use CodeIgniter\Session\Session;

class EntradasCartLib {
    //put your code here

    /**
     * Content cart (cache en memoria)
     * @var array
     */
    private $cart = null;

    /**
     * instance cart
     * @var string
     */
    private $instance;

    /**
     * Session instance
     * @var Session
     */
    protected $session;

    /**
     * Totals cache
     * @var array
     */
    private $totalsCache = null;

    /**
     * Flag para saber si hay cambios sin guardar
     * @var bool
     */
    private $isDirty = false;

    /**
     * Constructor
     */
    public function __construct($instance = "ajstEntrada") {
        $this->instance = "entradasCart" . $instance;
        $this->session = \Config\Services::session();

        // Cargar cart en memoria solo una vez
        $this->loadCart();
    }

    /**
     * Cargar cart desde sesión a memoria
     */
    private function loadCart() {
        if ($this->cart === null) {
            $this->cart = $this->session->get($this->instance);

            if ($this->cart === null) {
                $this->cart = [
                    '_meta' => [
                        'total_cart' => 0,
                        'total_articles' => 0,
                        'total_iva' => 0,
                        'totalcart_iva' => 0,
                        'total_bienes' => 0,
                        'total_servicios' => 0,
                        'tarif_cero' => 0,
                        'tarif_ceroneto' => 0,
                        'tarif_iva' => 0,
                        'tarif_ivaneto' => 0,
                        'ci_client' => null
                    ]
                ];
            }
        }
    }

    /**
     * Guardar solo cuando sea necesario
     */
//    private function saveToSession() {
//        if ($this->isDirty) {
//            $this->session->set($this->instance, $this->cart);
//            $this->isDirty = false;
//        }
//    }

    /**
     * Destructor - guardar automáticamente al finalizar
     */
//    public function __destruct() {
//        $this->saveToSession();
//    }

    /**
     * Guardar inmediatamente (fuerza el guardado)
     */
    public function save() {
        $this->session->set($this->instance, $this->cart);
        $this->isDirty = false;
    }

    public function instance_name() {
        return $this->instance;
    }

    /**
     * Insert con cálculos optimizados
     */
    public function insert($items = [], $update = false, $rowid_ = null) {

        if (!isset($items["id"]) || !isset($items["qty"]) || !isset($items["price"])) {
            throw new \Exception("Id, qty and price are required fields!");
        }

        if (!is_numeric($items["id"]) || !is_numeric($items["qty"]) || !is_numeric($items["price"])) {
            throw new \Exception("Id, qty and price must be numbers.");
        }

        if (!is_array($items) || empty($items)) {
            throw new \Exception("The last row insert method must be an array");
        }



        $rowid = $this->_insert($items, $update, $rowid_);

        if ($rowid) {
            $this->isDirty = true;
            $this->save();
            return true;
        }

        throw new \Exception("Error saving cart");
    }

//    public function insert2($items = array(), $update = false, $rowid_ = null) {
//        if (!isset($items["id"]) || !isset($items["qty"]) || !isset($items["price"])) {
//            throw new \Exception("Id, qty and price are required fields!");
//        }
//
//        if (!is_numeric($items["id"]) || !is_numeric($items["qty"]) || !is_numeric($items["price"])) {
//            throw new \Exception("Id, qty and price must be numbers.");
//        }
//
//        if (!is_array($items) || empty($items)) {
//            throw new \Exception("The last row insert method must be an array");
//        }
//
//        $rowid = $this->_insert2($items, $update, $rowid_);
//
//        if ($rowid) {
//            $this->isDirty = true;
//            return true;
//        }
//
//        throw new \Exception("Error saving cart");
//    }

    /**
     * Insert interno optimizado
     */
    private function _insert($items = [], $update = false, $rowid_) {
        // Generar rowid
        $randonNumer = rand(1, 1000000);

        if (!empty($rowid_)) {
            $rowid = $rowid_;
        } else {

            $rowid = ($items["permitirDuplicados"] ? md5($items["id"] . $randonNumer) : md5($items["id"]));
        }

        $items["rowid"] = $rowid;

        // Si existe y no es update, sumar cantidad
        if (isset($this->cart[$rowid]) && !$update) {
            $items["qty"] = $this->cart[$rowid]["qty"] + $items["qty"];
        }

        $items["qty"] = (float) trim(preg_replace('/([^0-9\.])/i', '', $items["qty"]));
        $items["price"] = (float) trim(preg_replace('/([^0-9\.])/i', '', $items["price"]));

        // Calcular taxes inline (más rápido que función separada)
        $price_neto = $items["price"];
        $qty = $items["qty"];

        $items["priceneto"] = $price_neto;
        $items["totalpriceneto"] = $price_neto * $qty;

        // ICE
        $iceporcent = isset($items['icePorcent']) ? (float) $items['icePorcent'] : 0;
        $iceval = $iceporcent > 0 ? ($price_neto * $iceporcent) / 100 : 0;

        $items['iceval'] = $iceval;
        $items['toticeval'] = $iceval * $qty;
        $items['priceice'] = $price_neto + $iceval;
        $items['totalpriceice'] = ($price_neto + $iceval) * $qty;

        // IVA
        $base_iva = $price_neto + $iceval; /* La base imponible para el iva es el precio neto del producto + ICE si es que lo tiene */
        $items['itembaseiva'] = $base_iva;
        $items['totitembaseiva'] = $base_iva * $qty;

        /* iva por cada unidad del producto */
        $ivaporcent = isset($items['ivaPorcent']) ? (float) $items['ivaPorcent'] : 0;
        $ivaval = ($base_iva * $ivaporcent) / 100;

        $items['ivaval'] = $ivaval;
        $items['totivaval'] = $ivaval * $qty;

        $priceiva = $base_iva + $ivaval;
        $items['priceiva'] = $priceiva;
        $items['totalpriceiva'] = $priceiva * $qty;
        $items["total"] = $items["qty"] * $items["price"];

        // Actualizar totales incrementalmente (más rápido que recalcular todo)
        $this->updateTotalsIncremental($items, isset($this->cart[$rowid]) ? $this->cart[$rowid] : null);

        $this->cart[$rowid] = $items;

        return $rowid;
    }

//    private function _insert2($items = array(), $update = false, $rowid_) {
//        $randNum2 = rand(1, 100000);
//
//        if (!empty($rowid_)) {
//            $rowid = $rowid_;
//        } else {
//            if (isset($items['options'])) {
//                $rowid = md5($items["id"] . $randNum2 . implode('', $items['options']));
//            } else {
//                $rowid = md5($items["id"] . $randNum2);
//            }
//        }
//
//        $items["rowid"] = $rowid;
//
//        if (isset($this->cart[$rowid]) && !$update) {
//            $items["qty"] = $this->cart[$rowid]["qty"] + $items["qty"];
//        }
//
//        $items["qty"] = (float) trim(preg_replace('/([^0-9\.])/i', '', $items["qty"]));
//        $items["price"] = (float) trim(preg_replace('/([^0-9\.])/i', '', $items["price"]));
//
//        // Calcular taxes inline
//        $price_neto = $items["price"];
//        $qty = $items["qty"];
//
//        $items["priceneto"] = $price_neto;
//        $items["totalpriceneto"] = $price_neto * $qty;
//
//        $iceporcent = isset($items['icePorcent']) ? (float) $items['icePorcent'] : 0;
//        $iceval = $iceporcent > 0 ? ($price_neto * $iceporcent) / 100 : 0;
//
//        $items['iceval'] = $iceval;
//        $items['toticeval'] = $iceval * $qty;
//        $items['priceice'] = $price_neto + $iceval;
//        $items['totalpriceice'] = ($price_neto + $iceval) * $qty;
//
//        $base_iva = $price_neto + $iceval;
//        $items['itembaseiva'] = $base_iva;
//        $items['totitembaseiva'] = $base_iva * $qty;
//
//        $ivaporcent = isset($items['ivaporcent']) ? (float) $items['ivaporcent'] : 0;
//        $ivaval = ($base_iva * $ivaporcent) / 100;
//
//        $items['ivaval'] = $ivaval;
//        $items['totivaval'] = $ivaval * $qty;
//
//        $priceiva = $base_iva + $ivaval;
//        $items['priceiva'] = $priceiva;
//        $items['totalpriceiva'] = $priceiva * $qty;
//        $items["total"] = $items["qty"] * $items["price"];
//
//        $this->updateTotalsIncremental($items, isset($this->cart[$rowid]) ? $this->cart[$rowid] : null);
//
//        $this->cart[$rowid] = $items;
//
//        return $rowid;
//    }

    /**
     * Actualizar totales incrementalmente (sin recalcular todo)
     */
    private function updateTotalsIncremental($newItem, $oldItem = null) {
        $meta = &$this->cart['_meta'];

        // Restar valores antiguos si existe
        if ($oldItem) {
            $meta['total_cart'] -= ($oldItem['price'] * $oldItem['qty']);
            $meta['total_articles'] -= $oldItem['qty'];
            $meta['total_iva'] -= ($oldItem['totivaval'] ?? 0);

            $ivaporcent = isset($oldItem['ivaPorcent']) ? $oldItem['ivaPorcent'] : 0;
            if ($ivaporcent == 0) {
                $meta['tarif_cero'] -= ($oldItem['price'] * $oldItem['qty']);
                $meta['tarif_ceroneto'] -= ($oldItem['priceneto'] * $oldItem['qty']);
            } else {
                $meta['tarif_iva'] -= ($oldItem['price'] * $oldItem['qty']);
                $meta['tarif_ivaneto'] -= ($oldItem['priceneto'] * $oldItem['qty']);
            }

            $servicio = isset($oldItem['servicio']) ? $oldItem['servicio'] : 0;
            if ($servicio == 0) {
                $meta['total_bienes'] -= ($oldItem['price'] * $oldItem['qty']);
            } else {
                $meta['total_servicios'] -= ($oldItem['price'] * $oldItem['qty']);
            }
        }

        // Sumar valores nuevos
        $meta['total_cart'] += ($newItem['price'] * $newItem['qty']);
        $meta['total_articles'] += $newItem['qty'];
        $meta['total_iva'] += ($newItem['totivaval'] ?? 0);

        $ivaporcent = isset($newItem['ivaPorcent']) ? $newItem['ivaPorcent'] : 0;
        if ($ivaporcent == 0) {
            $meta['tarif_cero'] += ($newItem['price'] * $newItem['qty']);
            $meta['tarif_ceroneto'] += ($newItem['priceneto'] * $newItem['qty']);
        } else {
            $meta['tarif_iva'] += ($newItem['price'] * $newItem['qty']);
            $meta['tarif_ivaneto'] += ($newItem['priceneto'] * $newItem['qty']);
        }

        $servicio = isset($newItem['servicio']) ? $newItem['servicio'] : 0;
        if ($servicio == 0) {
            $meta['total_bienes'] += ($newItem['price'] * $newItem['qty']);
        } else {
            $meta['total_servicios'] += ($newItem['price'] * $newItem['qty']);
        }

        $meta['totalcart_iva'] = $meta['total_cart'] + $meta['total_iva'];

        // Invalidar cache
        $this->totalsCache = null;
    }

    /**
     * Obtener contenido sin los metadatos
     */
    public function getContent() {

        $result = [];
        foreach ($this->cart as $key => $item) {
            if ($key !== '_meta') {
                $result[$key] = $item;
            }
        }
        return empty($result) ? null : $result;
    }

    /**
     * Totales desde cache en memoria
     */
    public function totalCart() {
        return round($this->cart['_meta']['total_cart'], 4) ?? 0;
    }

    public function totalIva() {
        return round($this->cart['_meta']['total_iva'], 4) ?? 0;
    }

    public function totalCartIva() {
        return round($this->cart['_meta']['totalcart_iva'], 4) ?? 0;
    }

    public function totalBienes() {
        return round($this->cart['_meta']['total_bienes'], 4) ?? 0;
    }

    public function totalServicios() {
        return round($this->cart['_meta']['total_servicios'], 4) ?? 0;
    }

    public function totalArticles() {
        return $this->cart['_meta']['total_articles'] ?? 0;
    }

    public function tarifCero() {
        return round($this->cart['_meta']['tarif_cero'], 4) ?? 0;
    }

    public function tarifCeroNeto() {
        return round($this->cart['_meta']['tarif_ceroneto'], 4) ?? 0;
    }

    public function tarifIva() {
        return round($this->cart['_meta']['tarif_iva'], 2) ?? 0;
    }

    public function tarifIvaNeto() {
        return round($this->cart['_meta']['tarif_ivaneto'], 4) ?? 0;
    }

//    public function update2($item = array(), $rowidRand) {
//        if ($this->cart === null) {
//            throw new \Exception("Cart does not exist!");
//        }
//
//        if (isset($item['options'])) {
//            $rowid = $rowidRand . implode('', $item['options']);
//        } else {
//            $rowid = $rowidRand;
//        }
//
//        if (!isset($this->cart[$rowid])) {
//            throw new \Exception("The rowid $rowid does not exist!");
//        }
//
//        if ($rowid !== $this->cart[$rowid]["rowid"]) {
//            throw new \Exception("Can not update the options!");
//        }
//
//        $this->insert2($item, true, $rowid);
//        return true;
//    }

    public function update($item = [], $rowidRand) {
        if ($this->cart === null) {
            throw new \Exception("Cart does not exist!");
        }

        if (isset($item['options'])) {
            $rowid = md5($item['id'] . implode('', $item['options']));
        } else {
//            $rowid = md5($item["id"]);
            $rowid = $rowidRand;
        }

        if (!isset($this->cart[$rowid])) {
            throw new \Exception("The rowid $rowid does not exist!");
        }

        if ($rowid !== $this->cart[$rowid]["rowid"]) {
            throw new \Exception("Can not update the options!");
        }

        $this->insert($item, true, $rowidRand);
        return true;
    }

    public function has_options($rowid = '') {
        if (!isset($this->cart[$rowid]['options']) ||
                count($this->cart[$rowid]['options']) === 0) {
            return false;
        }

        return true;
    }

    public function removeItem($rowid = '') {
        if ($this->cart === null) {
            throw new \Exception("Cart does not exist!");
        }

        if (!isset($this->cart[$rowid])) {
            throw new \Exception("The rowid $rowid does not exist!");
        }

        // Actualizar totales restando el item
        $this->updateTotalsIncremental(
                ['price' => 0, 'qty' => 0, 'totivaval' => 0, 'priceneto' => 0, 'ivaporcent' => 0, 'servicio' => 0],
                $this->cart[$rowid]
        );

        unset($this->cart[$rowid]);
        $this->isDirty = true;
        $this->save();
        return true;
    }

    public function destroy() {
        $this->cart = [
            '_meta' => [
                'total_cart' => 0,
                'total_articles' => 0,
                'total_iva' => 0,
                'totalcart_iva' => 0,
                'total_bienes' => 0,
                'total_servicios' => 0,
                'tarif_cero' => 0,
                'tarif_ceroneto' => 0,
                'tarif_iva' => 0,
                'tarif_ivaneto' => 0,
                'ci_client' => null
            ]
        ];
        $this->isDirty = true;
        $this->save();
        return true;
    }

    public function setClientCI($data) {
        $this->cart['_meta']['ci_client'] = $data;
        $this->save();
        $this->isDirty = true;
    }

    public function getClientCI() {
        return $this->cart['_meta']['ci_client'] ?? null;
    }
}
