
select c.per_liano,c.per_limes,d.tipo_escal,b.codc_categ,b.codc_uacad,count(distinct a.nro_cargo)
                        from mapuche.dh21h a ,mapuche.dh03 b,  mapuche.dh22 c, mapuche.dh11 d
                        where
 a.nro_cargo=b.nro_cargo and b.codc_categ=d.codc_categ and
                        a.nro_liqui=c.nro_liqui and
                        --and ((b.per_liano=2014 and b.per_limes>=2) or (b.per_liano=2015 and b.per_limes=1))
                        --and
                        --c.per_liano>=2007
                        --a.nro_liqui>=440
--                        and a.nro_liqui<={$param['id_liqui_fin']}
                        --and a.nro_liqui<>445 --sin contar dias de aguinaldo
                        --and codn_conce=1
                        --and ano_retro = 0
                        --and mes_retro = 0
group by             c.per_liano,c.per_limes,d.tipo_escal,b.codc_categ,b.codc_uacad
order by             c.per_liano,c.per_limes,d.tipo_escal,b.codc_uacad,b.codc_categ