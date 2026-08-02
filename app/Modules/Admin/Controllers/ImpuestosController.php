<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Libraries\ImpuestosLib;
use Modules\Admin\Models\ImpuestosModel;
use Throwable;
use function mb_strtoupper;
use function view;

class ImpuestosController extends BaseController {

    protected ImpuestosModel $impuestosModel;
    protected ImpuestosLib $impLib;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->impuestosModel = new ImpuestosModel();
        $this->impLib = new ImpuestosLib();
    }

    public function index() {
        $this->user->validateSession();

        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaImpuestos'] = $this->impuestosModel->getImpuestos();
        $data['listaCuentasContable'] = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_estado' => 1], 'ctad_codigo, CONCAT(ctad_codigo," ",ctad_nombre_cuenta) cuentadet');

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\impuestos\viewImpuestos', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        }

        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getTarifas() {
        $response = $this->impuestosModel->getTarifasImpuestos();
        return $this->response->setJSON($response ?: false);
    }

    public function getCuentasTarifaContable() {
        $response = $this->impuestosModel->getCuentasTarifaContable();
        return $this->response->setJSON($response ?: false);
    }

    public function saveTarifa() {
        $dataPost = $this->getDataTarifaPost();

        $this->validation->setRules($this->getReglasValidacion());

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => $this->getErroresValidacion(),
            ]);
        }

        $existe = $this->impuestosModel->existeTarifa(
                $dataPost['impt_codigo'],
                (float) $dataPost['impt_porcentage'],
                (int) $dataPost['fk_impuesto']
        );

        if ($existe) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe una tarifa registrada con el mismo impuesto, codigo SRI y porcentaje.</h5>',
            ]);
        }

        if ((int) $dataPost['impt_predeterminado'] === 1) {
            $this->impuestosModel->quitarPredeterminados((int) $dataPost['fk_impuesto']);
        }

        $tarifaId = $this->impuestosModel->guardarTarifa($dataPost);
        $this->logs->logSuccess('SE HA CREADO UNA TARIFA DE IMPUESTO CON EL ID ' . $tarifaId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Tarifa de impuesto registrada exitosamente</h5>',
        ]);
    }

    public function updateTarifa() {
        $idTarifa = (int) $this->request->getPost('idTarifa');
        $dataPost = $this->getDataTarifaPost();

        $this->validation->setRules($this->getReglasValidacion());

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => $this->getErroresValidacion(),
            ]);
        }

        $existe = $this->impuestosModel->existeTarifa(
                $dataPost['impt_codigo'],
                (float) $dataPost['impt_porcentage'],
                (int) $dataPost['fk_impuesto']
        );

        if ($existe && (int) $existe->id !== $idTarifa) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe una tarifa registrada con el mismo impuesto, codigo SRI y porcentaje.</h5>',
            ]);
        }

        if ((int) $dataPost['impt_predeterminado'] === 1) {
            $this->impuestosModel->quitarPredeterminados((int) $dataPost['fk_impuesto'], $idTarifa);
        }

        $this->impuestosModel->actualizarTarifa($idTarifa, $dataPost);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Tarifa de impuesto actualizada exitosamente</h5>',
        ]);
    }

    public function aplicarCambioMasivo() {
        try {
            $dataPost = json_decode(file_get_contents('php://input'));
            $tarifaDestinoId = (int) ($dataPost->tarifaDestinoId ?? 0);
            $tarifaOrigenId = (int) ($dataPost->tarifaOrigenId ?? 0);
            $metodoCalculo = trim((string) ($dataPost->metodoCalculo ?? ''));

            if ($tarifaDestinoId <= 0 || $tarifaOrigenId <= 0) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'Seleccione la tarifa destino y la tarifa antigua a reemplazar.',
                ]);
            }

            if ($tarifaDestinoId === $tarifaOrigenId) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'La tarifa destino no puede ser igual a la tarifa antigua.',
                ]);
            }

            if (!in_array($metodoCalculo, ['ASUMIR_VALOR_IVA', 'CALCULAR_BASE'], true)) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'Seleccione un metodo de calculo valido.',
                ]);
            }

            $tarifaDestino = $this->impuestosModel->getTarifaById($tarifaDestinoId);
            $tarifaOrigen = $this->impuestosModel->getTarifaById($tarifaOrigenId);

            if (!$tarifaDestino || !$tarifaOrigen) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'No se encontro la informacion completa de las tarifas seleccionadas.',
                ]);
            }

            if ((int) $tarifaDestino->fk_impuesto !== 1 || (int) $tarifaOrigen->fk_impuesto !== 1) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'El cambio masivo desde esta pantalla solo aplica para tarifas de IVA.',
                ]);
            }

            if ((int) $tarifaDestino->fk_impuesto !== (int) $tarifaOrigen->fk_impuesto) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'La tarifa destino y la tarifa antigua deben pertenecer al mismo impuesto.',
                ]);
            }

            if (($tarifaDestino->impt_grupo ?? '') !== ($tarifaOrigen->impt_grupo ?? '')) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'La tarifa destino y la tarifa antigua deben pertenecer al mismo grupo.',
                ]);
            }

            $resultado = $this->impLib->aplicarCambioMasivoProductos(
                    $tarifaOrigenId,
                    $tarifaDestinoId,
                    (int) $tarifaDestino->fk_impuesto,
                    $metodoCalculo,
                    (float) $tarifaOrigen->impt_porcentage,
                    (float) $tarifaDestino->impt_porcentage
            );

            if ((int) $resultado['productos'] === 0) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'No existen productos activos vinculados a la tarifa antigua seleccionada.',
                ]);
            }

            $this->logs->logSuccess(
                    'CAMBIO MASIVO DE IVA EN PRODUCTOS. '
                    . 'ORIGEN ID ' . $tarifaOrigenId . ' '
                    . $tarifaOrigen->impt_detalle . ' ' . (float) $tarifaOrigen->impt_porcentage . '%. '
                    . 'DESTINO ID ' . $tarifaDestinoId . ' '
                    . $tarifaDestino->impt_detalle . ' ' . (float) $tarifaDestino->impt_porcentage . '%. '
                    . 'METODO ' . $metodoCalculo . '. '
                    . 'PRODUCTOS ACTUALIZADOS ' . $resultado['productos'] . '. '
                    . 'PORCENTAJE PRODUCTO ACTUALIZADO ' . ($resultado['porcentaje_producto'] ?? 0) . '. '
                    . 'PRECIOS RECALCULADOS ' . $resultado['precios']
            );

            return $this->response->setJSON([
                        'status' => 'success',
                        'msg' => 'Cambio masivo aplicado correctamente. Productos actualizados: ' . $resultado['productos'] . '. Porcentaje IVA producto actualizado: ' . ($resultado['porcentaje_producto'] ?? 0) . '. Precios recalculados: ' . $resultado['precios'] . '.',
            ]);
        } catch (Throwable $e) {
            $this->logs->logError('ERROR EN CAMBIO MASIVO DE IVA EN PRODUCTOS: ' . $e->getMessage());

            return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => 'Error al aplicar cambio masivo de IVA: ' . $e->getMessage(),
            ]);
        }
    }

    public function aplicarCambioMasivoCuentas() {
        try {
            $dataPost = json_decode(file_get_contents('php://input'));
            $tarifaId = (int) ($dataPost->tarifaId ?? 0);
            $cuentaCompraOrigen = trim((string) ($dataPost->cuentaCompraOrigen ?? ''));
            $cuentaCompraDestino = trim((string) ($dataPost->cuentaCompraDestino ?? ''));
            $cuentaVentaOrigen = trim((string) ($dataPost->cuentaVentaOrigen ?? ''));
            $cuentaVentaDestino = trim((string) ($dataPost->cuentaVentaDestino ?? ''));

            if ($tarifaId <= 0) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'Seleccione la tarifa de IVA para aplicar el cambio de cuentas.',
                ]);
            }

            $actualizaCompras = $cuentaCompraOrigen !== '' || $cuentaCompraDestino !== '';
            $actualizaVentas = $cuentaVentaOrigen !== '' || $cuentaVentaDestino !== '';

            if (!$actualizaCompras && !$actualizaVentas) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'Seleccione al menos una cuenta origen y destino para compras o ventas.',
                ]);
            }

            if ($actualizaCompras && ($cuentaCompraOrigen === '' || $cuentaCompraDestino === '')) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'Para cambiar cuenta de compras debe seleccionar cuenta origen y cuenta destino.',
                ]);
            }

            if ($actualizaVentas && ($cuentaVentaOrigen === '' || $cuentaVentaDestino === '')) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'Para cambiar cuenta de ventas debe seleccionar cuenta origen y cuenta destino.',
                ]);
            }

            if ($cuentaCompraOrigen !== '' && $cuentaCompraOrigen === $cuentaCompraDestino) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'La cuenta de compras origen no puede ser igual a la cuenta destino.',
                ]);
            }

            if ($cuentaVentaOrigen !== '' && $cuentaVentaOrigen === $cuentaVentaDestino) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'La cuenta de ventas origen no puede ser igual a la cuenta destino.',
                ]);
            }

            $tarifa = $this->impuestosModel->getTarifaById($tarifaId);

            if (!$tarifa || (int) $tarifa->fk_impuesto !== 1) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'El cambio masivo de cuentas desde esta pantalla solo aplica para tarifas de IVA.',
                ]);
            }

            foreach ([$cuentaCompraOrigen, $cuentaCompraDestino, $cuentaVentaOrigen, $cuentaVentaDestino] as $cuenta) {
                if ($cuenta === '') {
                    continue;
                }
                $getCuenta = $this->ccm->getData("cc_cuenta_contabledet", ['ctad_codigo' => $cuenta, 'ctad_estado' => 1,], "ctad_codigo, ctad_nombre_cuenta", null, 1);

                if (!$getCuenta) {
                    return $this->response->setJSON([
                                'status' => 'warning',
                                'msg' => 'La cuenta contable ' . $cuenta . ' no existe o no esta activa.',
                    ]);
                }
            }

            $resultado = $this->impLib->aplicarCambioMasivoCuentasProductos(
                    $tarifaId,
                    (int) $tarifa->fk_impuesto,
                    $cuentaCompraOrigen ?: null,
                    $cuentaCompraDestino ?: null,
                    $cuentaVentaOrigen ?: null,
                    $cuentaVentaDestino ?: null
            );

            if ((int) $resultado['productos'] === 0) {
                return $this->response->setJSON([
                            'status' => 'warning',
                            'msg' => 'No existen productos activos vinculados a la tarifa seleccionada.',
                ]);
            }

            $this->logs->logSuccess(
                    'CAMBIO MASIVO DE CUENTAS CONTABLES EN PRODUCTOS. '
                    . 'TARIFA IVA ID ' . $tarifaId . ' '
                    . $tarifa->impt_detalle . ' ' . (float) $tarifa->impt_porcentage . '%. '
                    . 'CTA COMPRA ORIGEN ' . ($cuentaCompraOrigen ?: 'NO APLICA') . '. '
                    . 'CTA COMPRA DESTINO ' . ($cuentaCompraDestino ?: 'NO APLICA') . '. '
                    . 'CTA VENTA ORIGEN ' . ($cuentaVentaOrigen ?: 'NO APLICA') . '. '
                    . 'CTA VENTA DESTINO ' . ($cuentaVentaDestino ?: 'NO APLICA') . '. '
                    . 'PRODUCTOS BASE ' . $resultado['productos'] . '. '
                    . 'COMPRAS ACTUALIZADAS ' . $resultado['compras'] . '. '
                    . 'VENTAS ACTUALIZADAS ' . $resultado['ventas']
            );

            return $this->response->setJSON([
                        'status' => 'success',
                        'msg' => 'Cambio de cuentas aplicado correctamente. Compras actualizadas: ' . $resultado['compras'] . '. Ventas actualizadas: ' . $resultado['ventas'] . '.',
            ]);
        } catch (Throwable $e) {
            $this->logs->logError('ERROR EN CAMBIO MASIVO DE CUENTAS CONTABLES EN PRODUCTOS: ' . $e->getMessage());

            return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => 'Error al aplicar cambio masivo de cuentas: ' . $e->getMessage(),
            ]);
        }
    }

    public function saveCuentaTarifaContable() {
        $dataPost = $this->getDataCuentaTarifaContablePost();
        $validacion = $this->validarCuentaTarifaContable($dataPost);

        if ($validacion['status']) {
            return $this->response->setJSON([
                        'status' => 'warning',
                        'msg' => $validacion['msg'],
            ]);
        }

        $existe = $this->impuestosModel->existeCuentaTarifaContable(
                (int) $dataPost['fk_impuesto_tarifa'],
                $dataPost['tipo_movimiento'],
                $dataPost['tipo_cuenta']
        );

        if ($existe) {
            return $this->response->setJSON([
                        'status' => 'warning',
                        'msg' => 'Ya existe una cuenta configurada para esta tarifa, movimiento y tipo de cuenta.',
            ]);
        }

        $cuentaId = $this->impuestosModel->guardarCuentaTarifaContable($dataPost);
        $this->logs->logSuccess('SE HA CREADO CONFIGURACION CONTABLE DE TARIFA DE IMPUESTO CON EL ID ' . $cuentaId . '. COMENTARIO: ' . ($dataPost['comentario'] ?: 'SIN COMENTARIO'));

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Cuenta contable de tarifa registrada correctamente.',
        ]);
    }

    public function updateCuentaTarifaContable() {
        $idCuentaTarifa = (int) $this->request->getPost('idCuentaTarifa');
        $dataPost = $this->getDataCuentaTarifaContablePost();

        if ($idCuentaTarifa <= 0) {
            return $this->response->setJSON([
                        'status' => 'warning',
                        'msg' => 'Seleccione una configuracion para modificar.',
            ]);
        }

        $validacion = $this->validarCuentaTarifaContable($dataPost);

        if ($validacion['status']) {
            return $this->response->setJSON([
                        'status' => 'warning',
                        'msg' => $validacion['msg'],
            ]);
        }

        $existe = $this->impuestosModel->existeCuentaTarifaContable(
                (int) $dataPost['fk_impuesto_tarifa'],
                $dataPost['tipo_movimiento'],
                $dataPost['tipo_cuenta'],
                $idCuentaTarifa
        );

        if ($existe) {
            return $this->response->setJSON([
                        'status' => 'warning',
                        'msg' => 'Ya existe una cuenta configurada para esta tarifa, movimiento y tipo de cuenta.',
            ]);
        }

        $this->impuestosModel->actualizarCuentaTarifaContable($idCuentaTarifa, $dataPost);
        $this->logs->logSuccess('SE HA ACTUALIZADO CONFIGURACION CONTABLE DE TARIFA DE IMPUESTO CON EL ID ' . $idCuentaTarifa . '. COMENTARIO: ' . ($dataPost['comentario'] ?: 'SIN COMENTARIO'));

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Cuenta contable de tarifa actualizada correctamente.',
        ]);
    }

    private function getDataTarifaPost(): array {
        $fechaInicio = $this->normalizarFechaNullable($this->request->getPost('imptFechaInicioVigencia'));
        $fechaFin = $this->normalizarFechaNullable($this->request->getPost('imptFechaFinVigencia'));
        $grupo = $this->request->getPost('imptGrupo');

        return [
            'fk_impuesto' => (int) $this->request->getPost('fkImpuesto'),
            'impt_codigo' => trim((string) $this->request->getPost('imptCodigo')),
            'impt_porcentage' => (float) $this->request->getPost('imptPorcentage'),
            'impt_detalle' => mb_strtoupper(trim((string) $this->request->getPost('imptDetalle')), 'UTF-8'),
            'impt_fecha_inicio_vigencia' => $fechaInicio,
            'impt_fecha_fin_vigencia' => $fechaFin,
            'impt_estado' => $this->request->getPost('imptEstado'),
            'impt_predeterminado' => (int) $this->request->getPost('imptPredeterminado'),
            'impt_report_iva' => (int) $this->request->getPost('imptReportIva'),
            'impt_grupo' => $grupo ?: null,
        ];
    }

    private function getDataCuentaTarifaContablePost(): array {
        return [
            'fk_impuesto_tarifa' => (int) $this->request->getPost('fkImpuestoTarifa'),
            'tipo_movimiento' => trim((string) $this->request->getPost('tipoMovimiento')),
            'tipo_cuenta' => trim((string) $this->request->getPost('tipoCuenta')),
            'fk_cuentacontable_det' => trim((string) $this->request->getPost('fkCuentaContableDet')),
            'estado' => (int) $this->request->getPost('estado'),
            'comentario' => mb_strtoupper(trim((string) $this->request->getPost('comentario')), 'UTF-8') ?: null,
        ];
    }

    private function validarCuentaTarifaContable(array $dataPost): array {
        if ((int) $dataPost['fk_impuesto_tarifa'] <= 0) {
            return ['status' => true, 'msg' => 'Seleccione la tarifa de impuesto.'];
        }

        if (!in_array($dataPost['tipo_movimiento'], ['COMPRA', 'VENTA'], true)) {
            return ['status' => true, 'msg' => 'Seleccione un tipo de movimiento valido.'];
        }

        if (!in_array($dataPost['tipo_cuenta'], ['IVA', 'INVENTARIO', 'DESCUENTO'], true)) {
            return ['status' => true, 'msg' => 'Seleccione un tipo de cuenta valido.'];
        }

        if ($dataPost['fk_cuentacontable_det'] === '') {
            return ['status' => true, 'msg' => 'Seleccione la cuenta contable.'];
        }

        $tarifa = $this->impuestosModel->getTarifaById((int) $dataPost['fk_impuesto_tarifa']);

        if (!$tarifa) {
            return ['status' => true, 'msg' => 'La tarifa seleccionada no existe.'];
        }

        $cuenta = $this->ccm->getData('cc_cuenta_contabledet', [
            'ctad_codigo' => $dataPost['fk_cuentacontable_det'],
            'ctad_estado' => 1,
                ], 'ctad_codigo', null, 1);

        if (!$cuenta) {
            return ['status' => true, 'msg' => 'La cuenta contable seleccionada no existe o no esta activa.'];
        }

        return ['status' => false, 'msg' => ''];
    }

    private function normalizarFechaNullable($fecha): ?string {
        $fecha = trim((string) $fecha);

        if ($fecha === '' || $fecha === 'null' || $fecha === 'undefined' || $fecha === '0000-00-00') {
            return null;
        }

        return $fecha;
    }

    private function getReglasValidacion(): array {
        return [
            'fkImpuesto' => ['label' => 'Impuesto', 'rules' => 'trim|required'],
            'imptCodigo' => ['label' => 'Codigo SRI', 'rules' => 'trim|required'],
            'imptPorcentage' => ['label' => 'Porcentaje', 'rules' => 'trim|required'],
            'imptDetalle' => ['label' => 'Detalle', 'rules' => 'trim|required'],
            'imptEstado' => ['label' => 'Estado', 'rules' => 'trim|required'],
            'imptReportIva' => ['label' => 'Reporte IVA', 'rules' => 'trim|required'],
        ];
    }

    private function getErroresValidacion(): array {
        return [
            'fkImpuesto' => $this->validation->getError('fkImpuesto'),
            'imptCodigo' => $this->validation->getError('imptCodigo'),
            'imptPorcentage' => $this->validation->getError('imptPorcentage'),
            'imptDetalle' => $this->validation->getError('imptDetalle'),
            'imptEstado' => $this->validation->getError('imptEstado'),
            'imptReportIva' => $this->validation->getError('imptReportIva'),
        ];
    }
}
