<?php

class ci_presupuesto extends mocovi_dev_abm_ci {

    public $nombre_tabla = 'presupuesto';

    public function evt__formulario__alta($datos) {
        $datos['id_periodo'] = php_mocovi::instancia()->periodo_a_presupuestar(); /* 2016 */
        parent::evt__formulario__alta($datos);
    }

    public function evt__formulario__modificacion($datos) {

        $datos['id_periodo'] = php_mocovi::instancia()->periodo_a_presupuestar(); /* 2016 */
        parent::evt__formulario__modificacion($datos);
    }

    function conf__formulario(toba_ei_formulario $form) {
        if ($this->dep('datos')->esta_cargada()) {
            $datos = $this->dep('datos')->tabla($this->nombre_tabla)->get();
            if ($datos['id_objeto_del_gasto'] == 4 || $datos['id_objeto_del_gasto'] == 5 || $datos['id_objeto_del_gasto'] == 6) {
                toba::notificacion()->agregar('El monto solo se puede modificar agregando o quitando cargos', 'info');
            }

            $form->set_datos($datos);
        }
    }

    function evt__cuadro__seleccion($datos) {
        $this->dep('datos')->cargar($datos);
        $datos = $this->dep('datos')->tabla($this->nombre_tabla)->get();
        if ($datos['id_objeto_del_gasto'] == 4 || $datos['id_objeto_del_gasto'] == 5 || $datos['id_objeto_del_gasto'] == 6) {
            toba::notificacion()->agregar('El monto solo se puede modificar agregando o quitando cargos', 'info');
            //toba::vinculador()->navegar_a('mocovi_dev',819000047); //cargos
                    
           
            $this->resetear();
        } else {
            $this->set_pantalla('pant_edicion');
            
        }
    }

}
