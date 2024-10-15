<?php

namespace Classes;

//importa clase de PHPMailer;
use PHPMailer\PHPMailer\PHPMailer;

class Email{
    //creacion de atributos
    public $nombre;
    public $email;
    public $token;

    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        //crear el objeto de mail - Configurcion de servidor
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];       

        //recipientes
        // $mail->setFrom('cuentas@uptask.com', 'Admin'); //quien lo envía
        $mail->setFrom('jacob.goca@outlook.com', 'Admin'); //quien lo envía
        $mail->addAddress($this->email, $this->nombre); //hosting contratado
        $mail->Subject = 'Confirma tu cuenta';

        //set HTML format
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = '<html>';
        $contenido .= "<p><strong> Hola " . $this->nombre . "</strong></p>";
        $contenido .= "<p> Has creado tu cuenta en UpTask, solo debes confirmarla presionando el siguiente enlace. </p>";
        $contenido .= "<p> Presiona aquí <a href = '" . $_ENV['APP_URL'] . "/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p> Si tu no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
    }
    public function enviarInstrucciones(){
        //crear el objeto de mail-Configuracion de servidor
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];   
        
        //recipientes
        // $mail->setFrom('cuentas@uptask.com', 'Admin'); //quien lo envía
        $mail->setFrom('jacob.goca@outlook.com', 'Admin'); //quien lo envía
        $mail->addAddress($this->email, $this->nombre); //hosting contratado
        $mail->Subject = 'reestablece tu contraseña';

        //set HTML format
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = '<html>';
        $contenido .= "<p><strong> Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu contraseña, sigue el siguiente enlace para realizarlo. </p>";
        $contenido .= "<p>Presiona aquí <a href='" . $_ENV['APP_URL'] . "/reestablecer?token=" . $this->token . "'>Reestablecer Contraseña</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar este correo </p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
    }
}