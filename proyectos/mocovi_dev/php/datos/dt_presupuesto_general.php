<?php
class dt_presupuesto_general extends toba_datos_tabla
{
	function get_listado($where=null)
	{/*
         * Se podría Calcular, borrar todo e insertar cada vez que se lista
         */
/*
           * Para cálculo
           * 
           * Buscar todos los cargos de la dependencia mas los cargos que
 *          se Ceden en crédito y multiplicarlos por el básico 2015 y luego
 * por el índice promedio de cada escalafon a Marzo 2.6 Docente, 2.42 No Docente
 *  y 1.64 Autoridades

 * 
 * 
  Delete from presupuesto_general where id_periodo=5 -- actual 2016
  and id_objeto_del_gasto in (78,79,10);

 insert into presupuesto_general (id_periodo,id_unidad,inciso,id_objeto_del_gasto,monto)
 Select pc.id_periodo,pc.id_unidad,
 5832 as inciso,
  case pc.id_escalafon
  when 1 Then 78
  when 2 Then 79
  when 3 Then 10
  end as id_objeto_del_gasto 
 ,sum(cc.costo*pc.cantidad*e.indice*13)
   from
(select COALESCE(a.id_periodo,b.id_periodo) as id_periodo, COALESCE(a.id_unidad,b.id_unidad) as id_unidad, COALESCE(a.id_escalafon,b.id_escalafon) as id_escalafon, COALESCE(a.id_categoria,b.id_categoria) as id_categoria, COALESCE(sum (a.cantidad),0)+COALESCE(sum (b.cantidad),0) as cantidad
from presupuesto_cargos a full outer join presupuesto_permutas b on a.id_periodo=b.id_periodo and a.id_unidad=b.id_unidad and a.id_categoria=b.id_categoria and (b.id_tipo_permuta=1) --Cede
where a.id_periodo=5 or b.id_periodo=5 
-- (a.id_unidad=13 or b.id_unidad=13) -- Derecho


group by  b.id_periodo,b.id_unidad,b.id_categoria,a.id_periodo,a.id_escalafon,b.id_escalafon, a.id_unidad,a.id_categoria
) pc
inner join costo_categoria cc on pc.id_periodo=cc.id_periodo+1 and pc.id_categoria=cc.id_categoria
inner join escalafon e on pc.id_escalafon=e.id_escalafon

group by pc.id_periodo,pc.id_unidad,pc.id_escalafon
 *  */
            /*
             * Luego insertar y/o Actualizarlos.
             * 
             * 
             */
            $id_periodo_actual=php_mocovi::instancia()->periodo_a_presupuestar();
            $sql="   Delete from presupuesto_general where id_periodo=$id_periodo_actual -- actual 2016
  and id_objeto_del_gasto in (4,5,6);

 insert into presupuesto_general (id_periodo,id_unidad,inciso,id_objeto_del_gasto,monto)
 Select pc.id_periodo,pc.id_unidad,
 5832 as inciso,
  case pc.id_escalafon
  when 1 Then 4
  when 2 Then 5
  when 3 Then 6
  end as id_objeto_del_gasto 
 ,sum(cc.costo*pc.cantidad*e.indice*13)
   from
(
select id_periodo,id_unidad,id_escalafon,id_categoria,sum(cantidad) as cantidad
from (select id_periodo,id_unidad,id_escalafon, id_categoria,cantidad
from presupuesto_cargos
where id_periodo=$id_periodo_actual
Union
select id_periodo,id_unidad,id_escalafon,id_categoria,cantidad as cantidad
from presupuesto_permutas
where id_tipo_permuta=1 --Cede
and id_periodo=$id_periodo_actual
Union
select id_periodo,id_unidad,id_escalafon,id_categoria,cantidad * (-1) as cantidad
from presupuesto_permutas
where id_tipo_permuta=2 --Recibe
and id_periodo=$id_periodo_actual

) a


group by id_periodo,id_escalafon,id_unidad,id_categoria
--order by id_periodo,id_escalafon,id_unidad,id_categoria
) pc
inner join costo_categoria cc on pc.id_periodo=cc.id_periodo+1 and pc.id_categoria=cc.id_categoria
inner join escalafon e on pc.id_escalafon=e.id_escalafon

group by pc.id_periodo,pc.id_unidad,pc.id_escalafon";
            
                toba::db('mocovi_dev')->ejecutar($sql);
            if (is_null($where)) {
            $where = '';
        } else {
            $where = ' where '.$where;
        }
            
		$sql = "SELECT
			t_p.id_presupuesto,
                        t_p.descripcion,
			t_p1.anio as id_periodo_nombre,
                        
			t_u.codigo as id_unidad_nombre,
                        t_odg2.nombre as inciso_nombre,
                        t_odg2.codigo as inciso_codigo,
                        t_odg.codigo_completo,
			t_odg.nombre as id_objeto_del_gasto_nombre,
			t_p.monto
		FROM
			presupuesto_general as t_p	inner JOIN periodo as t_p1 ON (t_p.id_periodo = t_p1.id_periodo)
			inner JOIN unidad as t_u ON (t_p.id_unidad = t_u.id_unidad)
			LEFT OUTER JOIN objeto_del_gasto as t_odg ON (t_p.id_objeto_del_gasto = t_odg.id_objeto_del_gasto)
                        Left outer join objeto_del_gasto as t_odg2 on t_odg.elemento_padre=t_odg2.elemento
                        $where
                        order by t_odg.codigo_completo";
                $sql = toba::perfil_de_datos()->filtrar($sql);
                
                        $id_periodo_actual=php_mocovi::instancia()->periodo_a_presupuestar();
        
        
		return toba::db('mocovi_dev')->consultar($sql);
	}

}

