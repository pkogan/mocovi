<?php
class dt_tipo_credito extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_tipo_credito, tipo FROM tipo_credito ORDER BY tipo";
		return toba::db('mocovi_dev')->consultar($sql);
	}

	function get_listado()
	{
		$sql = "SELECT
			t_tc.id_tipo_credito,
			t_tc.tipo
		FROM
			tipo_credito as t_tc
		ORDER BY tipo";
		return toba::db('mocovi_dev')->consultar($sql);
	}

}
?>