<?php
class dt_escalafon extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_e.id_escalafon,
			t_e.escalafon,
			t_e.codigo
		FROM
			escalafon as t_e
		ORDER BY escalafon";
		return toba::db('mocovi_dev')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_escalafon, escalafon, codigo FROM escalafon ORDER BY escalafon";
		return toba::db('mocovi_dev')->consultar($sql);
	}





}
?>