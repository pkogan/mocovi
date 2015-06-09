<?php
class ci_mail extends mocovi_dev_ci
{
	
	
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Atrapa la interacción del usuario con el botón asociado
	 * @param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuración
	 */
	function evt__formulario__enviar($datos)
	{
            
               require_once('3ros/phpmailer/class.phpmailer.php');
                require_once('3ros/phpmailer/class.smtp.php');
                /*envio de notificacion al gestor del requerimiento*/        
                $mail=new PHPMailer();
                $mail->IsSMTP();

                $mail->SMTPAuth   = true;                  // enable SMTP authentication
                $mail->SMTPSecure = 'ssl';                 // sets the prefix to the servier
                $mail->Host       = 'smtp.gmail.com';      // sets GMAIL as the SMTP server
                //$mail->SMTDebug=1;//no olvidar sacar el debug cuando funcione
                $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
                $mail->Username   = 'lidia.lopez@fi.uncoma.edu.ar';  // GMAIL username --cambiar por subsecretariati@fi.uncoma.edu.ar
                $mail->Password   = 'lidia123';            // GMAIL password
                $mail->TimeOut = 100;
                //seteo From:
                $mail->SetFrom('lidia.lopez@fi.uncoma.edu.ar', 'Subsecretaria TI');//cambiar por subsecretariati@fi.uncoma.edu.ar o lidia.lopez@fi.uncoma.edu.ar
                //seteo To:
                $mail->AddAddress($datos['destino'],$datos['destino']);//correo y nombre del del gestor 
                //seteo el Subject
                $mail->Subject = $datos['asunto'] ;//cambie comilla doble por simple
                $mail->ContentType='text/html;charset=utf-8\r\n';//la agregue nueva
                //seteo el body
                $mail->Body =$datos['cuerpo'] ; //"<b>Mensaje de prueba en formato html</b>"
                
                
                if($mail->Send()) {
                    echo "Mensaje enviado correctamente";
                } else {
                    echo "Problemas enviando correo electrónico a ".$mail->ErrorInfo;
                }
	}

}
?>