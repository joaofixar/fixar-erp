<?php
namespace hardness;
definirClasse('CAD030');
class CAD030_
{
    /**
     * verificaDuplicidade
     *
     * @param Form $form
     * @return String/bool
     */
    public function verificaDuplicidade($form)
    {
        // Usuários
        $C019_C007_Id = $form->campoValorEnviado('C019_C007_Id');
        
        // Empresa
        $C019_C004_Id = $form->campoValorEnviado('C019_C004_Id');
        
        // Perfil
        $C019_C021_Id = $form->campoValorEnviado('C019_C021_Id');
        
        $sql   = "SELECT * FROM C019 WHERE C019_C007_Id='$C019_C007_Id' AND C019_C004_Id='$C019_C004_Id' AND C019_C021_Id='$C019_C021_Id'";
        $count = mysql_query($sql);
        if (mysql_num_rows($count) > 0) {
            return "Registro já incluído";
        } else {
            $sql   = "SELECT * FROM C019 WHERE C019_C007_Id='$C019_C007_Id' AND C019_C004_Id='$C019_C004_Id'";
            $count = mysql_query($sql);
            if (mysql_num_rows($count) > 0) {
                $sql    = "SELECT C007_Primeiro_Nome, C004_Nome_Empresa FROM C007 LEFT JOIN C004 ON C004_Id=$C019_C004_Id WHERE C007_Id='{$C019_C007_Id}'";
                $result = mysql_query($sql);
                $linha  = mysql_fetch_array($result);
                return <<<EOT
					O usuário "{$linha['C007_Primeiro_Nome']}" já possui um perfil cadastrado para a empresa "{$linha['C004_Nome_Empresa']}"
EOT;
            } else {
                return true;
            }
        }
        return true;
    }

    public function emailsPerfil($IdPeril, $idEmpresa) {
        if ($IdPeril == 0) {
            return '';
        }

        $emails = '';

        $sql  = " SELECT DISTINCT(C007.C007_Email) AS EMAIL ";
        $sql .=   " FROM C007 ";
        $sql .=        " LEFT JOIN C044 ON C044.C044_Id      = {$IdPeril} ";
        $sql .=        " LEFT JOIN D202 ON D202.D202_C044_Id = C044.C044_Id ";
        $sql .=        " LEFT JOIN C019 ON C019.C019_C004_Id = {$idEmpresa} ";
        $sql .=  " WHERE ((C007.C007_Id = D202.D202_C007_Id) ";
        $sql .=      " OR (C007.C007_Id      = C019.C019_C007_Id ";
        $sql .=      " AND C019.C019_C021_Id = D202.D202_C021_Id)) ";
        $query = mysql_query($sql);

        while ($dados = mysql_fetch_array($query)) {
            $emails += !empty($emails) ? ";" : "";
            $emails += $dados['EMAIL'];
        }

        return $emails;
    }

    public function testarEmail($C007_Id, $emailDestino = '') {
        $C007 = $this->testarEmailBuscarConfiguracao($C007_Id);
        if (empty($C007)) {
            return array('code' => false, 'data' => 'Usuario nao encontrado.');
        }

        $emailRemetente = !empty($C007['C007_Email']) ? trim($C007['C007_Email']) : trim($C007['C007_Email_SMTP_Usuario']);
        if (empty($emailDestino)) {
            $emailDestino = $emailRemetente;
        }

        if (empty($C007['C007_Email_SMTP_Servidor']) || empty($emailRemetente) || empty($emailDestino)) {
            return array('code' => false, 'data' => 'Preencha o e-mail, servidor SMTP e um destinatario para testar o envio.');
        }

        $destinatarios = $this->testarEmailDestinatarios($emailDestino);
        if (empty($destinatarios)) {
            return array('code' => false, 'data' => 'Informe um e-mail de destino valido para o teste.');
        }

        return $this->testarEmailEnviarSmtp($C007, $emailRemetente, $destinatarios);
    }

    private function testarEmailBuscarConfiguracao($C007_Id) {
        $C007_Id = mysql_real_escape_string($C007_Id);
        $sql = "SELECT C007_Email, C007_Primeiro_Nome, C007_Email_SMTP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_SMTP_Porta, C007_Email_SMTP_Autenticacao, C007_Email_SMTP_Seguranca FROM C007 WHERE C007_Id = '{$C007_Id}';";
        $resultado = mysql_query($sql);
        return mysql_fetch_assoc($resultado);
    }

