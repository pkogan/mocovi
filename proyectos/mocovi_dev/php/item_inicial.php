<?php

echo '<div class="logoinicio">';

//echo toba_recurso::imagen_proyecto('logo_grande.gif', true);
echo toba_recurso::imagen_proyecto('logo.png', true);
echo '</div>';


$tabla = toba::tabla('unidad');


echo "<h2> Permisos sobre Dependencia </h2>";
foreach ($tabla->get_descripciones() as $dependencia) {
    echo '<h1>'.$dependencia['nombre'].'</h1>';
    
}


?>