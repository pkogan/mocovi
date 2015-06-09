<?php

class wichi_portal
{
	function generar($testeo_directorio_salida='', $testeo_prefijo_archivos='')
	{
		$cargos_activos = config::get_parametro_rrhh("Información Gerencial", "Solo Cargos Activos");  // Se lee del rrhhini la variable ......

		$fec_datos = "'" . fechas::get_fecha_db() . "'";  // Esta es la fecha del dia
		$fec_datos = explode("-",$fec_datos);
		$fecha = "";
		for($i=0;$i<sizeof($fec_datos);$i++){
			$fecha = $fecha.$fec_datos[$i];
		}
		$fec_datos = $fecha;
		$periodo_corriente = mapuche::get_periodo_corriente();
        $anio_corriente = $periodo_corriente['per_anoct'];
		$mes_corriente  = $periodo_corriente['per_mesct'];
		$fecha_ultimo_dia_mes = fechas::get_dias_mes($mes_corriente, $anio_corriente) . '-' . $mes_corriente . '-' . $anio_corriente;

		// Calculo del mes/anio siguiente
		$periodo_siguiente = mapuche_varios::periodo_siguiente($periodo_corriente);
		$mes_siguiente  = $periodo_siguiente['per_mesct'];
		$anio_siguiente = $periodo_siguiente['per_anoct'];

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // Lista de cargos a tener en cuenta (se crea la tabla temporal cargos para que luego se utilice siempre la misma.
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        if ($cargos_activos) {
            $sql = "SELECT DISTINCT nro_cargo
                    INTO TEMP cargos
                    FROM ".MAP_ESQUEMA.".dh03 c, ".MAP_ESQUEMA.".dh01 l
                    WHERE c.nro_legaj=l.nro_legaj AND l.tipo_estad<>'P' AND
                        c.fec_alta < '01-".$mes_siguiente."-".$anio_siguiente."'::date AND
                        (c.fec_baja is null OR c.fec_baja >= '01-".$mes_corriente."-".$anio_corriente."'::date) AND
                        (SELECT count(*)
                        FROM ".MAP_ESQUEMA.".dh21 liq
                        WHERE c.nro_cargo=liq.nro_cargo AND liq.nro_orimp > 0 AND liq.codn_conce > 0) > 0;";
        } else {
        	$sql = "SELECT DISTINCT nro_cargo
                    INTO TEMP cargos
                    FROM ".MAP_ESQUEMA.".dh03 c, ".MAP_ESQUEMA.".dh01 l
                    WHERE c.nro_legaj=l.nro_legaj AND l.tipo_estad<>'P' AND
                        (
                         (c.fec_alta < '01-".$mes_siguiente."-".$anio_siguiente."'::date AND
                         (c.fec_baja is null OR c.fec_baja >= '".fechas::get_dias_mes($mes_corriente,$anio_corriente)."-".$mes_corriente."-".$anio_corriente."'::date)
                        ) OR (
                         c.nro_cargo IN (SELECT DISTINCT liq.nro_cargo
                         				 FROM ".MAP_ESQUEMA.".dh21 liq
                         				 WHERE nro_orimp > 0)
                        )
                    );";
        }
		toba::db()->ejecutar($sql);

		$sql = "SELECT DISTINCT nro_legaj
		        INTO TEMP legajos
		        FROM ".MAP_ESQUEMA.".dh03, cargos
		        WHERE dh03.nro_cargo =cargos.nro_cargo;";
		toba::db()->ejecutar($sql);

		// ------------------------------------------------------------------------------------------------------------------------------------------------
        // WT01: Dependencias
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wt01();

		// Agrega un registro con la dependencia no definida, no encontre como lo hace pampa pero lo hace.
		$sql = "INSERT INTO wt01_salida VALUES ('\"#\"', '\"Dependencia no definida\"' , $fec_datos::character varying);";
		toba::db()->ejecutar($sql);

		// Agrega todas las dependencias de dh30, el tema es que solo hay que agregar las que se usan.
		$sql = "INSERT INTO wt01_salida (unidadacad, nombre, fec_datos)
				SELECT DISTINCT
						'\"' || codc_uacad || '\"' AS codigo , '\"' || desc_depcia || '\"', $fec_datos::character varying
				FROM ".MAP_ESQUEMA.".dh03, cargos, ".MAP_ESQUEMA.".depcia
				WHERE dh03.nro_cargo =cargos.nro_cargo AND dh03.codc_uacad=depcia.cod_depcia
				ORDER BY codigo;" ;
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WT02: Categorias
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wt02();

		// Agrega todas las categorias de dh11, el tema es que solo hay que agregar las que se usan.
		$sql = "INSERT INTO wt02_salida
				SELECT DISTINCT
				'\"' || dh11.codc_categ || '\"' AS categoria, '\"' || dh11.desc_categ || '\"' AS nombre, $fec_datos::character varying AS fec_datos
				FROM ".MAP_ESQUEMA.".dh11, ".MAP_ESQUEMA.".dh03, cargos
				WHERE dh11.codc_categ=dh03.codc_categ AND dh03.nro_cargo=cargos.nro_cargo
				ORDER BY categoria;" ;
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WT03: Dedicaciones
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wt03();

		// Agrega todas las dedicaciones de dh31, el tema es que solo hay que agregar las que se usan.
		$sql = "INSERT INTO wt03_salida
				SELECT DISTINCT
						'\"' || dh31.codc_dedic || '\"' AS dedicacion, '\"' || desc_dedic || '\"' AS nombre, $fec_datos::character varying AS fec_datos
				FROM ".MAP_ESQUEMA.".dh31, ".MAP_ESQUEMA.".dh11, ".MAP_ESQUEMA.".dh03, cargos
				WHERE dh31.codc_dedic=dh11.codc_dedic AND dh11.codc_categ=dh03.codc_categ AND dh03.nro_cargo=cargos.nro_cargo
		 		ORDER BY dedicacion ASC;" ;
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WT04: Nivel de estudio
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wt04();

		$sql = "SELECT nro_legaj
				FROM legajos
				ORDER BY nro_legaj;";
		$rs = mapuche::consultar($sql);
		$niveles = array();
		foreach($rs as $registro) { // Toma los maximos niveles de estudio de todos los legajos a exportar.
            $nivel = agentes::get_datos_maximo_estudio_alcanzado($registro['nro_legaj']);
            $niveles[$nivel['codc_nivel']] = $nivel['cargo_estudio_nivel'];
        }
		$niveles['#'] = "Nivel de Estudio no definido";
		ksort($niveles);
        foreach($niveles as $cod_nivel => $desc_nivel) {
            if(strlen(trim($cod_nivel))>0) {
				$cod_nivel = '"' . trim($cod_nivel) . '"';
				$desc_nivel = '"' . $desc_nivel . '"';
				$sql = "INSERT INTO wt04_salida VALUES ('$cod_nivel', '$desc_nivel', $fec_datos::character varying);";
				toba::db()->ejecutar($sql);
	    	}
        }

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WT06: Fuentes
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wt06();

		// Agrega todas las fuentes de dh28
		$sql = "INSERT INTO wt06_salida
				SELECT DISTINCT fue.codn_fuent AS fuente , '\"' || fue.desc_fuent || '\"' AS nombre, $fec_datos::character varying AS fec_datos
				FROM ".MAP_ESQUEMA.".dh28 fue,".MAP_ESQUEMA.".dh21 liq
				WHERE liq.codn_fuent=fue.codn_fuent
				ORDER BY fuente ASC;";
		toba::db()->ejecutar($sql);

		// ------------------------------------------------------------------------------------------------------------------------------------------------
        // WH01: Cargos
        // ------------------------------------------------------------------------------------------------------------------------------------------------

        self::crea_wh01_temp();
        // ----------------------------
        // Obtener licencias
        // ----------------------------

        // Obtiene los cargos que tienen licencia sin goce de haberes por cargo
		$sql = "SELECT nro_cargo
					INTO TEMP cargos_en_licencia
					FROM ".MAP_ESQUEMA.".dh05 lic, ".MAP_ESQUEMA.".dl02 var
					WHERE nro_cargo is not null AND
						lic.nrovarlicencia=var.nrovarlicencia AND
						(var.es_remunerada=false OR porcremuneracion=0) AND
						lic.fec_desde <= '01-".$mes_corriente."-".$anio_corriente."'::date AND
						lic.fec_hasta >= '".fechas::get_dias_mes($mes_corriente, $anio_corriente)."-".$mes_corriente."-".$anio_corriente."'::date;";
		toba::db()->ejecutar($sql);

        // Obtiene los legajos que tienen licencia sin goce de haberes por legajo
		$sql = "SELECT nro_legaj
					INTO TEMP legajos_en_licencia
					FROM ".MAP_ESQUEMA.".dh05 lic, ".MAP_ESQUEMA.".dl02 var
					WHERE nro_legaj IS NOT null AND
						lic.nrovarlicencia=var.nrovarlicencia AND
						(var.es_remunerada=false OR porcremuneracion=0) AND
						lic.fec_desde <= '01-".$mes_corriente."-".$anio_corriente."'::date AND
						lic.fec_hasta >= '".fechas::get_dias_mes($mes_corriente, $anio_corriente)."-".$mes_corriente."-".$anio_corriente."'::date;";
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
		$sql = "INSERT INTO wh01_salida_temp (nro_cargo, nro_legajo, activo, anoperiodo, mesperiodo,
                    categoria, unidadacad, licencia, antiguedad, imp_neto, imp_dctos, imp_aporte, caracter, incentivo, escalafon, dedicacion,
                    apyno, fec_nacim, edad, sexo,
                    fec_datos)
                SELECT car.nro_cargo, l.nro_legaj, 'S', $anio_corriente, $mes_corriente,
                    car.codc_categ, car.codc_uacad,
                    CASE WHEN car.nro_cargo IN (SELECT nro_cargo FROM cargos_en_licencia) OR car.nro_legaj IN (SELECT nro_legaj FROM legajos_en_licencia) THEN 'S'
                         ELSE 'N'
                    END AS licencia,
                    (SELECT DISTINCT min(nov1_conce) AS antiguedad FROM ".MAP_ESQUEMA.".dh21 liq
                        WHERE codn_conce=".config::get_conceptos_antiguedad()." AND liq.nro_cargo=cargos.nro_cargo
                        GROUP BY liq.nro_cargo
                      UNION
                        SELECT 0 AS antiguedad
                    ORDER BY antiguedad DESC
                    LIMIT 1) AS antiguedad,
                    (SELECT round(sum(case  when nro_orimp <> 0 AND tipo_conce IN ('C', 'S', 'F', 'O') then impp_conce
                                            when nro_orimp <> 0 AND tipo_conce= 'D' then -impp_conce
                                            else 0 end)::numeric, 2)
                        FROM ".MAP_ESQUEMA.".dh21 WHERE nro_cargo=cargos.nro_cargo) as imp_neto,
                    (SELECT round(sum(case  when nro_orimp <> 0 AND tipo_conce='D' then impp_conce else 0 end)::numeric, 2)
                        FROM ".MAP_ESQUEMA.".dh21 WHERE nro_cargo=cargos.nro_cargo) as imp_dctos,
                    (SELECT round(sum(case  when nro_orimp <> 0 AND tipo_conce='A' then impp_conce else 0 end)::numeric, 2)
                        FROM ".MAP_ESQUEMA.".dh21 WHERE nro_cargo=cargos.nro_cargo) as imp_aporte,
                    CASE WHEN (cat.tipo_escal, car.codc_carac) IN (SELECT tipo_escal, codc_carac FROM ".MAP_ESQUEMA.".dh35)
                        THEN (SELECT tipo_carac FROM ".MAP_ESQUEMA.".dh35 tc WHERE tc.tipo_escal=cat.tipo_escal AND tc.codc_carac=car.codc_carac)
                        ELSE ''
                    END AS caracter,
                    tipo_incen,
                    cat.tipo_escal, cat.codc_dedic,
					'\"' || l.desc_appat || ', ' || l.desc_nombr || '\"' AS noyap ,
					to_char(l.fec_nacim,'YYYYMMDD') AS fec_nacim,
                    date_part('year',age('" . $fecha_ultimo_dia_mes . "'::timestamp, fec_nacim)) AS edad,
                    l.tipo_sexo,
					$fec_datos::character varying
				FROM cargos, ".MAP_ESQUEMA.".dh03 car, ".MAP_ESQUEMA.".dh01 l, ".MAP_ESQUEMA.".dh11 cat
				WHERE	cargos.nro_cargo=car.nro_cargo
						AND car.nro_legaj=l.nro_legaj
						AND car.codc_categ=cat.codc_categ;";
		toba::db()->ejecutar($sql);

