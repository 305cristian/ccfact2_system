<?php

namespace Modules\Compras\Libraries;

use CodeIgniter\Session\Session;

class ComprasCartLib {

    /**
     * Content cart cache.
     *
     * @var array|null
     */
    private $cart = null;

    /**
     * Session key.
     *
     * @var string
     */
    private $instance;

    /**
     * Session instance.
     *
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     */
    public function __construct($instance = "compra") {
        $this->instance = "comprasCart" . $instance;
        $this->session = \Config\Services::session();
        $this->loadCart();
    }

    /**
     * Load cart from session.
     */
    private function loadCart() {
        if ($this->cart !== null) {
            return;
        }

        $this->cart = $this->session->get($this->instance);

        if ($this->cart === null) {
            $this->cart = $this->emptyCart();
        }
    }

    /**
     * Empty cart structure.
     */
    private function emptyCart() {
        return [
            '_meta' => [
                'total_subtotal_bruto' => 0,
                'total_articles' => 0,
                'total_descuento' => 0,
                'total_descuento_global' => 0,
                'total_recargo' => 0,
                'total_servicios_adc' => 0,
                'total_subtotal_neto' => 0,
                'total_iva' => 0,
                'total_ice' => 0,
                'total_irbpnr' => 0,
                'total_general' => 0,
                'total_bienes' => 0,
                'total_servicios' => 0,
                'tarif_cero' => 0,
                'tarif_ceroneto' => 0,
                'tarif_iva' => 0,
                'tarif_ivaneto' => 0,
                'tarif_excento' => 0,
                'tarif_excentoneto' => 0,
                'tarif_noobjeto' => 0,
                'tarif_noobjetoneto' => 0,
                'bases_impuesto' => [],
            ]
        ];
    }

    /**
     * Persist cart in session.
     */
    public function save() {
        $this->session->set($this->instance, $this->cart);
        return true;
    }

    public function instance_name() {
        return $this->instance;
    }

    /**
     * Insert or update one item.
     */
    public function insert($items = [], $update = false, $rowid_ = null) {
        if (!is_array($items) || empty($items)) {
            throw new \Exception("The insert method must receive an array.");
        }

        if (!isset($items["id"]) || !isset($items["qty"]) || !isset($items["price"])) {
            throw new \Exception("Id, qty and price are required fields.");
        }

        if (!is_numeric($items["id"]) || !is_numeric($items["qty"]) || !is_numeric($items["price"])) {
            throw new \Exception("Id, qty and price must be numbers.");
        }

        $rowid = $this->_insert($items, $update, $rowid_);

        if (!$rowid) {
            throw new \Exception("Error saving cart.");
        }

        $this->save();
        return true;
    }

    /**
     * Internal insert.
     */
    private function _insert($items = [], $update = false, $rowid_ = null) {
        $randomNumber = rand(1, 1000000);
        $allowDuplicates = $this->boolValue($items["permitirDuplicados"] ?? false);

        if (!empty($rowid_)) {
            $rowid = $rowid_;
        } else {
            $rowid = $allowDuplicates ? md5($items["id"] . $randomNumber) : md5($items["id"]);
        }

        if (isset($this->cart[$rowid]) && !$update) {
            $items["qty"] = $this->cart[$rowid]["qty"] + $items["qty"];
        }

        $items["rowid"] = $rowid;

        $itemsCalculate = $this->calculateItem($items);

        $this->cart[$rowid] = $itemsCalculate;
        $this->recalculateTotals();

        return $rowid;
    }

