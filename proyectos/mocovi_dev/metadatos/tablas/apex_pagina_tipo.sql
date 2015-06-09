
------------------------------------------------------------
-- apex_pagina_tipo
------------------------------------------------------------
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'mocovi_dev', --proyecto
	'login', --pagina_tipo
	'login', --descripcion
	'tp_mocovi_login', --clase_nombre
	'tp_mocovi_login.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'819000002'  --punto_montaje
);
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'mocovi_dev', --proyecto
	'mocovi', --pagina_tipo
	'mocovi', --descripcion
	'tp_mocovi', --clase_nombre
	'./tp_mocovi.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'819000002'  --punto_montaje
);
