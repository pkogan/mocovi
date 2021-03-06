<?php
class ci_informe_gastos_presupuestados extends  mocovi_dev_abm_ci
{
    protected $s__where;
    protected $s__datos_filtro;
    public $nombre_tabla = 'presupuesto_general';

    //-----------------------------------------------------------------------------------
    //---- cuadro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    /**
     * Permite cambiar la configuración del cuadro previo a la generación de la salida
     * El formato de carga es de tipo recordset: array( array('columna' => valor, ...), ...)
     */
    function conf__cuadro(mocovi_dev_ei_cuadro $cuadro) {
        $datos = $this->dep('datos')->get_listado($this->s__where);
        $cuadro->set_datos($datos);
    }

    //-----------------------------------------------------------------------------------
    //---- filtro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    /**
     * Permite cambiar la configuración del formulario previo a la generación de la salida
     * El formato del carga debe ser array(<campo> => <valor>, ...)
     */
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

    /**
     * Atrapa la interacción del usuario con el botón asociado
     */
    function evt__filtro__cancelar() {
        
    }

}
