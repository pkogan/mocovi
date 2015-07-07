<?php
class dt_periodo extends toba_datos_tabla
{
    static function get_periodo_a_presupuestar(){
        	$sql = "SELECT * FROM periodo where presupuestando is true";
		return toba::db('mocovi_dev')->consultar($sql);
    }
            function get_listado()
	{
		$sql = "SELECT
			t_p.id_periodo,
			t_p.anio,
			t_p.fecha_inicio,
			t_p.fecha_fin,
			t_p.fecha_ultima_liquidacion,
			t_p.actual
		FROM
			periodo as t_p";
		return toba::db('mocovi_dev')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_periodo, anio FROM periodo ORDER BY id_periodo";
                $sql = toba::perfil_de_datos()->filtrar($sql);
		return toba::db('mocovi_dev')->consultar($sql);
	}








}
?>