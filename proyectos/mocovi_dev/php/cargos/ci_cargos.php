<?php
class ci_cargos extends mocovi_dev_abm_ci_presupuesto
{
    public $nombre_tabla='presupuesto_cargos';

        public function evt__formulario__alta($datos) {
        $datos['id_periodo']=php_mocovi::instancia()->periodo_a_presupuestar();/*2016*/
        parent::evt__formulario__alta($datos);
}
public function evt__formulario__modificacion($datos) {

    $datos['id_periodo']=php_mocovi::instancia()->periodo_a_presupuestar();/*2016*/
    parent::evt__formulario__modificacion($datos);
}
    
}