    /**
     * Calculate all monetary fields for one row.
     */
    private function calculateItem($items) {
        $qty = $this->number($items["qty"] ?? 0);
        $price = $this->number($items["price"] ?? 0);

        if ($qty <= 0) {
            throw new \Exception("La cantidad debe ser mayor a cero.");
        }

        if ($price < 0) {
            throw new \Exception("El precio no puede ser negativo.");
        }

        $discountPercent = $this->number($items["discountPercent"] ?? 0);
        $discountValue = $this->number($items["discountValue"] ?? 0);

        if ($discountPercent < 0 || $discountValue < 0) {
            throw new \Exception("El descuento no puede ser negativo.");
        }

        if ($discountPercent > 100) {
            throw new \Exception("El descuento por porcentaje no puede superar el 100%.");
        }

//        $discountPercentValue = $price * ($discountPercent / 100);
        $discountTotalUnit = min($price, $discountValue); //Se aplica MIN para que el descuento no sea menor al precio
        $priceNeto = max(0, $price - $discountTotalUnit); //Se aplica MAX para que el prcio neto no sea menor 0
        $subtotalBruto = $price * $qty;
        $subtotalNeto = $priceNeto * $qty;
        $discountTotal = $discountTotalUnit * $qty;

        $icePercent = $this->number($items["icePorcent"] ?? 0);
        $iceUnitario = $icePercent > 0 ? ($priceNeto * $icePercent) / 100 : 0;
        $iceTotal = $iceUnitario * $qty;

        $irbpnrUnitario = $this->number($items["irbpnrUnitario"] ?? 0);
        $irbpnrTotal = $irbpnrUnitario * $qty;

        $baseIvaUnit = $priceNeto + $iceUnitario;
        $baseIvaTotal = $baseIvaUnit * $qty;

        $ivaPercent = $this->number($items["ivaPorcent"] ?? 0);
        $ivaUnit = ($baseIvaUnit * $ivaPercent) / 100;
        $ivaTotal = $ivaUnit * $qty;

        $totalUnit = $priceNeto + $iceUnitario + $ivaUnit + $irbpnrUnitario;
        $total = $subtotalNeto + $iceTotal + $ivaTotal + $irbpnrTotal;

        $items["qty"] = $qty;
        $items["cantidad"] = $qty;
        $items["price"] = $price;

        $items["descuentoTotal"] = round($discountTotal, 4);
        $items["discountPercent"] = round($discountPercent, 4);
        $items["discountValue"] = round($discountValue, 4);

        $items["priceNeto"] = round($priceNeto, 4);
        $items["subtotalNeto"] = round($subtotalNeto, 4);

        $items["icePorcent"] = $icePercent;
        $items["iceValUnit"] = round($iceUnitario, 4);
        $items["iceValTotal"] = round($iceTotal, 4);

        $items["itemBaseIvaUnit"] = round($baseIvaUnit, 4);
        $items["itemBaseIvaTotal"] = round($baseIvaTotal, 4);

        $items["ivaPorcent"] = $ivaPercent;
        $items["ivaValUnit"] = round($ivaUnit, 4);
        $items["ivaValTotal"] = round($ivaTotal, 4);

        $items["irbpnr_total"] = round($irbpnrTotal, 4);

        $items["priceIva"] = round($priceNeto + $iceUnitario + $ivaUnit, 4);
        $items["totalPriceIva"] = round($subtotalNeto + $iceTotal + $ivaTotal, 4);
        $items["totalUnitario"] = round($totalUnit, 4);
        $items["total"] = round($total, 4);
        $items["subtotalBruto"] = round($subtotalBruto, 4);

        return $items;
    }

