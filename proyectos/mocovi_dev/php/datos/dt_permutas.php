<?php
class dt_permutas extends toba_datos_tabla
{
    public function set($fila) {
        /*escalafon docente*/
        $fila['id_escalafon']=1;
        ei_arbol($fila);
        parent::set($fila);
        
    }
	function get_listado()
	{
		$sql = "SELECT
			t_p.id_permuta,
			t_u.nombre as id_unidad_cede_nombre,
			t_u1.nombre as id_unidad_recibe_nombre,
			t_c.codigo_siu as id_categoria_nombre,
			t_p.cantidad,
			t_p.porcentaje,
			t_p.norma,
			t_p2.id_periodo as id_periodo_nombre
		FROM
			permutas as t_p,
			unidad as t_u,
			unidad as t_u1,
			categoria as t_c,
			periodo as t_p2
		WHERE
				t_p.id_unidad_cede = t_u.id_unidad
			AND  t_p.id_unidad_recibe = t_u1.id_unidad
			AND  t_p.id_categoria = t_c.id_categoria
			AND  t_p.id_periodo = t_p2.id_periodo
		ORDER BY norma";
		return toba::db('mocovi_dev')->consultar($sql);
	}

}

?>