﻿select mapuche.dh03.nro_legaj,mapuche.dh03.nro_cargo,dh03.codc_categ, dh03.codc_uacad, mapuche.dh01.desc_appat, mapuche.dh01.desc_nombr, mapuche.dh03.fec_alta, mapuche.dh03.fec_baja, mapuche.dh03.tipo_norma, mapuche.dh03.nro_norma, mapuche.dh03.nro_exped, mapuche.dh03.tipo_emite, mapuche.dh03.fec_norma, mapuche.dh03.*from mapuche.dh03 inner join mapuche.dh01 on mapuche.dh03.nro_legaj=mapuche.dh01.nro_legaj
where mapuche.dh03.nro_legaj in (select distinct nro_legaj from mapuche.dh03 where codc_uacad='FAIF')