    /**
     * Recalculate totals from scratch to keep tax grouping consistent.
     */
    private function recalculateTotals() {
        $meta = $this->emptyCart()['_meta'];

        foreach ($this->cart as $key => $item) {
            if ($key === '_meta') {
                continue;
            }

            $qty = $this->number($item["qty"] ?? 0);
            $subtotalBruto = $this->number($item["subtotalBruto"] ?? 0);
            $subtotalNeto = $this->number($item["subtotalNeto"] ?? 0);
            $discountTotal = $this->number($item["descuentoTotal"] ?? 0);
            $ivaTotal = $this->number($item["ivaValTotal"] ?? 0);
            $iceTotal = $this->number($item["iceValTotal"] ?? 0);
            $irbpnrTotal = $this->number($item["irbpnr_total"] ?? 0);
            $total = $this->number($item["total"] ?? 0);
            $ivaPercent = $this->number($item["ivaPorcent"] ?? 0);
            $servicio = (int) ($item["servicio"] ?? 0);
            $codigoImpuestoSelect = (int) ($item["codigoImpuestoSelect"] ?? 0);

            $meta["total_subtotal_bruto"] += $subtotalBruto;
            $meta["total_articles"] += $qty;
            $meta["total_descuento"] += $discountTotal;
            $meta["total_subtotal_neto"] += $subtotalNeto;
            $meta["total_iva"] += $ivaTotal;
            $meta["total_ice"] += $iceTotal;
            $meta["total_irbpnr"] += $irbpnrTotal;
            $meta["total_general"] += $total;

            if ($servicio === 0) {
                $meta["total_bienes"] += $subtotalNeto;
            } else {
                $meta["total_servicios"] += $subtotalNeto;
            }

            $taxKey = (string) ($codigoImpuestoSelect );

            if (!isset($meta["bases_impuesto"][$taxKey])) {

                $meta["bases_impuesto"][$taxKey] = [
                    "codigo" => $codigoImpuestoSelect ?? null,
                    "detalle" => $item["detalleImpuestoSelect"] ?? '',
                    "porcentaje" => $ivaPercent,
                    "subtotal_bruto" => 0,
                    "subtotal_neto" => 0,
                    "iva" => 0,
                ];
            }

            $meta["bases_impuesto"][$taxKey]["subtotal_bruto"] += $subtotalBruto;
            $meta["bases_impuesto"][$taxKey]["subtotal_neto"] += $subtotalNeto;
            $meta["bases_impuesto"][$taxKey]["iva"] += $ivaTotal;

            /*
              |--------------------------------------------------------------------------
              | Totales requeridos por SRI
              |--------------------------------------------------------------------------
             */

            if ($codigoImpuestoSelect == 0 && $ivaPercent == 0) {

                $meta["tarif_cero"] += $subtotalBruto;
                $meta["tarif_ceroneto"] += $subtotalNeto;
            } elseif ($codigoImpuestoSelect == 6) {

                $meta["tarif_noobjeto"] += $subtotalBruto;
                $meta["tarif_noobjetoneto"] += $subtotalNeto;
            } elseif ($codigoImpuestoSelect == 7) {

                $meta["tarif_excento"] += $subtotalBruto;
                $meta["tarif_excentoneto"] += $subtotalNeto;
            }
        }



        $meta['total_descuento_global'] = $this->number($this->cart['_meta']['total_descuento_global'] ?? 0);
        $meta['total_recargo'] = $this->number($this->cart['_meta']['total_recargo'] ?? 0);
        $meta['total_servicios_adc'] = $this->number($this->cart['_meta']['total_servicios_adc'] ?? 0);

        $this->aplicarDescuentoGlobal($meta);

        $meta["total_subtotal_bruto"] = round($meta["total_subtotal_bruto"], 4);
        $meta["total_descuento"] = round($meta["total_descuento"], 4);
        $meta["total_subtotal_neto"] = round($meta["total_subtotal_neto"], 4);
        $meta["total_iva"] = round($meta["total_iva"], 4);
        $meta["total_ice"] = round($meta["total_ice"], 4);
        $meta["total_irbpnr"] = round($meta["total_irbpnr"], 4);
        $meta["total_general"] = round($meta["total_general"], 4);
        $meta["total_bienes"] = round($meta["total_bienes"], 4);
        $meta["total_servicios"] = round($meta["total_servicios"], 4);

        $meta["tarif_cero"] = round($meta["tarif_cero"], 4);
        $meta["tarif_ceroneto"] = round($meta["tarif_ceroneto"], 4);

        $meta["tarif_noobjeto"] = round($meta["tarif_noobjeto"], 4);
        $meta["tarif_noobjetoneto"] = round($meta["tarif_noobjetoneto"], 4);

        $meta["tarif_excento"] = round($meta["tarif_excento"], 4);
        $meta["tarif_excentoneto"] = round($meta["tarif_excentoneto"], 4);

        $meta["tarif_iva"] = round($meta["tarif_iva"], 4);
        $meta["tarif_ivaneto"] = round($meta["tarif_ivaneto"], 4);


        foreach ($meta["bases_impuesto"] as $key => $base) {

            $meta["bases_impuesto"][$key]["subtotal_bruto"] = round($base["subtotal_bruto"], 4);

            $meta["bases_impuesto"][$key]["subtotal_neto"] = round($base["subtotal_neto"], 4);

            $meta["bases_impuesto"][$key]["iva"] = round($base["iva"], 4);
        }

        $this->cart["_meta"] = $meta;
    }

