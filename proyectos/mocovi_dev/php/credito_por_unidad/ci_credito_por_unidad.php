<?php

class ci_credito_por_unidad extends toba_ci {

    //---- Cuadro -----------------------------------------------------------------------
    protected $s__where;
    protected $s__datos_filtro;

    function conf__cuadro(toba_ei_cuadro $cuadro) {

        $datos = $this->dep('datos')->tabla('credito')->get_listado($this->s__where);
        // ei_arbol($datos);
        $cuadro->set_datos($datos);
        //$cuadro->set_datos($this->dep('datos')->tabla('credito')->get_listado());
    }

    function evt__cuadro__seleccion($datos) {
        $this->dep('datos')->cargar($datos);
    }

    //---- Formulario -------------------------------------------------------------------

    function conf__formulario(toba_ei_formulario $form) {
        if ($this->dep('datos')->esta_cargada()) {
            $form->set_datos($this->dep('datos')->tabla('credito')->get());
        }
    }

    function evt__formulario__alta($datos) {
        $this->dep('datos')->tabla('credito')->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();
    }

    function evt__formulario__modificacion($datos) {
        $this->dep('datos')->tabla('credito')->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();
    }

    function evt__formulario__baja() {
        $this->dep('datos')->eliminar_todo();
        $this->resetear();
    }

    function evt__formulario__cancelar() {
        $this->resetear();
    }

    function resetear() {
        $this->dep('datos')->resetear();
    }

    function conf__filtro(mocovi_dev_ei_filtro $filtro) {
        if (isset($this->s__datos_filtro))
            $filtro->set_datos($this->s__datos_filtro);
    }

    /**
     * Atrapa la interacción del usuario con el botón asociado
     * @param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuración
     */
    function evt__filtro__filtrar($datos) {
        $this->s__where = $this->dep('filtro')->get_sql_where();
        $this->s__datos_filtro = $datos;
    }

}

?>