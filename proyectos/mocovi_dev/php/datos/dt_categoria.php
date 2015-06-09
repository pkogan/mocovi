<?php
class dt_categoria extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_c.id_categoria,
			t_c.codigo_siu,
			t_c.codigo_viejo,
			t_e.escalafon as id_escalafon_nombre
		FROM
			categoria as t_c,
			escalafon as t_e
		WHERE
				t_c.id_escalafon = t_e.id_escalafon
		ORDER BY codigo_siu";
		return toba::db('mocovi_dev')->consultar($sql);
	}

	function get_descripciones($id_escalafon=null)
	{
            if(!is_null($id_escalafon)){
                $where=' where presupuestable is true and id_escalafon='.$id_escalafon;
            }  else {
            $where='';    
            }
		$sql = "SELECT id_categoria, codigo_siu FROM categoria $where ORDER BY codigo_siu";
		return toba::db('mocovi_dev')->consultar($sql);
	}

        function get_descripciones_docentes()
	{
            return $this->get_descripciones(1);
	}





}
?>