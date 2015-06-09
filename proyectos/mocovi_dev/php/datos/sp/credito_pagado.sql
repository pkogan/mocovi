CREATE OR REPLACE FUNCTION comahue.credito_pagado(date,date)
  RETURNS integer AS
$BODY$
DECLARE

reg		record;
pdiano		date;
udiano		date;
anouno		integer;
mesuno		integer;
anodos		integer;
mesdos		integer;

BEGIN
--se por ejemplo se considera el periodo anual 2014 entonces: desde 01-02-2014 al 31-01-2015
--llamar con 01-02-2014 al 31-01-2015 como parametro
pdiano:=$1;
udiano:=$2;
anouno:=date_part ('year', pdiano);
mesuno:=date_part ('month', pdiano);
anodos:=date_part ('year', udiano);
mesdos:=date_part ('month', udiano);

CREATE LOCAL TEMP TABLE pagado
(

  legajo 	integer,
  cargo		integer,
  codc_uacad	character(4),
  codc_categ 	character(4),
  tipo_escal 	character(1),
  mes 		integer,
  ano		integer,
  dias_pag	integer,
  dias_retro	integer
  
  );


CREATE LOCAL TEMP TABLE retro
(

  legajo 	integer,
  cargo		integer,
  codc_uacad	character(4),
  codc_categ 	character(4),
  tipo_escal 	character(1),
  mes 		integer,
  ano		integer,
  dias_ret	integer
   
  );  


--inserto lo pagado no retro
insert into pagado (legajo,cargo,codc_uacad,codc_categ,tipo_escal,mes,ano,dias_pag,dias_retro)
select a.nro_legaj,a.nro_cargo,a.codc_uacad,c.codc_categ,,tipoescalafon,b.per_liano,b.per_limes,MAX(a.nov1_conce) as dias_trab,0
from mapuche.dh21h a, mapuche.dh22 b, mapuche.dh03 c
where a.nro_liqui=b.nro_liqui
and ((b.per_liano=anouno and b.per_limes>=mesuno) or (b.per_liano=anodos and b.per_limes=mesdos))
and a.nro_liqui<>445 --sin contar dias de aguinaldo
and codn_conce=-51
and ano_retro = 0
and mes_retro = 0
and a.nro_cargo=c.nro_cargo
group by a.codc_uacad,b.per_liano,b.per_limes,a.nro_legaj,a.nro_cargo,c.codc_categ,tipoescalafon;



--calculo dias pagados que son retroactivos
insert into retro(legajo,cargo,codc_uacad,codc_categ,tipo_escal,mes,ano,dias_ret)
select a.nro_legaj,a.nro_cargo,a.codc_uacad,c.codc_categ,,tipoescalafon,b.per_liano,b.per_limes,MAX(a.nov1_conce) as dias_ret
from mapuche.dh21h a, mapuche.dh22 b, mapuche.dh03 c
where a.nro_liqui=b.nro_liqui
and ((b.per_liano=anouno and b.per_limes>=mesuno) or (b.per_liano=anodos and b.per_limes=mesdos))
and a.nro_liqui<>445 --sin contar dias de aguinaldo
and codn_conce=-51
and ano_retro <> 0
and mes_retro <>0
and a.nro_cargo=c.nro_cargo
group by a.codc_uacad,b.per_liano,b.per_limes,a.nro_legaj,a.nro_cargo,c.codc_categ,tipoescalafon;



--actualizo los dias retro de los que estan en ambas tablas
FOR reg IN
   	select * from pagado
	
   LOOP
   
     update pagado
     set dias_retro =
      select dias_ret from retro b 
      where reg.cargo=b.cargo
      and reg.legajo=b.legajo
      and reg.codc_uacad=b.codc_uacad
      and reg.per_liano=b.per_liano
      and reg.per_limes=b.per_limes
       


END LOOP ;


--insert en pagado los cargos que tienen solo dias retro pagados
insert into pagado(legajo,cargo,codc_uacad,codc_categ,tipo_escal,mes,ano,dias_pag,dias_retro)
select a.legajo,a.cargo,a.codc_uacad,a.codc_categ,a.tipo_escal,a.mes,a.ano,0,dias_ret 
from retro a
where not exists (select * from pagado b
                  where a.cargo=b.cargo
      			and a.legajo=b.legajo
      			and a.codc_uacad=b.codc_uacad
      			and a.per_liano=b.per_liano
      			and a.per_limes=b.per_limes
       );



return 1;
END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE


select comahue.credito_pagado ('2014-02-01','2015-01-31')

select codc_uacad,codc_categ,tipo_escal, sum(dias_pag) as dias_pagados,sum(dias_retro) as dias_retroa
from pagado
group by codc_uacad,codc_categ,tipo_escal 	



