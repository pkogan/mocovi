
select bruto.per_liano,bruto.per_limes,bruto.codc_categ,max(bruto.bruto) max_bruto ,max(aportes.aportes) max_aportes,min(bruto.bruto) min_bruto,min(aportes.aportes) min_aportes, avg(bruto.bruto) avg_bruto,avg(aportes.aportes)avg_aportes, sum(bruto.bruto) sum_bruto, sum(aportes.aportes) sum_aportes
from
(select c.nro_liqui,c.per_liano,c.per_limes,b.codc_categ,a.nro_legaj,a.nro_cargo, sum(a.impp_conce) as bruto
                        from mapuche.dh21h a ,mapuche.dh03 b , mapuche.dh22 c
                        where
                        a.nro_cargo=b.nro_cargo and
                        a.nro_liqui=c.nro_liqui and
                        c.per_liano>=2007 
                        --and 
                        --a.nro_liqui=449
--                        and a.nro_liqui<={$param['id_liqui_fin']}
                        --and a.nro_liqui<>445 --sin contar dias de aguinaldo
                        and codn_conce=-51
                        --and ano_retro = 0
                        --and mes_retro = 0
                        group by c.nro_liqui,c.per_liano,c.per_limes,a.nro_legaj,a.nro_cargo,b.codc_categ
                        )bruto
                       -- on trabajados.nro_cargo=bruto.nro_cargo

inner join (select d.nro_liqui,a.nro_legaj,a.nro_cargo, sum(a.impp_conce ) as aportes
                        from mapuche.dh21h a,  mapuche.dh12 c ,  mapuche.dh22 d
                        where --a.nro_liqui=b.nro_liqui
                        --and ((b.per_liano=2014 and b.per_limes>=2) or (b.per_liano=2015 and b.per_limes=1))
                        a.nro_liqui=d.nro_liqui and
                        d.per_liano>=2007 
--                        and a.nro_liqui<={$param['id_liqui_fin']}
                        --and a.nro_liqui<>445 --sin contar dias de aguinaldo
                        --and a.nro_liqui=b.nro_liqui
                        and a.codn_conce=c.codn_conce
                        and c.nro_orimp>0
                        and c.tipo_conce='A'
                        group by  d.nro_liqui,a.nro_legaj,a.nro_cargo
                        ) aportes
                        on bruto.nro_cargo=aportes.nro_cargo and bruto.nro_liqui=aportes.nro_liqui
group by bruto.per_liano,bruto.per_limes,bruto.codc_categ
order by codc_categ