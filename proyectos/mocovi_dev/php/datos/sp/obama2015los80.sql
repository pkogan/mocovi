
/*jerarquización; Los 65 legajos*/
select distinct dh01.desc_appat,dh01.desc_apmat,dh01.desc_nombr,a.codc_uacad,a.tipoescalafon, a.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo, per_limes, imp_bruto+imp_aporte as total
from(
select a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo, per_limes, 
 sum(case when codn_conce IN (-51, -52, -53, -56) then impp_conce  else 0 end) as imp_bruto,
                    
                    sum(case when codn_conce = -55 then impp_conce else 0 end) as imp_aporte
                        from mapuche.dh21h a ,mapuche.dh03 b, mapuche.dh22 c
                        where
 a.nro_cargo=b.nro_cargo and
    a.nro_liqui=c.nro_liqui
                       --todo 2014 
                        and a.nro_liqui<=452 and a.nro_liqui>=439
                       -- todo 2015
                       --and a.nro_liqui>452
                        and 	tipoescalafon='N'
                        and (
--los 30
a.nro_legaj in (22730, 20026, 21455, 22764, 22727, 22724, 22726, 22707, 22725, 58778, 56733, 22734, 22711, 22767, 21132, 22758, 22757, 22760, 22755, 22753, 57526, 22768, 22770, 21550, 22773, 22771, 57675, 22799, 22791, 22779)
--los 50
or a.nro_legaj in(22741, 22750, 22751, 22661, 56726, 22228, 22708, 22728, 21975, 22648, 22700, 22766, 22206, 22682, 22732, 22015, 21577, 22739, 22738, 22662, 22716, 22745, 22718, 22678, 22681, 22698, 22679, 22684, 22722, 205961, 55449, 22737, 22720, 22735, 22719, 21662, 22736, 22723, 21415, 21619, 21938, 22748, 21480, 21028, 22721, 22019, 22320, 22537, 22743, 22744) 
)
                        group by a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo, per_limes 
                      
order by a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo, per_limes
)as a
inner join dh01 on a.nro_legaj=dh01.nro_legaj