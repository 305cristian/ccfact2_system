<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesSalida\Libraries;

/**
 * Description of SalidasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 nov 2025
 * @time 12:23:01 p.m.
 */

namespace Modules\AjustesSalida\Libraries;

use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Libraries\ReservasLib;

class SalidasLib {

    protected $ccm;
    protected $user;
    protected $tipotransaccionCod = '38'; // AJUSTE SALIDA
    protected ProductoLib $productLib;
    protected StockBodegaLib $stockBodLib;
    protected ReservasLib $reservasLib;

    public function __construct() {

        //IMPORTAMOS SERVICIOS
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');

        //IMPORTAMOS LIBRERIAS
        $this->productLib = new ProductoLib();
        $this->stockBodLib = new StockBodegaLib();
        $this->reservasLib = new ReservasLib();
    }

    /**
     * Función para guardar un nuevo ajuste de salida en la base de datos, utilizando los datos del carrito de ajustes y la información proporcionada por el usuario a través de un formulario. La función saveAjuste recibe los datos del carrito de ajustes (cartData) y los datos del formulario (dataPostAjuste), y realiza el proceso de guardado del ajuste de salida en la base de datos. Primero, se determina si el ajuste se guarda como borrador o como aprobado, y luego se obtiene el siguiente número secuencial para el ajuste de salida. A continuación, se prepara un array de datos con toda la información necesaria para crear el registro del ajuste de salida, incluyendo detalles como la fecha, observaciones, estado, bodega, usuario responsable, totales del ajuste, entre otros. Finalmente, se utiliza el modelo ccModel para guardar el registro en la tabla cc_ajuste_salida y se devuelve el resultado de la operación. Esta función es esencial para permitir a los usuarios registrar nuevos ajustes de salida en el sistema, asegurando que toda la información relevante se almacene correctamente en la base de datos y que el proceso de creación del ajuste sea eficiente y confiable.
     * @param object $cartData Objeto que contiene los datos del carrito de ajustes, incluyendo totales, subtotales, impuestos, y otros detalles relacionados con los productos agregados al ajuste de salida.
     * @param object $dataPostAjuste Objeto que contiene los datos del formulario proporcionados por el usuario para crear el ajuste de salida, incluyendo fecha, observaciones, estado, bodega, motivo de ajuste, centro de costo, cliente, tipo de ajuste, servicio asociado, y otros detalles relevantes para la creación del ajuste de salida.  
     * @return int|false Retorna el identificador del ajuste de salida creado si la operación fue exitosa, o false si ocurrió un error durante el proceso de guardado del ajuste de salida en la base de datos.
     */
    public function saveAjuste(object $cartData, object $dataPostAjuste): int|false {
        $esBorrador = ($dataPostAjuste->ajesEstado == 1);

        $secuencial = $this->ccm->getData('cc_ajuste_salida', ['fk_proyecto' => getProyectoId()], 'ajes_secuencial', ['ajes_secuencial' => 'DESC'], 1);

        $datos = [
            'fk_proyecto' => getProyectoId(),
            'ajes_secuencial' => (isset($secuencial) ? $secuencial->ajes_secuencial + 1 : 1),
            'ajes_fecha' => $dataPostAjuste->ajesFecha,
            'ajes_observaciones' => $dataPostAjuste->ajesObservaciones,
            'ajes_estado' => $dataPostAjuste->ajesEstado,
            'ajes_fecha_anulacion' => null,
            'ajes_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataPostAjuste->ajesMotivo,
            'fk_bodega' => $dataPostAjuste->ajesBodega,
            'fk_user_id' => $this->user->id,
            'ajes_fecha_aprobacion' => $esBorrador ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esBorrador ? null : $this->user->id,
            'fk_centro_costo' => $dataPostAjuste->ajesCentrocosto,
            'fk_cliente' => !empty($dataPostAjuste->ajesCliente) ? $dataPostAjuste->ajesCliente : 1,
            'iva_porcentaje' => ivaPredeterminado(),
            'ajes_total_items' => $cartData->totalItems,
            'ajes_total' => $cartData->totalCart,
            'ajes_subtotal_bienes' => $cartData->totalBienes,
            'ajes_subtotal_servicios' => $cartData->totalServicios,
            'ajes_totalcartiva' => $cartData->totalCartIva,
            'ajes_totaliva' => $cartData->totalIva,
            'ajes_tarifacero' => $cartData->tarifCero,
            'ajes_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajes_tarifaiva' => $cartData->tarifIva,
            'ajes_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ajes_items_duplicados' => $dataPostAjuste->ajesPermitirDuplicados,
            'ajes_tipo' => $dataPostAjuste->ajesTipo,
            'fk_servicio' => $dataPostAjuste->ajesServicio,
        ];

        $save = $this->ccm->guardar($datos, 'cc_ajuste_salida');

        return $save;
    }

