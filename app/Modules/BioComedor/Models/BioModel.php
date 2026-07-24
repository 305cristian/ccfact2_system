<?php

namespace Modules\BioComedor\Models;

use CodeIgniter\Model;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of BioModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:07:06 p.m.
 */
class BioModel extends Model {
    //put your code here

    public function getListaEquipos(): array {

        $builder = $this->db->table("cc_bio_equipos tb1");
        $builder->select("tb1.*, tb2.com_codigo, tb2.com_nombre");
        $builder->join("cc_bio_comedores tb2", "tb2.id = tb1.fk_comedor");
        $builder->orderBy("tb1.id", "DESC");
        return $builder->get()->getResult();
    }

    public function getListaEquiposActivos(): array {

        $builder = $this->db->table("cc_bio_equipos tb1");
        $builder->select("tb1.id, tb1.fk_comedor, tb1.eq_codigo, tb1.eq_nombre, tb2.com_codigo, tb2.com_nombre");
        $builder->join("cc_bio_comedores tb2", "tb2.id = tb1.fk_comedor");
        $builder->where("tb1.eq_estado", 1);
        $builder->where("tb2.com_estado", 1);
        $builder->orderBy("tb2.com_nombre", "ASC");
        $builder->orderBy("tb1.eq_nombre", "ASC");
        return $builder->get()->getResult();
    }

