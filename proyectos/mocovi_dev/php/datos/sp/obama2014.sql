select a.desc_appat||','||a.desc_nombr,a.nro_legaj,a.nro_cuil1||lpad(cast(a.nro_cuil as text),8,'0')||a.nro_cuil2 as cuil,b.nro_cargo,b.fec_alta,b.fec_baja,b.codc_categ,b.nro_norma,b.fec_norma,b.codc_agrup
from mapuche.dh01 a, mapuche.dh03 b
where a.nro_legaj=b.nro_legaj
and a.nro_legaj in
(22730, 20026, 21455, 22764, 22727, 22724, 22726, 22707, 22725, 58778, 56733, 22734, 22711, 22767, 21132, 22758, 22757, 22760, 22755, 22753, 57526, 22768, 22770, 21550, 22773, 22771, 57675, 22799, 22791, 22779)

------
--30
--select * from mapuche.dh01 where nro_legaj in (22730, 20026, 21455, 22764, 22727, 22724, 22726, 22707, 22725, 58778, 56733, 22734, 22711, 22767, 21132, 22758, 22757, 22760, 22755, 22753, 57526, 22768, 22770, 21550, 22773, 22771, 57675, 22799, 22791, 22779)
--order by desc_appat

--50
select * 
from mapuche.dh01 where nro_legaj in (22741, 22750, 22751, 22661, 56726, 22228, 22708, 22728, 21975, 22648, 22700, 22766, 22206, 22682, 22732, 22015, 21577, 22739, 22738, 22662, 22716, 22745, 22718, 22678, 22681, 22698, 22679, 22684, 22722, 205961, 55449, 22737, 22720, 22735, 22719, 21662, 22736, 22723, 21415, 21619, 21938, 22748, 21480, 21028, 22721, 22019, 22320, 22537, 22743, 22744  )

--order by desc_appat
------30
select distinct a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1||lpad(cast(a.nro_cuil as text),8,'0')||a.nro_cuil2 as cuil,b.nro_norma as nroactoadm,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac as sitrev,d.impp_conce as bruto,per_liano, per_limes
from mapuche.dh01 a, mapuche.dh03 b, mapuche.dh11 c, mapuche.dh21h d, mapuche.dh22 e
where 
d.nro_liqui>=439
and d.codn_conce=-51
and d.nro_cargo=b.nro_cargo
and d.nro_liqui=e.nro_liqui
and a.nro_legaj=b.nro_legaj
and b.codc_categ=c.codc_categ
and c.tipo_escal='N'
and a.nro_legaj in
(22730, 20026, 21455, 22764, 22727, 22724, 22726, 22707, 22725, 58778, 56733, 22734, 22711, 22767, 21132, 22758, 22757, 22760, 22755, 22753, 57526, 22768, 22770, 21550, 22773, 22771, 57675, 22799, 22791, 22779)


--aportes
select d.nro_cargo,per_liano, per_limes,sum(d.impp_conce ) as ap
into temp aportes
from  mapuche.dh03 a,mapuche.dh21h d, mapuche.dh22 e, mapuche.dh12 f
where 
d.nro_liqui>=439
and a.nro_cargo=d.nro_cargo
and d.nro_liqui=e.nro_liqui
and d.codn_conce=f.codn_conce
and f.nro_orimp>0
and f.tipo_conce='A'
and a.nro_legaj in
(22730, 20026, 21455, 22764, 22727, 22724, 22726, 22707, 22725, 58778, 56733, 22734, 22711, 22767, 21132, 22758, 22757, 22760, 22755, 22753, 57526, 22768, 22770, 21550, 22773, 22771, 57675, 22799, 22791, 22779)
group by per_liano, per_limes,d.nro_cargo


-----50
select distinct a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1,a.nro_cuil,a.nro_cuil2 as cuil,b.nro_norma as nroactoadm,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac as sitrev,sum(d.impp_conce) as bruto,per_liano, per_limes
  into temp brutos
from mapuche.dh01 a, mapuche.dh03 b, mapuche.dh11 c, mapuche.dh21h d, mapuche.dh22 e
where 
d.nro_liqui>=439
and d.codn_conce=-51
and d.nro_cargo=b.nro_cargo
and d.nro_liqui=e.nro_liqui
and a.nro_legaj=b.nro_legaj
and b.codc_categ=c.codc_categ
and c.tipo_escal='N'
and a.nro_legaj in
(22741, 22750, 22751, 22661, 56726, 22228, 22708, 22728, 21975, 22648, 22700, 22766, 22206, 22682, 22732, 22015, 21577, 22739, 22738, 22662, 22716, 22745, 22718, 22678, 22681, 22698, 22679, 22684, 22722, 205961, 55449, 22737, 22720, 22735, 22719, 21662, 22736, 22723, 21415, 21619, 21938, 22748, 21480, 21028, 22721, 22019, 22320, 22537, 22743, 22744  )
group by a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1,a.nro_cuil ,a.nro_cuil2,b.nro_norma,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac ,per_liano, per_limes
order by b.nro_cargo,per_liano, per_limes