    /**
     * Función para guardar el detalle de un ajuste de salida en la base de datos, utilizando el identificador del ajuste de salida, los datos del producto a agregar al detalle, y el identificador del lote (si aplica). La función saveAjusteDetalle recibe el identificador del ajuste de salida (ajusteId), los datos del producto (val) que se van a agregar al detalle del ajuste, y el identificador del lote (lote) si el producto maneja lotes. Luego, se prepara un array de datos con la información necesaria para crear el registro del detalle del ajuste de salida, incluyendo el identificador del ajuste, el producto, el lote, la cantidad, el costo, el costo por cantidad, observaciones, y estado. Finalmente, se utiliza el modelo ccModel para guardar el registro en la tabla cc_ajuste_salida_det y se devuelve el resultado de la operación. Esta función es esencial para permitir a los usuarios agregar productos al detalle de un ajuste de salida, asegurando que toda la información relevante se almacene correctamente en la base de datos y que el proceso de creación del detalle del ajuste sea eficiente y confiable.
     * @param int $ajusteId Identificador del ajuste de salida al cual se va a agregar el detalle del producto.
     * @param object $val Objeto que contiene los datos del producto a agregar al detalle del ajuste de salida, incluyendo el identificador del producto, la cantidad, el precio, el total, y otros detalles relevantes para el registro del detalle del ajuste de salida.
     * @param int|null $lote Identificador del lote si el producto maneja lotes, o null si el producto no maneja lotes. Este parámetro es opcional y se utiliza para registrar el lote asociado al detalle del ajuste de salida en caso de que el producto lo requiera.
     * @return int|false Retorna el identificador del detalle del ajuste de salida creado si la operación fue exitosa, o false si ocurrió un error durante el proceso de guardado del detalle del ajuste de salida en la base de datos.
     
    */
    public function saveAjusteDetalle(int $ajusteId, object $val, int|null $lote) {
        $datos = [
            'fk_ajuste_salida' => $ajusteId,
            'fk_producto' => $val->id,
            'fk_lote' => $lote,
            'ajsd_itemcantidad' => $val->qty,
            'ajsd_itemcosto' => $val->price,
            'ajsd_itemcostoxcantidad' => $val->total,
            'ajsd_observacion' => null,
            'ajsd_estado' => 1,
        ];

        return $this->ccm->guardar($datos, 'cc_ajuste_salida_det');
    }