    public function getListaComensales(): array {

        $builder = $this->db->table("cc_bio_comensales tb1");
        $builder->select("
            tb1.*,
            tb2.cont_ruc,
            tb2.cont_nombre,
            tb3.proy_codigo,
            tb3.proy_nombre,
            tb4.fk_departamento,
            tb4.area_codigo,
            tb4.area_nombre,
            tb5.dep_codigo,
            tb5.dep_nombre
        ");
        $builder->join("cc_bio_contratistas tb2", "tb2.id = tb1.fk_contratista", "left");
        $builder->join("cc_bio_proyectos tb3", "tb3.id = tb1.fk_proyecto", "left");
        $builder->join("cc_bio_areas tb4", "tb4.id = tb1.fk_area", "left");
        $builder->join("cc_bio_departamentos tb5", "tb5.id = tb4.fk_departamento", "left");
        $builder->orderBy("tb1.id", "DESC");
        return $builder->get()->getResult();
    }

    public function getListaAreasActivas(): array {

        $builder = $this->db->table("cc_bio_areas tb1");
        $builder->select("tb1.id, tb1.fk_departamento, tb1.area_codigo, tb1.area_nombre, tb2.dep_codigo, tb2.dep_nombre");
        $builder->join("cc_bio_departamentos tb2", "tb2.id = tb1.fk_departamento", "left");
        $builder->where("tb1.area_estado", 1);
        $builder->orderBy("tb2.dep_nombre", "ASC");
        $builder->orderBy("tb1.area_nombre", "ASC");
        return $builder->get()->getResult();
    }

    public function getListaAreas(): array {

        $builder = $this->db->table("cc_bio_areas tb1");
        $builder->select("tb1.*, tb2.dep_codigo, tb2.dep_nombre");
        $builder->join("cc_bio_departamentos tb2", "tb2.id = tb1.fk_departamento", "left");
        $builder->orderBy("tb1.id", "DESC");
        return $builder->get()->getResult();
    }

    public function getListaDepartamentosActivos(): array {

        $builder = $this->db->table("cc_bio_departamentos tb1");
        $builder->select("tb1.id, tb1.dep_codigo, tb1.dep_nombre");
        $builder->where("tb1.dep_estado", 1);
        $builder->orderBy("tb1.dep_nombre", "ASC");
        return $builder->get()->getResult();
    }

    public function getListaHorarios(): array {

        $builder = $this->db->table("cc_bio_servicio_horarios tb1");
        $builder->select("tb1.*, tb2.serv_codigo, tb2.serv_nombre");
        $builder->join("cc_bio_servicios tb2", "tb2.id = tb1.fk_servicio", "left");
        $builder->orderBy("tb2.serv_orden", "ASC");
        $builder->orderBy("tb1.hor_hora_inicio", "ASC");
        return $builder->get()->getResult();
    }

    public function getListaMarcaciones(array $filtros = []): array {

        $builder = $this->db->table("cc_bio_marcaciones tb1");
        $builder->select("
            tb1.*,
            tb2.comens_codigo,
            tb2.comens_cedula,
            tb2.comens_nombres,
            tb2.comens_apellidos,
            tb3.com_codigo,
            tb3.com_nombre,
            tb4.eq_codigo,
            tb4.eq_nombre,
            tb5.serv_codigo,
            tb5.serv_nombre,
            tb6.cont_nombre,
            tb7.proy_nombre
        ");
        $builder->join("cc_bio_comensales tb2", "tb2.id = tb1.fk_comensal");
        $builder->join("cc_bio_comedores tb3", "tb3.id = tb1.fk_comedor");
        $builder->join("cc_bio_equipos tb4", "tb4.id = tb1.fk_equipo");
        $builder->join("cc_bio_servicios tb5", "tb5.id = tb1.fk_servicio");
        $builder->join("cc_bio_contratistas tb6", "tb6.id = tb1.fk_contratista");
        $builder->join("cc_bio_proyectos tb7", "tb7.id = tb1.fk_proyecto");

        if (!empty($filtros['fechaDesde'])) {
            $builder->where("tb1.marc_fecha >=", $filtros['fechaDesde']);
        }

        if (!empty($filtros['fechaHasta'])) {
            $builder->where("tb1.marc_fecha <=", $filtros['fechaHasta']);
        }

        if (!empty($filtros['fkComedor'])) {
            $builder->where("tb1.fk_comedor", $filtros['fkComedor']);
        }

        if (!empty($filtros['fkServicio'])) {
            $builder->where("tb1.fk_servicio", $filtros['fkServicio']);
        }

        if (!empty($filtros['marcEstado'])) {
            $builder->where("tb1.marc_estado", $filtros['marcEstado']);
        }

        if (($filtros['marcRetraso'] ?? '') !== '' && ($filtros['marcRetraso'] ?? null) !== null) {
            $builder->where("tb1.marc_es_retraso", $filtros['marcRetraso']);
        }

        if (!empty($filtros['texto'])) {
            $texto = trim((string) $filtros['texto']);
            $builder->groupStart();
            $builder->like("tb2.comens_codigo", $texto);
            $builder->orLike("tb2.comens_cedula", $texto);
            $builder->orLike("tb2.comens_nombres", $texto);
            $builder->orLike("tb2.comens_apellidos", $texto);
            $builder->orLike("tb1.marc_codigo_biometrico", $texto);
            $builder->groupEnd();
        }

        $builder->orderBy("tb1.marc_fecha_hora", "DESC");
        return $builder->get()->getResult();
    }

    public function getComensalPorIdentificador(string $tipoIdentificacion, string $identificador): ?object {

        $builder = $this->db->table("cc_bio_comensales tb1");
        $builder->select("
            tb1.*,
            tb2.cont_nombre,
            tb3.proy_nombre,
            tb4.area_nombre,
            tb5.dep_nombre
        ");
        $builder->join("cc_bio_contratistas tb2", "tb2.id = tb1.fk_contratista");
        $builder->join("cc_bio_proyectos tb3", "tb3.id = tb1.fk_proyecto");
        $builder->join("cc_bio_areas tb4", "tb4.id = tb1.fk_area", "left");
        $builder->join("cc_bio_departamentos tb5", "tb5.id = tb4.fk_departamento", "left");

        if ($tipoIdentificacion === 'RFID') {
            $builder->where("tb1.comens_uid_rfid", $identificador);
        } else if ($tipoIdentificacion === 'BIOMETRICO') {
            $builder->where("tb1.comens_identificador_biometrico", $identificador);
        } else {
            $builder->where("tb1.comens_codigo", $identificador);
        }

        return $builder->get()->getRow();
    }

    public function getComensalPorIdentificadorAutomatico(string $identificador): ?object {

        $builder = $this->db->table("cc_bio_comensales tb1");
        $builder->select("
            tb1.*,
            tb2.cont_nombre,
            tb3.proy_nombre,
            tb4.area_nombre,
            tb5.dep_nombre
        ");
        $builder->join("cc_bio_contratistas tb2", "tb2.id = tb1.fk_contratista");
        $builder->join("cc_bio_proyectos tb3", "tb3.id = tb1.fk_proyecto");
        $builder->join("cc_bio_areas tb4", "tb4.id = tb1.fk_area", "left");
        $builder->join("cc_bio_departamentos tb5", "tb5.id = tb4.fk_departamento", "left");
        $builder->groupStart();
        $builder->where("tb1.comens_codigo", $identificador);
        $builder->orWhere("tb1.comens_uid_rfid", $identificador);
        $builder->orWhere("tb1.comens_identificador_biometrico", $identificador);
        $builder->groupEnd();
        return $builder->get()->getRow();
    }

    public function getEquipoActivo(int $equipoId, int $comedorId): ?object {

        $builder = $this->db->table("cc_bio_equipos tb1");
        $builder->select("tb1.*, tb2.com_codigo, tb2.com_nombre");
        $builder->join("cc_bio_comedores tb2", "tb2.id = tb1.fk_comedor");
        $builder->where([
            "tb1.id" => $equipoId,
            "tb1.fk_comedor" => $comedorId,
            "tb1.eq_estado" => 1,
            "tb2.com_estado" => 1,
        ]);
        return $builder->get()->getRow();
    }

    public function getEquipoActivoPorId(int $equipoId): ?object {

        $builder = $this->db->table("cc_bio_equipos tb1");
        $builder->select("tb1.*, tb2.com_codigo, tb2.com_nombre");
        $builder->join("cc_bio_comedores tb2", "tb2.id = tb1.fk_comedor");
        $builder->where([
            "tb1.id" => $equipoId,
            "tb1.eq_estado" => 1,
            "tb2.com_estado" => 1,
        ]);
        return $builder->get()->getRow();
    }

    public function getHorarioServicioPorHora(string $hora): ?object {

        $builder = $this->db->table("cc_bio_servicio_horarios tb1");
        $builder->select("tb1.*, tb2.serv_codigo, tb2.serv_nombre");
        $builder->join("cc_bio_servicios tb2", "tb2.id = tb1.fk_servicio");
        $builder->where("tb1.hor_estado", 1);
        $builder->where("tb2.serv_estado", 1);
        $builder->groupStart();
        $builder->groupStart();
        $builder->where("tb1.hor_cruza_medianoche", 0);
        $builder->where("tb1.hor_hora_inicio <=", $hora);
        $builder->where("tb1.hor_hora_fin >=", $hora);
        $builder->groupEnd();
        $builder->orGroupStart();
        $builder->where("tb1.hor_cruza_medianoche", 1);
        $builder->groupStart();
        $builder->where("tb1.hor_hora_inicio <=", $hora);
        $builder->orWhere("tb1.hor_hora_fin >=", $hora);
        $builder->groupEnd();
        $builder->groupEnd();
        $builder->groupEnd();
        $builder->orderBy("tb2.serv_orden", "ASC");
        return $builder->get()->getRow();
    }

    public function getHorarioServicioPorServicioHora(int $servicioId, string $hora): ?object {

        $builder = $this->db->table("cc_bio_servicio_horarios tb1");
        $builder->select("tb1.*, tb2.serv_codigo, tb2.serv_nombre");
        $builder->join("cc_bio_servicios tb2", "tb2.id = tb1.fk_servicio");
        $builder->where("tb1.fk_servicio", $servicioId);
        $builder->where("tb1.hor_estado", 1);
        $builder->where("tb2.serv_estado", 1);
        $builder->groupStart();
        $builder->groupStart();
        $builder->where("tb1.hor_cruza_medianoche", 0);
        $builder->where("tb1.hor_hora_inicio <=", $hora);
        $builder->where("tb1.hor_hora_fin >=", $hora);
        $builder->groupEnd();
        $builder->orGroupStart();
        $builder->where("tb1.hor_cruza_medianoche", 1);
        $builder->groupStart();
        $builder->where("tb1.hor_hora_inicio <=", $hora);
        $builder->orWhere("tb1.hor_hora_fin >=", $hora);
        $builder->groupEnd();
        $builder->groupEnd();
        $builder->groupEnd();
        return $builder->get()->getRow();
    }

    public function getMarcacionValidaServicioDia(int $comensalId, int $servicioId, string $fecha): ?object {

        $builder = $this->db->table("cc_bio_marcaciones");
        $builder->select("id, marc_fecha_hora");
        $builder->where([
            "fk_comensal" => $comensalId,
            "fk_servicio" => $servicioId,
            "marc_fecha" => $fecha,
            "marc_estado" => "VALIDA",
        ]);
        $builder->orderBy("marc_fecha_hora", "DESC");
        return $builder->get()->getRow();
    }

    public function getMarcacionPorTolerancia(int $comensalId, string $fechaHoraDesde, string $fechaHoraHasta): ?object {

        $builder = $this->db->table("cc_bio_marcaciones");
        $builder->select("id, marc_fecha_hora");
        $builder->where("fk_comensal", $comensalId);
        $builder->where("marc_estado", "VALIDA");
        $builder->where("marc_fecha_hora >=", $fechaHoraDesde);
        $builder->where("marc_fecha_hora <=", $fechaHoraHasta);
        $builder->orderBy("marc_fecha_hora", "DESC");
        return $builder->get()->getRow();
    }

    public function getReporteMarcacionesResumen(array $filtros = []): object {

        $builder = $this->baseQueryReporteMarcaciones($filtros);
        $builder->select("
            COUNT(tb1.id) AS total_marcaciones,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' THEN 1 ELSE 0 END) AS total_validas,
            SUM(CASE WHEN tb1.marc_estado = 'REPETIDA' THEN 1 ELSE 0 END) AS total_repetidas,
            SUM(CASE WHEN tb1.marc_estado = 'ANULADA' THEN 1 ELSE 0 END) AS total_anuladas,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_es_retraso = 1 THEN 1 ELSE 0 END) AS total_retrasos,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_genera_consumo = 1 THEN 1 ELSE 0 END) AS total_consumos
        ");
        return $builder->get()->getRow() ?? (object) [];
    }

    public function getReportePorServicio(array $filtros = []): array {

        $builder = $this->baseQueryReporteMarcaciones($filtros);
        $builder->select("
            tb5.serv_nombre AS nombre,
            COUNT(tb1.id) AS marcaciones,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_genera_consumo = 1 THEN 1 ELSE 0 END) AS consumos,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_es_retraso = 1 THEN 1 ELSE 0 END) AS retrasos
        ");
        $builder->groupBy("tb5.id, tb5.serv_nombre");
        $builder->orderBy("tb5.serv_orden", "ASC");
        return $builder->get()->getResult();
    }

    public function getReportePorComedor(array $filtros = []): array {

        $builder = $this->baseQueryReporteMarcaciones($filtros);
        $builder->select("
            tb3.com_nombre AS nombre,
            COUNT(tb1.id) AS marcaciones,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_genera_consumo = 1 THEN 1 ELSE 0 END) AS consumos,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_es_retraso = 1 THEN 1 ELSE 0 END) AS retrasos
        ");
        $builder->groupBy("tb3.id, tb3.com_nombre");
        $builder->orderBy("tb3.com_nombre", "ASC");
        return $builder->get()->getResult();
    }

    public function getReportePorContratista(array $filtros = []): array {

        $builder = $this->baseQueryReporteMarcaciones($filtros);
        $builder->select("
            tb6.cont_nombre AS nombre,
            COUNT(tb1.id) AS marcaciones,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_genera_consumo = 1 THEN 1 ELSE 0 END) AS consumos,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_es_retraso = 1 THEN 1 ELSE 0 END) AS retrasos
        ");
        $builder->groupBy("tb6.id, tb6.cont_nombre");
        $builder->orderBy("consumos", "DESC");
        return $builder->get()->getResult();
    }

