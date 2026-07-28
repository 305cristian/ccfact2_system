<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Transferencias\Libraries;

/**
 * Description of TransferenciasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 dic 2025
 * @time 4:08:50 p.m.
 */
use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Libraries\ReservasLib;

class TransferenciasLib {

    protected $ccm;
    protected $user;
    protected $tipotransaccionCod = '17'; // TRANSFERENCIA DE PRODUCTOS 
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
     * @param object $cartData Objeto que contiene los datos del carrito de transferencias, incluyendo los productos, cantidades, precios y totales relacionados con la transferencia.  
     * @param object $dataPostTrb Objeto que contiene los datos adicionales de la transferencia, como bodegas de origen y destino, centro de costo, estado, observaciones, fecha, usuario destino y configuración de duplicados.    
     * @return int Retorna el identificador único de la transferencia registrada en la base de datos. Este ID se genera al guardar la transferencia y es utilizado para asociar los detalles de los productos transferidos, así como para futuras referencias y operaciones relacionadas con esta transferencia específica. El valor retornado será un número entero que representa el ID de la transferencia creada.
    */
    public function saveTransferencia(object $cartData, object $dataPostTrb):int {

        $secuencial = $this->ccm->getData('cc_transferencia_bodega', ['fk_proyecto' => getProyectoId()], 'trb_secuencial', ['trb_secuencial' => 'DESC'], 1);

        $datos = [
            'fk_proyecto' => getProyectoId(),
            'trb_secuencial' => (isset($secuencial) ? $secuencial->trb_secuencial + 1 : 1),
            'fk_bodega_origen' => $dataPostTrb->trbBodegaOrigen,
            'fk_bodega_destino' => $dataPostTrb->trbBodegaDestino,
            'fk_centro_costo' => $dataPostTrb->trbCentroCosto,
            'trb_estado' => $dataPostTrb->trbEstado,
            'trb_observaciones' => $dataPostTrb->trbObservaciones,
            'trb_fecha' => $dataPostTrb->trbFecha,
            'trb_totaliva' => $cartData->totalIva,
            'trb_totalcartiva' => $cartData->totalCartIva,
            'trb_total' => $cartData->totalCart,
            'trb_total_items' => $cartData->totalItems,
            'trb_items_duplicados' => $dataPostTrb->trbPermitirDuplicados,
            'fk_user_confirma' => $dataPostTrb->trbUsuarioDestino,
            'fk_user_crea' => $this->user->id,
        ];

        $save = $this->ccm->guardar($datos, 'cc_transferencia_bodega');

        return $save;
    }

    /**
     * @param object $cartData Objeto que contiene los datos del carrito de transferencias, incluyendo los productos, cantidades, precios y totales relacionados con la transferencia.  
     * @param object $dataPostTrb Objeto que contiene los datos adicionales de la transferencia, como bodegas de origen y destino, centro de costo, estado, observaciones, fecha, usuario destino y configuración de duplicados.    
     * @param int $transferenciaId Identificador único de la transferencia a actualizar. Este ID se utiliza para localizar la transferencia existente en la base de datos y aplicar las modificaciones correspondientes con los nuevos datos proporcionados. La función actualizará la transferencia con el ID especificado utilizando la información del carrito y los datos adicionales, asegurando que los cambios se reflejen correctamente en el registro de la transferencia. El valor retornado será un número entero que representa el resultado de la operación de actualización, indicando si la actualización fue exitosa o si ocurrió algún error durante el proceso.
     * 
    */
    public function updateTransferencia(object $cartData, object $dataPostTrb, int $transferenciaId) {


        $datos = [
            'fk_bodega_origen' => $dataPostTrb->trbBodegaOrigen,
            'fk_bodega_destino' => $dataPostTrb->trbBodegaDestino,
            'fk_centro_costo' => $dataPostTrb->trbCentroCosto,
            'trb_estado' => $dataPostTrb->trbEstado,
            'trb_observaciones' => $dataPostTrb->trbObservaciones,
            'trb_fecha' => $dataPostTrb->trbFecha,
            'trb_totaliva' => $cartData->totalIva,
            'trb_totalcartiva' => $cartData->totalCartIva,
            'trb_total' => $cartData->totalCart,
            'trb_total_items' => $cartData->totalItems,
            'trb_items_duplicados' => $dataPostTrb->trbPermitirDuplicados,
            'fk_user_confirma' => $dataPostTrb->trbUsuarioDestino,
            'fk_user_crea' => $this->user->id
        ];

        return $this->ccm->actualizar('cc_transferencia_bodega', $datos, ['id' => $transferenciaId, 'fk_proyecto' => getProyectoId()]);
    }