--aportes
select d.nro_cargo,per_liano, per_limes,sum(d.impp_conce ) as ap
into temp aportes
from  mapuche.dh03 a,mapuche.dh21h d, mapuche.dh22 e, mapuche.dh12 f
where 
d.nro_liqui>=439
and a.nro_cargo=d.nro_cargo
and d.nro_liqui=e.nro_liqui
and d.codn_conce=f.codn_conce
and f.nro_orimp>0
and f.tipo_conce='A'
and a.nro_legaj in(22741, 22750, 22751, 22661, 56726, 22228, 22708, 22728, 21975, 22648, 22700, 22766, 22206, 22682, 22732, 22015, 21577, 22739, 22738, 22662, 22716, 22745, 22718, 22678, 22681, 22698, 22679, 22684, 22722, 205961, 55449, 22737, 22720, 22735, 22719, 21662, 22736, 22723, 21415, 21619, 21938, 22748, 21480, 21028, 22721, 22019, 22320, 22537, 22743, 22744  )
group by per_liano, per_limes,d.nro_cargo


select distinct a.desc_appat||' '||a.desc_apmat||','||a.desc_nombr,a.nro_legaj,a.nro_cuil1||'-'||a.nro_cuil||'-'|| a.cuil, nroactoadm,a.fec_norma,a.fec_alta,a.nro_cargo,a.codc_categ,a.codc_agrup,sitrev, bruto,a.per_liano,a.per_limes,b.ap from brutos a, aportes b
where 
 a.nro_cargo=b.nro_cargo
and a.per_limes=b.per_limes
and a.per_liano=b.per_liano
order by a.nro_cargo,a.per_liano,a.per_limes



----
select distinct a.desc_appat||' '||a.desc_apmat||','||a.desc_nombr,a.nro_legaj,a.nro_cuil1||'-'||lpad(cast(a.nro_cuil as text),8,'0')||'-'||a.nro_cuil2 as cuil,b.nro_norma as nroactoadm,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac as sitrev,e.per_liano, e.per_limes,d.impp_conce as bruto,f.ap
from mapuche.dh01 a, mapuche.dh03 b, mapuche.dh11 c, mapuche.dh21h d, mapuche.dh22 e, aportes f
where 
d.nro_liqui>=439
and d.codn_conce=-51
and d.nro_cargo=b.nro_cargo
and d.nro_liqui=e.nro_liqui
and a.nro_legaj=b.nro_legaj
and b.codc_categ=c.codc_categ
and c.tipo_escal='N'
and a.nro_legaj in
(22741, 22750, 22751, 22661, 56726, 22228, 22708, 22728, 21975, 22648, 22700, 22766, 22206, 22682, 22732, 22015, 21577, 22739, 22738, 22662, 22716, 22745, 22718, 22678, 22681, 22698, 22679, 22684, 22722, 205961, 55449, 22737, 22720, 22735, 22719, 21662, 22736, 22723, 21415, 21619, 21938, 22748, 21480, 21028, 22721, 22019, 22320, 22537, 22743, 22744  )
and f.nro_cargo=b.nro_cargo
and f.per_limes=e.per_limes
and f.per_liano=e.per_liano
order by b.nro_cargo,e.per_liano,e.per_limes


---jerarquizacion
select nro_legaj,nro_docum from mapuche.dh01
where nro_docum in(
13653285,
6533897,
11204098,
12253126,
18614478,
6374728,
11112420,
12225628,
6440497,
10124594,
12930102,
10000079,
16692674,
7695074,
12285081,
11937325,
12715051,
6251396,
10669288,
12988234,
6046202,
10158427,
6152928,
8371052,
5639534,
13574347,
10371726,
10381706,
7777763,
11679064,
12299061,
10826798,
8259540,
7650298,
12262437,
14748591,
10995016,
12225148,
14175990,
10868911,
13104214,
10333736,
13089155,
16062103,
10521835,
14420784,
10998068,
11901331,
4448876,
7924703,
12638406,
13837792,
5614740,
10270017,
12438693,
8111796,
10809008,
13175641,
13837965,
11576872,
13869281,
11651534,
8212931,
13574454,
13671980
)






select distinct a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1,a.nro_cuil,a.nro_cuil2 as cuil,b.nro_norma as nroactoadm,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac as sitrev,sum(d.impp_conce) as bruto,per_liano, per_limes
 into temp brutos
from mapuche.dh01 a, mapuche.dh03 b, mapuche.dh11 c, mapuche.dh21h d, mapuche.dh22 e
where 
d.nro_liqui>=439
and d.codn_conce=-51
and d.nro_cargo=b.nro_cargo
and d.nro_liqui=e.nro_liqui
and a.nro_legaj=b.nro_legaj
and b.codc_categ=c.codc_categ

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
group by a.desc_appat,a.desc_apmat,a.desc_nombr,a.nro_legaj,a.nro_cuil1,a.nro_cuil ,a.nro_cuil2,b.nro_norma,b.fec_norma,b.fec_alta,b.nro_cargo,b.codc_categ,b.codc_agrup,b.codc_carac ,per_liano, per_limes
order by b.nro_cargo,per_liano, per_limes

--aportes
select d.nro_cargo,per_liano, per_limes,sum(d.impp_conce ) as ap
into temp aportes
from  mapuche.dh03 a,mapuche.dh21h d, mapuche.dh22 e, mapuche.dh12 f
where 
d.nro_liqui>=439
and a.nro_cargo=d.nro_cargo
and d.nro_liqui=e.nro_liqui
and d.codn_conce=f.codn_conce
and f.nro_orimp>0
and f.tipo_conce='A'
and a.nro_legaj in (54323,
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
group by per_liano, per_limes,d.nro_cargo

