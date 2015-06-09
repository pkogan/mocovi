
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
                       --todo 2014 and a.nro_liqui<=452 and a.nro_liqui>=439
                       -- todo 2015
                       and a.nro_liqui>452
                        and 	tipoescalafon='D'
                        and a.nro_legaj in
(54323,
20786,
53420,
54051,
51364,
51549,
51965,
52258,
51612,
52508,
53503,
52956,
51271,
53264,
51727,
53635,
53859,
51256,
54002,
53999,
51922,
51500,
52468,
51920,
22305,
52179,
51056,
51740,
52826,
52417,
53489,
51462,
53377,
51474,
50623,
51419,
53601,
51616,
52758,
52207,
51551,
51696,
52227,
53378,
51986,
51823,
51821,
51659,
51362,
50875,
50517,
51633,
52003,
54001,
51459,
51111,
51205,
51460,
22274,
51035,
53547,
51664,
52629,
52256,
52201)
                        group by a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo, per_limes 
                      
order by a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo, per_limes
)as a
inner join dh01 on a.nro_legaj=dh01.nro_legaj