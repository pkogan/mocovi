<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class mocovi_dev_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
            'mocovi_dev_abm_ci' => 'extension_toba/componentes/mocovi_dev_abm_ci.php',
            'mocovi_dev_abm_ci_presupuesto' => 'extension_toba/componentes/mocovi_dev_abm_ci_presupuesto.php',
            'php_mocovi' => 'php_mocovi.php',
		'mocovi_dev_ci' => 'extension_toba/componentes/mocovi_dev_ci.php',
		'mocovi_dev_cn' => 'extension_toba/componentes/mocovi_dev_cn.php',
		'mocovi_dev_datos_relacion' => 'extension_toba/componentes/mocovi_dev_datos_relacion.php',
		'mocovi_dev_datos_tabla' => 'extension_toba/componentes/mocovi_dev_datos_tabla.php',
		'mocovi_dev_ei_arbol' => 'extension_toba/componentes/mocovi_dev_ei_arbol.php',
		'mocovi_dev_ei_archivos' => 'extension_toba/componentes/mocovi_dev_ei_archivos.php',
		'mocovi_dev_ei_calendario' => 'extension_toba/componentes/mocovi_dev_ei_calendario.php',
		'mocovi_dev_ei_codigo' => 'extension_toba/componentes/mocovi_dev_ei_codigo.php',
		'mocovi_dev_ei_cuadro' => 'extension_toba/componentes/mocovi_dev_ei_cuadro.php',
		'mocovi_dev_ei_esquema' => 'extension_toba/componentes/mocovi_dev_ei_esquema.php',
		'mocovi_dev_ei_filtro' => 'extension_toba/componentes/mocovi_dev_ei_filtro.php',
		'mocovi_dev_ei_firma' => 'extension_toba/componentes/mocovi_dev_ei_firma.php',
		'mocovi_dev_ei_formulario' => 'extension_toba/componentes/mocovi_dev_ei_formulario.php',
		'mocovi_dev_ei_formulario_ml' => 'extension_toba/componentes/mocovi_dev_ei_formulario_ml.php',
		'mocovi_dev_ei_grafico' => 'extension_toba/componentes/mocovi_dev_ei_grafico.php',
		'mocovi_dev_ei_mapa' => 'extension_toba/componentes/mocovi_dev_ei_mapa.php',
		'mocovi_dev_servicio_web' => 'extension_toba/componentes/mocovi_dev_servicio_web.php',
		'mocovi_dev_comando' => 'extension_toba/mocovi_dev_comando.php',
		'mocovi_dev_modelo' => 'extension_toba/mocovi_dev_modelo.php',
	);
}
?>