<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

use App\Controllers\BaseController;
use Modules\Inventarios\Models\HistoricoModel;
use function view;

/**
 * Description of HistoricoController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 mar 2026
 * @time 9:34:19 p.m.
 */
class HistoricoController extends BaseController {

    //put your code here

    protected $dirViewModule;
    protected $hisModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Inventarios\Views';
        
        $this->hisModel = new HistoricoModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaGrupos'] = $this->ccm->getData('cc_grupos', ['gr_estado' => 1], '*');
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\historico\viewHistorico', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getInventarioHistorico() {
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
            'kardStock' => $dataPost->invStock !== '-1' ? $dataPost->invStock : null,
            'invGrupo' => $dataPost->invGrupo ?? null,
            'invIva' => $dataPost->invIva !== '-1' ? $dataPost->invIva : null,
            'fechaCorte' => $dataPost->fechaCorte ?? date('Y-m-d'),
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $data = $this->hisModel->getInventarioHistorico($filtros, $start, $length, $searchValue, $orderBy, $orderDir);
        $countProductosAll = $this->hisModel->countFilteredProducts($filtros);
        $countFilteredProducts = $this->hisModel->countFilteredProducts($filtros, $searchValue);

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'draw' => intval($draw),
                    'recordsTotal' => $countProductosAll,
                    'recordsFiltered' => $countFilteredProducts,
        ]);
    }
    
}
