<?php

class dt_credito extends toba_datos_tabla {

    function get_listado($where) {
        if (is_null($where)) {
            $where = '';
        } else {
            $where = ' and ' . $where;
        }
        $sql = "SELECT
			t_c.id_credito,
			t_p.anio as id_periodo_nombre,
			t_u.codigo as id_unidad_nombre,
			t_e.escalafon as id_escalafon_nombre,
			t_tc.tipo as id_tipo_credito_nombre,
			t_c.descripcion,
			t_c.credito
		FROM
			credito as t_c	LEFT OUTER JOIN periodo as t_p ON (t_c.id_periodo = t_p.id_periodo)
			LEFT OUTER JOIN unidad as t_u ON (t_c.id_unidad = t_u.id_unidad)
			LEFT OUTER JOIN escalafon as t_e ON (t_c.id_escalafon = t_e.id_escalafon),
			tipo_credito as t_tc
		WHERE
				t_c.id_tipo_credito = t_tc.id_tipo_credito
                                $where
		ORDER BY descripcion";
        return toba::db('mocovi_dev')->consultar($sql);
    }

    static function get_credito_periodo_actual() {
        $sql = "select u.codigo as unidad,e.codigo as escalafon, sum(credito) as credito from credito c
                inner join unidad u on c.id_unidad=u.id_unidad
                inner join escalafon e on e.id_escalafon=c.id_escalafon
                inner join periodo p on c.id_periodo=p.id_periodo and actual is true
                group by u.codigo,e.codigo 
               ";

        $credito_unidad = toba::db()->consultar($sql);

        $credito = array();
        /* costodiacategoria= (basico + zona)*13/360 */
        foreach ($credito_unidad as $row) {
            $credito[$row['unidad']][$row['escalafon']] = $row['credito'];
        }
        return $credito;
    }

}
