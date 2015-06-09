select distinct a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1,a.nro_cuil,a.nro_cuil2 as cuil,b.nro_norma as nroactoadm,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac as sitrev,sum(d.impp_conce) as bruto,per_liano, per_limes
into temp brutos
from mapuche.dh01 a, mapuche.dh03 b, mapuche.dh11 c, mapuche.dh21h d, mapuche.dh22 e
where 
per_liano>2007
and d.codn_conce=-51
and d.nro_cargo=b.nro_cargo
and d.nro_liqui=e.nro_liqui
and a.nro_legaj=b.nro_legaj
and b.codc_categ=c.codc_categ
and a.nro_legaj in
(53702,55053,55974,55303)
group by a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1,a.nro_cuil ,a.nro_cuil2,b.nro_norma,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac ,per_liano, per_limes;



--aportes
select d.nro_cargo,per_liano, per_limes,sum(d.impp_conce ) as ap
into temp aportes
from  mapuche.dh03 a,mapuche.dh21h d, mapuche.dh22 e, mapuche.dh12 f
where 
per_liano>2007
and a.nro_cargo=d.nro_cargo
and d.nro_liqui=e.nro_liqui
and d.codn_conce=f.codn_conce
and f.nro_orimp>0
and f.tipo_conce='A'
and a.nro_legaj in(21113 )
group by per_liano, per_limes,d.nro_cargo;


select distinct a.desc_appat||' '||a.desc_apmat||','||a.desc_nombr,a.nro_legaj,a.nro_cuil1||'-'||a.nro_cuil||'-'|| a.cuil, nroactoadm,a.fec_norma,a.fec_alta,a.nro_cargo,a.codc_categ,a.codc_agrup,sitrev, bruto,a.per_liano,a.per_limes,b.ap from brutos a, aportes b
where 
 a.nro_cargo=b.nro_cargo
and a.per_limes=b.per_limes
and a.per_liano=b.per_liano
order by a.nro_cargo,a.per_liano,a.per_limes;