		$sql = "DELETE FROM wh01_salida_temp
				WHERE (imp_neto is NULL OR imp_neto = 0)
				AND (imp_dctos is NULL OR imp_dctos = 0)
				AND (imp_aporte is NULL OR imp_aporte =  0)";
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------

        // Actualiza el maximo nivel de estudio alcanzado por cada agente
        $sql = "SELECT nro_legaj
        		FROM legajos;";
        $rs = mapuche::consultar($sql);

        foreach($rs as $registro) {
            $nivel = agentes::get_datos_maximo_estudio_alcanzado($registro['nro_legaj']);
            if ($nivel AND isset($nivel['codc_nivel'])) {
                $sql = "UPDATE wh01_salida_temp SET nivelestud = '" . $nivel['codc_nivel'] . "' " .
                       "WHERE nro_legajo = " . $registro['nro_legaj'] . ";";
				toba::db()->ejecutar($sql);
			}
		}

		$sql = "UPDATE wh01_salida_temp SET nivelestud = '#'
				WHERE nivelestud is NULL OR TRIM(nivelestud) = '';";
		toba::db()->ejecutar($sql);


        self::crea_wh01();
		//Se crea la tabla que con la cual se generará el archivo
        $sql = "INSERT INTO wh01_salida
				SELECT
					anoperiodo,
					mesperiodo,
					nro_cargo,
					'\"' || escalafon || '\"',
					'\"' || categoria || '\"',
					'\"' || dedicacion || '\"',
					'\"' || unidadacad || '\"',
					'\"' || licencia || '\"',
					'\"' || caracter || '\"',
					CASE WHEN incentivo IS NOT NULL THEN ('\"' || incentivo || '\"') ELSE '\"' || '\"' END,
					antiguedad,
					imp_neto,
					imp_dctos,
					imp_aporte,
					nro_legajo,
					apyno,
					fec_nacim,
					edad,
					'\"' || sexo || '\"',
					'\"' || trim(nivelestud) || '\"',
					fec_datos,
					'\"' || activo || '\"'
				FROM wh01_salida_temp
				ORDER BY apyno ASC,nro_cargo;";
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WH02: Cargos por Dependencia, Subdependencia y Fuente
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wh02();

