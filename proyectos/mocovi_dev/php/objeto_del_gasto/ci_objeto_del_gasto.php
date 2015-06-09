<?php
class ci_objeto_del_gasto 
 extends mocovi_dev_abm_ci
{
    public $nombre_tabla='objeto_del_gasto';

        public function evt__formulario__alta($datos) {
        $datos['id_periodo']=php_mocovi::instancia()->periodo_a_presupuestar()  ;/*2016*/
        parent::evt__formulario__alta($datos);
}
public function evt__formulario__modificacion($datos) {

    $datos['id_periodo']=php_mocovi::instancia()->periodo_a_presupuestar();/*2016*/
    parent::evt__formulario__modificacion($datos);
}
    
}
