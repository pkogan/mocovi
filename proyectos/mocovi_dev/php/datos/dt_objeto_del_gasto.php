<?php
class dt_objeto_del_gasto extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_odg.id_objeto_del_gasto,
			t_odg.codigo_completo,
			t_odg.nombre,
			t_odg.elemento_padre
		FROM
			objeto_del_gasto as t_odg
		ORDER BY codigo_completo";
		return toba::db('mocovi_dev')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT elemento as inciso,
			codigo_completo,
			 codigo || '.' ||nombre as nombre FROM objeto_del_gasto
                        where componente='IN' and presupuestable is true ORDER BY nombre";
		return toba::db('mocovi_dev')->consultar($sql);
	}
	function get_descripciones_partidas($inciso='NULL')
	{
           
		$sql = "SELECT id_objeto_del_gasto,
			 substring(codigo_completo from 0 for 6) || '.' ||nombre as nombre FROM objeto_del_gasto
                        where elemento_padre=$inciso
                        and presupuestable is true
                        ORDER BY nombre";
                //exit($sql);
		return toba::db('mocovi_dev')->consultar($sql);
	}
}