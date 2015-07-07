<?php
class ci_permutas extends mocovi_dev_abm_ci_presupuesto
{
    public $nombre_tabla='presupuesto_permutas';

    public function evt__formulario__alta($datos) {
        $datos['id_escalafon']=1;
        $datos['id_periodo']=5;/*2016*/
        parent::evt__formulario__alta($datos);
}
public function evt__formulario__modificacion($datos) {
    $datos['id_escalafon']=1;
    $datos['id_periodo']=5;/*2016*/
    parent::evt__formulario__modificacion($datos);
}

}
