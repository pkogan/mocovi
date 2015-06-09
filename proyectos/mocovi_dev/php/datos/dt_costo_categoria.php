<?php

class dt_costo_categoria extends toba_datos_tabla {

    function get_listado() {
        $sql = "SELECT
			t_cc.id_costo_categoria,
			t_p.anio as id_periodo_nombre,
			t_c.codigo_siu as id_categoria_nombre,
                        e.codigo as tipo_escal,
			t_cc.costo
		FROM
			costo_categoria as t_cc,
			periodo as t_p,
			categoria as t_c, 
                        escalafon as e
		WHERE
				t_cc.id_periodo = t_p.id_periodo
			AND  t_cc.id_categoria = t_c.id_categoria
                        AND t_c.id_escalafon=e.id_escalafon";
        return toba::db('mocovi_dev')->consultar($sql);
    }

    static function get_costo_categorias_periodo_actual() {
        $sql = "select c.codigo_siu,costo from costo_categoria cc 
                inner join categoria c on c.id_categoria=cc.id_categoria
                inner join periodo p on cc.id_periodo=p.id_periodo and actual is true
                ";

        $costos_categoria = toba::db()->consultar($sql);

        $costos = array();
         /*costodiacategoria= (basico + zona)*13/360*/
        foreach ($costos_categoria as $costo) {
            $costos[$costo['codigo_siu']] = $costo['costo']*1.4*13/360;
        }
        return $costos;
    }

}

?>