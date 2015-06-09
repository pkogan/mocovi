<?php
class ci_credito_disponible extends mocovi_dev_ci
{
    protected $s__where;
    protected $s__datos_filtro;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Permite cambiar la configuración del cuadro previo a la generación de la salida
	 * El formato de carga es de tipo recordset: array( array('columna' => valor, ...), ...)
	 */
	function conf__cuadro(mocovi_dev_ei_cuadro $cuadro)
	{
            $datos = toba::consulta_php('consultas')->get_totales($this->s__where);
  
            $cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Permite cambiar la configuración del formulario previo a la generación de la salida
	 * El formato del carga debe ser array(<campo> => <valor>, ...)
	 */
	function conf__filtro(mocovi_dev_ei_filtro $filtro)
	{
	}

}
?>