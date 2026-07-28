<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Libraries;

/**
 * Description of EntradasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 17 oct 2025
 * @time 5:19:30 p.m.
 */
use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;

class EntradasLib {

    protected $ccm;
    protected $user;
    protected $tipotransaccionCod = '39';
    protected $productLib;
    protected $stockBodLib;

    public function __construct() {

        //Import Servicios
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');

        //Import Librerias
        $this->productLib = new ProductoLib();
        $this->stockBodLib = new StockBodegaLib();
    }

    public function saveAjuste($cartData, $dataPostAjuste) {

        $esPendiente = ($dataPostAjuste->ajenEstado == 1);
        $secuencial = $this->ccm->getData('cc_ajuste_entrada', ['fk_proyecto' => getProyectoId()], 'ajen_secuencial', ['ajen_secuencial' => 'DESC'], 1);

        $datos = [
            'fk_proyecto' => getProyectoId(),
            'ajen_secuencial' => (isset($secuencial) ? $secuencial->ajen_secuencial + 1 : 1),
            'ajen_fecha' => $dataPostAjuste->ajenFecha,
            'ajen_observaciones' => $dataPostAjuste->ajenObservaciones,
            'ajen_estado' => $dataPostAjuste->ajenEstado,
            'ajen_tipo' => $dataPostAjuste->ajenTipo,
            'ajen_fecha_anulacion' => null,
            'ajen_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataPostAjuste->ajenMotivo,
            'fk_bodega' => $dataPostAjuste->ajenBodega,
            'fk_user_id' => $this->user->id,
            'ajen_fecha_aprobacion' => $esPendiente ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esPendiente ? null : $this->user->id,
            'fk_proveedor' => $dataPostAjuste->ajenProveedor,
            'fk_centro_costo' => $dataPostAjuste->ajenCentrocosto,
            'codigo_sustento' => $dataPostAjuste->ajenSustento,
            'iva_porcentaje' => ivaPredeterminado(),
            'ajen_total_items' => $cartData->totalItems,
            'ajen_total' => $cartData->totalCart,
            'ajen_subtotal_bienes' => $cartData->totalBienes,
            'ajen_subtotal_servicios' => $cartData->totalServicios,
            'ajen_totalcartiva' => $cartData->totalCartIva,
            'ajen_totaliva' => $cartData->totalIva,
            'ajen_tarifacero' => $cartData->tarifCero,
            'ajen_tarifaiva' => $cartData->tarifIva,
            'ajen_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajen_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ajen_items_duplicados' => $dataPostAjuste->ajenPermitirDuplicados,
        ];

        $save = $this->ccm->guardar($datos, 'cc_ajuste_entrada');

        return $save;
    }

    public function saveAjusteDetalle($ajusteId, $val, $lote) {

        $datos = [
            'fk_ajuste_entrada' => $ajusteId,
            'fk_producto' => $val->id,
            'fk_lote' => $lote,
            'ajend_itemcantidad' => $val->qty,
            'ajend_itemcosto' => $val->price,
            'ajend_itemcostoxcantidad' => $val->total,
            'ajend_observacion' => null,
            'ajend_estado' => 1,
        ];

        $saveDetalle = $this->ccm->guardar($datos, 'cc_ajuste_entrada_det');

        return $saveDetalle;
    }

