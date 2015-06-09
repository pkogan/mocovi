CREATE OR REPLACE FUNCTION comahue.presupuesto_ua_escal(date,date,date)
  RETURNS integer AS
$BODY$
DECLARE

reg		record;
pdiano		date;
udiano		date;
actual		date;
r		integer;
f 		integer;

BEGIN
--se considera el periodo anual 2014 desde 01-02-2014 al 31-01-2015
--pdiano:='2014-10-01';
--udiano:='2015-01-31';
pdiano:=$1; 
actual:=$2;
udiano:=$3;


CREATE LOCAL TEMP TABLE final
(

  legajo 		integer,
  nombre_apellido	character(50),
  cargo			integer,
  codigo_escalafon	character(1),
  codigo_unidad		character(4),
  codigo_categoria_siu  character(5),
  dias_trabajados	integer,
  dias_retroactivo	integer,
  dias_trabajados_total integer,
  dias_a_trabajar       integer,
  dias_licencia		integer,
  dias_a_trabajar_total integer
    );
  
CREATE LOCAL TEMP TABLE auxiliar
(

  legajo 		integer,
  cargo			integer,
  codigo_escalafon	character(1),
  codigo_unidad		character(4),
  codigo_categoria_siu  character(5),
  dias_pagados       	integer,
  dias_retro		integer
  
    );
    
--calculo los dias que se consideran pagar desde este momento hasta el final del periodo  
select into f comahue.credito_apagar('2014-10-01','2015-01-31');

insert into final(legajo,nombre_apellido,cargo,codigo_escalafon,codigo_unidad,codigo_categoria_siu,dias_trabajados,dias_retroactivo,dias_trabajados_total,dias_a_trabajar,dias_licencia,dias_a_trabajar_total)
select legajo,'',cargo,tipo_escal,codc_uacad,codc_categ,0,0,0,dias_anual,dias_lic,0
from apagar;

--llamo a una funcion para calcular los dias pagados hasta el momento
select into r comahue.credito_pagado(pdiano,udiano);

insert into auxiliar(legajo,cargo,codigo_escalafon,codigo_unidad,codigo_categoria_siu,dias_pagados,dias_retro)
select legajo,cargo,tipo_escal,codc_uacad,codc_categ, sum(dias_pag) as dp,sum(dias_retro) as dr
from pagado
group by legajo,cargo,tipo_escal,codc_uacad,codc_categ;


FOR reg IN
   	select * from final
	
   LOOP
     
     update final
     set dias_trabajados=a.dias_pagados,  dias_retroactivo=a.dias_retro
     select * from auxiliar a
     where a.cargo=reg.cargo
     and a.codigo_categoria_siu=reg.codigo_categoria_siu;

END LOOP ;


update final
set dias_trabajados_total= dias_trabajados+dias_retroactivo,dias_a_trabajar_total =dias_a_trabajar+dias_licencia;
   
  
return 1;
END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE

select * from comahue.presupuesto_ua_escal('2014-02-01','2014-11-','2015-01-31');

