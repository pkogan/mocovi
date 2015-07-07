<?php
require_once 'consultas_mapuche.php';
class dt_presupuesto_cargos extends toba_datos_tabla {

    function get_listado($where = null) {
        $sql = "SELECT
			t_pc.id_presupuesto_cargos,
                        t_pc.descripcion,
			t_p.anio as id_periodo_nombre,
			t_u.nombre as id_unidad_nombre,
			t_c.codigo_siu as id_categoria_nombre,
			t_pc.cantidad,
			t_e.escalafon as id_escalafon_nombre
		FROM
			presupuesto_cargos as t_pc	LEFT OUTER JOIN periodo as t_p ON (t_pc.id_periodo = t_p.id_periodo)
			INNER JOIN unidad as t_u ON (t_pc.id_unidad = t_u.id_unidad)
			LEFT OUTER JOIN categoria as t_c ON (t_pc.id_categoria = t_c.id_categoria)
			LEFT OUTER JOIN escalafon as t_e ON (t_pc.id_escalafon = t_e.id_escalafon)";
        $sql = toba::perfil_de_datos()->filtrar($sql);
        return toba::db('mocovi_dev')->consultar($sql);
    }

    function get_listado_agrupado_categoria($where = null) {
        $consultas_mapuche=new consultas_mapuche();
        $dias_categoria = $consultas_mapuche->get_dias_categoria($where);
        
        $id_periodo_actual=php_mocovi::instancia()->periodo_a_presupuestar();
        if (is_null($where)) {
            $where = '';
        } else {
            $where = ' where '.$where;
        }
        $sql = "

 Select aux.*
 from (
select pc.id_periodo,pc.id_unidad,pc.id_escalafon,pc.id_categoria,p.anio as periodo,c.codigo_siu as codc_categ,e.codigo as tipo_escal, u.codigo as codc_uacad, 
 cantidad,cede, recibe, -cede+recibe as permuta,  (-cede+recibe)*cc.costo*1.4*13 as costo_permuta, (cantidad)*cc.costo*1.4*13 as costo
   from
(
select id_periodo,id_unidad,id_escalafon,id_categoria,sum(cantidad) as cantidad, sum(cede) as cede, sum(recibe) as recibe
from (

select id_presupuesto_cargos,id_periodo,id_unidad,id_escalafon, id_categoria,cantidad, 0 as cede, 0 as recibe
from presupuesto_cargos
where id_periodo=$id_periodo_actual

Union

select id_presupuesto_permutas,id_periodo,id_unidad,id_escalafon,id_categoria,0 as cantidad,cantidad as cede,0 as recibe
from presupuesto_permutas
where id_tipo_permuta=1 --Cede
and id_periodo=$id_periodo_actual

Union

select id_presupuesto_permutas,id_periodo,id_unidad,id_escalafon,id_categoria,0 as cantidad,0 as cede,cantidad as recibe
from presupuesto_permutas
where id_tipo_permuta=2 --Recibe
and id_periodo=$id_periodo_actual

) a


group by id_periodo,id_escalafon,id_unidad,id_categoria
--order by id_periodo,id_escalafon,id_unidad,id_categoria
) pc
inner join periodo p on pc.id_periodo=p.id_periodo
inner join categoria c on pc.id_categoria=c.id_categoria
inner join unidad u on u.id_unidad=pc.id_unidad
inner join costo_categoria cc on pc.id_periodo=cc.id_periodo+1 and pc.id_categoria=cc.id_categoria
inner join escalafon e on pc.id_escalafon=e.id_escalafon
)aux
                   $where
                order by codc_uacad,tipo_escal,codc_categ";
        //$sql = toba::perfil_de_datos()->filtrar($sql);
        $presupuestado= toba::db('mocovi_dev')->consultar($sql);
        $salida=array();
        foreach ($dias_categoria as $fila){
            if(!isset($salida[$fila['codc_uacad'].'_'.$fila['codc_categ']])){
            $nuevafila=        
                    array(
                'periodo'=>'2016',//todo ver buscar periodo
                'codc_uacad'=>$fila['codc_uacad'],
                'tipo_escal'=>$fila['tipo_escal'],
            'codc_categ'=>$fila['codc_categ'],
            'cantidad2015'=>$fila['dias_total']/360,//round($fila['dias_total']/360,2),
            'ejecutado2015' => $fila['ejecutado'],
            'cantidad'=>0,
                'cede'=>0,
            'recibe'=>0,
            'permuta'=>0,
            'costo_permuta'=>0,
            'costo'=>0,
            'diferencia'=>$fila['ejecutado']
                );
            $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]=$nuevafila;
            }else{
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['cantidad2015']+=$fila['dias_total']/360;
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['ejecutado2015']+=$fila['ejecutado'];
            }
        }
        foreach ($presupuestado as $fila) {
            if(isset($salida[$fila['codc_uacad'].'_'.$fila['codc_categ']])){
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['cantidad']=$fila['cantidad'];
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['cede']=$fila['cede'];
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['recibe']=$fila['recibe'];
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['costo']=$fila['costo'];
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['permuta']=$fila['permuta'];
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['costo_permuta']=$fila['costo_permuta'];
           
                $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]['diferencia']-=$fila['costo'];
            }else{
                 $salida[$fila['codc_uacad'].'_'.$fila['codc_categ']]=array(
                'periodo'=>$fila['periodo'],//todo ver buscar periodo
                'codc_uacad'=>$fila['codc_uacad'],
                'tipo_escal'=>$fila['tipo_escal'],
            'codc_categ'=>$fila['codc_categ'],
            'cantidad2015'=>0,
            'ejecutado2015' => 0,
            'cantidad'=>$fila['cantidad'],
                'cede'=>$fila['cede'],
            'recibe'=>$fila['recibe'],
            'costo'=>$fila['costo'],
            'permuta'=>$fila['permuta'],
            'costo_permuta'=>$fila['costo_permuta'],
            'diferencia'=>-$fila['costo']
                );
            }
        }
        
        return $salida;
    }

}
