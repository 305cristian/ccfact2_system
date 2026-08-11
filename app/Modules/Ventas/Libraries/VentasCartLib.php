<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Ventas\Libraries;

use CodeIgniter\Session\Session;

/**
 * Description of VentasCartLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 4:16:40 p.m.
 */
class VentasCartLib {

    private $cart = null;
    private string $instance;
    protected Session $session;

    public function __construct($instance = "venta") {
        $this->instance = "ventasCart" . $instance;
        $this->session = \Config\Services::session();
        $this->loadCart();
    }

    private function loadCart(): void {
        if ($this->cart !== null) {
            return;
        }

        $this->cart = $this->session->get($this->instance);

        if ($this->cart === null) {
            $this->cart = $this->emptyCart();
        }
    }

    private function emptyCart(): array {
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
                'tarif_cerobruto' => 0,
                'tarif_ceroneto' => 0,
                'tarif_ivabruto' => 0,
                'tarif_ivaneto' => 0,
                'tarif_excento' => 0,
                'tarif_excentoneto' => 0,
                'tarif_noobjeto' => 0,
                'tarif_noobjetoneto' => 0,
                'subtotal_bienes_bruto' => 0,
                'subtotal_bienes_neto' => 0,
                'subtotal_servicios_bruto' => 0,
                'subtotal_servicios_neto' => 0,
                'base_iva' => 0,
                'iva_bienes' => 0,
                'iva_servicios' => 0,
                'bases_impuesto' => [],
            ]
        ];
    }

    public function save(): bool {
        $this->session->set($this->instance, $this->cart);
        return true;
    }

    public function insert(array $items = [], bool $update = false, ?string $rowid_ = null): bool {
        if (empty($items)) {
            throw new \Exception("The insert method must receive an array.");
        }

        if (!isset($items["id"], $items["qty"], $items["price"])) {
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

    private function _insert(array $items = [], bool $update = false, ?string $rowid_ = null): string {
        $randomNumber = rand(1, 1000000);
        $allowDuplicates = $this->boolValue($items["permitirDuplicados"] ?? false);
        $rowid = $rowid_ ?: ($allowDuplicates ? md5($items["id"] . $randomNumber) : md5($items["id"]));

        if (isset($this->cart[$rowid]) && !$update) {
            $items["qty"] = $this->cart[$rowid]["qty"] + $items["qty"];
        }

        $items["rowid"] = $rowid;
        $this->cart[$rowid] = $this->calculateItem($items);
        $this->recalculateTotals();

        return $rowid;
    }

    private function calculateItem(array $items): array {
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

        $discountTotalUnit = $discountValue > 0 ? min($price, $discountValue) : ($price * ($discountPercent / 100));
        $priceNeto = max(0, $price - $discountTotalUnit);
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

    private function recalculateTotals(): void {
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
                $meta["subtotal_bienes_bruto"] += $subtotalBruto;
                $meta["subtotal_bienes_neto"] += $subtotalNeto;
                $meta["iva_bienes"] += $ivaTotal;
            } else {
                $meta["subtotal_servicios_bruto"] += $subtotalBruto;
                $meta["subtotal_servicios_neto"] += $subtotalNeto;
                $meta["iva_servicios"] += $ivaTotal;
            }

            $taxKey = (string) $codigoImpuestoSelect;

            if (!isset($meta["bases_impuesto"][$taxKey])) {
                $meta["bases_impuesto"][$taxKey] = [
                    "impuesto_tarifa_id" => $item["impuestoSelect"] ?? null,
                    "codigo" => $codigoImpuestoSelect,
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

            if ($codigoImpuestoSelect === 0 && $ivaPercent == 0) {
                $meta["tarif_cerobruto"] += $subtotalBruto;
                $meta["tarif_ceroneto"] += $subtotalNeto;
            } elseif ($codigoImpuestoSelect === 6) {
                $meta["tarif_noobjeto"] += $subtotalBruto;
                $meta["tarif_noobjetoneto"] += $subtotalNeto;
            } elseif ($codigoImpuestoSelect === 7) {
                $meta["tarif_excento"] += $subtotalBruto;
                $meta["tarif_excentoneto"] += $subtotalNeto;
            } else {
                $meta["tarif_ivabruto"] += $subtotalBruto;
                $meta["tarif_ivaneto"] += $subtotalNeto;
            }
        }

        $meta['total_descuento_global'] = $this->number($this->cart['_meta']['total_descuento_global'] ?? 0);
        $meta['total_recargo'] = $this->number($this->cart['_meta']['total_recargo'] ?? 0);
        $meta['total_servicios_adc'] = $this->number($this->cart['_meta']['total_servicios_adc'] ?? 0);
        $meta['base_iva'] = 0;

        foreach ($meta['bases_impuesto'] as $base) {
            if ($base['porcentaje'] > 0) {
                $meta['base_iva'] += $base['subtotal_neto'];
            }
        }

        foreach ($meta as $key => $value) {
            if ($key === 'bases_impuesto') {
                continue;
            }
            $meta[$key] = is_numeric($value) ? round((float) $value, 4) : $value;
        }

        foreach ($meta["bases_impuesto"] as $key => $base) {
            $meta["bases_impuesto"][$key]["subtotal_bruto"] = round($base["subtotal_bruto"], 4);
            $meta["bases_impuesto"][$key]["subtotal_neto"] = round($base["subtotal_neto"], 4);
            $meta["bases_impuesto"][$key]["iva"] = round($base["iva"], 4);
        }

        $this->cart["_meta"] = $meta;
    }

    public function update(array $item = [], ?string $rowidRand = null): bool {
        $rowid = $rowidRand ?: ($item["rowid"] ?? null);

        if (empty($rowid) || !isset($this->cart[$rowid])) {
            throw new \Exception("The rowid $rowid does not exist.");
        }

        return $this->insert($item, true, $rowid);
    }

    public function removeItem(string $rowid = ''): bool {
        if (!isset($this->cart[$rowid])) {
            throw new \Exception("The rowid $rowid does not exist.");
        }

        unset($this->cart[$rowid]);

        if (empty($this->getContent())) {
            $this->cart['_meta']['total_descuento_global'] = 0;
            $this->cart['_meta']['total_recargo'] = 0;
            $this->cart['_meta']['total_servicios_adc'] = 0;
        }

        $this->recalculateTotals();
        $this->save();

        return true;
    }

    public function getContent(): ?array {
        $result = [];

        foreach ($this->cart as $key => $item) {
            if ($key !== '_meta') {
                $result[$key] = $item;
            }
        }

        return empty($result) ? null : $result;
    }

    public function getImpuestos(): array {
        return array_values($this->cart['_meta']['bases_impuesto'] ?? []);
    }

    public function totalSubtotalBruto(): float { return round($this->cart['_meta']['total_subtotal_bruto'], 4); }
    public function totalSubtotalNeto(): float { return round($this->cart['_meta']['total_subtotal_neto'], 4); }
    public function totalDescuentoItems(): float { return round($this->cart['_meta']['total_descuento'], 4); }
    public function totalDescuentoGlobal(): float { return round($this->cart['_meta']['total_descuento_global'], 4); }
    public function totalRecargo(): float { return round($this->cart['_meta']['total_recargo'], 4); }
    public function totalServiciosAdc(): float { return round($this->cart['_meta']['total_servicios_adc'], 4); }
    public function totalExcentoIva(): float { return round($this->cart['_meta']['tarif_excentoneto'], 4); }
    public function totalnoObjetoImpuestos(): float { return round($this->cart['_meta']['tarif_noobjetoneto'], 4); }
    public function totalIva(): float { return round($this->cart['_meta']['total_iva'], 4); }
    public function totalIce(): float { return round($this->cart['_meta']['total_ice'], 4); }
    public function totalIrbpnr(): float { return round($this->cart['_meta']['total_irbpnr'], 4); }
    public function totalGeneral(): float { return round($this->cart['_meta']['total_general'], 4); }
    public function totalBienesBruto(): float { return round($this->cart['_meta']['subtotal_bienes_bruto'], 4); }
    public function totalBienesNeto(): float { return round($this->cart['_meta']['subtotal_bienes_neto'], 4); }
    public function totalServiciosBruto(): float { return round($this->cart['_meta']['subtotal_servicios_bruto'], 4); }
    public function totalServiciosNeto(): float { return round($this->cart['_meta']['subtotal_servicios_neto'], 4); }
    public function totalArticles(): float { return $this->cart['_meta']['total_articles'] ?? 0; }
    public function tarifCeroBruto(): float { return round($this->cart['_meta']['tarif_cerobruto'], 4); }
    public function tarifCeroNeto(): float { return round($this->cart['_meta']['tarif_ceroneto'], 4); }
    public function tarifIvaBruto(): float { return round($this->cart['_meta']['tarif_ivabruto'], 4); }
    public function tarifIvaNeto(): float { return round($this->cart['_meta']['tarif_ivaneto'], 4); }
    public function totalBaseIva(): float { return round($this->cart['_meta']['base_iva'], 4); }
    public function totalIvaBienes(): float { return round($this->cart['_meta']['iva_bienes'], 4); }
    public function totalIvaServicios(): float { return round($this->cart['_meta']['iva_servicios'], 4); }

    public function destroy(): bool {
        $this->cart = $this->emptyCart();
        $this->save();
        return true;
    }

    private function number($value): float {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) trim(preg_replace('/([^0-9\.\-])/i', '', (string) $value));
    }

    private function boolValue($value): bool {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'si'], true);
    }
}
