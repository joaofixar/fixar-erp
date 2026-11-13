<?php
namespace hardness;

definirClasse('ETC006');
class ETC006_
{
    /**
     * processarWhitelist
     */
    public function processarWhitelist()
    {
        global $g;
        include_once('configuracoes/_dominiosPublicos.php');

        $whitelist = array();
        $addWhitelistMail = array();
        $addWhitelistDominio = array();
        $sqlT258 = mysql_query("SELECT * FROM T258");
        while($rowT258 = mysql_fetch_assoc($sqlT258)){
            $whitelist[] = $rowT258['T258_Registro'];
        }

        $checkList = array();

        //Para os emails dos usuarios do sistema, vinculamos ao D024 da empresa 1
        $sqlC004 = mysql_query("SELECT C004_D024_Id, C004_Cnpj FROM C004 WHERE C004_Id = '1'");
        $rowC004 = mysql_fetch_assoc($sqlC004);
        if(!empty($rowC004['C004_D024_Id'])){
            $C004_D024_Id = $rowC004['C004_D024_Id'];
        } else {
            $sqlD024 = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Cnpj = '{$rowC004['C004_Cnpj']}'");
            $rowD024 = mysql_fetch_assoc($sqlD024);
            if(!empty($rowD024['D024_Id'])){
                $C004_D024_Id = $rowD024['D024_Id'];
            } else {
                $C004_D024_Id = '9999999999'; //Caso não seja encontrado um D024, atribuimos o valor mais alto para corrigir os registros D085 posteriormente
            }
        }

        // Adiciona os emails do C007 ao checklist
        $sqlC007 = mysql_query("SELECT IFNULL(C007_Email, '') C007_Email FROM C007");
        while($rowC007 = mysql_fetch_assoc($sqlC007)){
            $email = '';
            if(!empty($rowC007['C007_Email'])){
                $email .= strtoupper(str_replace(' ', '', $rowC007['C007_Email'])) . ';';
            }
            $email = trim($email, ';');
            if($email == '') { continue; }

            $email = str_replace(array('/',',','|'), ';', $email);
            if(strpos($email, ';') !== false){
                $emails = explode(";", $email);
                foreach ($emails as $email) {
                    if(rRegx('email', $email) === true && !isset($checkList[$email])){
                        $checkList[$email] = array($C004_D024_Id, '0');
                    }
                }
            } else {
                if(rRegx('email', $email) === true && !isset($checkList[$email])){
                    $checkList[$email] = array($C004_D024_Id, '0');
                }
            }
        }

        // Adiciona os emails do D024 ao checklist
        $sqlD024 = mysql_query("SELECT D024_Id, IFNULL(D024_E_Mail,'') D024_E_Mail, IFNULL(D024_Cobranca_Email,'') D024_Cobranca_Email, IFNULL(D024_Entrega_Email,'') D024_Entrega_Email, IFNULL(D024_Mail_NFe,'') D024_Mail_NFe, IFNULL(D024_E_Mail_Nota,'') D024_E_Mail_Nota, IFNULL(D024_E_Mail_Boleto,'') D024_E_Mail_Boleto FROM D024");
        while($rowD024 = mysql_fetch_assoc($sqlD024)){
            $email = '';
            if(!empty($rowD024['D024_E_Mail'])){
                $email .= strtoupper(str_replace(' ', '', $rowD024['D024_E_Mail'])) . ';';
            }
            if(!empty($rowD024['D024_Cobranca_Email'])){
                $email .= strtoupper(str_replace(' ', '', $rowD024['D024_Cobranca_Email'])) . ';';
            }
            if(!empty($rowD024['D024_Entrega_Email'])){
                $email .= strtoupper(str_replace(' ', '', $rowD024['D024_Entrega_Email'])) . ';';
            }
            if(!empty($rowD024['D024_Mail_NFe'])){
                $email .= strtoupper(str_replace(' ', '', $rowD024['D024_Mail_NFe'])) . ';';
            }
            if(!empty($rowD024['D024_E_Mail_Nota'])){
                $email .= strtoupper(str_replace(' ', '', $rowD024['D024_E_Mail_Nota'])) . ';';
            }
            if(!empty($rowD024['D024_E_Mail_Boleto'])){
                $email .= strtoupper(str_replace(' ', '', $rowD024['D024_E_Mail_Boleto'])) . ';';
            }
            $email = trim($email, ';');
            if($email == '') { continue; }

            $email = str_replace(array('/',',','|'), ';', $email);
            if(strpos($email, ';') !== false){
                $emails = explode(";", $email);
                foreach ($emails as $email) {
                    if(rRegx('email', $email) === true && !isset($checkList[$email])){
                        $checkList[$email] = array($rowD024['D024_Id'], '0');
                    }
                }
            } else {
                if(rRegx('email', $email) === true && !isset($checkList[$email])){
                    $checkList[$email] = array($rowD024['D024_Id'], '0');
                }
            }
        }

        // Adiciona os emails do D013 ao checklist
        $sqlD013 = mysql_query("SELECT D013_Id, D013_E_Mail, D013_D024_Id FROM D013");
        while($rowD013 = mysql_fetch_assoc($sqlD013)){
            if(!empty($rowD013['D013_E_Mail'])){
                $email = strtoupper(str_replace(' ', '', $rowD013['D013_E_Mail']));
            }

            $email = str_replace(array('/',',','|'), ';', $email);
            if(strpos($email, ';') !== false){
                $emails = explode(";", $email);
                foreach ($emails as $email) {
                    if(rRegx('email', $email) === true && !isset($checkList[$email])){
                        $checkList[$email] = array($rowD013['D013_D024_Id'], $rowD013['D013_Id']);
                    }
                }
            } else {
                if(rRegx('email', $email) === true && !isset($checkList[$email])){
                    $checkList[$email] = array($rowD013['D013_D024_Id'], $rowD013['D013_Id']);
                }
            }
        }
        //Verifica se o email já está na whitelist, caso não esteja verifica se o dominio deve ser incluido também (verifica dominio publico)
        foreach($checkList as $email => $info){
            if(!in_array($email, $whitelist) && !isset($addWhitelistMail[$email])){
                $addWhitelistMail[$email] = $info;
            }
            $dominio = explode("@", $email);
            $dominio = $dominio[1];
            if(!in_array($dominio, $dominiosPublicos) && !in_array($dominio, $whitelist) && !isset($addWhitelistDominio[$dominio])){
                $addWhitelistDominio[$dominio] = $info;
            }
        }

        foreach ($addWhitelistMail as $email => $info) {
            mysqli_query("INSERT INTO T258 (T258_Registro, T258_Tipo, T258_D024_Id, T258_D013_Id) VALUES ('{$email}', 'E', '{$info[0]}', '{$info[1]}')");
        }
        foreach ($addWhitelistDominio as $dominio => $info) {
            mysqli_query("INSERT INTO T258 (T258_Registro, T258_Tipo, T258_D024_Id, T258_D013_Id) VALUES ('{$dominio}', 'D', '{$info[0]}', '{$info[1]}')");
        }

        return true;
    }

