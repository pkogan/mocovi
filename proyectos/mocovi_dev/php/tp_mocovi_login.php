<?php

class tp_mocovi_login extends toba_tp_logon {

    function pre_contenido()
	{
		echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo.gif",true);
                
                echo "<div>";
                php_mocovi::instancia()->mensaje();
                echo "</div>";

		echo "<div>versión ".toba::proyecto()->get_version();
                echo " <a href='manual.pdf'>Descargar Manual Ayuda</a>" ."</div>";
		echo "</div>";
		echo "<div align='center' class='cuerpo'>";		
	}
    
    function post_contenido()
	{

                php_mocovi::instancia()->mostrar();
	}
//    protected function info_usuario() {
//        echo '<div class="enc-usuario">';
//        echo "<span class='enc-usuario-nom'>" . texto_plano(toba::usuario()->get_nombre()) . "</span>";
//        echo "<span class='enc-usuario-id'>" . texto_plano(toba::usuario()->get_id()) . "</span>";
//        echo '</div>';
//    }

//      function pie()
//      {
//          
//
//      parent::pie();
//      }

}

?>