    /**
     * Función para actualizar un ajuste de salida existente en la base de datos, utilizando los datos del carrito de ajustes y la información proporcionada por el usuario a través de un formulario. La función updateAjuste recibe los datos del carrito de ajustes (cartData), los datos del formulario (dataPostAjuste), y el identificador del ajuste de salida a actualizar (ajusteId). Luego, se determina si el ajuste se guarda como borrador o como aprobado, y se prepara un array de datos con toda la información necesaria para actualizar el registro del ajuste de salida, incluyendo detalles como la fecha, observaciones, estado, bodega, usuario responsable, totales del ajuste, entre otros. Finalmente, se utiliza el modelo ccModel para actualizar el registro en la tabla cc_ajuste_salida con el nuevo conjunto de datos y se devuelve el resultado de la operación. Esta función es esencial para permitir a los usuarios modificar ajustes de salida existentes en el sistema, asegurando que toda la información relevante se actualice correctamente en la base de datos y que el proceso de actualización del ajuste sea eficiente y confiable.
     * @param object $cartData Objeto que contiene los datos del carrito de ajustes, incluyendo totales, subtotales, impuestos, y otros detalles relacionados con los productos agregados al ajuste de salida.
     * @param object $dataPostAjuste Objeto que contiene los datos del formulario proporcionados por el usuario para actualizar el ajuste de salida, incluyendo fecha, observaciones, estado, bodega, motivo de ajuste, centro de costo, cliente, tipo de ajuste, servicio asociado, y otros detalles relevantes para la actualización del ajuste de salida.
     * @param int $ajusteId Identificador del ajuste de salida que se va a actualizar en la base de datos.
    */
    public function updateAjuste(object $cartData, object $dataPostAjuste, int $ajusteId) {
        $esBorrador = ($dataPostAjuste->ajesEstado == 1);

        $datos = [
            'ajes_fecha' => $dataPostAjuste->ajesFecha,
            'ajes_observaciones' => $dataPostAjuste->ajesObservaciones,
            'ajes_estado' => $dataPostAjuste->ajesEstado,
            'ajes_fecha_anulacion' => null,
            'ajes_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataPostAjuste->ajesMotivo,
            'fk_bodega' => $dataPostAjuste->ajesBodega,
            'fk_user_id' => $this->user->id,
            'ajes_fecha_aprobacion' => $esBorrador ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esBorrador ? null : $this->user->id,
            'fk_centro_costo' => $dataPostAjuste->ajesCentrocosto,
            'fk_cliente' => isset($dataPostAjuste->ajesCliente) ? $dataPostAjuste->ajesCliente : 1,
            'iva_porcentaje' => ivaPredeterminado(),
            'ajes_total_items' => $cartData->totalItems,
            'ajes_total' => $cartData->totalCart,
            'ajes_subtotal_bienes' => $cartData->totalBienes,
            'ajes_subtotal_servicios' => $cartData->totalServicios,
            'ajes_totalcartiva' => $cartData->totalCartIva,
            'ajes_totaliva' => $cartData->totalIva,
            'ajes_tarifacero' => $cartData->tarifCero,
            'ajes_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajes_tarifaiva' => $cartData->tarifIva,
            'ajes_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ajes_items_duplicados' => $dataPostAjuste->ajesPermitirDuplicados,
            'ajes_tipo' => $dataPostAjuste->ajesTipo,
            'fk_servicio' => $dataPostAjuste->ajesServicio,
        ];

        return $this->ccm->actualizar('cc_ajuste_salida', $datos, ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()]);
    }

    
    /**
     * Función para actualizar el kardex general, kardex bodega, y kardex bodega lote (si aplica) al registrar un ajuste de salida, utilizando el identificador del ajuste de salida, los datos del producto a ajustar, el identificador del lote (si aplica), y la información proporcionada por el usuario a través de un formulario. La función updateKardex recibe el identificador del ajuste de salida (ajusteId), los datos del producto (producto) que se va a ajustar, el identificador del lote (loteId) si el producto maneja lotes, y los datos del formulario (dataPostAjuste) para obtener la fecha, hora, bodega, y otros detalles necesarios para actualizar los registros de kardex correspondientes al ajuste de salida. Luego, se realiza la actualización del kardex general restando la cantidad ajustada del stock actual del producto, calculando el nuevo costo promedio y costo último, y registrando un nuevo movimiento en el kardex general. A continuación, se actualiza el kardex bodega restando la cantidad ajustada del stock actual en la bodega correspondiente y registrando un nuevo movimiento en el kardex bodega. Finalmente, si el producto maneja lotes, se actualiza el kardex bodega lote restando la cantidad ajustada del stock actual en el lote correspondiente y registrando un nuevo movimiento en el kardex bodega lote. Esta función es esencial para mantener actualizados los registros de inventario en el sistema al registrar ajustes de salida, asegurando que toda la información relevante se refleje correctamente en los movimientos de inventario y que el proceso de actualización del kardex sea eficiente y confiable.
     * @param int $ajusteId Identificador del ajuste de salida que se está registrando, utilizado para asociar los movimientos de kardex al ajuste correspondiente.
     * @param object $producto Objeto que contiene los datos del producto que se va a ajustar, incluyendo el identificador
     * del producto, la cantidad a ajustar, el precio, el total, y otros detalles relevantes para el registro de los movimientos de kardex.
     * @param int|null $loteId Identificador del lote si el producto maneja
     * lotes, o null si el producto no maneja lotes. Este parámetro es opcional y se utiliza para registrar el lote asociado a los movimientos de kardex en caso de que el producto lo requiera.
     * @param object $dataPostAjuste Objeto que contiene los datos del formulario proporcionados por el usuario para registrar el ajuste de salida, incluyendo fecha, hora, bodega, y otros detalles relevantes para la actualización de los movimientos de kardex.
     * @return array Retorna un array con el estado de la operación ('success' o 'error') y un mensaje descriptivo sobre el resultado de la actualización de los movimientos de kardex al registrar el ajuste de salida.
    */
    public function updateKardex(int $ajusteId, object $producto, int|null $loteId, object $dataPostAjuste):array {
        $fecha = $dataPostAjuste->ajesFecha ?? date('Y-m-d');
        $hora = date('H:i:s');
        $bodegaId = $dataPostAjuste->ajesBodega;

        // 1. Kardex general (resta)
        $kardex = $this->actualizarKardexGeneral($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId);
        if (!$kardex['kardexId']) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex general (salida).'];
        }

        // 2. Kardex bodega
        $okBod = $this->actualizarKardexBodega($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
        if (!$okBod) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex bodega (salida).'];
        }

        // 3. Kardex bodega lote
        if ($loteId) {
            $okLot = $this->actualizarKardexBodegaLote($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
            if (!$okLot) {
                return ['status' => 'error', 'msg' => 'Error al actualizar kardex lote (salida).'];
            }
        }

        return ['status' => 'success'];
    }


    /**
     * Función para actualizar el kardex general al registrar un ajuste de salida, restando la cantidad ajustada del stock actual del producto, calculando el nuevo costo promedio y costo último, y registrando un nuevo movimiento en el kardex general. La función actualizarKardexGeneral recibe el identificador del ajuste de salida (ajusteId), los datos del producto (producto) que se va a ajustar, el identificador del lote (loteId) si el producto maneja lotes, y los datos del formulario (dataPostAjuste) para obtener la fecha, hora, bodega, y otros detalles necesarios para actualizar el registro de kardex general correspondiente al ajuste de salida. Luego, se obtiene el stock actual del producto, se calcula el nuevo stock restando la cantidad ajustada, se obtiene el costo de inventario actual del producto y se calcula el nuevo costo de inventario restando el total ajustado. A continuación, se obtiene el costo de inventario total de la empresa y se calcula el nuevo costo de inventario total restando el total ajustado. Luego, se calcula el nuevo costo promedio considerando el nuevo stock y nuevo costo de inventario del producto. Finalmente, se inserta un nuevo registro en la tabla cc_kardex con toda la información relevante del movimiento de ajuste de salida. Esta función es esencial para mantener actualizado el kardex general en el sistema al registrar ajustes de salida, asegurando que toda la información relevante se refleje correctamente en los movimientos de inventario y que el proceso de actualización del kardex general sea eficiente y confiable.
     * @param object $producto Objeto que contiene los datos del producto que se va a ajustar, incluyendo el identificador del producto, la cantidad a ajustar, el precio, el total, y otros detalles relevantes para el registro del movimiento de kardex general.
     * @param int $ajusteId Identificador del ajuste de salida que se está registrando, utilizado para asociar el movimiento de kardex al ajuste correspondiente.
     * @param int|null $loteId Identificador del lote si el producto maneja lotes, o null si el producto no maneja lotes. Este parámetro es opcional y se utiliza para registrar el lote asociado al movimiento de kardex general en caso de que el producto lo requiera.
     * @param string $fecha Fecha del movimiento de ajuste de salida, obtenida del formulario o asignada por defecto a la fecha actual. 
     * @param string $hora Hora del movimiento de ajuste de salida, asignada por defecto a la hora actual.
     * @param int $bodegaId Identificador de la bodega desde la cual se está realizando el ajuste de salida, obtenido del formulario para registrar el movimiento de kardex general con la información correcta de la bodega involucrada en el ajuste de salida.
     * @return array Retorna un array con el identificador del movimiento de kardex general creado ('kardexId'), el nuevo costo promedio del producto ('costoPromedio'), y el nuevo costo último del producto ('costoUltimo') después de registrar el ajuste de salida en el kardex general. Si ocurre
    */
    public function actualizarKardexGeneral(object $producto, int $ajusteId, int|null $loteId, string $fecha, string $hora, int $bodegaId) {

        // Obtengo stock actual del producto
        $stockActual = $this->productLib->getStockProducto($producto->id);
        $nuevoStock = $stockActual - $producto->qty;

        // Obtengo costo de inventario del producto
        $costoInvProducto = $this->productLib->getCostoInventarioProducto($producto->id);
        $nuevoCostoInvProducto = $costoInvProducto - $producto->total;

        // Obtengo costo de inventario total (empresa)
        $costoInvTotal = $this->productLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal - $producto->total;

//        if ($nuevoStock < 0) {
//            throw new \Exception("El producto {$producto->name} quedaría con stock negativo.");
//        }
        // Calcular costo promedio (solo se recalcular en entradas que afecte el costo del producto)
//        $costoPromedio = $nuevoStock > 0 ? ($nuevoCostoInvProducto / $nuevoStock) : 0;
        $costoPromedio = $this->productLib->getCostoPromedio($producto->id);

        //Obtengo el costo último
        $costoUltimo = $this->productLib->getCostoUltimo($producto->id);

        // Insertar registro en kardex
        $dataKardex = [
            'fk_producto' => $producto->id,
            'kar_kardex' => -$producto->qty,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimo,
            'kar_total_costo' => abs($producto->total), //SIEMPRE POSITIVO LOS COSTOS
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
            $this->productLib->updateCostosProducto(
                    $producto->id,
                    $nuevoStock,
                    $costoPromedio,
                    $costoUltimo,
                    $nuevoCostoInvProducto
            );
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
     * Función para actualizar el kardex bodega al registrar un ajuste de salida, restando la cantidad ajustada del stock actual en la bodega correspondiente y registrando un nuevo movimiento en el kardex bodega. La función actualizarKardexBodega recibe el identificador del ajuste de salida (ajusteId), los datos del producto (producto) que se va a ajustar, el identificador del lote (loteId) si el producto maneja lotes, y los datos del formulario (dataPostAjuste) para obtener la fecha, hora, bodega, y otros detalles necesarios para actualizar el registro de kardex bodega correspondiente al ajuste de salida. Luego, se obtiene el stock actual del producto en la bodega, se calcula el nuevo stock restando la cantidad ajustada, y se inserta un nuevo registro en la tabla cc_kardex_bodega con toda la información relevante del movimiento de ajuste de salida en la bodega. Finalmente, se actualiza o crea el registro de stock por bodega con el nuevo stock calculado. Esta función es esencial para mantener actualizado el kardex bodega en el sistema al registrar ajustes de salida, asegurando que toda la información relevante se refleje correctamente en los movimientos de inventario por bodega y que el proceso de actualización del kardex bodega sea eficiente y confiable. 
     * @param object $producto Objeto que contiene los datos del producto que se va a ajustar, incluyendo el identificador del producto, la cantidad a ajustar, el precio, el total, y otros detalles relevantes para el registro del movimiento de kardex bodega.
     * @param int $ajusteId Identificador del ajuste de salida que se está registrando, utilizado para asociar el movimiento de kardex bodega al ajuste correspondiente.
     * @param int|null $loteId Identificador del lote si el producto maneja lotes, o null si el producto no maneja lotes. Este parámetro es opcional y se utiliza para registrar el lote asociado al movimiento de kardex bodega en caso de que el producto lo requiera.
     * @param string $fecha Fecha del movimiento de ajuste de salida, obtenida del formulario o asignada por defecto a la fecha actual.
     * @param string $hora Hora del movimiento de ajuste de salida, asignada por defecto a la hora actual.
     * @param int $bodegaId Identificador de la bodega desde la cual se está realizando el ajuste de salida, obtenido del formulario para registrar el movimiento de kardex bodega con la información correcta de la bodega involucrada en el ajuste de salida.
     * @param array $kardexCostos Array que contiene el nuevo costo promedio y costo último del producto calculados en el kardex general, utilizados para registrar el movimiento de kardex bodega con la información correcta de costos después de registrar el ajuste de salida en el kardex general.
     * @return int|false Retorna el identificador del movimiento de kardex bodega creado si la operación fue exitosa, o false si ocurrió un error durante el proceso de guardado del movimiento de kardex bodega en la base de datos al
    */
    public function actualizarKardexBodega(object $producto, int $ajusteId, int|null $loteId, string $fecha, string $hora, int $bodegaId, array $kardexCostos):int {
        // Obtener stock actual en bodega
        $stockBodega = $this->stockBodLib->getStockBodega($bodegaId, $producto->id);
        $nuevoStockBodega = $stockBodega - $producto->qty;

//        if ($nuevoStockBodega < 0) {
//            throw new \Exception("Stock en bodega insuficiente para el producto {$producto->name}.");
//        }
        // Insertar registro en kardex_bodega
        $dataKardexBodega = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => -$producto->qty,
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
     * Función para actualizar el kardex bodega lote al registrar un ajuste de salida, restando la cantidad ajustada del stock actual en el lote correspondiente y registrando un nuevo movimiento en el kardex bodega lote. La función actualizarKardexBodegaLote recibe el identificador del ajuste de salida (ajusteId), los datos del producto (producto) que se va a ajustar, el identificador del lote (loteId), y los datos del formulario (dataPostAjuste) para obtener la fecha, hora, bodega, y otros detalles necesarios para actualizar el registro de kardex bodega lote correspondiente al ajuste de salida. Luego, se obtiene el stock actual del producto en el lote de la bodega, se calcula el nuevo stock restando la cantidad ajustada, y se inserta un nuevo registro en la tabla cc_kardex_bodega_lote con toda la información relevante del movimiento de ajuste de salida en el lote de la bodega. Finalmente, se actualiza o crea el registro de stock por bodega y lote con el nuevo stock calculado. Esta función es esencial para mantener actualizado el kardex bodega lote en el sistema al registrar ajustes de salida, asegurando que toda la información relevante se refleje correctamente en los movimientos de inventario por bodega y lote, y que el proceso de actualización del kardex bodega lote sea eficiente y confiable.
     * @param object $producto Objeto que contiene los datos del producto que se va a
     * ajustar, incluyendo el identificador del producto, la cantidad a ajustar, el precio, el total, y otros detalles relevantes para el registro del movimiento de kardex bodega lote.
     * @param int $ajusteId Identificador del ajuste de salida que se está registrando, utilizado para asociar el movimiento de kardex bodega lote al ajuste correspondiente.
     * @param int $loteId Identificador del lote, utilizado para registrar el movimiento de kardex bodega lote con la información correcta del lote involucrado en el ajuste de salida.
     * @param string $fecha Fecha del movimiento de ajuste de salida, obtenida del formulario o asignada por defecto a la fecha actual.
     * @param string $hora Hora del movimiento de ajuste de salida, asignada por defecto a la hora actual.
     * @param int $bodegaId Identificador de la bodega desde la cual se está realizando el ajuste de salida, obtenido del formulario para registrar el movimiento de kardex bodega lote con la información correcta de la bodega involucrada en el ajuste de salida.
     * @param array $kardexCostos Array que contiene el nuevo costo promedio y costo último del producto calculados en el kardex general, utilizados para registrar el movimiento de kardex bodega lote con la información correcta de costos después de registrar el ajuste de salida en el kardex general.
     * @return int|false Retorna el identificador del movimiento de kardex bodega lote creado si la operación fue exitosa, o false si ocurrió un error
    */
    public function actualizarKardexBodegaLote(object $producto, int $ajusteId, int|null $loteId, string $fecha, string $hora, int $bodegaId, array $kardexCostos):int {
        // Obtener stock actual en bodega por lote
        $stockBodegaLote = $this->stockBodLib->getStockBodegaLote($bodegaId, $producto->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote - $producto->qty;

//        if ($nuevoStockBodegaLote < 0) {
//            throw new \Exception("Stock por lote insuficiente para producto {$producto->name}.");
//        }
//        
        // Insertar registro en kardex_bodega_lote
        $dataKardexLote = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => -$producto->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $kardexCostos['costoPromedio'],
            'karbl_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karbl_documento_id' => $ajusteId,
            'karbl_codigo_transaccion' => $this->tipotransaccionCod,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
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
     * Se usa SOLO cuando el ajuste queda en BORRADOR
     * Genera las reservas reales en BORRADOR
     * NO toca stock ni kardex
     * @param int $ajusteId Identificador del ajuste de salida para el cual se van a registrar las reservas en caso de que el
     * ajuste quede en estado de BORRADOR, utilizando los datos del carrito de ajustes y la información proporcionada por el usuario a través de un formulario. La función registrarReservas recibe el identificador del ajuste de salida (ajusteId), los datos del carrito de ajustes (cartData), y los datos del formulario (dataPostAjuste) para obtener la información necesaria para registrar las reservas correspondientes al ajuste de salida en caso de que este quede en estado de BORRADOR. Luego, se limpian las reservas previas asociadas al ajuste de salida utilizando la función liberarReservasDocumento, y se crean nuevas reservas para cada producto en el carrito de ajustes utilizando la función reservarLinea, pasando la información relevante como el origen, el identificador del ajuste, la bodega, el producto, el lote (si aplica), y la cantidad a reservar. Esta función es esencial para gestionar las reservas de productos en el sistema cuando un ajuste de salida queda en estado de BORRADOR, asegurando que las reservas se registren correctamente sin afectar el stock ni los movimientos de kardex hasta que el ajuste sea aprobado.
     * @param object $cartData Objeto que contiene los datos del carrito de ajustes, incluyendo los productos agregados al ajuste de salida, sus cantidades, precios, totales, y otros detalles relevantes para el registro de las reservas correspondientes al ajuste de salida en caso de que este quede en estado de BORRADOR.
     * @param object $dataPostAjuste Objeto que contiene los datos del formulario proporcionados por el usuario para registrar el ajuste de salida, incluyendo fecha, observaciones, estado, bodega, motivo de ajuste, centro de costo, cliente, tipo de
     * ajuste, servicio asociado, y otros detalles relevantes para el registro de las reservas correspondientes al ajuste de salida en caso de que este quede en estado de BORRADOR.
     */
    public function registrarReservas(int $ajusteId, object $cartData, object $dataPostAjuste): array {

        try {

            //Limpia reservas previas de este ajuste
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $ajusteId);

            //Crea reservas nuevamente
            foreach ($cartData->cartContent as $val) {

                //Solo en caso de que se haya pasado un producto tipo servicio, lo ignoramos
                if ($val->servicio === '1') {
                    continue;
                }

                $reservaId = $this->reservasLib->reservarLinea(
                        $this->tipotransaccionCod, // origen
                        $ajusteId, // id documento
                        $dataPostAjuste->ajesBodega, // bodega
                        $val->id, // producto
                        $val->tieneLote === '1' ? $val->idLote : null,
                        $val->qty// cantidad
                );

                // VALIDAR RESULTADO
                if (!$reservaId) {
                    return [
                        'status' => 'error',
                        'msg' => "No se pudo reservar el producto {$val->name}"
                    ];
                }
            }

            return ['status' => 'success'];
        } catch (\Exception $exc) {
            log_message('error', '[AJUSTE SALIDA][RESERVAS] ' . $exc->getMessage());
            return [
                'status' => 'error',
                'msg' => 'Error interno al registrar reservas'
            ];
        }
    }

    /**
     * Función para anular un ajuste de salida, liberando las reservas asociadas al ajuste, actualizando el estado del ajuste a anulado, y revirtiendo los movimientos de kardex realizados por el ajuste de salida. La función anularAjuste recibe el identificador del ajuste de salida a anular (ajusteId) y el motivo de la anulación (motivo) proporcionado por el usuario. Luego, se obtiene el ajuste de salida utilizando su identificador y se verifica que exista y que no esté ya anulado. Si el ajuste está en estado de BORRADOR, se liberan las reservas asociadas al ajuste y se marca como anulado sin tocar los movimientos de kardex. Si el ajuste ya fue aprobado, se obtiene el detalle del ajuste y se recorren los productos para revertir los movimientos de kardex correspondientes a cada producto ajustado utilizando la función updateKardex con cantidades inversas. Finalmente, se liberan las reservas nuevamente para asegurar que no queden reservas colgadas, se actualiza el estado del ajuste a anulado con la información del motivo y fecha de anulación, y se anula el asiento contable asociado al ajuste si existe. Esta función es esencial para gestionar la anulación de ajustes de salida en el sistema, asegurando que las reservas se liberen correctamente, los movimientos de kardex se reviertan adecuadamente, y que toda la información relevante sobre la anulación se registre correctamente en la base de datos.
     * @param int $ajusteId Identificador del ajuste de salida que se va a anular, utilizado para obtener el ajuste de salida, verificar su estado, liberar las reservas asociadas al ajuste, revertir los movimientos de kardex correspondientes, y actualizar el estado del ajuste a anulado en la base de datos.
     * @param string $motivo Motivo de la anulación proporcionado por el usuario, utilizado para registrar la razón de la anulación en el ajuste de salida y en el asiento contable asociado al ajuste, asegurando que toda la información relevante sobre la anulación se registre correctamente en la base de datos para futuras referencias y auditorías. 
     * @return array Retorna un array con el estado de la operación ('success', 'error', o 'warning') y un mensaje descriptivo sobre el resultado de la anulación del ajuste de salida, incluyendo información sobre si el ajuste fue anulado exitosamente, si ya estaba anulado previamente, o
    */
    public function anularAjuste(int $ajusteId, string $motivo): array {
        // ️Obtener el ajuste
        $ajuste = $this->ccm->getData('cc_ajuste_salida', ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$ajuste) {
            return ['status' => 'error', 'msg' => 'No se encontró el ajuste especificado'];
        }

        if ($ajuste->ajes_estado == -1) {
            return ['status' => 'warning', 'msg' => 'El ajuste ya se encuentra anulado'];
        }

        //BORRADOR: solo liberar reservas y marcar anulado
        if ($ajuste->ajes_estado == 1) {

            // Liberar reservas
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $ajusteId);

            // Marcar ajuste como anulado
            $datos = [
                'ajes_estado' => -1,
                'ajes_motivo_anulacion' => $motivo,
                'ajes_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anulacion' => $this->user->id
            ];

            $this->ccm->actualizar('cc_ajuste_salida', $datos, ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()]);

            return [
                'status' => 'success',
                'msg' => 'Ajuste de salida en BORRADOR anulado exitosamente'
            ];
        }

        $detalle = $this->ccm->getData('cc_ajuste_salida_det', ['fk_ajuste_salida' => $ajusteId]);

        if (!$detalle) {
            return ['status' => 'error', 'msg' => 'No se encontró detalle asociado al ajuste'];
        }

        try {

            // Código de transacción para ANULACIÓN DE AJUSTE DE SALIDA
            $this->tipotransaccionCod = '40';

            $dataAjuste = (object) [
                        'ajesFecha' => date('Y-m-d'),
                        'ajesBodega' => $ajuste->fk_bodega,
                        'ajesEstado' => -1
            ];

            foreach ($detalle as $val) {
                // Producto inverso
                $producto = (object) [
                            'id' => $val->fk_producto,
                            'qty' => - abs($val->ajsd_itemcantidad), // ENTRADA compensatoria
                            'price' => $val->ajsd_itemcosto,
                            'total' => - abs($val->ajsd_itemcostoxcantidad),
                            'tieneLote' => 0,
                            'servicio' => 0
                ];

                // Info producto
                $prodInfo = $this->ccm->getData('cc_productos', ['id' => $val->fk_producto], 'prod_ctrllote, prod_isservicio', null, 1);

                if ($prodInfo) {
                    $producto->tieneLote = $prodInfo->prod_ctrllote;
                    $producto->servicio = $prodInfo->prod_isservicio;
                }

                // No tocar kardex si es servicio
                if ($producto->servicio == '0') {
                    $kardexOk = $this->updateKardex($ajusteId, $producto, $val->fk_lote, $dataAjuste);
                    if ($kardexOk['status'] !== 'success') {
                        return [
                            'status' => 'error',
                            'msg' => 'Error al revertir kardex del ajuste de salida'
                        ];
                    }
                }
            }
            //Liberar reservas (si quedaron por ahi colgadas)
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $ajusteId);

            //Marcar ajuste como ANULADO
            $datos = [
                'ajes_estado' => -1,
                'ajes_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anulacion' => $this->user->id,
                'ajes_motivo_anulacion' => $motivo ?? 'Anulación manual'
            ];

            $this->ccm->actualizar('cc_ajuste_salida', $datos, ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()]);

            //Anular asiento contable
            $asientoId = $this->ccm->getValueWhere('cc_asiento_contable', ['ac_documento_id' => $ajusteId, 'ac_codigo_transaccion' => '38', 'fk_proyecto' => getProyectoId(), 'ac_estado' => 1], 'id');

            if ($asientoId) {
                $datos = [
                    'ac_estado' => -1,
                    'ac_fecha_anulacion' => date('Y-m-d H:i:s'),
                    'fk_user_id_anulacion' => $this->user->id,
                    'ac_motivo_anulacion' =>
                    "Asiento anulado automáticamente por anulación del ajuste de salida #{$ajuste->ajes_secuencial}"
                ];
                $this->ccm->actualizar('cc_asiento_contable', $datos, ['id' => $asientoId, 'fk_proyecto' => getProyectoId()]);
            }
            return [
                'status' => 'success',
                'msg' => "Ajuste de salida #{$ajuste->ajes_secuencial} anulado exitosamente."
            ];
        } catch (\Exception $exc) {
//            echo $exc->getTraceAsString();
            return [
                'status' => 'error',
                'msg' => 'Error al anular ajuste de salida: ' . $exc->getMessage()
            ];
        } finally {
            // Volvemos al código normal de AJUSTE SALIDA
            $this->tipotransaccionCod = '38';
        }
    }
}
