
select
CASE WHEN a_trabajar.nro_legaj is Null THEN ya_trabajados.nro_legaj ELSE a_trabajar.nro_legaj END AS nro_legaj,
CASE WHEN a_trabajar.nro_cargo is Null THEN ya_trabajados.nro_cargo ELSE a_trabajar.nro_cargo END AS nro_cargo,
CASE WHEN a_trabajar.codc_uacad is Null THEN ya_trabajados.codc_uacad ELSE a_trabajar.codc_uacad END AS codc_uacad,
CASE WHEN a_trabajar.tipo_escal is Null THEN ya_trabajados.tipo_escal ELSE a_trabajar.tipo_escal END AS tipo_escal,
CASE WHEN a_trabajar.codc_categ is Null THEN ya_trabajados.codc_categ ELSE a_trabajar.codc_categ END AS codc_categ,
CASE WHEN ya_trabajados.dias_trabajados is Null THEN 0 ELSE ya_trabajados.dias_trabajados END AS dias_trabajados,
CASE WHEN ya_trabajados.dias_retro is Null THEN 0 ELSE ya_trabajados.dias_retro END AS dias_retroactivos,
CASE WHEN ya_trabajados.dias_trabajados_total is Null THEN 0 ELSE ya_trabajados.dias_trabajados_total END AS dias_trabajados_total,
CASE WHEN a_trabajar.dias_a_trabajar is Null THEN 0 ELSE a_trabajar.dias_a_trabajar END AS dias_a_trabajar,
CASE WHEN a_trabajar.dias_licencia is Null THEN 0 ELSE a_trabajar.dias_licencia END AS dias_licencia,
CASE WHEN a_trabajar.dias_a_trabajar_total is Null THEN 0 ELSE a_trabajar.dias_a_trabajar_total END AS dias_a_trabajar_total




from (
-- todo junto agrupado
select t.nro_legaj, t.nro_cargo, t.codc_uacad,  t.tipo_escal, t.codc_categ, sum(dias_anual) as dias_a_trabajar, sum(dias_lic) dias_licencia, sum(dias_a_trabajar) dias_a_trabajar_total
from
--dias trabajados
(
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

-- dias licencia
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
group by t.nro_legaj,t.nro_cargo,t.codc_uacad,  t.tipo_escal, t.codc_categ
) a_trabajar full outer join

(select t.nro_legaj,t.nro_cargo,c.codc_categ,c.codc_uacad ,b.tipo_escal, sum(t.dias_trabajados) as dias_trabajados, sum(t.dias_retro) as dias_retro, sum(t.dias_trabajados)+ sum(t.dias_retro) as dias_trabajados_total
from(
select
CASE WHEN trabajados.nro_legaj is Null THEN retro.nro_legaj ELSE trabajados.nro_legaj END AS nro_legaj,
CASE WHEN trabajados.nro_cargo is Null THEN retro.nro_cargo ELSE trabajados.nro_cargo END AS nro_cargo,
CASE WHEN trabajados.dias_trab is Null THEN 0 ELSE trabajados.dias_trab END AS dias_trabajados,
CASE WHEN retro.dias_retro is Null THEN 0 ELSE retro.dias_retro END AS dias_retro

from
(select a.nro_legaj,a.nro_cargo, sum(a.nov1_conce) as dias_trab
from mapuche.dh21h a, mapuche.dh22 b
where a.nro_liqui=b.nro_liqui
--and ((b.per_liano=2014 and b.per_limes>=2) or (b.per_liano=2015 and b.per_limes=1))
and a.nro_liqui>=440
and a.nro_liqui<=449
and a.nro_liqui<>445 --sin contar dias de aguinaldo
and codn_conce=-51
and ano_retro = 0
and mes_retro = 0
group by a.nro_legaj,a.nro_cargo
)trabajados full outer join
(select a.nro_legaj,a.nro_cargo, sum(a.nov1_conce) as dias_retro
from mapuche.dh21h a
, mapuche.dh22 b
where 
a.nro_liqui=b.nro_liqui
--and ((b.per_liano=2014 and b.per_limes>=2) or (b.per_liano=2014 and b.per_limes=10))
and a.nro_liqui>=440
and a.nro_liqui<=449
and b.nro_liqui<>445 --sin contar dias de aguinaldo
and codn_conce=-51
and ano_retro <> 0
and mes_retro <>0
group by a.nro_legaj,a.nro_cargo
)retro
on trabajados.nro_cargo=retro.nro_cargo) t inner join dh03 c
on t.nro_cargo=c.nro_cargo
inner join dh11 b on  c.codc_categ=b.codc_categ
group by t.nro_legaj,t.nro_cargo,c.codc_categ,c.codc_uacad ,b.tipo_escal) ya_trabajados

on a_trabajar.nro_cargo=ya_trabajados.nro_cargo