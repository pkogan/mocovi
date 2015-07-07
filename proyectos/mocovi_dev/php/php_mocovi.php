<?php
include_once '../php/datos/dt_periodo.php';

class php_mocovi
{
	private static $instancia;
	protected $archivos = array();
	protected $expandido = false;
	
	/**
	 * @return php_mocovi
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new php_mocovi();	
		}
		return self::$instancia;
	}
	
	function agregar($archivo)
	{
		$this->archivos[] = $archivo;	
	}
	
	function set_expandido($expandido)
	{
		$this->expandido = $expandido;
	}
        
        function periodo_a_presupuestar(){
            $periodo=dt_periodo::get_periodo_a_presupuestar();
            //ei_arbol($periodo);
            if(!isset($periodo[0])){
                throw new toba_error_autorizacion('Error no hay un periodo a presupuestar');
                
            }
            return $periodo[0]['id_periodo']; /*2016*/
        }
	
        function periodo_a_presupuestar_activo(){
            $periodo=dt_periodo::get_periodo_a_presupuestar();
            //ei_arbol($periodo);
            if(!isset($periodo[0])){
                throw new toba_error_autorizacion('Error no hay un periodo a presupuestar');
            }
            return $periodo[0]['activo_para_carga_presupuestando']; /*2016*/
        }
        
        
        function mensaje(){
            echo 'Aplicación web para planificación y ejecución del presupuesto Universitario';
        }
	function mostrar()
	{
		echo "</div>";		
		echo "<div class='login-pie'>";
              
    		echo "<div>Desarrollado por <strong> <a href='http://sti.uncoma.edu.ar'>" . toba_recurso::imagen_proyecto("isosubti.png",true,null,'20px')."</a>Secretaría de Hacienda <a href='http://www.uncoma.edu.ar' style='text-decoration: none' target='_blank'> - Uncoma</a></strong></div>
			<div>2014 - ".date('Y')."</div>";
		echo "</div>";
//		if (! empty($this->archivos)) {
//			echo '<div id="php-referencia-cont">
//			Extensiones utilizadas:
//			<ul>';
//			foreach ($this->archivos as $i => $archivo) {
//				echo '<li><a href="#archivo_'.$i.'" title="Ver extensión" onclick="toggle_nodo($$(\'archivo_'.$i.'\'));">'.
//						basename($archivo)."</a></li>";
//			}
//			echo "</ul></div>";
//			echo "<div id='archivos'>";
//			$oculto = ($this->expandido) ? "" : "style='display:none'";
//			foreach ($this->archivos as $i => $archivo) {
//				echo "<div id='archivo_$i' class='php-referencia' $oculto>";
//				echo "<strong>$archivo</strong>:<br /><br />";
//				highlight_file($archivo);
//				echo "</div>";
//			}
//			echo "</div>";
//		}
	}
}