    private function testarEmailSeguranca($valor, $porta) {
        $valor = strtolower(trim((string) $valor));
        if ($valor == '1' || $valor == 'ssl') {
            return 'ssl';
        }
        if ($valor == '2' || $valor == 'tls') {
            return 'tls';
        }
        $porta = (string) ((int) $porta);
        if ($porta == '465') {
            return 'ssl';
        }
        if ($porta == '587') {
            return 'tls';
        }
        return '';
    }

    private function testarEmailLimparErro($erro) {
        $erro = strip_tags((string) $erro);
        $erro = str_replace(array("\r", "\n", "\t"), ' ', $erro);
        return trim(preg_replace('/\s+/', ' ', $erro));
    }

    private function testarEmailDestinatarios($emailDestino) {
        $destinatarios = preg_split("/[;,]+/", $emailDestino);
        $destinatariosEnviados = array();
        foreach ($destinatarios as $email) {
            $email = trim($email);
            if (empty($email)) {
                continue;
            }
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $email = trim($email);
            if (empty($email)) {
                continue;
            }
            $destinatariosEnviados[] = $email;
        }
        return $destinatariosEnviados;
    }

    private function testarEmailEnviarSmtp($C007, $emailRemetente, $destinatarios) {
        require_once('bibliotecas/phpmailer/class.phpmailer.php');

        $mail = new \phpmailer;
        $mail->CharSet = 'utf-8';
        $mail->SetLanguage('br');
        $mail->IsHTML(true);
        $mail->IsSMTP();
        $mail->Timeout = 20;
        $mail->Host = trim($C007['C007_Email_SMTP_Servidor']);
        $mail->From = $emailRemetente;
        $mail->FromName = !empty($C007['C007_Primeiro_Nome']) ? $C007['C007_Primeiro_Nome'] : $emailRemetente;
        $mail->Subject = '=?UTF-8?B?'.base64_encode('Teste de envio SMTP').'?=';
        $mail->Body = '<p>Este e um e-mail de teste do cadastro de usuario.</p>'
            . '<p><b>Data:</b> ' . date('d/m/Y H:i:s') . '<br />'
            . '<b>Servidor SMTP:</b> ' . htmlspecialchars($mail->Host, ENT_COMPAT, 'UTF-8') . '<br />'
            . '<b>Porta:</b> ' . htmlspecialchars((string) $C007['C007_Email_SMTP_Porta'], ENT_COMPAT, 'UTF-8') . '</p>';
        $mail->AltBody = 'Este e um e-mail de teste do cadastro de usuario. Data: ' . date('d/m/Y H:i:s');
        $autenticacao = strtolower(trim((string) $C007['C007_Email_SMTP_Autenticacao']));
        $mail->SMTPAuth = in_array($autenticacao, array('1', 's', 'sim', 'true', 'y', 'yes'), true);
        $mail->Username = trim((string) $C007['C007_Email_SMTP_Usuario']);
        $mail->Password = (string) $C007['C007_Email_SMTP_Senha'];

        if (!empty($C007['C007_Email_SMTP_Porta'])) {
            $mail->Port = (int) $C007['C007_Email_SMTP_Porta'];
        }

        $seguranca = $this->testarEmailSeguranca($C007['C007_Email_SMTP_Seguranca'], $mail->Port);
        if (!empty($seguranca)) {
            $mail->SMTPSecure = $seguranca;
        }

        foreach ($destinatarios as $email) {
            $mail->AddAddress($email);
        }

        log('Teste SMTP cad030: usuario=' . $C007['C007_Email'] . ' servidor=' . $mail->Host . ' porta=' . $mail->Port . ' destino=' . implode(',', $destinatarios));

        if ($mail->Send()) {
            return array('code' => true, 'data' => 'E-mail de teste enviado com sucesso para: ' . implode(', ', $destinatarios));
        }

        $erro = $this->testarEmailLimparErro($mail->ErrorInfo);
        log('Erro teste SMTP cad030: ' . $erro);

        return array(
            'code' => false,
            'data' => 'Nao foi possivel enviar o e-mail de teste. Servidor: ' . $mail->Host . ' Porta: ' . $mail->Port . (!empty($mail->SMTPSecure) ? ' Seguranca: ' . strtoupper($mail->SMTPSecure) : '') . (!empty($erro) ? ' Erro: ' . $erro : '')
        );
    }

}
