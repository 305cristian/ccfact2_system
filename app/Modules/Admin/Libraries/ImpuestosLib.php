<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ImpuestosLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 1 ago 2026
 * @time 1:26:20 p.m.
 */

namespace Modules\Admin\Libraries;

use Modules\Admin\Models\ImpuestosModel;
use RuntimeException;
use function service;

class ImpuestosLib {

    //put your code here

    protected $ccm;
    protected $user;
    protected ImpuestosModel $impModel;
    protected $db;

    public function __construct() {
        //IMPORTAMOS SERVICIOS
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->db = \Config\Database::connect();

        $this->impModel = new ImpuestosModel();
    }

    public function aplicarCambioMasivoProductos(int $tarifaOrigenId, int $tarifaDestinoId, int $impuestoId, string $metodoCalculo, float $porcentajeOrigen, float $porcentajeDestino): array {

        $productos = $this->impModel->getProductosPorTarifa($tarifaOrigenId, $impuestoId, true);

        if (!$productos) {
            return [
                'productos' => 0,
                'precios' => 0,
            ];
        }

        $productosIds = array_map(static fn($producto) => (int) $producto->fk_producto, $productos);
        $preciosActualizados = 0;
        $porcentajeProductoActualizado = 0;

        $this->db->transBegin();

        if ($metodoCalculo === 'ASUMIR_VALOR_IVA') {
            $preciosActualizados = $this->impModel->actualizarPreciosManteniendoPvp($productosIds, $porcentajeOrigen, $porcentajeDestino);
        }

        if ($impuestoId === 1) {
            $porcentajeProductoActualizado = $this->ccm->actualizarIn('cc_productos',['prod_ivaporcentage' => $porcentajeDestino],['prod_estado' => 1],['id' => $productosIds] );
        }

        foreach ($productosIds as $productoId) {
            $this->actualizarTarifaProducto($productoId, $tarifaOrigenId, $tarifaDestinoId, $impuestoId);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new RuntimeException('No se pudo completar la actualizacion masiva de IVA.');
        }

        $this->db->transCommit();

        return [
            'productos' => count($productosIds),
            'precios' => $preciosActualizados,
            'porcentaje_producto' => $porcentajeProductoActualizado,
        ];
    }

    public function actualizarTarifaProducto(int $productoId, int $tarifaOrigenId, int $tarifaDestinoId, int $impuestoId): void {

        $whereDestino = [
            'fk_producto' => $productoId,
            'fk_impuestotarifa' => $tarifaDestinoId,
            'fk_impuesto' => $impuestoId,
        ];

        $existeDestino = $this->ccm->getData('cc_producto_impuestotarifa', $whereDestino, 'fk_producto', null, 1);

        if ($existeDestino) {
            $this->ccm->eliminar('cc_producto_impuestotarifa', ['fk_producto' => $productoId, 'fk_impuestotarifa' => $tarifaOrigenId, 'fk_impuesto' => $impuestoId,]);
            return;
        }

        $whereData = [
            'fk_producto' => $productoId,
            'fk_impuestotarifa' => $tarifaOrigenId,
            'fk_impuesto' => $impuestoId,
        ];
        $this->ccm->actualizar('cc_producto_impuestotarifa', ['fk_impuestotarifa' => $tarifaDestinoId], $whereData);
    }

    //===========================================
    //ACTUALIZACIÓN DE CUENTAS CONTABLES MASIVO
    //===========================================

    public function aplicarCambioMasivoCuentasProductos(int $tarifaId, int $impuestoId, ?string $cuentaCompraOrigen, ?string $cuentaCompraDestino, ?string $cuentaVentaOrigen, ?string $cuentaVentaDestino): array {

        $productos = $this->impModel->getProductosPorTarifa($tarifaId, $impuestoId, true);

        if (!$productos) {
            return [
                'productos' => 0,
                'compras' => 0,
                'ventas' => 0,
            ];
        }

        $productosIds = array_map(static fn($producto) => (int) $producto->fk_producto, $productos);

        $this->db->transBegin();

        $comprasActualizadas = 0;
        $ventasActualizadas = 0;

        if ($cuentaCompraOrigen && $cuentaCompraDestino) {

            $dataSet = ['fk_cuentacontablecompras' => $cuentaCompraDestino];
            $whereData = ['fk_cuentacontablecompras' => $cuentaCompraOrigen, 'prod_estado' => 1];
            $comprasActualizadas = $this->ccm->actualizarIn("cc_productos", $dataSet, $whereData, ['id' => $productosIds]);
        }

        if ($cuentaVentaOrigen && $cuentaVentaDestino) {

            $dataSet = ['fk_cuentacontableventas' => $cuentaVentaDestino];
            $whereData = ['fk_cuentacontableventas' => $cuentaVentaOrigen, 'prod_estado' => 1];

            $ventasActualizadas = $this->ccm->actualizarIn("cc_productos", $dataSet, $whereData, ['id' => $productosIds]);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new \RuntimeException('No se pudo completar la actualizacion masiva de cuentas contables.');
        }

        $this->db->transCommit();

        return [
            'productos' => count($productosIds),
            'compras' => $comprasActualizadas,
            'ventas' => $ventasActualizadas,
        ];
    }
}
