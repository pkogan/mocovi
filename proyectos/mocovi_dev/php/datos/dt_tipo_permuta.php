<?php
class dt_tipo_permuta extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_tipo_permuta, tipo_permuta FROM tipo_permuta ORDER BY tipo_permuta";
		return toba::db('mocovi_dev')->consultar($sql);
	}

}

?>