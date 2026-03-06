<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

/**
 * Description of CaducidadController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 mar 2026
 * @time 9:33:20 p.m.
 */
use Modules\Inventarios\Models\CaducidadModel;

class CaducidadController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $caducModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\Inventarios\Views';

        $this->caducModel = new CaducidadModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaGrupos'] = $this->ccm->getData('cc_grupos', ['gr_estado' => 1], '*');
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\caducidad\viewCaducidad', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function consultarProductos() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $draw = $dataPost->draw;
        $start = (int) $dataPost->start;
        $length = (int) $dataPost->length;
        $searchValue = (string) $dataPost->search ?? '';

        $orderData = $dataPost->order ?? [];
        $orderBy = ($orderData[0]->column ?? '') ?: 'prod_nombre';
        $orderDir = ($orderData[0]->dir ?? '') ?: 'asc';

        $filtros = [
            'invBodega' => $dataPost->invBodega ?? null,
            'invStock' => $dataPost->invStock !== '-1' ? $dataPost->invStock : null,
            'invGrupo' => $dataPost->invGrupo ?? null,
            'invIva' => $dataPost->invIva !== '-1' ? $dataPost->invIva : null,
            'caducFechas' => $dataPost->caducFechas ?? null,
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $reservas = $this->caducModel->getReservaLotesProductos((int) $dataPost->invBodega);

        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto . '|' . $val->fk_lote] = $val->res_cantidad;
        }

        $data = $this->caducModel->getProductosCaducidad($filtros, $start, $length, $searchValue, $orderBy, $orderDir);
        $countProductosCaducidad = $this->caducModel->countFilteredProductsCaducidad($filtros);
        $countFilteredProductsCaducidad = $this->caducModel->countFilteredProductsCaducidad($filtros, $searchValue);

        foreach ($data as $val) {
            $val->reservaProducto = $reservasProducto[$val->id . '|' . $val->fk_lote] ?? 0;
            $val->stockDisponible =$val->stbl_stock - $val->reservaProducto;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'draw' => intval($draw),
                    'recordsTotal' => $countProductosCaducidad,
                    'recordsFiltered' => $countFilteredProductsCaducidad,
        ]);
    }
}