    /**
     * Función para actualizar un ajuste de entrada existente, modificando los datos del ajuste y recalculando los totales en base a los datos proporcionados
     * @param object $cartData Objeto con los datos del carrito de compras, incluyendo totales, subtotales, impuestos y tarifas
     * @param object $dataPostAjuste Objeto con los datos del ajuste de entrada a actualizar, incluyendo fecha, estado, bodega, proveedor, centro de costo, motivo de ajuste, etc
     * @param int $ajusteId El identificador único del ajuste de entrada a actualizar
    */
    public function updateAjuste(object $cartData, object $dataPostAjuste, int $ajusteId) {

        $esPendiente = ($dataPostAjuste->ajenEstado == 1);

        $datos = [
            'ajen_fecha' => $dataPostAjuste->ajenFecha,
            'ajen_observaciones' => $dataPostAjuste->ajenObservaciones,
            'ajen_estado' => $dataPostAjuste->ajenEstado,
            'ajen_tipo' => $dataPostAjuste->ajenTipo,
            'ajen_fecha_anulacion' => null,
            'ajen_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataPostAjuste->ajenMotivo,
            'fk_bodega' => $dataPostAjuste->ajenBodega,
            'fk_user_id' => $this->user->id,
            'ajen_fecha_aprobacion' => $esPendiente ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esPendiente ? null : $this->user->id,
            'fk_proveedor' => $dataPostAjuste->ajenProveedor,
            'fk_centro_costo' => $dataPostAjuste->ajenCentrocosto,
            'codigo_sustento' => $dataPostAjuste->ajenSustento,
            'iva_porcentaje' => getSettings('IVA'),
            'ajen_total_items' => $cartData->totalItems,
            'ajen_total' => $cartData->totalCart,
            'ajen_subtotal_bienes' => $cartData->totalBienes,
            'ajen_subtotal_servicios' => $cartData->totalServicios,
            'ajen_totalcartiva' => $cartData->totalCartIva,
            'ajen_totaliva' => $cartData->totalIva,
            'ajen_tarifacero' => $cartData->tarifCero,
            'ajen_tarifaiva' => $cartData->tarifIva,
            'ajen_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajen_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ajen_items_duplicados' => $dataPostAjuste->ajenPermitirDuplicados,
        ];

        $update = $this->ccm->actualizar('cc_ajuste_entrada', $datos, ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()]);

