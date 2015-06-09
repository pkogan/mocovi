
--fecha de alta y baja a pagar

select nro_legaj,nro_cargo,codc_carac,a.codc_categ,codc_uacad ,b.tipo_escal,CASE WHEN fec_alta<'2014-10-01' THEN '2014-10-01'
            
            ELSE fec_alta
       END as alta,CASE WHEN fec_baja>'2015-01-31' or fec_baja is null THEN '2015-01-31'
            ELSE fec_baja 
       END as baja,0,0,0
     
from mapuche.dh03 a, mapuche.dh11 b
where fec_alta <= '2015-01-31' and (fec_baja >= '2014-10-01' or fec_baja is null)
and a.chkstopliq=0
and a.codc_categ=b.codc_categ
;


--dias de licencia en el perido
--TODO: licencias al 25 o al 50

select  CASE WHEN sum(hasta-desde) is null THEN 0 ELSE sum(hasta-desde)+1 END as dias_lic ,nro_cargo from 
   (select a.nro_cargo, CASE WHEN c.fec_desde<'2014-10-01' THEN '2014-10-01' ELSE c.fec_desde END as desde,
   CASE WHEN c.fec_hasta>'2015-01-31' THEN '2015-01-31' ELSE c.fec_hasta END as hasta
                                                                               
   from mapuche.dh03 a, mapuche.dh11 b,mapuche.dh05 c,mapuche.dl02 d, mapuche.dl01 e
   where 
   --a.fec_alta <= '2015-01-31' and (a.fec_baja >= '2014-10-01' or a.fec_baja is null)   --cargo activo  dentro del periodo 2014
   --a.nro_cargo=reg.cargo
   and a.chkstopliq=0
   and   (a.nro_legaj=c.nro_legaj or a.nro_legaj=c.nro_legaj )--tiene una licencia del cargo o del legajo
   and c.nrovarlicencia = d.nrovarlicencia 
   and (d.es_remunerada=false )--sin goce  dias*porcentaje
   and a.codc_categ=b.codc_categ
   and d.nrodefiniclicencia=e.nrodefiniclicencia
   and c.fec_desde<='2015-01-31' and c.fec_hasta>='2014-10-01'
   --((c.fec_desde>='2014-10-01' and c.fec_hasta<='2015-01-31') or (c.fec_hasta>='2014-10-01' and c.fec_hasta<='2015-01-31') or (c.fec_desde>='2014-10-01' and c.fec_desde<='2015-01-31')) 
   ) auxi
   group by nro_cargo;
   
  -- todo  junto

  select dias.nro_legaj, dias.nro_cargo, dias.codc_carac, dias.codc_categ, dias.codc_uacad, dias.tipo_escal,
dias.alta, dias.baja,
  baja-alta+1 as dias_anual,
       CASE WHEN licencia.dias_lic IS NULL THEN 0
       ELSE licencia.dias_lic END as dias_lic,
       CASE WHEN licencia.dias_lic IS NULL THEN baja-alta+1
       ELSE baja-alta+1-licencia.dias_lic END as dias_a_trabajar 
from
(
select a.nro_legaj,a.nro_cargo,codc_carac,a.codc_categ,codc_uacad ,b.tipo_escal,
      CASE WHEN a.fec_alta<'2014-10-01' THEN '2014-10-01'
            ELSE a.fec_alta
       END as alta,
       CASE WHEN a.fec_baja>'2015-01-31' or a.fec_baja is null THEN '2015-01-31'
            ELSE a.fec_baja 
       END as baja
     
     
from mapuche.dh03 a, mapuche.dh11 b
where fec_alta <= '2015-01-31' and (fec_baja >= '2014-10-01' or fec_baja is null)
and a.chkstopliq=0
and a.codc_categ=b.codc_categ) dias left outer join