    public function getReportePorProyecto(array $filtros = []): array {

        $builder = $this->baseQueryReporteMarcaciones($filtros);
        $builder->select("
            tb7.proy_nombre AS nombre,
            COUNT(tb1.id) AS marcaciones,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_genera_consumo = 1 THEN 1 ELSE 0 END) AS consumos,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_es_retraso = 1 THEN 1 ELSE 0 END) AS retrasos
        ");
        $builder->groupBy("tb7.id, tb7.proy_nombre");
        $builder->orderBy("consumos", "DESC");
        return $builder->get()->getResult();
    }

    public function getReportePorFecha(array $filtros = []): array {

        $builder = $this->baseQueryReporteMarcaciones($filtros);
        $builder->select("
            tb1.marc_fecha AS fecha,
            COUNT(tb1.id) AS marcaciones,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_genera_consumo = 1 THEN 1 ELSE 0 END) AS consumos,
            SUM(CASE WHEN tb1.marc_estado = 'VALIDA' AND tb1.marc_es_retraso = 1 THEN 1 ELSE 0 END) AS retrasos
        ");
        $builder->groupBy("tb1.marc_fecha");
        $builder->orderBy("tb1.marc_fecha", "ASC");
        return $builder->get()->getResult();
    }

    private function baseQueryReporteMarcaciones(array $filtros) {

        $builder = $this->db->table("cc_bio_marcaciones tb1");
        $builder->join("cc_bio_comensales tb2", "tb2.id = tb1.fk_comensal");
        $builder->join("cc_bio_comedores tb3", "tb3.id = tb1.fk_comedor");
        $builder->join("cc_bio_equipos tb4", "tb4.id = tb1.fk_equipo");
        $builder->join("cc_bio_servicios tb5", "tb5.id = tb1.fk_servicio");
        $builder->join("cc_bio_contratistas tb6", "tb6.id = tb1.fk_contratista");
        $builder->join("cc_bio_proyectos tb7", "tb7.id = tb1.fk_proyecto");

        if (!empty($filtros['fechaDesde'])) {
            $builder->where("tb1.marc_fecha >=", $filtros['fechaDesde']);
        }

        if (!empty($filtros['fechaHasta'])) {
            $builder->where("tb1.marc_fecha <=", $filtros['fechaHasta']);
        }

        if (!empty($filtros['fkComedor'])) {
            $builder->where("tb1.fk_comedor", $filtros['fkComedor']);
        }

        if (!empty($filtros['fkServicio'])) {
            $builder->where("tb1.fk_servicio", $filtros['fkServicio']);
        }

        if (!empty($filtros['fkContratista'])) {
            $builder->where("tb1.fk_contratista", $filtros['fkContratista']);
        }

        if (!empty($filtros['fkProyecto'])) {
            $builder->where("tb1.fk_proyecto", $filtros['fkProyecto']);
        }

        if (!empty($filtros['marcEstado'])) {
            $builder->where("tb1.marc_estado", $filtros['marcEstado']);
        }

        if (($filtros['marcRetraso'] ?? '') !== '' && ($filtros['marcRetraso'] ?? null) !== null) {
            $builder->where("tb1.marc_es_retraso", $filtros['marcRetraso']);
        }

        return $builder;
    }

    public function generarCodigoComensal(): string {

        $builder = $this->db->table("cc_bio_comensales");
        $builder->select("comens_codigo");
        $builder->like("comens_codigo", "CME-", "after");
        $builder->orderBy("id", "DESC");
        $builder->limit(1);

        $ultimo = $builder->get()->getRow();

        if (!$ultimo || empty($ultimo->comens_codigo)) {
            return "CME-000001";
        }

        $secuencia = (int) str_replace("CME-", "", $ultimo->comens_codigo);
        return "CME-" . str_pad($secuencia + 1, 6, "0", STR_PAD_LEFT);
    }
}
