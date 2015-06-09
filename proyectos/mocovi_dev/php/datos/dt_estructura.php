<?php
class dt_estructura extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_e.id_estructura,
			t_c.codigo_siu as id_categoria_nombre,
			t_u.nombre as id_unidad_nombre,
			t_e.cantidad,
			t_e.norma,
			t_p.id_periodo as id_periodo_nombre
		FROM
			estructura as t_e,
			categoria as t_c,
			unidad as t_u,
			periodo as t_p
		WHERE
				t_e.id_categoria = t_c.id_categoria
			AND  t_e.id_unidad = t_u.id_unidad
			AND  t_e.id_periodo = t_p.id_periodo
		ORDER BY norma";
		return toba::db('mocovi_dev')->consultar($sql);
	}

}

?>