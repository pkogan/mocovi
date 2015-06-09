
select a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo,  
 sum(case when codn_conce IN (-51, -52, -53, -56) then impp_conce  else 0 end) as imp_bruto,
                    
                    sum(case when codn_conce = -55 then impp_conce else 0 end) as imp_aporte
                        from mapuche.dh21h a ,mapuche.dh03 b --, mapuche.dh22 b
                        where
 a.nro_cargo=b.nro_cargo and
                        a.nro_liqui=453
                        group by a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo,  
                      
order by a.codc_uacad,a.tipoescalafon, b.codc_categ,a.codn_fuent, a.nro_legaj,a.nro_cargo,  

/*****************/

select b.codc_categ,a.nro_legaj,a.nro_cargo, a.codn_fuent, a.tipoescalafon, a.codc_uacad,
 sum(case when codn_conce IN (-51, -52, -53, -56) then impp_conce  else 0 end) as imp_bruto,
sum(case when codn_conce IN (-51) then impp_conce  else 0 end) as imp_bruto_aportes,
sum(case when codn_conce IN ( -52) then impp_conce  else 0 end) as imp_bruto_no_aportes,
sum(case when codn_conce IN ( -53) then impp_conce  else 0 end) as imp_bruto_salario,
                    
                    sum(case when codn_conce = -55 then impp_conce else 0 end) as imp_aporte,
sum(a.impp_conce) as bruto
                        from mapuche.dh21h a ,mapuche.dh03 b --, mapuche.dh22 b
                        where
 a.nro_cargo=b.nro_cargo and
                        a.nro_liqui=453
and a.nro_legaj=59356
                        group by a.nro_legaj,a.nro_cargo,b.codc_categ
                       