        $sql = "INSERT INTO wh02_salida (anoperiodo, mesperiodo, nro_cargo,
                    dependen, subdepen, fuente,
                    imp_neto, imp_dctos, imp_aporte,
                    fec_datos,apeynom)
               SELECT $anio_corriente, $mes_corriente, car.nro_cargo,
                    liq.codn_area, liq.codn_subar, liq.codn_fuent,
                    sum(case when nro_orimp <> 0 AND tipo_conce IN ('C', 'S', 'F', 'O') then impp_conce when nro_orimp <> 0 AND tipo_conce= 'D' then -impp_conce else 0 end) as imp_neto,
                    sum(case when nro_orimp <> 0 AND tipo_conce='D' then impp_conce else 0 end) as imp_dctos,
                    sum(case when nro_orimp <> 0 AND tipo_conce='A' then impp_conce else 0 end) as imp_aporte,
                    $fec_datos::character varying,
                    emp.desc_appat || ',' || emp.desc_nombr AS nom
                FROM cargos, ".MAP_ESQUEMA.".dh03 car, ".MAP_ESQUEMA.".dh21 liq, ".MAP_ESQUEMA.".dh01 emp
                WHERE 	cargos.nro_cargo = car.nro_cargo
                		AND car.nro_cargo = liq.nro_cargo
						AND liq.nro_legaj = emp.nro_legaj
                GROUP BY 1,2,3,4,5,6,10,nom
                ORDER BY nom ASC;";

 		toba::db()->ejecutar($sql);

 		$sql = "ALTER TABLE wh02_salida DROP COLUMN apeynom;";
 		toba::db()->ejecutar($sql);

		$sql= "DELETE FROM wh02_salida WHERE imp_neto = 0 AND imp_dctos = 0 AND imp_aporte = 0";
		toba::db()->ejecutar($sql);
		// ------------------------------------------------------------------------------------------------------------------------------------------------
        // WH03: Totales por Unidad Academica, Escalafon, Dependencia, Subdependencia y Fuente
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wh03();

        // TODO: Ahora trabaja con los subtotales, cuando desaparezcan se deberá modificar y sumar los conceptos
        $sql = "INSERT INTO wh03_salida (anoperiodo, mesperiodo,
                    escalafon,
                    unidadacad, dependen, subdepen, fuente,
                    imp_neto, imp_dctos, imp_aporte,
                    fec_datos)
                SELECT $anio_corriente, $mes_corriente,
                    '\"' || (CASE WHEN tipoescalafon='C' THEN 'S' ELSE tipoescalafon END) || '\"' AS escalafon,
                    '\"' || liq.codc_uacad || '\"' AS academica,
                    liq.codn_area, liq.codn_subar, liq.codn_fuent,
                    sum(case when codn_conce IN (-51, -52, -53, -56) then impp_conce when codn_conce = -54 then -impp_conce else 0 end) as imp_neto,
                    sum(case when codn_conce = -54 then impp_conce else 0 end) as imp_dctos,
                    sum(case when codn_conce = -55 then impp_conce else 0 end) as imp_aporte,
                    $fec_datos::character varying
                FROM ".MAP_ESQUEMA.".dh21 liq
                GROUP BY 1,2,3,4,5,6,7,11
                ORDER BY escalafon,academica,liq.codn_area, liq.codn_subar,liq.codn_fuent;";
		toba::db()->ejecutar($sql);
		// Se eliminan los registros que tienen los tres importes en cero.
		$sql = "DELETE FROM wh03_salida WHERE imp_neto=0 AND imp_dctos=0 AND imp_aporte=0;";
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WH04: Proyectado por Unidad Academica, Dependencia, Subdependencia y Fuente
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wh04();
       /*Se comenta el codigo de generacion del archivo WH04 ya que
        * se estaba generando con diferencia, respecto a la generacion de escritorio.
        if($mes_corriente > 1){
	        $sql = "INSERT INTO wh04_salida (anoperiodo, mesperiodo,
	                    unidadacad, dependen, subdepen, fuente,
	                    imp_pagado, imp_proyec, imp_sacpro, imp_antpro,
	                    fec_datos)
	                SELECT $anio_corriente, $mes_corriente,
	                    '\"' || CASE WHEN (substr(proy.codc_imput,1,2)::integer,substr(proy.codc_imput,3,2)::integer) IN (SELECT codn_depen, codn_subde FROM ".MAP_ESQUEMA.".dp29)
	                        THEN (SELECT codc_uacad
	                                    FROM ".MAP_ESQUEMA.".dp29
	                                    WHERE codn_depen=substr(proy.codc_imput,1,2)::integer AND codn_subde=substr(proy.codc_imput,3,2)::integer
	                                    LIMIT 1)
	                        ELSE '#'
	                    END || '\"',
	                    substr(proy.codc_imput,1,2)::integer, substr(proy.codc_imput,3,2)::integer, proy.codn_fuent,
	                    sum((" . self::imp_mensu_anteriores($mes_corriente) . ") * (13 - $mes_corriente)) AS imp_pagado,
	                    sum((" . self::imp_proye_anterior($mes_corriente) . ") * (13 - $mes_corriente)) AS imp_proyec,
	                    sum((" . self::imp_mensu_anteriores_sin_sac($mes_corriente) . " + (" . self::imp_proye_anterior($mes_corriente) . " * (13 - $mes_corriente) ) ) / 12)
	                        AS imp_sacpro,
	                    sum(" . self::imp_antig_anterior($mes_corriente) . ") AS imp_antpro,
	                    $fec_datos::character varying
	                FROM ".MAP_ESQUEMA.".dh50 proy
	                GROUP BY 1,2,3,4,5,6,11
	                ORDER BY 3;";
			toba::db()->ejecutar($sql);
			/* Los registros que sobran en realidad faltan en Clarion por error en el agregado del dbase.
			 * (Da error de grabacion de file system el dbase con numeros grandes.)
			 * No se compara imp_sacpro porque en Clarion esta mal calculado.
			 * (El if Items = 6 C_ImporteSACProyectado = 0.00 elimina
			 * el proyectado cargado en registros anteriores.) */
		//}
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WH05:
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wh05();

        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // WT05: Dependencias y Subdependencias
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        self::crea_wt05();
        self::crea_wt05_temp();

        //Agregamos las dependecias y subdependencias que se fueron cargando en wh02,wh03 y wh04
		$sql = "INSERT INTO wt05_salida_temp
				SELECT DISTINCT $anio_corriente AS anio,sal0.dependen,sal0.subdepen,
				CASE WHEN (sal0.subdepen <> 0 ) THEN CASE WHEN (SELECT COUNT(*) FROM ".MAP_ESQUEMA.".dp19 WHERE dp19.codn_area = sal0.dependen AND dp19.codn_subar = sal0.subdepen) = 0
													 THEN ('SubDependencia no definida') ELSE (SELECT dp19.desc_subar FROM ".MAP_ESQUEMA.".dp19 WHERE dp19.codn_area = sal0.dependen AND dp19.codn_subar = sal0.subdepen) END
					ELSE '' END AS nombre
				FROM wh02_salida sal0
				UNION
				SELECT DISTINCT $anio_corriente AS anio,sal1.dependen,sal1.subdepen,
				CASE WHEN (sal1.subdepen <> 0 ) THEN CASE WHEN (SELECT COUNT(*) FROM ".MAP_ESQUEMA.".dp19 WHERE dp19.codn_area = sal1.dependen AND dp19.codn_subar = sal1.subdepen) = 0
													 	  THEN ('SubDependencia no definida') ELSE (SELECT dp19.desc_subar FROM ".MAP_ESQUEMA.".dp19 WHERE dp19.codn_area = sal1.dependen AND dp19.codn_subar = sal1.subdepen) END
					ELSE '' END AS nombre
				FROM wh03_salida sal1
				UNION
				SELECT DISTINCT $anio_corriente AS anio,sal2.dependen,sal2.subdepen,
				CASE WHEN (sal2.subdepen <> 0 ) THEN CASE WHEN (SELECT COUNT(*) FROM ".MAP_ESQUEMA.".dp19 WHERE dp19.codn_area = sal2.dependen AND dp19.codn_subar = sal2.subdepen) = 0
													 	  THEN ('SubDependencia no definida') ELSE (SELECT dp19.desc_subar FROM ".MAP_ESQUEMA.".dp19 WHERE dp19.codn_area = sal2.dependen AND dp19.codn_subar = sal2.subdepen) END
				ELSE '' END AS nombre
				FROM wh04_salida sal2
				;";
		toba::db()->ejecutar($sql);

		$sql = "INSERT INTO wt05_salida
				SELECT anoperiodo, dependen, subdepen,
				CASE WHEN (nombre = '' ) THEN CASE WHEN (SELECT COUNT(*) FROM ".MAP_ESQUEMA.".dp18 WHERE dp18.codn_area = wt05_salida_temp.dependen) = 0
													 	  THEN '\"' ||'Dependencia no definida'|| '\"' ELSE '\"' || trim((SELECT dp18.desc_area FROM ".MAP_ESQUEMA.".dp18 WHERE dp18.codn_area = wt05_salida_temp.dependen))|| '\"' END
				ELSE '\"' || trim(nombre) || '\"' END AS nombre,
				$fec_datos::character varying AS fec_datos
				FROM wt05_salida_temp
				ORDER BY anoperiodo, dependen, subdepen;";
		toba::db()->ejecutar($sql);

        // ------------------------------------------------------------------------------------------------------------------------------------------------
       	// self::mostrar_resultados();
        // ------------------------------------------------------------------------------------------------------------------------------------------------

        return self::generar_archivos($testeo_directorio_salida, $testeo_prefijo_archivos);

	}

	// ========================================================================================================================================
    // FUNCIONES USADAS PARA PROYECCION, DEFINEN LOS TEXTOS QUE NOMBRAN LOS CAMPOS A UTILIZAR DEL DH50
	// ========================================================================================================================================

	// Retorna la lista de nombres de atributo correspondiente a imp_mensu para todos los meses anteriores al actual
	protected function imp_mensu_anteriores($mes)
    {
        $resultado = 'imp_mensu_1';
        for ($i=2;$i<$mes;$i++)
            $resultado .= ' + imp_mensu_'.$i;
        return($resultado);
    }

    // Retorna la lista de nombres de atributo correspondiente a imp_mensu para todos los meses anteriores al actual exceptuando tambien el mes 6 (aguinaldo)
    protected function imp_mensu_anteriores_sin_sac($mes)
    {
        if (($mes > 1) AND ($mes < 6)) {
            $resultado = 'imp_mensu_1';
            for ($i=2;$i<$mes;$i++)
                $resultado .= ' + imp_mensu_'.$i;
            } elseif (($mes > 6) AND ($mes < 13)) {
            $resultado = 'imp_mensu_7';
            for ($i=8;$i<$mes;$i++)
                $resultado .= ' + imp_mensu_'.$i;
            } else {
                $resultado = '0';
            }
        return($resultado);
    }

    protected function imp_proye_anterior($mes)
    {
        return ('imp_proye_'.($mes - 1));
    }

    protected function imp_antig_anterior($mes)
    {
        return ('imp_antig_'.($mes - 1));
    }

	// ========================================================================================================================================
    // FUNCIONES USADAS PARA GENERAR ARCHIVOS CON LA INFORMACION DE TABLAS EN CSV
	// ========================================================================================================================================

	public function generar_archivos($testeo_directorio_salida, $testeo_prefijo_archivos)
	{
		self::generar_archivo('wt01_salida','WT01.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wt02_salida','WT02.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wt03_salida','WT03.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wt04_salida','WT04.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wt05_salida','WT05.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wt06_salida','WT06.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wh01_salida','WH01.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wh02_salida','WH02.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wh03_salida','WH03.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wh04_salida','WH04.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		self::generar_archivo('wh05_salida','WH05.DBF',$testeo_directorio_salida, $testeo_prefijo_archivos);
		if ($testeo_directorio_salida == '') {
			return self::armar_zip();
		}
	}

	protected function generar_archivo($tabla, $archivo,$testeo_directorio_salida, $testeo_prefijo_archivos) {
	    $separador = '|';
	    $salto_linea = "\r\n";
	    if ($testeo_directorio_salida <> '') {
		    $archivo_completo = $testeo_directorio_salida . $testeo_prefijo_archivos . $archivo . '.csv';
        } else {
		    $archivo_completo = toba::proyecto()->get_path_temp() . '/comunicacion/wichi/' . $archivo . '.csv';
		}
		$fh = fopen($archivo_completo, 'w') or die("Error: No se pudo abrir el archivo $archivo_completo para escritura."); //TODO pasar este mensaje a la clase
	    $sql = "SELECT * from $tabla;";
        $rs = mapuche::consultar($sql);
        if (count($rs) > 0) {
			$claves = array_keys($rs[0]);
			for($i=0; $i<sizeof($claves);$i++)
			{
			$claves[strtoupper($claves[$i])] = strtoupper($claves[$i]);
			unset($claves[$i]);
			}
			fwrite($fh,implode($separador,array_keys($claves)) . $salto_linea);
			foreach($rs AS $datos) {
				fwrite($fh,implode($separador,$datos) . $salto_linea);
			}
		}
	}

	protected function armar_zip()
	{
	    $path = toba::proyecto()->get_path_temp() . '/comunicacion/wichi/';
	    $nombre = 'wichi';
	    $extension = '.zip';
	    $archivo_zip = $path . $nombre . $extension;
		if (file_exists($archivo_zip)) {
			unlink($archivo_zip);
		}
		$zip = new mapuche_zip($path, $nombre);

 		$zip->agregar_archivo($path.'WT01.DBF.csv');
 		$zip->agregar_archivo($path.'WT02.DBF.csv');
 		$zip->agregar_archivo($path.'WT03.DBF.csv');
 		$zip->agregar_archivo($path.'WT04.DBF.csv');
 		$zip->agregar_archivo($path.'WT05.DBF.csv');
 		$zip->agregar_archivo($path.'WT06.DBF.csv');
		$zip->agregar_archivo($path.'WH01.DBF.csv');
 		$zip->agregar_archivo($path.'WH02.DBF.csv');
 		$zip->agregar_archivo($path.'WH03.DBF.csv');
 		$zip->agregar_archivo($path.'WH04.DBF.csv');
 		$zip->agregar_archivo($path.'WH05.DBF.csv');

		$zip->cerrar();
		return $archivo_zip;
	}

	// ========================================================================================================================================
    // Funcion para mostrar la comparacion entre todas las tablas generadas
	// ========================================================================================================================================

	protected function mostrar_resultados()
    {
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // Se muestran todas las comparaciones de las tablas generadas
        // ------------------------------------------------------------------------------------------------------------------------------------------------
		mapuche_varios::compara_tablas('wichi.WT01.DBF','wt01_salida');
		mapuche_varios::compara_tablas('wichi.WT02.DBF','wt02_salida');
		mapuche_varios::compara_tablas('wichi.WT03.DBF','wt03_salida');
		echo "Inicio<br><b>Los archivos WH02.DBF y WH03.DBF dan distintos totales</b> (Creo que el 03 incluye cargos que no estan en el 02.)<br>";
		mapuche_varios::compara_tablas('wichi.WT04.DBF','wt04_salida');
		mapuche_varios::compara_tablas('wichi.WT05.DBF','wt05_salida');
		mapuche_varios::compara_tablas('wichi.WT06.DBF','wt06_salida');
		mapuche_varios::compara_tablas('wichi.WH01.DBF','wh01_salida');
		mapuche_varios::compara_tablas('wichi.WH02.DBF','wh02_salida');
		mapuche_varios::compara_tablas('wichi.WH03.DBF','wh03_salida');
		mapuche_varios::compara_tablas('wichi.WH04.DBF','wh04_salida','anoperiodo, mesperiodo, unidadacad, dependen, subdepen, fuente, imp_pagado, imp_proyec, imp_antpro, fec_datos');
		echo	"<b>Los registros que sobran en realidad faltan en Clarion por error en el agregado del dbase.</b>(Da error de grabacion de file system el dbase con numeros grandes.)<br>" .
				"<b>No se compara imp_sacpro porque en Clarion esta mal calculado.</b> (El if Items = 6 C_ImporteSACProyectado = 0.00 elimina " .
				"el proyectado cargado en registros anteriores.)<br>";
		mapuche_varios::compara_tablas('wichi.WH05.DBF','wh05_salida');
	}

	// ========================================================================================================================================
    // Funciones para crear las tablas temporales con la estructura correcta, se crean y se borran en este proceso.
	// ========================================================================================================================================

    protected function crea_wt01()
    {
        $sql = "CREATE TEMP TABLE wt01_salida
					(
						unidadacad character varying(10),
						nombre character varying(50),
						fec_datos character varying(8)
					);";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wt02()
    {
        $sql = "CREATE TEMP TABLE wt02_salida
				(
					categoria character varying(8),
					nombre character varying(50),
					fec_datos character varying(8)
				);";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wt03()
    {
        $sql = "CREATE TEMP TABLE wt03_salida
                (
					dedicacion character varying(8),
					nombre character varying(50),
					fec_datos character varying(8)
                );";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wt04()
    {
		$sql = "CREATE TEMP TABLE wt04_salida
				(
					nivelestud character varying(8),
					nombre character varying(50),
					fec_datos character varying(8)
				);";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wt05()
    {
		$sql = "CREATE TEMP TABLE wt05_salida
				(
					anoperiodo numeric(4,0),
					dependen numeric(3,0),
					subdepen numeric(3,0),
					nombre character varying(50),
					fec_datos character varying(8)
				);";
		toba::db()->ejecutar($sql);
    }

	protected function crea_wt05_temp()
	{
		$sql = "CREATE TEMP TABLE wt05_salida_temp
				(
					anoperiodo numeric(4,0),
					dependen numeric(3,0),
					subdepen numeric(3,0),
					nombre character varying(50),
					fec_datos character varying(8)
				);";
		toba::db()->ejecutar($sql);
	}

    protected function crea_wt06()
    {
		$sql = "CREATE TEMP TABLE wt06_salida
				(
					fuente numeric(2,0),
					nombre character varying(50),
					fec_datos character varying(8)
				);";
		toba::db()->ejecutar($sql);
    }

	protected function crea_wh01_temp()
    {
		$sql = "CREATE TEMP TABLE wh01_salida_temp
                (
                    anoperiodo numeric(4,0),
                    mesperiodo numeric(2,0),
                    nro_cargo numeric(9,0),
                    escalafon character varying(1),
                    categoria character varying(4),
                    dedicacion character varying(4),
                    unidadacad character varying(4),
                    licencia character varying(1),
                    caracter character varying(1),
                    incentivo character varying(1),
                    antiguedad numeric(4,0),
                    imp_neto double precision,
                    imp_dctos double precision,
                    imp_aporte double precision,
                    nro_legajo numeric(6,0),
                    apyno character varying(50),
					fec_nacim character varying(8),
                    edad numeric(3,0),
                    sexo character varying(1),
                    nivelestud character varying(4),
					fec_datos character varying(8),
                    activo character(1)
				);";
    	toba::db()->ejecutar($sql);
    }

 	protected function crea_wh01()
	{
		$sql = "CREATE TEMP TABLE wh01_salida
				(
					anoperiodo numeric(4,0),
					mesperiodo numeric(2,0),
					nro_cargo numeric(9,0),
					escalafon character varying(4),
					categoria character varying(6),
					dedicacion character varying(6),
					unidadacad character varying(6),
					licencia character varying(3),
					caracter character varying(3),
					incentivo character varying(3),
					antiguedad numeric(4,0),
					imp_neto double precision,
					imp_dctos double precision,
					imp_aporte double precision,
					nro_legajo numeric(6,0),
					apyno character varying(50),
					fec_nacim character varying(8),
					edad numeric(3,0),
					sexo character varying(3),
					nivelestud character varying(6),
					fec_datos character varying(8),
					activo character(3)
				);";
		toba::db()->ejecutar($sql);
	}

    protected function crea_wh02()
    {
        $sql = "CREATE TEMP TABLE wh02_salida
                (
                    anoperiodo numeric(4,0),
                    mesperiodo numeric(2,0),
                    nro_cargo numeric(9,0),
                    dependen numeric(3,0),
                    subdepen numeric(3,0),
                    fuente numeric(2,0),
                    imp_neto numeric(10,2),
                    imp_dctos numeric(10,2),
                    imp_aporte numeric(10,2),
					fec_datos character varying,
					apeynom character varying(70)
                );";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wh03()
    {
        $sql = "CREATE TEMP TABLE wh03_salida
                (
                    anoperiodo numeric(4,0),
                    mesperiodo numeric(2,0),
					escalafon character varying(3),
					unidadacad character varying(6),
                    dependen numeric(3,0),
                    subdepen numeric(3,0),
                    fuente numeric(2,0),
                    imp_neto numeric(10,2),
                    imp_dctos numeric(10,2),
                    imp_aporte numeric(10,2),
					fec_datos character varying(8)
                );";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wh04()
    {
        $sql = "CREATE TEMP TABLE wh04_salida
                (
                    anoperiodo numeric(4,0),
                    mesperiodo numeric(2,0),
                    unidadacad character varying(20),
                    dependen numeric(3,0),
                    subdepen numeric(3,0),
                    fuente numeric(2,0),
                    imp_pagado numeric(20,2),
                    imp_proyec numeric(20,2),
                    imp_sacpro numeric(20,2),
                    imp_antpro numeric(20,2),
                    fec_datos character varying(8)
                );";
		toba::db()->ejecutar($sql);
    }

    protected function crea_wh05()
    {
        $sql = "CREATE TEMP TABLE wh05_salida
                (
                    anoperiodo numeric(4,0),
                    mesperiodo numeric(2,0),
                    nro_planta numeric(9,0),
                    escalafon character varying(1),
                    categoria character varying(4),
                    dedicacion character varying(4),
                    unidadacad character varying(4),
                    fec_datos date
                );";
		toba::db()->ejecutar($sql);
    }

}

?>