    /**
     * @param int $transferenciaId Identificador único de la transferencia a la cual se le guardarán los detalles de los productos transferidos. Este ID se utiliza para asociar cada detalle de producto con la transferencia correspondiente en la base de datos, asegurando que los productos transferidos estén correctamente vinculados a la transferencia principal. La función iterará sobre los productos incluidos en el carrito de transferencias y guardará cada uno de ellos como un detalle asociado a la transferencia identificada por este ID, permitiendo así un registro completo y organizado de los productos involucrados en la transferencia. El valor retornado será un número entero que representa el resultado de la operación de guardado de los detalles, indicando si los detalles se guardaron correctamente o si ocurrió algún error durante el proceso. 
     * @param object $item Objeto que contiene los datos de un producto específico a transferir, incluyendo su identificador, cantidad, precio y total. Este objeto representa un producto individual que será registrado como detalle de la transferencia en la base de datos. La función utilizará la información contenida en este objeto para crear un registro detallado del producto transferido, asegurando que todos los atributos relevantes del producto se almacenen correctamente junto con la transferencia principal. El valor retornado será un número entero que indica el resultado de la operación de guardado del detalle del producto, confirmando si se realizó con éxito o si hubo algún problema durante el proceso.
     * @param int|null $idLote Identificador único del lote asociado al producto transferido, si aplica. Este parámetro permite vincular el detalle del producto con un lote específico en la base de datos, asegurando un seguimiento adecuado de los productos por lotes cuando sea necesario. Si el producto no tiene un lote asociado, este valor puede ser nulo. La función utilizará este identificador para registrar correctamente la relación entre el producto transferido y su lote correspondiente, si existe. El valor retornado será un número entero que indica el resultado de la operación de guardado del detalle del producto con respecto al lote, confirmando si se realizó con éxito o si hubo algún problema durante el proceso.
    */
    public function saveTransferenciaDetalle(int $transferenciaId, object $item, int|null $idLote) {
        $datos = [
            'fk_transferencia_bodega' => $transferenciaId,
            'fk_producto' => $item->id,
            'fk_lote' => $idLote,
            'trbd_itemcantidad' => $item->qty,
            'trbd_itemcosto' => $item->price,
            'trbd_itemcostoxcantidad' => $item->total,
            'trbd_observaciones' => null,
            'trbd_estado' => 1
        ];

        return $this->ccm->guardar($datos, 'cc_transferencia_bodega_det');
    }

    /**
     * Actualiza kardex restando stock (salida)
     */
    public function updateKardex(int $transferenciaId, object $producto, ?int $loteId, object $dataTransfer, bool $isOrigen, ?bool $isAnulacion = false): array {

        $fecha = $dataTransfer->trb_fecha ?? date('Y-m-d');
        $hora = date('H:i:s');

        $bodegaId = $isOrigen ? $dataTransfer->fk_bodega_origen : $dataTransfer->fk_bodega_destino;

        // 1. Kardex general (resta si es origen, suma si es destino, si es anulación hace lo contrario)
        $kardex = $this->actualizarKardexGeneral($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $isOrigen, $isAnulacion);
        if (!$kardex['kardexId']) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex general (Transferencia).'];
        }

