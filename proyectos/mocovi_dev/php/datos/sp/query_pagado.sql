
-- 
--inserto lo pagado no retro
--insert into pagado (legajo,cargo,codc_uacad,codc_categ,tipo_escal,mes,ano,dias_pag,dias_retro)
--Todo analizar la quita de liquidaciones Aguinaldos
-- buscar primer liquidación del periodo.

select * from dh22 where per_limes=2 and per_liano=2014

-- buscar último periodo 
select * from dh22 where per_limes=10 and per_liano=2014

-- dias pagados
select a.nro_legaj,a.nro_cargo,a.codc_uacad,a.nro_liqui,b.per_liano,b.per_limes,a.nov1_conce as dias_trab
from mapuche.dh21h a, mapuche.dh22 b
where a.nro_liqui=b.nro_liqui
--and ((b.per_liano=2014 and b.per_limes>=2) or (b.per_liano=2015 and b.per_limes=1))
and a.nro_liqui>=440
and a.nro_liqui<=449
and a.nro_liqui<>445 --sin contar dias de aguinaldo
and codn_conce=-51
and ano_retro = 0
and mes_retro = 0
--and a.nro_cargo=c.nro_cargo
--and a.nro_legaj=57031
--order by b.per_limes



--Todo Junto
select t.nro_legaj,t.nro_cargo,c.codc_categ,c.codc_uacad ,b.tipo_escal, sum(t.dias_trabajados) as dias_trabajados, sum(t.dias_retro) as dias_retro, sum(t.dias_trabajados)+ sum(t.dias_retro) as dias_trabajados_total
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

group by t.nro_legaj,t.nro_cargo,c.codc_categ,c.codc_uacad ,b.tipo_escal