        return $update;
    }

    /**
     * Actualiza todo el kardex (general, bodega y lote)
     * 
     * @param int $ajusteId ID del documento (ajuste, compra, venta, etc)
     * @param object $producto Array con datos del producto ['id', 'qty', 'price', 'total']
     * @param int|null $loteId ID del lote (null si no maneja lotes)
     * @param object $dataPostAjuste (fecha, estado, bodega, etc)
     * @return array
     */
    public function updateKardex(int $ajusteId, object $producto, int|null $loteId, object $dataPostAjuste):array {
        try {
            $fecha = $dataPostAjuste->ajenFecha ?? date('Y-m-d');
            $hora = date('H:i:s');
            $bodegaId = $dataPostAjuste->ajenBodega;

            // 1. Actualizar kardex general
            $kardex = $this->actualizarKardexGeneral($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $dataPostAjuste->ajenTipo);
            if (!$kardex['kardexId']) {
                return [
                    'status' => 'error',
                    'msg' => 'Error al actualizar kardex general.',
                ];
            }

            // 2. Actualizar kardex por bodega
            $kardexBodegaOk = $this->actualizarKardexBodega($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
            if (!$kardexBodegaOk) {
                return [
                    'status' => 'error',
                    'msg' => 'Error al actualizar kardex por bodega',
                ];
            }

            // 3. Si maneja lotes, actualizar kardex por lote
            if ($loteId) {
                $kardexLoteOk = $this->actualizarKardexBodegaLote($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
                if (!$kardexLoteOk) {
                    return [
                        'status' => 'error',
                        'msg' => 'Error al actualizar kardex por lote',
                    ];
                }
            }

            return ['status' => 'success'];
        } catch (\Throwable $e) {
            throw new \Exception('Error al generar kardex: ' . $e->getMessage() . $e->getTraceAsString());
        }
    }

    /**
     * Función para actualizar el kardex general de un producto, calculando el nuevo stock, costo promedio y costo último, e insertando un registro en la tabla de kardex
     * @param object $producto Objeto con los datos del producto a ajustar, incluyendo id, cantidad, precio y total
     * @param int $ajusteId El identificador único del ajuste de entrada asociado al movimiento de inventario
     * @param int|null $loteId El identificador del lote asociado al movimiento de inventario (null si el producto no maneja lotes)
     * @param string $fecha La fecha del movimiento de inventario
     * @param string $hora La hora del movimiento de inventario
     * @param int $bodegaId El identificador de la bodega donde se realiza el movimiento de inventario
     * @param string $tipoAjuste El tipo de ajuste que se está realizando (e.g., 'AJUSTE_NORMAL', 'AJUSTE_INICIAL', 'AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO'), utilizado para determinar la lógica de cálculo de costos y stock en el kardex
     * @return array Un array con el resultado de la actualización del kardex, incluyendo el estado de la operación, mensaje descriptivo y los costos calculados (costo promedio y costo último) si la operación fue exitosa    
     * La función actualiza el kardex general de un producto realizando los siguientes pasos:
     * 1. Obtiene el stock actual del producto utilizando la biblioteca ProductoLib.
     * 2. Calcula el nuevo stock sumando o restando la cantidad del producto según el tipo de ajuste (positivo o negativo).
     * 3. Obtiene el costo de inventario actual del producto y el costo de inventario total de la empresa.
     * 4. Calcula el nuevo costo de inventario del producto sumando o restando el total del producto al costo de inventario actual, y calcula el nuevo costo de inventario total sumando o restando el total del producto al costo de inventario total actual.
     * 5. Calcula el costo promedio y costo último del producto según la lógica definida para cada tipo de ajuste.
     * 6. Inserta un registro en la tabla de kardex con los datos del movimiento de inventario, incluyendo el nuevo stock, costos calculados, fecha, hora, bodega, lote y usuario responsable.
     * 7. Si la inserción en el kardex es exitosa, actualiza los costos del producto utilizando la biblioteca ProductoLib y actualiza el costo de inventario total de la empresa.
     * 8. Devuelve un array con el resultado de la operación, incluyendo el estado ('success' o 'error'), mensaje descriptivo y los costos calculados (costo promedio y costo último) si la operación fue exitosa, o un mensaje de error si ocurrió
    */
    public function actualizarKardexGeneral(object $producto, int $ajusteId, int|null $loteId, string $fecha, string $hora, int $bodegaId, string $tipoAjuste):array {

        // Obtengo stock actual del producto
        $stockActual = $this->productLib->getStockProducto($producto->id);
        $nuevoStock = $stockActual + $producto->qty;

        // Obtengo costo de inventario del producto
        $costoInvProducto = $this->productLib->getCostoInventarioProducto($producto->id);
        $nuevoCostoInvProducto = $costoInvProducto + $producto->total;

        // Obtengo costo de inventario total (empresa)
        $costoInvTotal = $this->productLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal + $producto->total;

        $promedioActual = (float) $this->productLib->getCostoPromedio($producto->id);
        $costoUltimoActual = (float) $this->productLib->getCostoUltimo($producto->id);

        // Calcular costo promedio
        if ($tipoAjuste === 'AJUSTE_NORMAL' || $tipoAjuste === 'AJUSTE_INICIAL') {
            
            $costoPromedio = $promedioActual > 0 ? $promedioActual : $producto->price;
            $costoUltimo = $costoUltimoActual > 0 ? $costoUltimoActual : $producto->price;
        } else {
            $costoPromedio = $nuevoStock > 0 ? ($nuevoCostoInvProducto / $nuevoStock) : $promedioActual;
            $costoUltimo = (float) $producto->price > 0 ? (float) $producto->price : $costoUltimoActual;
        }


        // Insertar registro en kardex
        $dataKardex = [
            'fk_producto' => $producto->id,
            'kar_kardex' => $producto->qty,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimo,
            'kar_total_costo' => abs($producto->total), //SIEMPRE POSITIVO
            'kar_documento_id' => $ajusteId,
            'kar_codigo_transaccion' => $this->tipotransaccionCod,
            'kar_fecha' => $fecha,
            'kar_hora' => $hora,
            'kar_costoinventario_producto' => $nuevoCostoInvProducto,
            'kar_costoinventario_total' => $nuevoCostoInvTotal,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexId = $this->ccm->guardar($dataKardex, 'cc_kardex');

        if ($kardexId) {
            // Actualizar producto
            $this->productLib->updateCostosProducto($producto->id, $nuevoStock, $costoPromedio, $costoUltimo, $nuevoCostoInvProducto);

            // Actualizar costo inventario total
            $this->productLib->actualizarCostoInventarioTotal($nuevoCostoInvTotal);
        }

        $responseKardex = [
            'kardexId' => $kardexId,
            'costoPromedio' => $costoPromedio,
            'costoUltimo' => $costoUltimo,
        ];
        return $responseKardex;
    }

    /**
     * Función para actualizar el kardex por bodega de un producto, calculando el nuevo stock en la bodega y actualizando o creando el registro correspondiente en la tabla de kardex por bodega
     * @param object $producto Objeto con los datos del producto a ajustar, incluyendo id, cantidad, precio y total
     * @param int $ajusteId El identificador único del ajuste de entrada asociado al movimiento de inventario
     * @param int|null $loteId El identificador del lote asociado al movimiento de inventario (null si el producto no maneja lotes)
     * @param string $fecha La fecha del movimiento de inventario
     * @param string $hora La hora del movimiento de inventario
     * @param int $bodegaId El identificador de la bodega donde se realiza el movimiento de inventario
     * @param array $kardexCostos Array con los costos calculados en el kardex general (costo promedio y costo último) para utilizar en el kardex por bodega
     * @return int Retorna true si la actualización del kardex por bodega fue exitosa, o false si ocurrió un error durante el proceso de actualización del kardex por bodega
     * La función actualiza el kardex por bodega de un producto realizando los siguientes pasos:
     * 1. Obtiene el stock actual del producto en la bodega utilizando la biblioteca StockBodegaLib.
     * 2. Calcula el nuevo stock en la bodega sumando o restando la cantidad del producto según el tipo de ajuste (positivo o negativo).
     * 3. Inserta un registro en la tabla de kardex por bodega con los datos del movimiento de inventario, incluyendo el nuevo stock en la bodega, costos calculados en el kardex general, fecha, hora, lote y usuario responsable.
     * 4. Si la inserción en el kardex por bodega es exitosa, actualiza o crea el registro de stock por bodega utilizando la biblioteca StockBodegaLib para reflejar el nuevo stock en la bodega.
     * 5. Retorna true si la actualización del kardex por bodega fue exitosa, o false si ocurrió un error durante el proceso de actualización del kardex por bodega     
    */
    public function actualizarKardexBodega(object $producto, int $ajusteId, int|null $loteId, string $fecha, string $hora, int $bodegaId, array $kardexCostos): int{
        // Obtener stock actual en bodega
        $stockBodega = $this->stockBodLib->getStockBodega($bodegaId, $producto->id);
        $nuevoStockBodega = $stockBodega + $producto->qty;

        // Insertar registro en kardex_bodega
        $dataKardexBodega = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $producto->qty,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $kardexCostos['costoPromedio'],
            'karb_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karb_documento_id' => $ajusteId,
            'karb_codigo_transaccion' => $this->tipotransaccionCod,
            'karb_fecha' => $fecha,
            'karb_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexBodegaId = $this->ccm->guardar($dataKardexBodega, 'cc_kardex_bodega');

        if ($kardexBodegaId) {
            // Actualizamos o creamos registro de stock por bodega
            $this->stockBodLib->actualizarStockBodega($bodegaId, $producto->id, $nuevoStockBodega);
        }

        return $kardexBodegaId;
    }

    /**
     * Función para actualizar el kardex por bodega y lote de un producto, calculando el nuevo stock en la bodega por lote y actualizando o creando el registro correspondiente en la tabla de kardex por bodega y lote
     * @param object $producto Objeto con los datos del producto a ajustar, incluyendo id, cantidad, precio y total
     * @param int $ajusteId El identificador único del ajuste de entrada asociado al movimiento de inventario
     * @param int $loteId El identificador del lote asociado al movimiento de inventario
     * @param string $fecha La fecha del movimiento de inventario
     * @param string $hora La hora del movimiento de inventario
     * @param int $bodegaId El identificador de la bodega donde se realiza el movimiento de inventario
     * @param array $kardexCostos Array con los costos calculados en el kardex general (costo promedio y costo último) para utilizar en el kardex por bodega y lote
     * @return int Retorna true si la actualización del kardex por bodega y lote fue exitosa, o false si ocurrió un error durante el proceso de actualización del kardex por bodega y lote
     * La función actualiza el kardex por bodega y lote de un producto realizando los siguientes pasos:
     * 1. Obtiene el stock actual del producto en la bodega por lote utilizando la biblioteca StockBodegaLib.   
     * 2. Calcula el nuevo stock en la bodega por lote sumando o restando la cantidad del producto según el tipo de ajuste (positivo o negativo).
     * 3. Inserta un registro en la tabla de kardex por bodega y lote con los datos del movimiento de inventario, incluyendo el nuevo stock en la bodega por lote, costos calculados en el kardex general, fecha, hora, lote y usuario responsable.
     * 4. Si la inserción en el kardex por bodega y lote es exitosa, actualiza o crea el registro de stock por bodega y lote utilizando la biblioteca StockBodegaLib para reflejar el nuevo stock en la bodega por lote.
     * 5. Retorna true si la actualización del kardex por bodega y lote fue exitosa, o false si ocurrió un error durante el proceso de actualización del kardex por bodega y lote   
    */
    public function actualizarKardexBodegaLote(object $producto, int $ajusteId, int|null $loteId, string $fecha, string $hora, int $bodegaId, array $kardexCostos):int {
        // Obtener stock actual en bodega por lote
        $stockBodegaLote = $this->stockBodLib->getStockBodegaLote($bodegaId, $producto->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote + $producto->qty;

        // Insertar registro en kardex_bodega_lote
        $dataKardexLote = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => $producto->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $kardexCostos['costoPromedio'],
            'karbl_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karbl_documento_id' => $ajusteId,
            'karbl_codigo_transaccion' => $this->tipotransaccionCod,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexLoteId = $this->ccm->guardar($dataKardexLote, 'cc_kardex_bodega_lote');

        if ($kardexLoteId) {
            // Actualizar o crear registro de stock por bodega y lote
            $this->stockBodLib->actualizarStockBodegaLote($bodegaId, $producto->id, $loteId, $nuevoStockBodegaLote);
        }

        return $kardexLoteId;
    }

    /**
     * Función para anular un ajuste de entrada, actualizando el estado del ajuste, el motivo de anulación y la fecha de anulación, y realizando los movimientos inversos en el kardex para los productos involucrados
     * @param int $ajusteId El identificador único del ajuste de entrada a anular
     * @param string $motivo El motivo de anulación proporcionado por el usuario
     * @param string $tipoAjuste El tipo de ajuste que se está anulando
     * @return array Un array con el resultado de la operación, incluyendo el estado ('success', 'error' o 'warning') y un mensaje descriptivo del resultado de la anulación    
     * La función realiza los siguientes pasos para anular un ajuste de entrada:
     * 1. Obtiene el ajuste de entrada correspondiente al ajusteId proporcionado.
     * 2. Valida si el ajuste existe y si su estado es diferente de anulado (-1). Si el ajuste no existe o ya está anulado, retorna un mensaje de error o advertencia.
     * 3. Si el ajuste está en estado pendiente (1), actualiza el estado del ajuste a anulado (-1), registra el motivo de anulación y la fecha de anulación, y retorna un mensaje de éxito indicando que el ajuste en estado borrador fue anulado exitosamente.
     * 4. Si el ajuste está aprobado, obtiene el detalle del ajuste y realiza los movimientos inversos en el kardex para cada producto involucrado, actualizando el stock y los costos según corresponda.
     * 5. Marca el ajuste como anulado, actualiza el estado, la fecha de anulación, el usuario que anuló y el motivo de anulación.
     * 6. Busca el asiento contable asociado al ajuste y, si existe, lo anula automáticamente, registrando el motivo de anulación.
     * 7. Retorna un mensaje de éxito indicando que el ajuste fue anulado exitosamente, o un mensaje de error si ocurrió algún problema durante el proceso de anulación.
    */
    public function anularAjuste( int $ajusteId, string $motivo, string $tipoAjuste):array {

        // Obtenemos el ajuste
        $ajuste = $this->ccm->getData('cc_ajuste_entrada', ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()], '*', null, 1);
        if (!$ajuste) {
            return ['status' => 'error', 'msg' => 'No se encontró el ajuste especificado.'];
        }

        // Validamos estado
        if ($ajuste->ajen_estado == -1) {
            return ['status' => 'warning', 'msg' => 'El ajuste ya se encuentra anulado.'];
        }

        //Anulamos en pendiente
        if ($ajuste->ajen_estado == 1) {
            $dataSet = [
                'ajen_estado' => '-1',
                'ajen_motivo_anulacion' => $motivo,
                'ajen_fecha_anulacion' => date('Y-m-d H:i:s')
            ];
            $this->ccm->actualizar('cc_ajuste_entrada', $dataSet, ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()]);
            return ['status' => 'success', 'msg' => 'Ajuste en estado BORRADOR anulado exitosamente'];
        }

        // Cargamos detalle
        $detalle = $this->ccm->getData('cc_ajuste_entrada_det', ['fk_ajuste_entrada' => $ajusteId]);
        if (!$detalle) {
            return ['status' => 'error', 'msg' => 'No se encontró detalle asociado al ajuste.'];
        }


        try {
            $this->tipotransaccionCod = '41'; // Código definido para ANULACIÓN DE AJUSTE DE ENTRADA
            // Creamos objeto similar a $dataPostAjuste para pasar a updateKardex
            $dataAjuste = (object) [
                        'ajenFecha' => date('Y-m-d'),
                        'ajenBodega' => $ajuste->fk_bodega,
                        'ajenEstado' => -1,
                        'ajenTipo' => $tipoAjuste
            ];

            foreach ($detalle as $val) {
                // Armamos el producto para el movimiento inverso
                $producto = (object) [
                            'id' => $val->fk_producto,
                            'qty' => -abs($val->ajend_itemcantidad), // cantidad negativa
                            'price' => $val->ajend_itemcosto,
                            'total' => -abs($val->ajend_itemcostoxcantidad),
                            'tieneLote' => 0,
                            'servicio' => 0
                ];

                // Obtenemos si el producto maneja lote y si es de tipo servicio
                $prodInfo = $this->ccm->getData('cc_productos', ['id' => $val->fk_producto], 'prod_ctrllote, prod_isservicio', null, 1);
                if ($prodInfo) {
                    $producto->tieneLote = $prodInfo->prod_ctrllote;
                    $producto->servicio = $prodInfo->prod_isservicio;
                }

                // No recalculamos si es servicio
                if ($producto->servicio == '0') {
                    $kardexOk = $this->updateKardex($ajusteId, $producto, $val->fk_lote, $dataAjuste);
                    if ($kardexOk['status'] !== 'success') {
                        return ['status' => 'error', 'msg' => 'Ha ocurrido un error al actualizr el kardex'];
                    }
                }
            }
            // Marcamos el ajuste como anulado
            $dataSet = [
                'ajen_estado' => -1,
                'ajen_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anulacion' => $this->user->id,
                'ajen_motivo_anulacion' => $motivo ?? 'Anulación manual',
            ];
            $this->ccm->actualizar('cc_ajuste_entrada', $dataSet, ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()]);

            // Buscamos asiento contable asociado al ajuste
            $asientoId = $this->ccm->getValueWhere('cc_asiento_contable', ['ac_documento_id' => $ajusteId, 'ac_codigo_transaccion' => 39, 'fk_proyecto' => getProyectoId(), 'ac_estado' => 1], 'id');

            if ($asientoId) {
                $dataSet = [
                    'ac_estado' => -1,
                    'ac_fecha_anulacion' => date('Y-m-d H:i:s'),
                    'fk_user_id_anulacion' => $this->user->id,
                    'ac_motivo_anulacion' => "Asiento anulado automáticamente por anulación del ajuste de entrada #{$ajuste->ajen_secuencial}"
                ];
                $this->ccm->actualizar('cc_asiento_contable', $dataSet, ['id' => $asientoId, 'fk_proyecto' => getProyectoId()]);
            }


            return [
                'status' => 'success',
                'msg' => "Ajuste #{$ajuste->ajen_secuencial} anulado exitosamente."
            ];
        } catch (\Exception $exc) {
            return ['status' => 'error', 'msg' => 'Error al anular ajuste: ' . $exc->getMessage()];
        } finally {
            $this->tipotransaccionCod = '39'; // volvemos al código de AJUSTE ENTRADA
        }
    }
}
