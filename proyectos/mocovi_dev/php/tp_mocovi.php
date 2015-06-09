<?php

class tp_mocovi extends toba_tp_normal {

    protected $titulo;

    function titulo_item() {
        if (!isset($this->titulo)) {
            $info['basica'] = toba::solicitud()->get_datos_item();
            $item = new toba_item_info($info);
            $item->cargar_rama();
//busca la dependencia sobre la que se tiene permisos
            $tabla = toba::tabla('unidad');
           
            $depe = '';
            foreach ($tabla->get_descripciones() as $dependencia) {
                $depe.=$dependencia['nombre'] . ' >';
            }


            //Se recorre la rama
            $camino =  $item->get_nombre();
            while ($item->get_padre() != null) {
                $item = $item->get_padre();
                if (!$item->es_raiz()) {
                    $camino = '<span style="font-weight:normal;">' . $item->get_nombre() . ' > </span>' . $camino;
                }
            }
            if(strlen($depe)>200){
                $depe='Varias Dependencias >';
            }
            $this->titulo = '<span style="font-weight:bold;">' . $depe . ' </span>'.$camino;
        }
        return $this->titulo;
    }

//    protected function info_usuario() {
//        echo '<div class="enc-usuario">';
//        echo "<span class='enc-usuario-nom'>" . texto_plano(toba::usuario()->get_nombre()) . "</span>";
//        echo "<span class='enc-usuario-id'>" . texto_plano(toba::usuario()->get_id()) . "</span>";
//        echo '</div>';
//    }

 function post_contenido()
	{

                php_mocovi::instancia()->mostrar();
	}
    
}