    /**
     * removerEmailSpam
     * @param $T139_Id string
     * @param $D024_Id string
     * @param $registrarEmail bool
     */
    public function removerEmailSpam($T139_Id, $D024_Id, $D013_Id, $registrarEmail = false)
    {
        global $g;
        include_once('configuracoes/_dominiosPublicos.php');

        $sqlT139 = mysql_query("SELECT T139_De FROM T139 WHERE T139_Id = '{$T139_Id}'");
        if(mysql_num_rows($sqlT139) <= 0){
            return false;
        }
        $rowT139 = mysql_fetch_assoc($sqlT139);
        mysql_query("UPDATE T139 SET T139_Flag_Spam = 'N', T139_D024_Id = '{$D024_Id}', T139_D013_Id = '{$D013_Id}' WHERE T139_Id = '{$T139_Id}'");
        $email = $rowT139['T139_De'];

        if (strpos($email, '<') !== false) {
            $email = explode('<', $email);
            $email = explode('>', $email[1]);
            $email = strtolower($email[0]);
        }
        $email = mysql_real_escape_string($email);

        //Whitelist
        $whitelist = array();
        $addWhitelistMail = array();
        $addWhitelistDominio = array();
        $sqlT258 = mysql_query("SELECT * FROM T258");
        while($rowT258 = mysql_fetch_assoc($sqlT258)){
            $whitelist[] = $rowT258['T258_Registro'];
        }

        if(!in_array($email, $whitelist)){
            mysqli_query("INSERT INTO T258 (T258_Registro, T258_Tipo, T258_D024_Id) VALUES ('{$email}', 'E', '{$D024_Id}')");
        }
        $dominio = explode("@", $email);
        $dominio = strtolower($dominio[1]);
        if(!in_array($dominio, $dominiosPublicos) && !in_array($dominio, $whitelist)){
            mysqli_query("INSERT INTO T258 (T258_Registro, T258_Tipo, T258_D024_Id) VALUES ('{$dominio}', 'D', '{$D024_Id}')");
        }

        $sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
        $resultado = mysql_query($sql);
        $C007 = mysql_fetch_assoc($resultado);
        // Pega infos
        require_once('sistema/comunicacaoEmail.php');
        $mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
        $extra = ($registrarEmail) ? "" : "AND T139_Id != '{$T139_Id}'";
        $select = "SELECT T139_Id, T139_D024_Id, T139_Pasta, T139_De, T139_Para, T139_D013_Id, T139_C007_Id, T139_Titulo, T139_Data_Hora FROM T139 WHERE T139_De LIKE '%{$email}%' AND T139_Flag_Spam = 'S' {$extra}";
        $result = mysql_query($select);
        $select = base64_encode($select);
        $mail->associarEmails(false, $select);
        return ($result) ? mysql_num_rows($result) : false;
    }

    /**
     * montarEmailAgenda
     * @param $D085_Id string
     */
    public function montarEmailAgenda($D085_Id)
    {
        $sql = "SELECT D013_E_Mail, D085_Mensagem FROM D085 LEFT JOIN D013 ON D013_Id=D085_D013_Id WHERE D085_Id = {$D085_Id};";
        $retorno = mysql_query($sql);
        $row = mysql_fetch_assoc($retorno);
        $monta = array();
        $monta['Para'] = $row['D013_E_Mail'];
        $monta['Corpo'] = $row['D085_Mensagem'];
        $monta['Corpo'] = '<p><br _moz_dirty="" type="_moz"></p>' . "<br /><hr>" . $monta['Corpo'];
        return $monta;
    }


    function personalizacaoDe($C007_Primeiro_Nome) 
    {
        $return = array();

        $return['De'] = $C007_Primeiro_Nome;

        return $return;
    }
}