        // 2. Kardex bodega
        $okBod = $this->actualizarKardexBodega($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $kardex, $isOrigen, $isAnulacion);
        if (!$okBod) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex bodega (Transferencia).'];
        }

        // 3. Kardex bodega lote
        if ($loteId) {
            $okLot = $this->actualizarKardexBodegaLote($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $kardex, $isOrigen, $isAnulacion);
            if (!$okLot) {
                return ['status' => 'error', 'msg' => 'Error al actualizar kardex lote (Transferencia).'];
            }
        }

        return ['status' => 'success'];
    }

    /**
     * @param object $producto Objeto que contiene los datos del producto a transferir, incluyendo su identificador, cantidad, precio y total. Este objeto representa un producto individual que será registrado como detalle de la transferencia en la base de datos. La función utilizará la información contenida en este objeto para crear un registro detallado del producto transferido, asegurando que todos los atributos relevantes del producto se almacenen correctamente junto con la transferencia principal. El valor retornado será un número entero que indica el resultado de la operación de guardado del detalle del producto, confirmando si se realizó con éxito o si hubo algún problema durante el proceso.  
     * @param int $transferenciaId Identificador único de la transferencia a la cual se le guardarán los detalles de los productos transferidos. Este ID se utiliza para asociar cada detalle de producto con la transferencia correspondiente en la base de datos, asegurando que los productos transferidos estén correctamente vinculados a la transferencia principal. La función iterará sobre los productos incluidos en el carrito de transferencias y guardará cada uno de ellos como un detalle asociado a la transferencia identificada por este ID, permitiendo así un registro completo y organizado de los productos involucrados en la transferencia. El valor retornado será un número entero que representa el resultado de la operación de guardado de los detalles, indicando si los detalles se guardaron correctamente o si ocurrió algún error durante el proceso.
     * @param int|null $loteId Identificador único del lote asociado al producto transferido, si aplica. Este parámetro permite vincular el detalle del producto con un lote específico en la base de datos, asegurando un seguimiento adecuado de los productos por lotes cuando sea necesario. Este valor puede ser nulo si el producto no tiene un lote asociado. La función utilizará este identificador para registrar correctamente la relación entre el producto transferido y su lote correspondiente, si existe.   
     * @param string $fecha Fecha de la transferencia, utilizada para registrar la fecha en el kardex y otros registros relacionados con la transferencia. Este valor se utiliza para mantener un historial preciso de las operaciones de transferencia y asegurar que los registros reflejen correctamente la fecha en que se realizó la transferencia.
     * @param string $hora Hora de la transferencia, utilizada para registrar la hora en el kardex y otros registros relacionados con la transferencia. Este valor se utiliza para mantener un historial preciso de las operaciones de transferencia y asegurar que los registros reflejen correctamente la hora en que se realizó la transferencia.
     * @param int $bodegaId Identificador único de la bodega involucrada en la transferencia, ya sea como bodega de origen o destino. Este ID se utiliza para actualizar correctamente el stock en la bodega correspondiente durante el proceso de transferencia, asegurando que las cantidades de productos se ajusten adecuadamente según la bodega involucrada en la operación. La función utilizará este identificador para determinar en qué bodega se debe realizar el ajuste de stock y garantizar que los registros de la transferencia reflejen correctamente la bodega asociada a la operación.
     * @param bool $isOrigen Parámetro booleano que indica si la transferencia se está realizando desde la bodega de origen (true) o hacia la bodega de destino (false). Este valor se utiliza para determinar el signo del movimiento en el kardex, restando stock si es una transferencia desde la bodega de origen y sumando stock si es una transferencia hacia la bodega de destino. La función utilizará este parámetro para calcular correctamente el ajuste de stock en el kardex y asegurar que los registros de la transferencia reflejen adecuadamente si el movimiento es una salida (origen) o una entrada (destino).
     * @param bool $isAnulacion Parámetro booleano que indica si la operación de transferencia es una anulación (true) o una transferencia normal (false). Este valor se utiliza para determinar el signo del movimiento en el kardex, invirtiendo el ajuste de stock en caso de anulación, sumando stock si es una anulación desde la bodega de origen y restando stock si es una anulación hacia la bodega de destino. La función utilizará este parámetro junto con el indicador de origen para calcular correctamente el ajuste de stock en el kardex durante una anulación y asegurar que los registros de la transferencia reflejen adecuadamente si el movimiento es una anulación o una transferencia normal, ajustando el stock de manera correcta según la naturaleza de la operación.
     * @return array Retorna un array con el estado de la operación y un mensaje descriptivo. El estado será 'success' si la actualización del kardex se realizó correctamente, o 'error' si ocurrió algún problema durante el proceso. El mensaje proporcionará detalles adicionales sobre el
    */
    public function actualizarKardexGeneral(object $producto, int $transferenciaId, int|null $loteId, string $fecha, string $hora, int $bodegaId, bool $isOrigen, bool $isAnulacion):array {

        // ===============================
        // 1. Definimos el signo del movimiento
        // ===============================
        // Transferencia normal:
        //   Origen  -> -1
        //   Destino -> +1
        //
        // Anulación:
        //   Origen  -> +1
        //   Destino -> -1

        if ($isAnulacion) {
            $factor = $isOrigen ? 1 : -1;
        } else {
            $factor = $isOrigen ? -1 : 1;
        }
        // Obtengo stock actual del producto
        $stockActual = $this->productLib->getStockProducto($producto->id);
        $nuevoStock = $stockActual + ($factor * $producto->qty);

        // Obtengo costo de inventario del producto (LOS COSTOS DE MANTIENEN POR QUE EN TRANSFERNCIAS SOLO MOVEMOS STOCK)
        $costoInvProducto = $this->productLib->getCostoInventarioProducto($producto->id);
        $nuevoCostoInvProducto = $costoInvProducto;

        // Obtengo costo de inventario total (empresa)  (LOS COSTOS DE MANTIENEN POR QUE EN TRANSFERNCIAS SOLO MOVEMOS STOCK)
        $costoInvTotal = $this->productLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal;

//        if ($nuevoStock < 0) {
//            throw new \Exception("El producto {$producto->name} quedaría con stock negativo.");
//        }
//        
        // Calcular costo promedio (LOS COSTOS DE MANTIENEN POR QUE EN TRANSFERNCIAS SOLO MOVEMOS STOCK)
//        $costoPromedio = $nuevoStock > 0 ? ($costoInvProducto / $nuevoStock) : 0;
        //Obtengo el costo último
        $costoPromedio = $this->productLib->getCostoPromedio($producto->id);

        //Obtengo el costo último
        $costoUltimo = $this->productLib->getCostoUltimo($producto->id);

        // Insertar registro en kardex
        $dataKardex = [
            'fk_producto' => $producto->id,
            'kar_kardex' => $factor * $producto->qty,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimo,
            'kar_total_costo' => abs($producto->total), //SIEMPRE POSITIVO LOS COSTOS
            'kar_documento_id' => $transferenciaId,
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
            'costoUltimo' => $producto->price,
        ];
        return $responseKardex;
    }


     /**
     * @param object $producto Objeto que contiene los datos del producto a transferir, incluyendo su identificador, cantidad, precio y total. Este objeto representa un producto individual que será registrado como detalle de la transferencia en la base de datos. La función utilizará la información contenida en este objeto para crear un registro detallado del producto transferido, asegurando que todos los atributos relevantes del producto se almacenen correctamente junto con la transferencia principal. El valor retornado será un número entero que indica el resultado de la operación de guardado del detalle del producto, confirmando si se realizó con éxito o si hubo algún problema durante el proceso.  
     * @param int $transferenciaId Identificador único de la transferencia a la cual se le guardarán los detalles de los productos transferidos. Este ID se utiliza para asociar cada detalle de producto con la transferencia correspondiente en la base de datos, asegurando que los productos transferidos estén correctamente vinculados a la transferencia principal. La función iterará sobre los productos incluidos en el carrito de transferencias y guardará cada uno de ellos como un detalle asociado a la transferencia identificada por este ID, permitiendo así un registro completo y organizado de los productos involucrados en la transferencia. El valor retornado será un número entero que representa el resultado de la operación de guardado de los detalles, indicando si los detalles se guardaron correctamente o si ocurrió algún error durante el proceso.
     * @param int|null $loteId Identificador único del lote asociado al producto transferido, si aplica. Este parámetro permite vincular el detalle del producto con un lote específico en la base de datos, asegurando un seguimiento adecuado de los productos por lotes cuando sea necesario. Este valor puede ser nulo si el producto no tiene un lote asociado. La función utilizará este identificador para registrar correctamente la relación entre el producto transferido y su lote correspondiente, si existe.   
     * @param string $fecha Fecha de la transferencia, utilizada para registrar la fecha en el kardex y otros registros relacionados con la transferencia. Este valor se utiliza para mantener un historial preciso de las operaciones de transferencia y asegurar que los registros reflejen correctamente la fecha en que se realizó la transferencia.
     * @param string $hora Hora de la transferencia, utilizada para registrar la hora en el kardex y otros registros relacionados con la transferencia. Este valor se utiliza para mantener un historial preciso de las operaciones de transferencia y asegurar que los registros reflejen correctamente la hora en que se realizó la transferencia.
     * @param int $bodegaId Identificador único de la bodega involucrada en la transferencia, ya sea como bodega de origen o destino. Este ID se utiliza para actualizar correctamente el stock en la bodega correspondiente durante el proceso de transferencia, asegurando que las cantidades de productos se ajusten adecuadamente según la bodega involucrada en la operación. La función utilizará este identificador para determinar en qué bodega se debe realizar el ajuste de stock y garantizar que los registros de la transferencia reflejen correctamente la bodega asociada a la operación.
     * @param array $kardexCostos Array que contiene los costos relacionados con el producto transferido, incluyendo el costo promedio y el costo último. Este array se utiliza para registrar los costos en el kardex de bodega, asegurando que la información de costos se mantenga precisa y actualizada durante el proceso de transferencia. La función utilizará estos costos para calcular correctamente el valor del movimiento en el kardex de bodega y garantizar que los registros reflejen adecuadamente los costos asociados a la transferencia.    
     * @param bool $isOrigen Parámetro booleano que indica si la transferencia se está realizando desde la bodega de origen (true) o hacia la bodega de destino (false). Este valor se utiliza para determinar el signo del movimiento en el kardex, restando stock si es una transferencia desde la bodega de origen y sumando stock si es una transferencia hacia la bodega de destino. La función utilizará este parámetro para calcular correctamente el ajuste de stock en el kardex y asegurar que los registros de la transferencia reflejen adecuadamente si el movimiento es una salida (origen) o una entrada (destino).
     * @param bool $isAnulacion Parámetro booleano que indica si la operación de transferencia es una anulación (true) o una transferencia normal (false). Este valor se utiliza para determinar el signo del movimiento en el kardex, invirtiendo el ajuste de stock en caso de anulación, sumando stock si es una anulación desde la bodega de origen y restando stock si es una anulación hacia la bodega de destino. La función utilizará este parámetro junto con el indicador de origen para calcular correctamente el ajuste de stock en el kardex durante una anulación y asegurar que los registros de la transferencia reflejen adecuadamente si el movimiento es una anulación o una transferencia normal, ajustando el stock de manera correcta según la naturaleza de la operación.
    */
    public function actualizarKardexBodega(object $producto, int $transferenciaId, int|null $loteId, string $fecha, string $hora, int $bodegaId, array $kardexCostos, bool $isOrigen, bool $isAnulacion) {
        // ===============================
        // 1. Definimos el signo del movimiento
        // ===============================
        // Transferencia normal:
        //   Origen  -> -1
        //   Destino -> +1
        //
        // Anulación:
        //   Origen  -> +1
        //   Destino -> -1

        if ($isAnulacion) {
            $factor = $isOrigen ? 1 : -1;
        } else {
            $factor = $isOrigen ? -1 : 1;
        }

        // Obtener stock actual en bodega
        $stockBodega = $this->stockBodLib->getStockBodega($bodegaId, $producto->id);
        $nuevoStockBodega = $stockBodega + ($factor * $producto->qty);

//        if ($nuevoStockBodega < 0) {
//            throw new \Exception("Stock en bodega insuficiente para el producto {$producto->name}.");
//        }
        // Insertar registro en kardex_bodega
        $dataKardexBodega = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $factor * $producto->qty,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $kardexCostos['costoPromedio'],
            'karb_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karb_documento_id' => $transferenciaId,
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
     * @param object $producto Objeto que contiene los datos del producto a transferir, incluyendo su identificador, cantidad, precio y total. Este objeto representa un producto individual que será registrado como detalle de la transferencia en la base de datos. La función utilizará la información contenida en este objeto para crear un registro detallado del producto transferido, asegurando que todos los atributos relevantes del producto se almacenen correctamente junto con la transferencia principal. El valor retornado será un número entero que indica el resultado de la operación de guardado del detalle del producto, confirmando si se realizó con éxito o si hubo algún problema durante el proceso.  
     * @param int $transferenciaId Identificador único de la transferencia a la cual se le guardarán los detalles de los productos transferidos. Este ID se utiliza para asociar cada detalle de producto con la transferencia correspondiente en la base de datos, asegurando que los productos transferidos estén correctamente vinculados a la transferencia principal. La función iterará sobre los productos incluidos en el carrito de transferencias y guardará cada uno de ellos como un detalle asociado a la transferencia identificada por este ID, permitiendo así un registro completo y organizado de los productos involucrados en la transferencia. El valor retornado será un número entero que representa el resultado de la operación de guardado de los detalles, indicando si los detalles se guardaron correctamente o si ocurrió algún error durante el proceso.
     * @param int|null $loteId Identificador único del lote asociado al producto transferido, si aplica. Este parámetro permite vincular el detalle del producto con un lote específico en la base de datos, asegurando un seguimiento adecuado de los productos por lotes cuando sea necesario. Este valor puede ser nulo si el producto no tiene un lote asociado. La función utilizará este identificador para registrar correctamente la relación entre el producto transferido y su lote correspondiente, si existe.   
     * @param string $fecha Fecha de la transferencia, utilizada para registrar la fecha en el kardex y otros registros relacionados con la transferencia. Este valor se utiliza para mantener un historial preciso de las operaciones de transferencia y asegurar que los registros reflejen correctamente la fecha en que se realizó la transferencia.
     * @param string $hora Hora de la transferencia, utilizada para registrar la hora en el kardex y otros registros relacionados con la transferencia. Este valor se utiliza para mantener un historial preciso de las operaciones de transferencia y asegurar que los registros reflejen correctamente la hora en que se realizó la transferencia.
     * @param int $bodegaId Identificador único de la bodega involucrada en la transferencia, ya sea como bodega de origen o destino. Este ID se utiliza para actualizar correctamente el stock en la bodega correspondiente durante el proceso de transferencia, asegurando que las cantidades de productos se ajusten adecuadamente según la bodega involucrada en la operación. La función utilizará este identificador para determinar en qué bodega se debe realizar el ajuste de stock y garantizar que los registros de la transferencia reflejen correctamente la bodega asociada a la operación.
     * @param array $kardexCostos Array que contiene los costos relacionados con el producto transferido, incluyendo el costo promedio y el costo último. Este array se utiliza para registrar los costos en el kardex de bodega, asegurando que la información de costos se mantenga precisa y actualizada durante el proceso de transferencia. La función utilizará estos costos para calcular correctamente el valor del movimiento en el kardex de bodega y garantizar que los registros reflejen adecuadamente los costos asociados a la transferencia.    
     * @param bool $isOrigen Parámetro booleano que indica si la transferencia se está realizando desde la bodega de origen (true) o hacia la bodega de destino (false). Este valor se utiliza para determinar el signo del movimiento en el kardex, restando stock si es una transferencia desde la bodega de origen y sumando stock si es una transferencia hacia la bodega de destino. La función utilizará este parámetro para calcular correctamente el ajuste de stock en el kardex y asegurar que los registros de la transferencia reflejen adecuadamente si el movimiento es una salida (origen) o una entrada (destino).
     * @param bool $isAnulacion Parámetro booleano que indica si la operación de transferencia es una anulación (true) o una transferencia normal (false). Este valor se utiliza para determinar el signo del movimiento en el kardex, invirtiendo el ajuste de stock en caso de anulación, sumando stock si es una anulación desde la bodega de origen y restando stock si es una anulación hacia la bodega de destino. La función utilizará este parámetro junto con el indicador de origen para calcular correctamente el ajuste de stock en el kardex durante una anulación y asegurar que los registros de la transferencia reflejen adecuadamente si el movimiento es una anulación o una transferencia normal, ajustando el stock de manera correcta según la naturaleza de la operación.
    */
    public function actualizarKardexBodegaLote(object $producto, int $transferenciaId, int|null $loteId, string $fecha, string $hora, int $bodegaId, array $kardexCostos, bool $isOrigen, bool  $isAnulacion) {

        // ===============================
        // 1. Definimos el signo del movimiento
        // ===============================
        // Transferencia normal:
        //   Origen  -> -1
        //   Destino -> +1
        //
        // Anulación:
        //   Origen  -> +1
        //   Destino -> -1

        if ($isAnulacion) {
            $factor = $isOrigen ? 1 : -1;
        } else {
            $factor = $isOrigen ? -1 : 1;
        }

        // Obtener stock actual en bodega por lote
        $stockBodegaLote = $this->stockBodLib->getStockBodegaLote($bodegaId, $producto->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote + ($factor * $producto->qty);

//        if ($nuevoStockBodegaLote < 0) {
//            throw new \Exception("Stock por lote insuficiente para producto {$producto->name}.");
//        }
//        
        // Insertar registro en kardex_bodega_lote
        $dataKardexLote = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => $factor * $producto->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $kardexCostos['costoPromedio'],
            'karbl_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karbl_documento_id' => $transferenciaId,
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
     * @param int $transferenciaId Identificador único de la transferencia para la cual se registrarán las reservas de los productos involucrados en la transferencia. Este ID se utiliza para asociar cada reserva con la transferencia correspondiente en la base de datos, asegurando que las reservas estén correctamente vinculadas a la transferencia principal. La función iterará sobre los productos incluidos en el carrito de transferencias y creará una reserva para cada producto, asociándola con la transferencia identificada por este ID, lo que permitirá un seguimiento adecuado de las reservas relacionadas con cada transferencia. El valor retornado será un array que indica el estado de la operación y un mensaje descriptivo, confirmando si las reservas se registraron correctamente o si ocurrió algún error durante el proceso. 
     * @param object $cartData Objeto que contiene los datos del carrito de transferencias, incluyendo la lista de productos a transferir, sus cantidades, precios y totales. Este objeto representa la información completa del carrito de transferencias que se utilizará para registrar las reservas de los productos involucrados en la transferencia. La función utilizará los datos contenidos en este objeto para iterar sobre los productos incluidos en el carrito y crear las reservas correspondientes para cada producto, asegurando que todas las reservas estén correctamente registradas y asociadas con la transferencia principal. El valor retornado será un array
     * @param object $dataPostTrb Objeto que contiene los datos adicionales necesarios para registrar las reservas de los productos en la transferencia, como la bodega de origen y otros detalles relevantes. Este objeto proporciona información complementaria que se utilizará junto con los datos del carrito de transferencias para crear las reservas de manera precisa y completa, asegurando que todas las reservas estén correctamente configuradas según los detalles específicos de la transferencia. La función utilizará esta información adicional para garantizar que las reservas se registren correctamente y reflejen adecuadamente las condiciones de la transferencia. El valor retornado será un array que indica el estado de la operación y un mensaje descriptivo, confirmando si las reservas se registraron correctamente o si ocurrió algún error durante el proceso.
     * @return array Retorna un array con el estado de la operación y un mensaje descriptivo. El estado será 'success' si las reservas se registraron correctamente, o 'error'
    */
    public function registrarReservas(int $transferenciaId, object $cartData, object $dataPostTrb): array {

        try {

            //Limpia reservas previas de este documento
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $transferenciaId);

            //Crea reservas nuevamente
            foreach ($cartData->cartContent as $val) {

                //Solo en caso de que se haya pasado un producto tipo servicio, lo ignoramos
                if ($val->servicio === '1') {
                    continue;
                }

                $reservaId = $this->reservasLib->reservarLinea(
                        $this->tipotransaccionCod, // origen
                        $transferenciaId, // id documento
                        $dataPostTrb->trbBodegaOrigen, // bodega
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
            log_message('error', '[TRANSFERENCIA DE PRODUCTOS][RESERVAS] ' . $exc->getMessage());
            return [
                'status' => 'error',
                'msg' => 'Error interno al registrar reservas'
            ];
        }
    }

    /**
     * @param int $transferenciaId Identificador único de la transferencia que se desea anular. Este ID se utiliza para localizar la transferencia específica en la base de datos y realizar el proceso de anulación, asegurando que la operación se aplique correctamente a la transferencia correspondiente. La función verificará el estado actual de la transferencia y, dependiendo de si está en estado borrador, por confirmar o confirmada, realizará las acciones necesarias para anularla, como liberar reservas o realizar movimientos inversos en el kardex. El valor retornado será un array que indica el estado de la operación y un mensaje descriptivo, confirmando si la transferencia se anuló correctamente o si ocurrió algún error durante el proceso.
     * @param string $motivoAnulacion Cadena de texto que describe el motivo
     * de la anulación de la transferencia. Este motivo se registra en la base de datos junto con la información de la transferencia anulada, proporcionando un contexto claro sobre por qué se realizó la anulación. La función utilizará este motivo para actualizar el registro de la transferencia en la base de datos, asegurando que se mantenga un historial completo y detallado de las acciones realizadas sobre cada transferencia. El valor retornado será un array que indica el estado de la operación y un mensaje descriptivo, confirmando si la transferencia se anuló correctamente o si ocurrió algún error durante el proceso.
     * @return array Retorna un array con el estado de la operación y un mensaje descriptivo. El estado será 'success' si la transferencia se anuló correctamente, 'warning' si
    */
    public function anularTransferencia(int $transferenciaId, string $motivoAnulacion): array {


        $transfer = $this->ccm->getData('cc_transferencia_bodega', ['id' => $transferenciaId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$transfer) {
            return ['status' => 'error', 'msg' => 'Transferencia no encontrada'];
        }

        if ($transfer->trb_estado == -1) {
            return ['status' => 'warning', 'msg' => 'La transferencia ya está anulada'];
        }

        // Si NO estaba confirmada → liberar reservas (unicamente libera reservas si esta en estado borador o por confirmar, no involucra kardex)
        if (in_array($transfer->trb_estado, [1, 2])) {
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $transferenciaId);
            $text = $transfer->trb_estado == 1 ? 'BORRADOR' : 'POR CONFIRMAR';

            $dataUpdate = [
                'trb_estado' => -1,
                'trb_motivo_anulacion' => $motivoAnulacion,
                'trb_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anula' => $this->user->id,
                'trb_observaciones' => "Transferencia anulada en estado {$text}"
            ];

            $this->ccm->actualizar('cc_transferencia_bodega', $dataUpdate, ['id' => $transferenciaId, 'fk_proyecto' => getProyectoId()]);

            return [
                'status' => 'success',
                'msg' => "Transferencia en estado {$text} anulado exitosamente"
            ];
        }
        try {

            // Código de transacción para ANULACIÓN DE TRANSFERENCIA
            $this->tipotransaccionCod = '44';

            // Si ya estaba CONFIRMADA → kardex inverso
            $detalle = $this->ccm->getData('cc_transferencia_bodega_det', ['fk_transferencia_bodega' => $transferenciaId, 'trbd_estado' => 1]);

            foreach ($detalle as $item) {

                $producto = (object) [
                            'id' => $item->fk_producto,
                            'qty' => $item->trbd_itemcantidad,
                            'price' => $item->trbd_itemcosto,
                            'total' => $item->trbd_itemcostoxcantidad,
                            'servicio' => 0,
                            'tieneLote' => $item->fk_lote ? 1 : 0
                ];
                // Reverso ENTRADA-SALIDA
                // Kardex ANTES ERA SALIDA (origen) AHORA AL ANULAR ES INGRESO
                $this->updateKardex($transferenciaId, $producto, $item->fk_lote, $transfer,
                        true, //ES ORIGEN 
                        true// ES ANULACIÓN
                );

                // Kardex ANTES ERA ENTRADA (destino) AHORA AL ANULAR ES SALIDA
                $this->updateKardex($transferenciaId, $producto, $item->fk_lote, $transfer,
                        false, //ES ORIGEN
                        true// ES ANULACIÓN
                );
            }

            $dataUpdate = [
                'trb_estado' => -1,
                'trb_motivo_anulacion' => $motivoAnulacion,
                'trb_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anula' => $this->user->id
            ];
            $this->ccm->actualizar('cc_transferencia_bodega', $dataUpdate, ['id' => $transferenciaId, 'fk_proyecto' => getProyectoId()]);

            return [
                'status' => 'success',
                'msg' => "Transferencia #{$transfer->trb_secuencial} anulado exitosamente."
            ];
        } catch (\Exception $exc) {
//            echo $exc->getTraceAsString();
            return [
                'status' => 'error',
                'msg' => 'Error al anular la transferencia: ' . $exc->getMessage()
            ];
        } finally {
            $this->tipotransaccionCod = '17';
        }
    }
}
