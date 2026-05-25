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
                'total_cart' => 0,
                'total_articles' => 0,
                'total_descuento' => 0,
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
                'impuestos' => [],
                'proveedor' => null
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
        $items = $this->calculateItem($items);

        $this->cart[$rowid] = $items;
        $this->recalculateTotals();

        return $rowid;
    }

    /**
     * Calculate all monetary fields for one row.
     */
    private function calculateItem($items) {
        $qty = $this->number($items["qty"] ?? $items["cantidad"] ?? 0);
        $price = $this->number($items["price"] ?? $items["precio"] ?? 0);

        if ($qty <= 0) {
            throw new \Exception("La cantidad debe ser mayor a cero.");
        }

        if ($price < 0) {
            throw new \Exception("El precio no puede ser negativo.");
        }

        $discountPercent = $this->number($items["descuento_porcentaje"] ?? $items["discountPercent"] ?? 0);
        $discountValue = $this->number($items["descuento_valor"] ?? $items["discountValue"] ?? $items["descuento"] ?? 0);

        if ($discountPercent < 0 || $discountValue < 0) {
            throw new \Exception("El descuento no puede ser negativo.");
        }

        if ($discountPercent > 100) {
            throw new \Exception("El descuento por porcentaje no puede superar el 100%.");
        }

        $discountPercentValue = $price * ($discountPercent / 100);
        $discountTotalUnit = min($price, $discountPercentValue + $discountValue);
        $priceNet = max(0, $price - $discountTotalUnit);
        $subtotalGross = $price * $qty;
        $subtotalNet = $priceNet * $qty;
        $discountTotal = $discountTotalUnit * $qty;

        $icePercent = $this->number($items["icePorcent"] ?? $items["ice_porcentaje"] ?? 0);
        $iceUnit = $icePercent > 0 ? ($priceNet * $icePercent) / 100 : 0;
        $iceTotal = $iceUnit * $qty;

        $irbpnrUnit = $this->number($items["irbpnr_unitario"] ?? $items["irbpnr"] ?? 0);
        $irbpnrTotal = $irbpnrUnit * $qty;

        $baseIvaUnit = $priceNet + $iceUnit;
        $baseIvaTotal = $baseIvaUnit * $qty;

        $ivaPercent = $this->number($items["ivaPorcent"] ?? $items["iva_porcentaje"] ?? $items["impt_porcentaje"] ?? 0);
        $ivaUnit = ($baseIvaUnit * $ivaPercent) / 100;
        $ivaTotal = $ivaUnit * $qty;

        $totalUnit = $priceNet + $iceUnit + $ivaUnit + $irbpnrUnit;
        $total = $subtotalNet + $iceTotal + $ivaTotal + $irbpnrTotal;

        $items["qty"] = $qty;
        $items["cantidad"] = $qty;
        $items["price"] = $price;
        $items["precio"] = $price;

        $items["descuento_porcentaje"] = $discountPercent;
        $items["descuento_valor"] = $discountValue;
        $items["descuento_porcentaje_valor"] = round($discountPercentValue, 4);
        $items["descuento_unitario"] = round($discountTotalUnit, 4);
        $items["descuento_total"] = round($discountTotal, 4);

        $items["priceneto"] = round($priceNet, 4);
        $items["precio_neto"] = round($priceNet, 4);
        $items["totalpriceneto"] = round($subtotalNet, 4);
        $items["subtotal"] = round($subtotalNet, 4);

        $items["icePorcent"] = $icePercent;
        $items["ice_porcentaje"] = $icePercent;
        $items["iceval"] = round($iceUnit, 4);
        $items["ice_valor_unitario"] = round($iceUnit, 4);
        $items["toticeval"] = round($iceTotal, 4);
        $items["ice_valor"] = round($iceTotal, 4);

        $items["itembaseiva"] = round($baseIvaUnit, 4);
        $items["base_iva_unitaria"] = round($baseIvaUnit, 4);
        $items["totitembaseiva"] = round($baseIvaTotal, 4);
        $items["base_iva_total"] = round($baseIvaTotal, 4);

        $items["ivaPorcent"] = $ivaPercent;
        $items["iva_porcentaje"] = $ivaPercent;
        $items["ivaval"] = round($ivaUnit, 4);
        $items["iva_valor_unitario"] = round($ivaUnit, 4);
        $items["totivaval"] = round($ivaTotal, 4);
        $items["iva_valor"] = round($ivaTotal, 4);

        $items["irbpnr_unitario"] = round($irbpnrUnit, 4);
        $items["irbpnr_total"] = round($irbpnrTotal, 4);

        $items["priceiva"] = round($priceNet + $iceUnit + $ivaUnit, 4);
        $items["totalpriceiva"] = round($subtotalNet + $iceTotal + $ivaTotal, 4);
        $items["total_unitario"] = round($totalUnit, 4);
        $items["total"] = round($total, 4);
        $items["subtotal_bruto"] = round($subtotalGross, 4);

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
            $subtotalGross = $this->number($item["subtotal_bruto"] ?? 0);
            $subtotalNet = $this->number($item["subtotal"] ?? $item["totalpriceneto"] ?? 0);
            $discountTotal = $this->number($item["descuento_total"] ?? 0);
            $ivaTotal = $this->number($item["iva_valor"] ?? $item["totivaval"] ?? 0);
            $iceTotal = $this->number($item["ice_valor"] ?? $item["toticeval"] ?? 0);
            $irbpnrTotal = $this->number($item["irbpnr_total"] ?? 0);
            $total = $this->number($item["total"] ?? 0);
            $ivaPercent = $this->number($item["iva_porcentaje"] ?? $item["ivaPorcent"] ?? 0);
            $servicio = (int) ($item["servicio"] ?? $item["prod_servicio"] ?? 0);

            $meta["total_cart"] += $subtotalGross;
            $meta["total_articles"] += $qty;
            $meta["total_descuento"] += $discountTotal;
            $meta["total_subtotal_neto"] += $subtotalNet;
            $meta["total_iva"] += $ivaTotal;
            $meta["total_ice"] += $iceTotal;
            $meta["total_irbpnr"] += $irbpnrTotal;
            $meta["total_general"] += $total;

            if ($servicio === 0) {
                $meta["total_bienes"] += $subtotalNet;
            } else {
                $meta["total_servicios"] += $subtotalNet;
            }

            if ($ivaPercent == 0) {
                $meta["tarif_cero"] += $subtotalGross;
                $meta["tarif_ceroneto"] += $subtotalNet;
            } else {
                $meta["tarif_iva"] += $subtotalGross;
                $meta["tarif_ivaneto"] += $subtotalNet;
            }

            $taxKey = (string) ($item["fk_impuesto_tarifa"] ?? $item["fk_impuestotarifa"] ?? $ivaPercent);
            if (!isset($meta["impuestos"][$taxKey])) {
                $meta["impuestos"][$taxKey] = [
                    "fk_impuesto_tarifa" => $item["fk_impuesto_tarifa"] ?? $item["fk_impuestotarifa"] ?? null,
                    "codigo" => $item["impt_codigo"] ?? null,
                    "detalle" => $item["impt_detalle"] ?? ("IVA " . $ivaPercent . "%"),
                    "porcentaje" => $ivaPercent,
                    "base" => 0,
                    "valor" => 0
                ];
            }

            $meta["impuestos"][$taxKey]["base"] += $this->number($item["base_iva_total"] ?? $item["totitembaseiva"] ?? 0);
            $meta["impuestos"][$taxKey]["valor"] += $ivaTotal;
        }

        $meta["total_cart"] = round($meta["total_cart"], 4);
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
        $meta["tarif_iva"] = round($meta["tarif_iva"], 4);
        $meta["tarif_ivaneto"] = round($meta["tarif_ivaneto"], 4);

        foreach ($meta["impuestos"] as $key => $tax) {
            $meta["impuestos"][$key]["base"] = round($tax["base"], 4);
            $meta["impuestos"][$key]["valor"] = round($tax["valor"], 4);
        }

        $provider = $this->cart["_meta"]["proveedor"] ?? null;
        $this->cart["_meta"] = $meta;
        $this->cart["_meta"]["proveedor"] = $provider;
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

    public function getMeta() {
        return $this->cart['_meta'];
    }

    public function getImpuestos() {
        return array_values($this->cart['_meta']['impuestos'] ?? []);
    }

    public function totalCart() {
        return round($this->cart['_meta']['total_cart'], 4);
    }

    public function totalDescuento() {
        return round($this->cart['_meta']['total_descuento'], 4);
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

    public function setProveedor($data) {
        $this->cart['_meta']['proveedor'] = $data;
        $this->save();
        return true;
    }

    public function getProveedor() {
        return $this->cart['_meta']['proveedor'] ?? null;
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
