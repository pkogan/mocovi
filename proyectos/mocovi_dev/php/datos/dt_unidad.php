<?php
class dt_unidad extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_u.id_unidad,
			t_u.nombre,
			t_u.codigo
		FROM
			unidad as t_u
		ORDER BY nombre";
		return toba::db('mocovi_dev')->consultar($sql);
	}

		function get_descripciones()
		{
                   /* $this->_from='unidad';
                    $this->*/
			$sql = "SELECT id_unidad, nombre,codigo FROM unidad ORDER BY nombre";
                        $sql = toba::perfil_de_datos()->filtrar($sql);
			return toba::db('mocovi_dev')->consultar($sql);
		}

            function get_descripciones_unidades()
		{
                   /* $this->_from='unidad';
                    $this->*/
			$sql = "SELECT id_unidad, nombre, codigo FROM unidad
                                where id_tipo_dependencia=1 ORDER BY nombre";
                        //$sql = toba::perfil_de_datos()->filtrar($sql);
			return toba::db('mocovi_dev')->consultar($sql);
		}












}
?>