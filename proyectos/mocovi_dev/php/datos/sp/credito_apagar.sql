DROP TABLE comahue.creditodocente_x_ua;
CREATE  TABLE comahue.creditodocente_x_ua
(
  ua	      	character(4),
  credito	double precision 
  );
  
  
insert into comahue.creditodocente_x_ua (ua,credito) values('ASMA',3460.119 );
insert into comahue.creditodocente_x_ua (ua,credito) values('AUZA',1619.471 );
insert into comahue.creditodocente_x_ua (ua,credito) values('CRUB',16404.134);
insert into comahue.creditodocente_x_ua (ua,credito) values('CUZA',14015.363);
insert into comahue.creditodocente_x_ua (ua,credito) values('ESCM',2390.928 );
insert into comahue.creditodocente_x_ua (ua,credito) values('FAAS',7942.343 );
insert into comahue.creditodocente_x_ua (ua,credito) values('FACA',10719.104);
insert into comahue.creditodocente_x_ua (ua,credito) values('FACE',19862.016);
insert into comahue.creditodocente_x_ua (ua,credito) values('FADE',21523.369);
insert into comahue.creditodocente_x_ua (ua,credito) values('FAEA',17567.323);
insert into comahue.creditodocente_x_ua (ua,credito) values('FAHU',14645.795);
insert into comahue.creditodocente_x_ua (ua,credito) values('FAIF',6164.132 );
insert into comahue.creditodocente_x_ua (ua,credito) values('FAIN',26167.255);
insert into comahue.creditodocente_x_ua (ua,credito) values('FALE',8666.157 );
insert into comahue.creditodocente_x_ua (ua,credito) values('FAME',9778.114 );
insert into comahue.creditodocente_x_ua (ua,credito) values('FATA',3982.544 );
insert into comahue.creditodocente_x_ua (ua,credito) values('FATU',6708.533 );


DROP TABLE comahue.macheo_categ_doc;
CREATE TABLE comahue.macheo_categ_doc
(
  cat_siu  	      	character(4),
  cat_presu		character(5), 
  codigo		int
  );

insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('TITE','PTR_1',800);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('ASOE','PAS_1',801);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('ADJE','PAD_1',802);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('JTPE','JTP_1',803);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('AY1E','AYP_1',804);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('TITS','PTR_2',805);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('ASOS','PAS_2',806);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('ADJ2','PAD_2',807);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('JTPS','JTP_2',808);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('AY1S','AYP_2',809);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('TIT1','PTR_3',810);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('ASO1','PAS_3',811);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('ADJ1','PAD_3',812);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('JTP1','JTP_3',813);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('AY11','AYP_3',814);
insert into comahue.macheo_categ_doc(cat_siu,cat_presu,codigo) values ('AY21','AYS_3',815);



cada categoria tiene un monto basico

codigo_cat
basico
zona (40% del basico)
costo_anual (basico+ zona)*13
costo_mensual costo_anual/12
costo_diario  costo_anual/365



drop function comahue.credito_presupuesto();
CREATE OR REPLACE FUNCTION comahue.credito_apagar(date,date)
  RETURNS integer AS
$BODY$
DECLARE

reg		record;
pdiano		date;
udiano		date;
dias		integer;

BEGIN
--se considera el periodo anual 2014 desde 01-02-2014 al 31-01-2015
--pdiano:='2014-10-01';
--udiano:='2015-01-31';
pdiano:=$1;
udiano:=$2;

CREATE LOCAL TEMP TABLE apagar
(

  legajo 	integer,
  cargo		integer,
  codc_carac	character(4),
  codc_categ 	character(4),
  codc_uacad	character(4),
  tipo_escal 	character(1),
  fec_alta	date,
  fec_baja	date,
  dias_anual	integer, 
  dias_lic	integer,  --total de dias de licencia sin goce que la persona tomo durante el periodo considerado
  total_dias	integer
  
  );

--calculo la cantidad de dias trabajados por cada legajo

insert into apagar 
select nro_legaj,nro_cargo,codc_carac,a.codc_categ,codc_uacad ,b.tipo_escal,CASE WHEN fec_alta<pdiano THEN pdiano
            
            ELSE fec_alta
       END as alta,CASE WHEN fec_baja>udiano or fec_baja is null THEN udiano
            ELSE fec_baja 
       END as baja,0,0,0
     
from mapuche.dh03 a, mapuche.dh11 b
where fec_alta <= udiano and (fec_baja >= pdiano or fec_baja is null)
and a.chkstopliq=0
and a.codc_categ=b.codc_categ
;


FOR reg IN
   	select * from apagar
	
   LOOP
   
   dias:=0;
   
   
   ----por cada cargo calculo la cantidad total de dias de licencia sin goce dentro del periodo considerado
   select into dias CASE WHEN sum(hasta-desde) is null THEN 0 ELSE sum(hasta-desde)+1 END as dias_lic ,nro_cargo from 
   (select a.nro_cargo, CASE WHEN c.fec_desde<pdiano THEN pdiano ELSE c.fec_desde END as desde,
   CASE WHEN c.fec_hasta>udiano THEN udiano ELSE c.fec_hasta END as hasta
                                                                               
   from mapuche.dh03 a, mapuche.dh11 b,mapuche.dh05 c,mapuche.dl02 d, mapuche.dl01 e
   where 
   --a.fec_alta <= udiano and (a.fec_baja >= pdiano or a.fec_baja is null)   --cargo activo  dentro del periodo 2014
   a.nro_cargo=reg.cargo
   and a.chkstopliq=0
   and   (a.nro_legaj=c.nro_legaj or a.nro_legaj=c.nro_legaj )--tiene una licencia del cargo o del legajo
   and c.nrovarlicencia = d.nrovarlicencia 
   and (d.es_remunerada=false )--sin goce  dias*porcentaje
   and a.codc_categ=b.codc_categ
   and d.nrodefiniclicencia=e.nrodefiniclicencia
   and c.fec_desde<=udiano and c.fec_hasta>=pdiano
   --((c.fec_desde>=pdiano and c.fec_hasta<=udiano) or (c.fec_hasta>=pdiano and c.fec_hasta<=udiano) or (c.fec_desde>=pdiano and c.fec_desde<=udiano)) 
   ) auxi
   group by nro_cargo;

   
   --actualizo dias_lic
   
   if dias is not null then
   	update apagar a
   	set dias_lic=dias
   	where reg.cargo=a.cargo;
   end if;	
     


END LOOP ;

update apagar 
set dias_anual=((fec_baja-fec_alta)+1), total_dias=(fec_baja-fec_alta)+1-dias_lic;



return 1;
END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE

  select comahue.credito_apagar('2014-10-01','2015-01-31')
  
    
  
  --suma la cantidad de dias trabajados por cada categoria en cada UA
  select codc_uacad,tipo_escal,codc_categ,sum(total_dias) 
  from apagar
  group by codc_uacad,tipo_escal,codc_categ
  
  
  