    private function aplicarDescuentoGlobal(array &$meta) {

        /*
          |--------------------------------------------------------------------------
          | Aplicar descuento global proporcional a las bases
          |--------------------------------------------------------------------------
         */
        $meta['total_general'] = $meta['total_subtotal_neto'] + $meta['total_iva'] + $meta['total_ice'] + $meta['total_irbpnr'] + $meta['total_recargo'] + $meta['total_servicios_adc'];

        $descuentoGlobal = $this->number($meta['total_descuento_global'] ?? 0);

        if ($descuentoGlobal <= 0) {
            return;
        }
        $subtotalNetoOriginal = $meta['total_subtotal_neto'];

        if ($subtotalNetoOriginal <= 0) {
            return;
        }

        $nuevoTotalIva = 0;

        foreach ($meta['bases_impuesto'] as $key => &$base) {

            $subtotalBaseOriginal = $base['subtotal_neto'];

            $proporcion = $subtotalBaseOriginal / $subtotalNetoOriginal;

            $descuentoAsignado = $descuentoGlobal * $proporcion;

            $nuevoSubtotal = $subtotalBaseOriginal - $descuentoAsignado;

            $base['subtotal_neto'] = $nuevoSubtotal;

            $base['iva'] = ($nuevoSubtotal * $base['porcentaje']) / 100;

            $nuevoTotalIva += $base['iva'];
        }

        unset($base);

        /*
          |--------------------------------------------------------------------------
          | Recalcular bases SRI
          |--------------------------------------------------------------------------
         */

        $meta['tarif_ceroneto'] = 0;
        $meta['tarif_ivaneto'] = 0;
        $meta['tarif_excentoneto'] = 0;
        $meta['tarif_noobjetoneto'] = 0;

        foreach ($meta['bases_impuesto'] as $base) {

            $codigo = (int) $base['codigo'];

            if ($codigo === 0 && $base['porcentaje'] == 0) {
                $meta['tarif_ceroneto'] += $base['subtotal_neto'];
            } elseif ($codigo === 6) {
                $meta['tarif_noobjetoneto'] += $base['subtotal_neto'];
            } elseif ($codigo === 7) {
                $meta['tarif_excentoneto'] += $base['subtotal_neto'];
            } else {
                $meta['tarif_ivaneto'] += $base['subtotal_neto'];
            }
        }

        /*
          |--------------------------------------------------------------------------
          | Totales generales
          |--------------------------------------------------------------------------
         */

        $meta['total_subtotal_neto'] = $subtotalNetoOriginal - $descuentoGlobal;

        $meta['total_iva'] = $nuevoTotalIva;

        $meta['total_general'] = $meta['total_subtotal_neto'] + $meta['total_iva'] + $meta['total_ice'] + $meta['total_irbpnr'] + $meta['total_recargo'] + $meta['total_servicios_adc'];
    }

    /**
     * Update cart row.
     */
    public function update($item = [], $rowidRand = null) {
        if ($this->cart === null) {
            throw new \Exception("Cart does not exist.");
        }

        $rowid = $rowidRand ?: ($item["rowid"] ?? null);

        if (empty($rowid) || !isset($this->cart[$rowid])) {
            throw new \Exception("The rowid $rowid does not exist.");
        }

        if ($rowid !== $this->cart[$rowid]["rowid"]) {
            throw new \Exception("Can not update the options.");
        }

        return $this->insert($item, true, $rowid);
    }

