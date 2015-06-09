<?php
class dt_presupuesto_permutas extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_pp.id_presupuesto_permutas,
                        t_pp.descripcion,
			t_p.anio as periodo,
			t_u.nombre as unidad,
			t_c.codigo_siu as id_categoria_nombre,
			t_uu.codigo as id_unidad_permuta_nombre,
			t_f.tipo_permuta as id_tipo_permuta_nombre,
			t_pp.cantidad,
			t_e.escalafon as id_escalafon_nombre
		FROM
			presupuesto_permutas as t_pp inner JOIN periodo as t_p ON (t_pp.id_periodo = t_p.id_periodo)
			inner JOIN unidad as t_u ON (t_pp.id_unidad = t_u.id_unidad)
			LEFT OUTER JOIN categoria as t_c ON (t_pp.id_categoria = t_c.id_categoria)
			LEFT OUTER JOIN escalafon as t_e ON (t_pp.id_escalafon = t_e.id_escalafon)
                        left outer join tipo_permuta as t_f on t_pp.id_tipo_permuta=t_f.id_tipo_permuta
                        ";
                $sql = toba::perfil_de_datos()->filtrar($sql);
                $sql= $sql. " left outer join unidad as t_uu on (t_pp.id_unidad_permuta = t_uu.id_unidad) ";
               //ei_arbol($sql);
		return toba::db('mocovi_dev')->consultar($sql);
	}

}

?>