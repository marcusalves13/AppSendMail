<?php
require "bibliotecas/PHPmailer/Exception.php";
require "bibliotecas/PHPmailer/OAuth.php";
require "bibliotecas/PHPmailer/PHPMailer.php";
require "bibliotecas/PHPmailer/POP3.php";
require "bibliotecas/PHPmailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;



class Mensagem
{
    private $para = null;
    private $assunto = null;
    private $mensagem = null;
    public $status = ['cogigo_status' => null, 'descricao_status' => ''];

    public function __get($atr)
    {
        return $this->$atr;
    }

    public function __set($atr, $valor)
    {
        $this->$atr = $valor;
    }

    public function mensagemValida()
    {
        if (empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
            return false;
        }
        return true;
    }
}

$mensagem = new Mensagem();


$mensagem->__set('para', $_POST['para']);
$mensagem->__set('assunto', $_POST['assunto']);
$mensagem->__set('mensagem', $_POST['mensagem']);

if (!$mensagem->mensagemValida()) {
    echo 'Mensagem nao e valida';
    header('Location:index.php?campos=invalidos');
}
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = false;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'seuEmail6@gmail.com';                     // SMTP username
    $mail->Password   = 'suaSenha';                               // SMTP password
    $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('SeuEmail', 'NomeExibidoRemetente');
    $mail->addAddress($mensagem->__get('para'));     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    // Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $mensagem->__get('assunto');
    $mail->Body    = $mensagem->__get('mensagem');
    $mail->AltBody ='E necessario usar um client com suporte HTML para visualizar toda mensagem';

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->send();
    $mensagem->status['codigo_status'] = 1;
    $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso';
} catch (Exception $e) {
    $mensagem->status['codigo_status'] = 2;
    $mensagem->status['descricao_status'] = 'Não foi possivel enviar esse e-mail, por favor verifique se o e-mail destino está correto ou tente novamente mais tarde. Erro: ' . $mail->ErrorInfo;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Mail Send</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>

    <div class="container">
        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="logo_pg.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?if ($mensagem->status['codigo_status']==1) {?>
                <div class="container">
                    <h1 class="display-4 text-success">Sucesso</h1>
                    <p>
                        <? echo $mensagem->status['descricao_status']; ?>
                    </p>
                    <a href="index.php" class=" btn btn-success btn-lg mt-5 text-white">Voltar</a>
                </div>
                <?}?>

                <?if ($mensagem->status['codigo_status']==2) {?>
                <div class="container">
                    <h1 class="display-4 text-danger">Erro</h1>
                    <p>
                        <? echo $mensagem->status['descricao_status']; ?>
                    </p>
                    <a href="index.php" class=" btn btn-danger btn-lg mt-5 text-white">Voltar</a>
                </div>
                <?}?>
            </div>
        </div>

    </div>

</body>

</html>