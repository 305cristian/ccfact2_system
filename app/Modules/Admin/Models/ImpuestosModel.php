<?php

namespace Modules\Admin\Models;

class ImpuestosModel extends \CodeIgniter\Model {

    public function getImpuestos(): array {
        $builder = $this->db->table('cc_impuestos');
        $builder->select('id, imp_nombre, imp_codigo');
        $builder->orderBy('id', 'ASC');
        return $builder->get()->getResult();
    }

    public function getTarifasImpuestos(): array {
        $builder = $this->db->table('cc_impuesto_tarifa tb1');
        $builder->select('tb1.*, tb2.imp_nombre, tb2.imp_codigo');
        $builder->join('cc_impuestos tb2', 'tb2.id = tb1.fk_impuesto', 'left');
        $builder->orderBy('tb1.id', 'ASC');
        return $builder->get()->getResult();
    }

    public function getTarifaById(int $tarifaId): ?object {
        $builder = $this->db->table('cc_impuesto_tarifa tb1');
        $builder->select('tb1.*, tb2.imp_nombre, tb2.imp_codigo');
        $builder->join('cc_impuestos tb2', 'tb2.id = tb1.fk_impuesto', 'left');
        $builder->where('tb1.id', $tarifaId);
        return $builder->get()->getRow();
    }

    public function getProductosPorTarifa(int $tarifaId, int $impuestoId, bool $excluirGrupoDescuentos = false): array {
        $builder = $this->db->table('cc_producto_impuestotarifa tb1');
        $builder->select('tb1.fk_producto');
        $builder->join('cc_productos tb2', 'tb2.id = tb1.fk_producto');
        $builder->join('cc_subgrupos tb3', 'tb3.id = tb2.fk_subgrupo', 'left');
        $builder->join('cc_grupos tb4', 'tb4.id = tb3.fk_grupo', 'left');
        $builder->where([
            'tb1.fk_impuestotarifa' => $tarifaId,
            'tb1.fk_impuesto' => $impuestoId,
            'tb2.prod_estado' => 1,
        ]);

        if ($excluirGrupoDescuentos) {
            $builder->groupStart();
            $builder->where('tb4.gr_nombre !=', 'DESCUENTOS');
            $builder->orWhere('tb4.gr_nombre', null);
            $builder->groupEnd();
        }

        $builder->groupBy('tb1.fk_producto');
        return $builder->get()->getResult();
    }

    public function existeTarifa(string $codigo, float $porcentaje, int $impuestoId): ?object {
        $builder = $this->db->table('cc_impuesto_tarifa');
        $builder->select('id, impt_codigo, impt_porcentage, fk_impuesto');
        $builder->where([
            'impt_codigo' => $codigo,
            'fk_impuesto' => $impuestoId,
        ]);
        $builder->where('impt_porcentage', $porcentaje);
        return $builder->get()->getRow();
    }

    public function quitarPredeterminados(int $impuestoId, ?int $exceptoId = null): bool {
        $builder = $this->db->table('cc_impuesto_tarifa');
        $builder->where('fk_impuesto', $impuestoId);

        if ($exceptoId) {
            $builder->where('id !=', $exceptoId);
        }

        return $builder->update(['impt_predeterminado' => 0]);
    }

    public function guardarTarifa(array $data): int {
        $this->db->table('cc_impuesto_tarifa')->insert($data);
        return (int) $this->db->insertID();
    }

    public function actualizarTarifa(int $id, array $data): bool {
        $builder = $this->db->table('cc_impuesto_tarifa');
        $builder->where('id', $id);
        return $builder->update($data);
    }

    public function getCuentasTarifaContable(): array {
        $builder = $this->db->table('cc_impuesto_tarifa_cuenta_contable tb1');
        $builder->select('tb1.*, tb2.impt_codigo, tb2.impt_porcentage, tb2.impt_detalle, tb2.impt_estado, tb2.impt_grupo, tb3.imp_nombre, tb4.ctad_nombre_cuenta');
        $builder->join('cc_impuesto_tarifa tb2', 'tb2.id = tb1.fk_impuesto_tarifa');
        $builder->join('cc_impuestos tb3', 'tb3.id = tb2.fk_impuesto');
        $builder->join('cc_cuenta_contabledet tb4', 'tb4.ctad_codigo = tb1.fk_cuentacontable_det');
        $builder->orderBy('tb1.id', 'DESC');
        return $builder->get()->getResult();
    }

    public function existeCuentaTarifaContable(int $tarifaId, string $tipoMovimiento, string $tipoCuenta, ?int $exceptoId = null): ?object {
        $builder = $this->db->table('cc_impuesto_tarifa_cuenta_contable');
        $builder->select('id');
        $builder->where([
            'fk_impuesto_tarifa' => $tarifaId,
            'tipo_movimiento' => $tipoMovimiento,
            'tipo_cuenta' => $tipoCuenta,
        ]);

        if ($exceptoId) {
            $builder->where('id !=', $exceptoId);
        }

        return $builder->get()->getRow();
    }

    public function guardarCuentaTarifaContable(array $data): int {
        $this->db->table('cc_impuesto_tarifa_cuenta_contable')->insert($data);
        return (int) $this->db->insertID();
    }

    public function actualizarCuentaTarifaContable(int $id, array $data): bool {
        $builder = $this->db->table('cc_impuesto_tarifa_cuenta_contable');
        $builder->where('id', $id);
        return $builder->update($data);
    }


    public function actualizarPreciosManteniendoPvp(array $productosIds, float $porcentajeOrigen, float $porcentajeDestino): int {
        $builder = $this->db->table('cc_producto_precios');
        $builder->select('fk_tipo_precio, fk_producto, pp_valor');
        $builder->whereIn('fk_producto', $productosIds);
        $precios = $builder->get()->getResult();

        $factorOrigen = 1 + ($porcentajeOrigen / 100);
        $factorDestino = 1 + ($porcentajeDestino / 100);
        $actualizados = 0;

        foreach ($precios as $precio) {
            $precioBaseActual = (float) $precio->pp_valor;
            $precioFinalActual = $precioBaseActual * $factorOrigen;
            $nuevoPrecioBase = $factorDestino > 0 ? round($precioFinalActual / $factorDestino, 4) : $precioBaseActual;

            $builderUpdate = $this->db->table('cc_producto_precios');
            $builderUpdate->where([ 'fk_producto' => (int) $precio->fk_producto, 'fk_tipo_precio' => (int) $precio->fk_tipo_precio]);
            $builderUpdate->update(['pp_valor' => $nuevoPrecioBase]);
            $actualizados++;
        }
       

        return $actualizados;
    }

}