    /**
     * Remove row.
     */
    public function removeItem($rowid = '') {
        if ($this->cart === null) {
            throw new \Exception("Cart does not exist.");
        }

        if (!isset($this->cart[$rowid])) {
            throw new \Exception("The rowid $rowid does not exist.");
        }

        unset($this->cart[$rowid]);

        // Si ya no quedan items, limpiar valores globales
        if (empty($this->getContent())) {

            $this->cart['_meta']['total_descuento_global'] = 0;
            $this->cart['_meta']['total_recargo'] = 0;
            $this->cart['_meta']['total_servicios_adc'] = 0;
        }

        $this->recalculateTotals();
        $this->save();

        return true;
    }

    /**
     * Update valores globales.
     */
    public function updateValoresGlobales(float $descuentoGlobal = 0, float $recargo = 0, float $serviciosAdc = 0) {

        $this->cart['_meta']['total_descuento_global'] = $descuentoGlobal;
        $this->cart['_meta']['total_recargo'] = $recargo;
        $this->cart['_meta']['total_servicios_adc'] = $serviciosAdc;

        $this->recalculateTotals();

        $this->save();

        return true;
    }

    /**
     * Get cart rows without metadata.
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

//    public function getMeta() {
//        return $this->cart['_meta'];
//    }

    public function getImpuestos() {
        return array_values($this->cart['_meta']['bases_impuesto'] ?? []);
    }

    public function totalSubtotalBruto() {
        return round($this->cart['_meta']['total_subtotal_bruto'], 4);
    }

    public function totalDescuentoItems() {
        return round($this->cart['_meta']['total_descuento'], 4);
    }

    public function totalDescuentoGlobal() {
        return round($this->cart['_meta']['total_descuento_global'], 4);
    }

    public function totalRecargo() {
        return round($this->cart['_meta']['total_recargo'], 4);
    }

    public function totalServiciosAdc() {
        return round($this->cart['_meta']['total_servicios_adc'], 4);
    }

    public function totalExcentoIva() {
        return round($this->cart['_meta']['tarif_excentoneto'], 4);
    }

    public function totalnoObjetoImpuestos() {
        return round($this->cart['_meta']['tarif_noobjetoneto'], 4);
    }

    public function totalSubtotalNeto() {
        return round($this->cart['_meta']['total_subtotal_neto'], 4);
    }

    public function totalIva() {
        return round($this->cart['_meta']['total_iva'], 4);
    }

    public function totalIce() {
        return round($this->cart['_meta']['total_ice'], 4);
    }

    public function totalIrbpnr() {
        return round($this->cart['_meta']['total_irbpnr'], 4);
    }

    public function totalGeneral() {
        return round($this->cart['_meta']['total_general'], 4);
    }

    public function totalBienes() {
        return round($this->cart['_meta']['total_bienes'], 4);
    }

    public function totalServicios() {
        return round($this->cart['_meta']['total_servicios'], 4);
    }

    public function totalArticles() {
        return $this->cart['_meta']['total_articles'] ?? 0;
    }

    public function tarifCero() {
        return round($this->cart['_meta']['tarif_cero'], 4);
    }

    public function tarifCeroNeto() {
        return round($this->cart['_meta']['tarif_ceroneto'], 4);
    }

    public function tarifIva() {
        return round($this->cart['_meta']['tarif_iva'], 4);
    }

    public function tarifIvaNeto() {
        return round($this->cart['_meta']['tarif_ivaneto'], 4);
    }

    public function destroy() {
        $this->cart = $this->emptyCart();
        $this->save();
        return true;
    }

    private function number($value) {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) trim(preg_replace('/([^0-9\.\-])/i', '', (string) $value));
    }

    private function boolValue($value) {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'si'], true);
    }
}
