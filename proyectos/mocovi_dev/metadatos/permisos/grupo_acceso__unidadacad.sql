
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	'Unidad Acad�mica', --nombre
	NULL, --nivel_acceso
	'Unidad Acad�mica', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'0', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 819
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000044'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000046'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000047'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000048'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000051'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000056'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000057'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'mocovi_dev', --proyecto
	'unidadacad', --usuario_grupo_acc
	NULL, --item_id
	'819000058'  --item
);
--- FIN Grupo de desarrollo 819
