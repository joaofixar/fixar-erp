<?php
namespace hardness;
definirClasse('B2B001');
class B2B001_
{
    public function B2BGerarPedido($informacoesExtras = false) 
	{
       global $g;
       
        require_once("bibliotecas/classes/VEN014.php");
        $VEN014 = new VEN014();

        $T269 = mysql_query("select * from T269 where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
        $D024 = array();    
        $T005 = array();
        $T006 = array();
        $precoTotal = 0;

        $D024['D024_Id'] = $g['clienteAtual'];
        if($g['clienteAtualContato']!="99999"){
            $D024['D013_Id'] = $g['clienteAtualContato'];
        }else{
            $D024['D013_Id'] = "";
        }
        
        //cabeçalho do pedido
        $T005["T005_Id_Pedido_Externo"]='B2B';
        $T005["T005_Ordem_Compra"]='B2B';
        $T005['C007_Id']=$g['clienteAtualVendedor'];
        $T005['C007_Vendedor_Externo']=$g['clienteAtualVendedorExterno'];
        $T005['T005_D021_Id']=$g['clienteAtualRepresentante'];
        $T005['T005_Observacao_Comercial'] = "Observação B2B - ".$informacoesExtras['observacaoComercial'];
                
        $count=0;
        while ($mT269 = mysql_fetch_array($T269)){
            //itens do pedido
            $T006["D009_Id"] = $mT269['T269_D009_Id'];
            $T006["T006_Quantidade"]=$mT269['T269_Quantidade'];              
            $T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$mT269['T269_Preco'];
            $T006["T006_Valor_Total_Preco_Sem_Desconto"]=$mT269['T269_Preco_Total'];
            $T006["T006_Valor_Preco_Original"]=$mT269['T269_Preco'];
            $precoTotal+=$mT269['T269_Preco_Total'];
            $produtos[] = $T006;
        }

        $T005['D024'] = $D024;
        $T005['T006'] = $produtos;


        // Recortar a transportadora da observação comercial
        $frete = stristr($informacoesExtras['observacaoComercial'],"Frete:"); 

        if (!empty($frete)){

            $inicioFrete = strpos($frete,":")+2;
            $finalFrete = strpos($frete,".")-$inicioFrete;
            $frete = substr($frete,$inicioFrete,$finalFrete);  

            $D022 = mysql_query("select D022_Id from D022 where D022_Descricao_Ecommerce like '%$frete%'");
            $mD022 = mysql_fetch_array($D022);
    
            $T005['T005_D022_Id'] = $mD022['D022_Id'];

            log("If B2B001=". $mD022['D022_Id']); 
            

        } else {

            $D022 = mysql_query("select T005_Id,T005_D022_Id from T005 where T005_D024_Id='{$D024['D024_Id']}' order by T005_Id desc limit 1");
            $mD022 = mysql_fetch_array($D022);

            $T005['T005_D022_Id'] = $mD022['T005_D022_Id'];

            log("B2B001 - D022_Id " . $mD022['T005_D022_Id']);

        }

        //Gerando parcelas de acordo com o digitado pelo usuario nas observações
        //O padrão para digitar é Pagamento:parcelas separado por ,(virgula) finalizar com .(ponto)
        //Ex: Pagamento:10,20,30.
        $parcelas = $this->B2BGerarParcelas($informacoesExtras['observacaoComercial'],$precoTotal,"T090");
        if($parcelas!==false){
            $T005['T090'] = $parcelas;
        }

        $T005_Id = $VEN014->cadastrarPedido($T005);  
        $result = array();
                    
        if($T005_Id === false){
            $result[0] = false;//pedido não enviado
            $result[1] = false;// se email não será enviado
            $result[2] = $T005_Id;
        }else{
            $result[0] = true;//pedido enviado
            $result[1] = $this->EnviarEmailPedido($T005_Id,"",$g['clienteAtualContato']); 
            $result[2] = $T005_Id;
        }       
        return $result;
    }

    public function B2BGerarParcelas($observacaoComercial,$valorTotal,$tabela){
        global $g;
        $pagamento = array();
        $parcela = array();
        $total_parcelas = 0;

        $observacao = stristr($observacaoComercial,"Pagamento:"); 
        if(empty($observacao)){
            return false;
        }

        $inicio = strpos($observacao,":")+1;
        $final = strpos($observacao,".")-$inicio;
        $observacao = substr($observacao,$inicio,$final);

        $parcelas = explode(",",$observacao);
        $numParcelas = count($parcelas);
        $valorParcela = number_format($valorTotal/$numParcelas,2,'.','');
    
        for ($i=1; $i <= $numParcelas ; $i++) { 
            $dadosParcela = explode("-",$parcelas[$i-1]);
            $prazo_dias = $dadosParcela[0];
            $descricaoPortador = $dadosParcela[1];

            if(!empty($descricaoPortador)){
                $sqlD027 = mysql_query("SELECT D027_Id,D027_Descricao_B2B FROM D027 WHERE D027_Descricao_B2B LIKE '%".$descricaoPortador."%'");
                if(mysql_num_rows($sqlD027) > 0){
                    $rowD027 = mysql_fetch_assoc($sqlD027);
                    $D027_Id = $rowD027['D027_Id'];
                }else{
                    $D027_Id="";
                }
            }
            
            $data_vencimento = date('Y-m-d', strtotime("+{$prazo_dias} days"));
            $parcela[$tabela."_Numero_Parcela"]=$i;
            $parcela[$tabela."_Data_Vencimento"]=$data_vencimento;
            $parcela[$tabela."_Valor_Parcela"]=$valorParcela;
            $parcela[$tabela."_Prazos_Dias"]=$prazo_dias;
            $parcela[$tabela."_D027_Id"]=$D027_Id;
            $total_parcelas += $parcela[$tabela."_Valor_Parcela"]; 
            $pagamento[] = $parcela;
        }

        $diferenca_parcelas = round($valorTotal - $total_parcelas,2);
        if($diferenca_parcelas<0){
            $pagamento[0][$tabela."_Valor_Parcela"] = $pagamento[0][$tabela."_Valor_Parcela"] - abs($diferenca_parcelas);
        }else{
            $pagamento[0][$tabela."_Valor_Parcela"] = $pagamento[0][$tabela."_Valor_Parcela"] + abs($diferenca_parcelas);
        }
       
        return $pagamento;
    }

    public function B2BGerarOrcamento($informacoesExtras = false)
	{
       global $g;
       
        require_once("bibliotecas/classes/VEN014.php");
        $VEN014 = new VEN014();

        $T269 = mysql_query("select * from T269 where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
        $D024 = array();    
        $T003 = array();
        $T004 = array();

        $D024['D024_Id'] = $g['clienteAtual'];
        if($g['clienteAtualContato']!="99999"){
            $D024['D013_Id'] = $g['clienteAtualContato'];
        }else{
            $D024['D013_Id'] = "";
        }

        //cabeçalho do pedido
        $T003["T003_Id_Pedido_Externo"]='B2B';
        $T003['C007_Id']=$g['clienteAtualVendedor'];
        $T003['T003_D021_Id']=$g['clienteAtualRepresentante'];
        $T003['C007_Vendedor_Externo']=$g['clienteAtualVendedorExterno'];
        $T003['T003_Observacao_Comercial'] = "Observação B2B - ".$informacoesExtras['observacaoComercial'];
    
        while ($mT269 = mysql_fetch_array($T269)){
                            
            //itens do pedido
            $T004["D009_Id"] = $mT269['T269_D009_Id'];
            $T004["T004_Valor_Preco_Sem_Desconto_Unitario"] = $mT269['T269_Preco'];
            $T004["T004_Valor_Preco_Sem_Desconto"] = $mT269['T269_Preco'];
            $T004["T004_Valor_Total_Preco_Sem_Desconto"]=$mT269['T269_Preco_Total'];
            $T004["T004_Quantidade"]=$mT269['T269_Quantidade'];
    
            $produtos[] = $T004;

        }

        $T003['D024'] = $D024;
        $T003['T004'] = $produtos;
        
        $T003_Id = $VEN014->cadastrarOrcamento($T003);  
        $result = array();
                    
        if($T005_Id === false){
            $result[0] = false;//pedido não enviado
            $result[1] = false;// se email não será enviado 
        }else{
            $result[0] = true;//pedido enviado
            $result[1] = $this->EnviarEmailPedido("",$T003_Id,$g['clienteAtualContato']);   
        }       
        return $result;   
    }
 
    public function EnviarEmailPedido($T005_Id,$T003_Id,$D013_Id){

        global $g,$confUsuario;

        $sqlEmailUsuario = mysql_query("SELECT D024_C004_Id,
                                               D024_Nome_Empresa,
                                               D013_E_Mail, 
                                               D013_Id,
                                               D013_Senha,
                                               D013_Nome_Contato,
                                               C007_Email,
                                               C007_Primeiro_Nome,
                                               C007_Email_SMTP_Servidor,
                                               C007_Email_SMTP_Autenticacao,
                                               C007_Email_SMTP_Usuario,
                                               C007_Email_SMTP_Senha
                                            FROM D024
                                       LEFT JOIN D013 ON D013_D024_Id=D024_Id
                                        LEFT JOIN C007 on C007_Id = D024_C007_Vendedor_Interno   
                                           WHERE D013_Id='{$D013_Id}' limit 1");
        
        $rowEmailUsuario = mysql_fetch_array($sqlEmailUsuario);
        
        $erro = mysql_error();
        if(!empty($erro)){
            die($erro);
        }

        if($confUsuario['dbDatabase']=='e212bkp'){
            $sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id=4";
        }else{
            $sql = "SELECT C004_Nome_Abreviado FROM C004 ORDER BY C004_Id ASC LIMIT 1";
        }

        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
         
        $logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['empresaAtual']}.jpg";
        $row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 30);
        
        if($T005_Id>0){

            $cClausula=mysql_query("Select * from T005 where T005_Id='{$T005_Id}'");
            $rT005 = mysql_fetch_array($cClausula);
            $Data_Emissao = gCorrigeData($rT005['T005_Data_Emissao']);
            $Valor_Total = gCorrigeNumero($rT005['T005_Valor_Total']);

            $corpoEmail = <<<EOT
            <!DOCTYPE html>
                <head>
                    <meta charset="UTF-8">
                    <title></title>         
                    
                    <style>                             
                        .container{                     
                            width: 600px;
                            text-align: center;
                            padding:20px 40px;               
                            height:300px;
                            align-content: center;
                            background-color:#FFFAFA;
                        }
                        p, h2 {
                            font-family:arial;
                            color:#000;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div style="text-align:left;margin-left:50px;margin-top:20px">
                            <img src="{$logo}" style="width: 150px; height: 50px;" />
                        </div>
                        <div style="width:600px; margin-left:50px;text-align:left;">
                            <h2>PEDIDO</h2>
                            <p>Seu pedido de Nº {$rT005['T005_Id']} foi recebido com sucesso!</p>
                            <p>Em breve o vendedor da sua região entrará em contato para dar andamento no faturamento.</p>
                            
                            <p>Segue abaixo alguns dados:</p>
                            <p>
                            EMPRESA: {$row['C004_Nome_Abreviado']}<br/>
                            EMISSÃO: {$Data_Emissao}<br/>
                            VALOR TOTAL: R$ {$Valor_Total}<br/>
                            <p>Esse e-mail foi enviado por um sistema automático e não poderá ser respondido.</p>   
                            <br/><br/>          
                        </div>          
                    </div>
                </body>
            <html>  
EOT;
        $assunto = "Confirmação de pedido - Nº ".$T005_Id;

        }else if($T003_Id>0){
            $cClausula=mysql_query("Select * from T003 where T003_Id='{$T003_Id}'");
            $rT003 = mysql_fetch_array($cClausula);

            $Data_Emissao = gCorrigeData($rT003['T003_Data_Emissao']);
            $Valor_Total = gCorrigeNumero($rT003['T003_Valor_Total']);

            $corpoEmail = <<<EOT
            <!DOCTYPE html>
                <head>
                    <meta charset="UTF-8">
                    <title></title>         
                    
                    <style>                             
                        .container{                     
                            width: 600px;
                            text-align: center;
                            padding:20px 40px;               
                            height:300px;
                            align-content: center;
                            background-color:#FFFAFA;
                        }
                        p, h2 {
                            font-family:arial;
                            color:#000;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div style="text-align:left;margin-left:50px;margin-top:20px">
                            <img src="{$logo}" style="width: 150px; height: 50px;" />
                        </div>
                        <div style="width:600px; margin-left:50px;text-align:left;">
                            <h2>ORÇAMENTO</h2>
                            <p>Seu orçamento de Nº {$rT003['T003_Id']} foi recebido com sucesso!</p>
                            <p>Em breve o vendedor da sua região entrará em contato para dar andamento no faturamento.</p> 
                            <p>Segue abaixo alguns dados:</p>
                            <p>
                            EMPRESA: {$row['C004_Nome_Abreviado']}<br/>
                            EMISSÃO: {$Data_Emissao}<br/>
                            VALOR TOTAL: R$ {$Valor_Total}<br/>
                            <p>Esse e-mail foi enviado por um sistema automático e não poderá ser respondido.</p>   
                            <br/><br/>          
                        </div>          
                    </div>
                </body>
            <html>  
EOT;
        $assunto = "Confirmação de orçamento - Nº ".$T003_Id;
        }
        
        if(empty($rowEmailUsuario['C007_Email'] || empty($rowEmailUsuario['C007_Email_SMTP_Senha'] || empty($rowEmailUsuario['C007_Email_SMTP_Usuario'])))){
            return false;
        }else{

            require_once("bibliotecas/phpmailer/class.phpmailer.php");
            $mail           = new \phpmailer;
            // Mail
            $mail->From     = $rowEmailUsuario['C007_Email'];
            $mail->FromName = $rowEmailUsuario['C007_Primeiro_Nome'];
            $mail->Host     = $rowEmailUsuario['C007_Email_SMTP_Servidor'];
            $mail->Mailer   = "smtp";
            $mail->SMTPAuth = $rowEmailUsuario['C007_Email_SMTP_Autenticacao'];
            $mail->Username = $rowEmailUsuario['C007_Email_SMTP_Usuario'];
            $mail->Password = $rowEmailUsuario['C007_Email_SMTP_Senha'];
            $mail->Subject  = '=?UTF-8?B?'.base64_encode($assunto).'?=';
            $mail->Body     =  $corpoEmail;
            $mail->AltBody  = "ALT BODY";
            $mail->CharSet  = 'utf-8';
            $mail->SetLanguage("br");
            $mail->IsHTML(true);
            if ($rowEmailUsuario['C007_Email_SMTP_Servidor'] == "smtp.gmail.com") {
                $mail->SMTPSecure = "ssl";
                $mail->Port       = "465";
            }
            if ($rowEmailUsuario['C007_Email_SMTP_Servidor'] == "smtp.live.com") {
                $mail->SMTPSecure = "ssl";
            }
            if (!empty($rowEmailUsuario['C007_Email_SMTP_Porta'])) {
                $mail->Port = $rowEmailUsuario['C007_Email_SMTP_Porta'];
            }
           
            $monta_['Para'] = $rowEmailUsuario['D013_E_Mail'];
            $monta_['cc']   = '';
            $monta_['Bcc']  = $rowEmailUsuario['C007_Email'];
            
        
            $todosEmailsEnviados = array();
            $mPara               = preg_split("/[;,]+/", $monta_['Para']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddAddress($email);
            }
            if (isset($monta_['cc'])) {
                $mPara = preg_split("/[;,]+/", $monta_['cc']);
                for ($x = 0; $x < count($mPara); $x++) {
                    $email = $mPara[$x];
                    if (strpos($email, '<') !== false) {
                        $email = explode('<', $email);
                        $email = explode('>', $email[1]);
                        $email = $email[0];
                    }
                    $todosEmailsEnviados[] = strtolower(trim($email));
                    $mail->AddCC($email);
                }
            }
            if (isset($monta_['Bcc'])) {
                $mPara = preg_split("/[;,]+/", $monta_['Bcc']);
                for ($x = 0; $x < count($mPara); $x++) {
                    $email = $mPara[$x];
                    if (strpos($email, '<') !== false) {
                        $email = explode('<', $email);
                        $email = explode('>', $email[1]);
                        $email = $email[0];
                    }
                    $todosEmailsEnviados[] = strtolower(trim($email));
                    $mail->AddBCC($email);
                }
            }
            
            $anexosGravar   = array();
            $T139_Estrutura = array(
                'anexosManual' => array()
            );
            if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    $anexosGravar[]                   = $path;
                    $T139_Estrutura['anexosManual'][] = basename($path);
                    $mail->AddAttachment($path, basename($path));
                }
            }
            
            if ($mail->Send()) {
                return true;
            }else{
                return false;
            }
        }    
    }

    /**
     * montarEmailLink
     *
     * @param int $T218_Id
     */
    public function montarEmailSenhaB2B($D013_Id)
    {
        log("entrando na função do email");
        global $g, $confUsuario;
        
        $D013_Id  = mysql_real_escape_string($D013_Id);
        $sql      = "SELECT * FROM D013 
                         LEFT JOIN D024 ON D024_Id=D013_D024_Id
                         LEFT JOIN D179 ON D179_Login=D013_E_Mail 
                             WHERE D013_Id='{$D013_Id}'";
        $Consulta = mysql_query($sql);
        $Consulta = mysql_fetch_array($Consulta);
    
        $D024_Id   = $Consulta['D024_Id'];
        $parametro = base64_encode($g['empresaAtual'] . '|' . $D013_Id . '|' . $D024_Id);
        
        $sql          = "select C004_Nome_Empresa,C004_Site,C004_Logo_Marca,C004_Nome_Abreviado,C004_Imagem_Celular_B2B,C004_Logo_Marca_B2B from C004 where C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        }
        
        $array['Para']    = $Consulta['D013_E_Mail'];
        $array['Assunto'] = "Dados acesso área cliente ".$empresaAtual['C004_Nome_Abreviado'];
        $array['Corpo']   = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        
        $array['Corpo'] .= <<<EOT

        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title></title>         
                
                <style>                             
                    .container{                     
                        width: 400px;
                        text-align: left;
                        padding:20px 40px;               
                        height:180px;
                        align-content: center;
                    }
                    p, h2 {
                        font-family:arial;
                        color:#000;
                    }
                </style>
            </head>
            <body>
                <img style="margin-left:70px;" src="http://{$_SERVER['HTTP_HOST']}/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$empresaAtual['C004_Logo_Marca_B2B']}">
                <div class="container">

                    <span style="font-family:arial,verdana;font-size:14px;">
                        Segue login e senha para acessar sua área de cliente da empresa {$empresaAtual['C004_Nome_Abreviado']}<br /><br />
                        Email:{$Consulta['D179_Login']}<br/>
                        Senha:{$Consulta['D179_Senha']}
                    </span>      
                </div>
            </body>
        <html>              
EOT;
        return $array;
    }

} 