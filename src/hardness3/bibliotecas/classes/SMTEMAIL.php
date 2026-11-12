<?php
namespace hardness;

use Error;
use Exception;

definirClasse('SMTEMAIL');
class SMTEMAIL_{

    /**
     * Descrição dos itens possíveis de criação do e-mail.
     *
     * @C007_Id      -> Obrigatório       int
     * @destinatario -> Obrigatório       Array(String) ou String(Unico)
     * @cc           -> Não Obrigatório   Array(String) ou String(Unico)
     * @assunto      -> Obrigatório       String
     * @corpo        -> Não Obrigatório   String
     * @anexo        -> Não Obrigatório   Array(String/Path) 
     * 
     */
    public function geraEmail($arrayDadosEmail) {
        try{
            $validaDados = $this->validaDadosEmail($arrayDadosEmail);

            if ($validaDados != true) {
                return $validaDados;
            } else {
                $geraEmail = $this->enviaEmail($arrayDadosEmail);
            }

            return $geraEmail;
        }catch(Exception $e){
            log('Este é o log de erro da execução do e-mail:' . $e);
            return $e;
        }
    }

    private function validaDadosEmail($arrayDadosEmail) {
        $C007_Id = $arrayDadosEmail['C007_Id'];

        if (!isset($arrayDadosEmail['destinatario']) || empty($arrayDadosEmail['destinatario'])) {
            return "Campo Destinatario vazio ou não informado";
        }

        if (!isset($arrayDadosEmail['anexo']) && !isset($arrayDadosEmail['corpo'])) {
            return "E-mail sem corpo e sem anexo!";
        }

        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}'");
        $C007 = mysql_fetch_assoc($C007);

        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Usuário está com as configurações de E-mail incompletas.";
        }

        return true;
    }

    private function enviaEmail($arrayDadosEmail) {
        $C007_Id = $arrayDadosEmail['C007_Id'];
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}'");
        $C007 = mysql_fetch_assoc($C007);

        log('Iniciando motangem do e-mail e disparo');

        require_once("bibliotecas/phpmailer/class.phpmailer.php");
        $mail = new \phpmailer;

        // Mail
        $mail->CharSet  = 'UTF-8';
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = "=?utf-8?B?" . base64_encode($arrayDadosEmail['assunto']) . "?=";
        $mail->Body     = $arrayDadosEmail['corpo'];
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        $mail->IsSMTP();

        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com") {
            $mail->SMTPSecure = "ssl";
            $mail->Port       = "465";
        }

        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com") {
            $mail->SMTPSecure = "ssl";
        }

        if (!empty($C007['C007_Email_SMTP_Porta'])) {
            $mail->Port = $C007['C007_Email_SMTP_Porta'];
        }

        switch ($C007['C007_Email_SMTP_Seguranca']) {
            case 1:
                $mail->SMTPSecure   = "ssl";
                $mail->SMTPSecurity = 'ssl';
                break;
            case 2:
                $mail->SMTPSecure   = "tls";
                $mail->SMTPSecurity = 'tls';
                break;
        }

        //Adição do/dos Destinatários
        if (is_array($arrayDadosEmail['destinatario'])) {
            foreach ($arrayDadosEmail['destinatario'] as $email) {
                $mail->AddAddress($email);
            }
        } else {
            $destinatario = $arrayDadosEmail['destinatario'];
            $destinatario = strtolower($destinatario);
            $mail->AddAddress($destinatario);
        }

        //Adição da/das cópias a serem enviadas
        if (is_array($arrayDadosEmail['cc'])) {
            foreach ($arrayDadosEmail['cc'] as $email) {
                $mail->AddCC($email);
            }
        } else {
            $cc = $arrayDadosEmail['cc'];
            if (!empty($cc)) {
                $cc = strtolower($cc);
                $mail->AddCC($cc);
            }
        }

        if ((isset($arrayDadosEmail['anexo'])) && (!empty($arrayDadosEmail['anexo']))) {
            $initPath = '/var/www/html/hardness3/';
            if (is_array($arrayDadosEmail['anexo'])) {
                foreach ($arrayDadosEmail['anexo'] as $path) {
                    // $path = $initPath .= $path;
                    $mail->AddAttachment($path);
                }
            } else {
                $mail->AddAttachment($arrayDadosEmail['anexo']);
            }
        }

        log('Dados do serviço de email: ' . json_encode($mail));

        if($mail->send()) {
            log('Envio de e-mail foi concluido!');
            return "E-mail enviado";
        } else {
            log('Falha ao realizar o envio de e-mail.');
            log(json_encode($mail->ErrorInfo));
            return $mail->ErrorInfo;
        }

    }

}