(select  CASE WHEN sum(hasta-desde) is null THEN 0 ELSE sum(hasta-desde)+1 END as dias_lic ,nro_cargo from 
   (select a.nro_cargo, CASE WHEN c.fec_desde<'2014-10-01' THEN '2014-10-01' ELSE c.fec_desde END as desde,
   CASE WHEN c.fec_hasta>'2015-01-31' THEN '2015-01-31' ELSE c.fec_hasta END as hasta
                                                                               
   from mapuche.dh03 a, mapuche.dh11 b,mapuche.dh05 c,mapuche.dl02 d, mapuche.dl01 e
   where 
   --a.fec_alta <= '2015-01-31' and (a.fec_baja >= '2014-10-01' or a.fec_baja is null)   --cargo activo  dentro del periodo 2014
   --a.nro_cargo=reg.cargo
   a.chkstopliq=0
   and   (a.nro_legaj=c.nro_legaj or a.nro_legaj=c.nro_legaj )--tiene una licencia del cargo o del legajo
   and c.nrovarlicencia = d.nrovarlicencia 
   and (d.es_remunerada=false )--sin goce  dias*porcentaje
   and a.codc_categ=b.codc_categ
   and d.nrodefiniclicencia=e.nrodefiniclicencia
   and c.fec_desde<='2015-01-31' and c.fec_hasta>='2014-10-01'
   --((c.fec_desde>='2014-10-01' and c.fec_hasta<='2015-01-31') or (c.fec_hasta>='2014-10-01' and c.fec_hasta<='2015-01-31') or (c.fec_desde>='2014-10-01' and c.fec_desde<='2015-01-31')) 
   ) auxi
   group by nro_cargo) licencia


on dias.nro_cargo=licencia.nro_cargo


-- todo junto agrupado
select t.codc_uacad,  t.tipo_escal, t.codc_categ, sum(dias_anual), sum(dias_lic), sum(dias_a_trabajar)
from(
 select dias.nro_legaj, dias.nro_cargo, dias.codc_carac, dias.codc_categ, dias.codc_uacad, dias.tipo_escal,
dias.alta, dias.baja,
  baja-alta+1 as dias_anual,
       CASE WHEN licencia.dias_lic IS NULL THEN 0
       ELSE licencia.dias_lic END as dias_lic,
       CASE WHEN licencia.dias_lic IS NULL THEN baja-alta+1
       ELSE baja-alta+1-licencia.dias_lic END as dias_a_trabajar 
from
(
select a.nro_legaj,a.nro_cargo,codc_carac,a.codc_categ,codc_uacad ,b.tipo_escal,
      CASE WHEN a.fec_alta<'2014-10-01' THEN '2014-10-01'
            ELSE a.fec_alta
       END as alta,
       CASE WHEN a.fec_baja>'2015-01-31' or a.fec_baja is null THEN '2015-01-31'
            ELSE a.fec_baja 
       END as baja
     
     
from mapuche.dh03 a, mapuche.dh11 b
where fec_alta <= '2015-01-31' and (fec_baja >= '2014-10-01' or fec_baja is null)
and a.chkstopliq=0
and a.codc_categ=b.codc_categ) dias left outer join


(select  CASE WHEN sum(hasta-desde) is null THEN 0 ELSE sum(hasta-desde)+1 END as dias_lic ,nro_cargo from 
   (select a.nro_cargo, CASE WHEN c.fec_desde<'2014-10-01' THEN '2014-10-01' ELSE c.fec_desde END as desde,
   CASE WHEN c.fec_hasta>'2015-01-31' THEN '2015-01-31' ELSE c.fec_hasta END as hasta
                                                                               
   from mapuche.dh03 a, mapuche.dh11 b,mapuche.dh05 c,mapuche.dl02 d, mapuche.dl01 e
   where 
   --a.fec_alta <= '2015-01-31' and (a.fec_baja >= '2014-10-01' or a.fec_baja is null)   --cargo activo  dentro del periodo 2014
   --a.nro_cargo=reg.cargo
   a.chkstopliq=0
   and   (a.nro_legaj=c.nro_legaj or a.nro_legaj=c.nro_legaj )--tiene una licencia del cargo o del legajo
   and c.nrovarlicencia = d.nrovarlicencia 
   and (d.es_remunerada=false )--sin goce  dias*porcentaje
   and a.codc_categ=b.codc_categ
   and d.nrodefiniclicencia=e.nrodefiniclicencia
   and c.fec_desde<='2015-01-31' and c.fec_hasta>='2014-10-01'
   --((c.fec_desde>='2014-10-01' and c.fec_hasta<='2015-01-31') or (c.fec_hasta>='2014-10-01' and c.fec_hasta<='2015-01-31') or (c.fec_desde>='2014-10-01' and c.fec_desde<='2015-01-31')) 
   ) auxi
   group by nro_cargo) licencia
on dias.nro_cargo=licencia.nro_cargo) t
group by t.codc_uacad,  t.tipo_escal, t.codc_categ

