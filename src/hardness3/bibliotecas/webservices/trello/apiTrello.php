<?php
namespace hardness;

Class Trello
{

    public function envioDeCard($T234_Id)
    {
        global $g,$confUsuario,$sys;

        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];

        //SELECIONANDO OS DADOS DO CHAMADO
        $sqlT234 = mysql_query("SELECT 
                                   T234_Id,
                                   T234_D024_Id,
                                   T234_Data_Hora_Abertura,
                                   T234_Solicitante,
                                   T234_Descricao,
                                   D024_Nome_Empresa,
                                   T234_Link_Screenshot,
                                   T234_Flag_Tipo_OS,
                                   T272_Mes,
                                   T272_Ano,
                                   T234_T272_Id, 
                                   T234_Flag_Tipo_OS,
                                   D166_T272_Id,
                                   D166_Lista_Analise_Id,
                                   T234_Origem_Chamado,
                                   D166_Id  
                                   FROM T234 
                              LEFT JOIN D024 ON T234_D024_Id=D024_Id 
                              LEFT JOIN T272 ON T234_T272_Id=T272_Id
                              LEFT JOIN D166 ON D166_T272_Id=T272_Id 
                                  WHERE T234_Id='{$T234_Id}'");
        $rT234 = mysql_fetch_array($sqlT234);
        $erro = mysql_error();

        if(!empty($erro)){
            $result[0]=false;
            $result[1]="Erro mysql (1):".$erro;
            return $result;
        }
        
       
        //ID DA LISTA DA CONFIGURAÇÂO NO CRIAR CARD NO TRELLO
        $D167_Id =  $g['C031']['TrelloListaCriarCard'];
        $sqlD167 = mysql_query("SELECT 
                                   D166_Id,
                                   D166_Id_Quadro_Trello,
                                   D167_Id_Lista_Trello,
                                   D166_Lista_Analise_Id,
                                   D166_Flag_Atendimento
                                  FROM D167 
                             LEFT JOIN D166 ON D167_D166_Id=D166_Id 
                                 WHERE D167_Id='{$D167_Id}'");
        $D167 = mysql_fetch_array($sqlD167);

        //VALIDANDO SE O CHAMADO É DO SUPORTE INTERNO OU SUPORTE TECNICO PARA CRIAR O TITULO DO CHAMADO COM NOME DO USUARIO OU DA EMPRESA QUE SOLICITOU
        if($rT234["T234_Origem_Chamado"]=="I"){
            $nomeCard=substr($rT234["T234_Solicitante"],0,17)." - ".$rT234["T234_Id"];
        }else{
            $nomeCard=substr($rT234["D024_Nome_Empresa"],0,17)." - ".$rT234["T234_Id"];
        }

        //TRATANDO TITULO DO CARD PARA ENVIO PARA O TRELLO
        if ((mb_detect_encoding($nomeCard)=='UTF-8') && (!mb_detect_encoding($nomeCard,'UTF-8',true))) {
            $nomeCard = utf8_encode($nomeCard);
        }
        $nomeCard = urlencode($nomeCard);

        $T234_Data_Hora_Abertura = gcorrigeData($rT234["T234_Data_Hora_Abertura"]);

        //TRATANDO DESCRICAO DO CHAMADO PARA ENVIO PARA TRELLO
        $descCard = $T234_Data_Hora_Abertura." - ". $rT234["T234_Solicitante"].chr(13).chr(10).$rT234["T234_Descricao"];
        $descCard = str_replace("<div>", chr(13).chr(10),$descCard);
        $descCard = str_replace("</div>","",$descCard);
        $descCard = str_replace("&nbsp;","",$descCard);
        $descCard = strip_tags($descCard);
        if ((mb_detect_encoding($descCard)=='UTF-8') && (!mb_detect_encoding($descCard,'UTF-8',true))) {
            $descCard = utf8_encode($descCard);
        }
        $descCard = urlencode($descCard);

        //BUSCANDO O USUARIO LOGADO NO SISTEMA
        $sqlC007 = mysql_query("SELECT C007_Primeiro_Nome,C007_Id_Usuario_Trello FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007 = mysql_fetch_array($sqlC007);

        //TRATANDO ANEXO DA IMAGEM AUTOMATICA DO SISTEMA
        $nomeImagem = 'tela_'.$rT234["T234_Id"].'.png';
        $urlImagem = $confUsuario['urlRaiz'] . 'hardness3/dados_usuarios/'.$confUsuario['dbDatabase'].'/tmp/'.$nomeImagem;
        $idMember = $C007['C007_Id_Usuario_Trello'];
        if ((mb_detect_encoding($idMember)=='UTF-8') && (!mb_detect_encoding($idMember,'UTF-8',true))) {
            $idMember = utf8_encode($idMember);
        }
        $idMember = urlencode($idMember);

        if ((mb_detect_encoding($urlImagem)=='UTF-8') && (!mb_detect_encoding($urlImagem,'UTF-8',true))) {
            $urlImagem = utf8_encode($urlImagem);
        }
        $urlImagem = urlencode($urlImagem);
        $pathImg = $confUsuario['pathRaiz'] . 'dados_usuarios/'.$confUsuario['dbDatabase'].'/tmp/'.$nomeImagem;

        exec("xvfb-run --server-args='-screen 0, 1280x1200x24' cutycapt --url={$rT234['T234_Link_Screenshot']} --out={$pathImg}");
        //Verifica se o chamado é de melhoria, obrigatório estar vinculado com um pacote - não utilizado mais
        if($rT234['T234_Flag_Tipo_OS'] == 'X'){       
            if($rT234['D166_T272_Id'] <= 0 || empty($rT234['D166_T272_Id'])){ //se não tiver um pacote vinculado
                //PEGA NAS CONFIGURAÇÕES O ID DO BACKLOG, PARA ONDE SERÁ ENVIADO UM CHAMADO DE MELHORIA SEM PACOTE SELECIONADO
                $D167_Id =  $g['C031']['TrelloListaCardMelhoria'];
                $msqlD167 = mysql_query("SELECT 
                                   D167_Id,    
                                   D166_Id,
                                   D166_Id_Quadro_Trello,
                                   D167_Id_Lista_Trello,
                                   D166_Lista_Analise_Id,
                                   D166_Flag_Atendimento
                                  FROM D167 
                             LEFT JOIN D166 ON D167_D166_Id=D166_Id 
                                 WHERE D167_Id='{$D167_Id}'");
                $mD167 = mysql_fetch_array($msqlD167);
                $D167['D167_Id_Lista_Trello'] = $mD167['D167_Id_Lista_Trello'];
                $D167_Id = $mD167['D167_Id'];
                $D167['D166_Id'] = $mD167['D166_Id']; 
            }else{
                //se estiver vinculado, verifica se existe uma lista vinculado ao quadro
                if($rT234['D166_Lista_Analise_Id'] <= 0){
                    $result[0]=false;
                    $result[1]= "O quadro selecionado não possui nenhuma lista vinculada";
                    return $result;    
                }else{
                    $lista = mysql_query("SELECT D167_Id_Lista_Trello, D167_Id
                                              FROM D167 
                                             WHERE D167_Id='{$rT234['D166_Lista_Analise_Id']}'");
                    $mlista = mysql_fetch_array($lista);
                    if(!empty($mlista['D167_Id_Lista_Trello']) || $mlista['D167_Id_Lista_Trello'] > 0){
                        $D167['D167_Id_Lista_Trello'] = $mlista['D167_Id_Lista_Trello'];
                        $D167_Id = $mlista['D167_Id'];
                        $D167['D166_Id'] = $rT234['D166_Id']; 
                    }
                }        
            }
        }

        //Se tipo de chamado for backlog cria o card em backlog, senão cria em fila atendimento
        if($rT234['T234_Flag_Tipo_OS'] == 'B'){
            //PEGA NAS CONFIGURAÇÕES O ID DO BACKLOG, PARA ONDE SERÁ ENVIADO UM CHAMADO DE MELHORIA SEM PACOTE SELECIONADO
            $D167_Id =  $g['C031']['TrelloListaCardMelhoria'];
            $msqlD167 = mysql_query("SELECT 
                               D167_Id,    
                               D166_Id,
                               D166_Id_Quadro_Trello,
                               D167_Id_Lista_Trello,
                               D166_Lista_Analise_Id,
                               D166_Flag_Atendimento
                              FROM D167 
                         LEFT JOIN D166 ON D167_D166_Id=D166_Id 
                             WHERE D167_Id='{$D167_Id}'");
            $mD167 = mysql_fetch_array($msqlD167);
            $D167['D167_Id_Lista_Trello'] = $mD167['D167_Id_Lista_Trello'];
            $D167_Id = $mD167['D167_Id'];
            $D167['D166_Id'] = $mD167['D166_Id'];
        }

        //CRIANDO O CARD COM OS DADOS TRATADOS
        $ch = curl_init("{$urlApi}cards?name={$nomeCard}&desc={$descCard}&idList={$D167['D167_Id_Lista_Trello']}&&idMembers={$idMember}&urlSource={$urlImagem}&keepFromSource=all&key={$key}&token={$token}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = array();
        if($respostaHttp==200){
            //CARD NOVO CRIADO COM SUCESSO
            //pegando o id do card criado
            $retornoCard = (array) json_decode($data);
            $idCard = $retornoCard['id'];

            //VERIFICANDO O TIPO DE CHAMADO PARA ENVIAR A ETIQUETA CORRETA
            $tipoChamado = $rT234['T234_Flag_Tipo_OS'];
            $etiqueta=null;
            $comentario = "";
            $mes="";

            if($tipoChamado=="M"){
                //SE FOR MELHORIA RECEBE A ETIQUETA, TROCA O NUMERO DO MES PARA O NOME DO MES DO PACOTE, ENVIA UM COMENTARIO PARA O CHAMADO
                $etiqueta="green";

                if($rT234['T234_T272_Id']>0){
                    //TROCAR NUMERO DO MÊS PARA O NOME DO MÊS PARA ENVIAR NO COMENTÁRIO - NÂO SENDO UTILIZADO MAIS
                    switch($rT234['T272_Mes']){
                        case 1:
                            $mes="Janeiro";
                            break;
                        case 2:
                            $mes="Fevereiro";
                            break;
                        case 3:
                            $mes="Março";
                            break;
                        case 4:
                            $mes="Abril";
                            break;
                        case 5:
                            $mes="Maio";
                            break;
                        case 6:
                            $mes="Junho";
                            break;
                        case 7:
                            $mes="Julho";
                            break;
                        case 8:
                            $mes="Agosto";
                            break;
                        case 9:
                            $mes="Setembro";
                            break;
                        case 10:
                            $mes="Outubro";
                            break;
                        case 11:
                            $mes="Novembro";
                            break;
                        case 12:
                            $mes="Dezembro";
                            break;
                    }

                    $comentario = "**Este chamado foi selecionado para o pacote de {$mes} de {$rT234['T272_Ano']}.**";
                    if ((mb_detect_encoding($comentario)=='UTF-8') && (!mb_detect_encoding($comentario,'UTF-8',true))) {
                        $comentario = utf8_encode($comentario);
                    }
                    $comentario = urlencode($comentario);
                    
                    //ENVIANDO O COMENTÁRIO 
                    $ch = curl_init("{$urlApi}cards/{$idCard}/actions/comments?text={$comentario}&key={$key}&token={$token}");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    $data = curl_exec($ch);
                    curl_close($ch);
                }

            }else if($tipoChamado=="E"){
                $etiqueta="red";
            }else if($tipoChamado=="D"){
                $etiqueta="yellow";
            }else if($tipoChamado=="B"){
                $etiqueta="green";
            }

            //ENVIAR A ETIQUETA COM A COR DE ACORDO COM O TIPO DE CHAMADO ABERTO
            $ch = curl_init("{$urlApi}cards/{$idCard}/labels?color={$etiqueta}&key={$key}&token={$token}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            curl_close($ch);

            $clausulaFlagAtendimento = "";
            if($D167['D166_Flag_Atendimento']=="S"){
                $clausulaFlagAtendimento = ",T234_Flag_Finalizado='A'";
            }else{
                $clausulaFlagAtendimento = ",T234_Flag_Finalizado='N'";
            }

            //ATUALIZANDO OS DADOS NO BANCO
            $result[0]=true;
            $data = mysql_real_escape_string($data);
            mysql_query("UPDATE T234 SET T234_Retorno_Card='{$retornoCard}',T234_IdCard='{$idCard}',T234_Flag_Triagem='N',T234_C007_Id_Programador='{$C007['C007_Primeiro_Nome']}',T234_Flag_Personalizado='S',T234_C007_Id='{$g['usuarioAtual']}',T234_D167_Id='{$D167_Id}',T234_D166_Id='{$D167['D166_Id']}'{$clausulaFlagAtendimento} WHERE T234_Id='{$T234_Id}'");
            $erro=mysql_error();

            $sqlT252 = mysql_query("SELECT T252_Id FROM T252 WHERE T252_T234_Id={$T234_Id} Limit 1");
            $T252 = mysql_fetch_array($sqlT252);

            mysql_query("UPDATE T252 SET T252_Detalhe_Trello_Id='{$idCard}' WHERE T252_Id={$T252['T252_Id']}");

            //valida se deu erro ao fazer o update e manda a mensagem
            if(!empty($erro)){
                $result[1]="Erro Mysql:".$erro;
            }else{
                $retorno = $this->atualizarDetalhesChamadoTrello($T234_Id,$idCard);
                $result[1]="Enviado com sucesso!<br>"."<br>".$retorno[1];
            }
        }else{
            //nao deu certo o envio das informações para o trello
            $result[0]=false;
            $result[1]="Erro ao enviar os dados para o trello! Erro:".$data;
        }
        return $result;
    }   


    public function sincronizarTrello($organizations,$filaAtendimento=false)
    {
        global $g,$confUsuario;
        mysql_set_charset('utf8');

        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];

        $result = "";
        $resultado=true;
        $sucesso = 0;
        $quadro = "";
        $chamadosInteracoes = ""; 
        $organizacao = $g['C031']['TrelloNomeTimeDesenvolvimento'];
        $T234_C007_Id_Programador = "";
        $listaBacklog = $g['C031']['TrelloListaCardMelhoria'];

        //Selecionando o quadro backlog atraves da lista backlog definida nas configurações do trello
        //Essa configuração vai servir para mudar os chamados que estiverem em backlog para tipo B(MELHORIA* ou BACKLOG)
        $sqlD166Backlog = mysql_query("SELECT D166_Id FROM D166 LEFT JOIN D167 ON D167_D166_Id=D166_Id WHERE D167_Id={$listaBacklog};");
        $D166_Quadro_Backlog_Id = mysql_fetch_assoc($sqlD166Backlog);

        //Buscando Quadros pertencentes ao time que esta sendo atualizado - Metodo GET
        $ch = curl_init("{$urlApi}organizations/{$organizations}/boards?key={$key}&token={$token}&showExternal=true");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $board = (array) json_decode($data);

        if($respostaHttp!=200){
            $resultado=false;
            $result.="<br>Erro ao encontrar os quadros. ({$urlApi}organizations/{$organizations}/boards?key={$key}&token={$token}&showExternal=true)" ;
            $retorno[0] = $resultado;
            $retorno[1] = $result;
            return $retorno;
        }

        foreach ($board as $chave => $boards) {
            $quadro = (array) $boards;
            $idBoard = $quadro["id"];
            $nomeBoard = $quadro["name"];

            //Verificar se quadro já existe no sistema
            $sqlD166 = mysql_query("SELECT D166_Id,D166_Grupo_Suporte,D166_Flag_Processar_Feedback,D166_Nome_Time,D166_Flag_Melhoria,D166_Flag_Atendimento,D166_T272_Id,D166_Lista_Analise_Id,D166_Flag_Sincronizar_Trello from D166 where D166_Id_Quadro_Trello='{$idBoard}'");
            $rowsD166 = mysql_num_rows($sqlD166);

            //Caso quadro exista verifica insofmrações para atualizar(nome quadro...)
            if($rowsD166>0){
                $D166 = mysql_fetch_array($sqlD166);
                  
                $D166_Id=$D166["D166_Id"];

                if($D166["D166_Flag_Melhoria"]=="N" && $organizations==$organizacao){
                    mysql_query("UPDATE D166 SET D166_Flag_Melhoria='S' WHERE D166_Id='{$D166_Id}'");
                }

                if($D166["D166_Grupo_Suporte"]!=$nomeBoard){
                    mysql_query("UPDATE D166 SET D166_Grupo_Suporte='{$nomeBoard}' WHERE D166_Id='{$D166_Id}'");                    
                }

                if($D166["D166_Nome_Time"]!=$organizations){
                    mysql_query("UPDATE D166 SET D166_Nome_Time='{$organizations}' WHERE D166_Id='{$D166_Id}'");                    
                }
            }else{
                //Caso quadro não exista importa para o sistema
                mysql_query("INSERT INTO D166 (D166_Grupo_Suporte,D166_Id_Quadro_Trello,D166_Nome_Time) VALUES('{$nomeBoard}','{$idBoard}','{$organizations}')"); 
                $D166_Id =$g['mysqlLastId'];
                $result.= "<br>Inserido o quadro {$nomeBoard}";

                $sqlD166 = mysql_query("SELECT D166_Id,D166_Grupo_Suporte,D166_Flag_Processar_Feedback,D166_Nome_Time,D166_Flag_Atendimento,D166_Flag_Melhoria,D166_T272_Id,D166_Lista_Analise_Id,D166_Flag_Sincronizar_Trello from D166 where D166_Id='{$D166_Id}'");
                $D166 = mysql_fetch_array($sqlD166);
            }

            //Caso a função tenha sido chamada para atualizar fila atendimento somente e o quadro não pertence a fila atendimento
            if($filaAtendimento===true && $D166["D166_Flag_Atendimento"]=="N"){
                $D166["D166_Flag_Sincronizar_Trello"]="N";
            }
            
            if($D166["D166_Flag_Sincronizar_Trello"]=="S"){

                $result.="<br><b>Processando lista {$D166['D166_Grupo_Suporte']}</b>" ;

                //Buscando Lista pertencentes ao quadro - Metodo GET
                $ch = curl_init("{$urlApi}boards/{$idBoard}/lists?key={$key}&token={$token}");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $data = curl_exec($ch);
                $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $list = (array) json_decode($data);
                
                if($respostaHttp!=200){
                    $resultado=false;
                    $result.="<br>Erro ao encontrar as listas." ;
                    $retorno[0] = $resultado;
                    $retorno[1] = $result;
                    return $retorno;
                }
                
                foreach ($list as $chave => $lists) {
                    $lista = (array) $lists;

                    $idList = $lista["id"];
                    $nomeList = $lista["name"];
                    $idListBoard = $lista["idBoard"];

                    //verificando se a lista ja foi inserida anteriormente
                    $sqlD167 = mysql_query("SELECT D167_Id,D167_Nome_Lista,D167_Horas_Validade from D167 where D167_Id_Lista_Trello='{$idList}'");
                    $rowsD167 = mysql_num_rows($sqlD167);

                    //Se lista já foi inserida verifica para atualizar informações(nome...)
                    if($rowsD167>0){
                       $D167 = mysql_fetch_array($sqlD167);
                       $D167_Id=$D167["D167_Id"];

                        if($D167["D167_Nome_Lista"]!=$nomeList){
                            mysql_query("UPDATE D167 SET D167_Nome_Lista='{$nomeList}' WHERE D167_Id='{$D167_Id}'");                    
                        }
                    }else{
                       //Se lista não existe importa para o sistema
                       mysql_query("INSERT INTO D167 (D167_Nome_Lista,D167_Id_Lista_Trello,D167_D166_Id,D167_Flag_Enviar_Detalhes) VALUES('{$nomeList}','{$idList}','{$D166_Id}','S')"); 
                       $D167_Id =$g['mysqlLastId'];
                       $result.= "<br>Inserido a lista {$nomeList}.";
                    }

                    $result.="<br><b>-->{$nomeList}</b>" ;
    
                    //Buscando Cartões pertencentes a lista - Metodo GET
                    $ch = curl_init("{$urlApi}lists/{$idList}/cards?key={$key}&token={$token}");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    $data = curl_exec($ch);
                    $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    $card = (array) json_decode($data);
                    
                    if($respostaHttp!=200){
                        $resultado=false;
                        $result.="<br>Erro ao encontrar os cartões." ;
                        $retorno[0] = $resultado;
                        $retorno[1] = $result;
                        return $retorno;
                    }

                    //Zera a lista e quadro do chamado
                    mysql_query("UPDATE T234 SET T234_D166_Id=0,T234_D167_Id=0 where T234_D167_Id={$D167_Id}");

                    foreach ($card as $chave => $cards) {
                        $cartao = (array) $cards;

                        //Validações de informações que serão atualizadas no chamado
                        $idCard = $cartao["id"];
                        $nomeCard=$cartao["name"];
                        $idChamado = preg_replace("/[^0-9]/", "", $nomeCard);
                        $dataEntrega = "0000-00-00 00:00:00";
                        if(!empty($cartao['due'])){
                            $dataEntrega = date("Y-m-d H:i:s", strtotime($cartao['due']));
                        }
                        
                        //Buscando a cor da etiqueta do chamado
                        $labels = (array) $cartao['labels'];
                        $label = (array) $labels[0];
                        
                        $tipo = "";
                        if($label['color']=="green"){
                            $tipo = "M";
                        }else if($label['color']=="yellow"){
                            $tipo = "D";
                        }else if($label['color']=="red"){
                            $tipo = "E";
                        }

                        if($D166_Id==$D166_Quadro_Backlog_Id['D166_Id']){
                            $tipo = "B";
                        }

                        //select nos cartoes
                        $sqlT234 = mysql_query("SELECT T234_Id,T234_IdCard,T234_D166_Id,T234_D167_Id,T234_Flag_Finalizado,T234_Flag_Tipo_OS,T234_Origem_Chamado FROM T234 WHERE T234_IdCard='{$idCard}' OR T234_Id='{$idChamado}'");
                        $T234 = mysql_fetch_array($sqlT234);

                        //Verificando se existe um chamado vinculado com o id do card do trello(id preenchido quando chamado é criado)
                        if($T234['T234_Id']>0){

                            $update="";

                            $membros = (array) $cartao["idMembers"];

                            //Preenchendo os membros do chamado de acordo com os usuarios do trello
                            for ($i=0;$i<count($membros);$i++) {

                                $sqlC007 = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id_Usuario_Trello='{$membros[$i]}'");
                                $C007 = mysql_fetch_array($sqlC007);
                                
                                if(!empty($T234_C007_Id_Programador)){
                                    $T234_C007_Id_Programador.= "<br>".$C007['C007_Primeiro_Nome'];
                                }else{
                                    $T234_C007_Id_Programador.= $C007['C007_Primeiro_Nome'];
                                }
                            }    

                            // if(($D167['D167_Horas_Validade']!='00:00:00') || (empty($D167['D167_Horas_Validade']))){
                            //     $update.=" T234_Horas_Atendimento='{$D167['D167_Horas_Validade']}' ";
                            // }
                            
                            //Update dinamico para atualização das informações no chamado
                            if(!empty($T234_C007_Id_Programador)){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_C007_Id_Programador='{$T234_C007_Id_Programador}'"; 
                                $T234_C007_Id_Programador = "";
                            }

                            if(($organizations==$organizacao) && !(empty($D166["D166_T272_Id"]))){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_T272_Id={$D166["D166_T272_Id"]}"; 
                            }

                            if(empty($T234["T234_IdCard"])){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_IdCard='{$idCard}' ";
                            }

                            if($D166["D166_Flag_Atendimento"]=="S"){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_Flag_Finalizado='A' ";
                            }else{
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_Flag_Finalizado='N' ";
                            }

                            if($T234["T234_D166_Id"]!=$D166_Id){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_D166_Id='{$D166_Id}' ";
                            }

                            if($T234["T234_D167_Id"]!=$D167_Id){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_D167_Id='{$D167_Id}' ";
                            }

                            if($T234["T234_Flag_Tipo_OS"]!=$tipo){
                                if (!empty($update)){
                                    $update .= ',';
                                }
                                $update.=" T234_Flag_Tipo_OS='{$tipo}' ";
                            }

                            if (($T234["T234_Data_Previsao"] != $dataEntrega) && ($dataEntrega!="0000-00-00 00:00:00")) {
                                if (!empty($update)) {
                                    $update .= ',';
                                }
                                $update .= " T234_Data_Previsao='{$dataEntrega}' ";
                            }

                            if(!empty($update)){
                                mysql_query("UPDATE T234 SET {$update} WHERE T234_Id='{$T234['T234_Id']}'"); 
                                log("UPDATE T234 SET {$update} WHERE T234_Id='{$T234['T234_Id']}'");
                                $sucesso++;
                            }                  
                        
                            //Se chamado não estiver finalizado e estiver em conclusão(identificado pelo quadro com a flag D166_Flag_Processar_Feedback=S) importa os detalhes(interações, anexos...)
                            if(($T234['T234_Flag_Finalizado']!="S")){
                                //Buscando os detalhes pertencentes ao chamado - Metodo GET
                                $ch = curl_init("{$urlApi}cards/{$idCard}/actions?key={$key}&token={$token}&filter=all");
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HEADER, 0);
                                $data = curl_exec($ch);
                                $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                curl_close($ch);

                                if($respostaHttp!=200){
                                    $resultado=false;
                                    $result.="<br>Erro ao importar detalhes chamado {$T234['T234_Id']}." ;
                                    $retorno[0] = $resultado;
                                    $retorno[1] = $result;
                                    return $retorno;
                                } else {
                                    $result.="<br>+ Processando detalhes chamado {$T234['T234_Id']} idCard {$idCard}." ;
                                }

                                $action = (array) json_decode($data);

                                $comentario_anexo='';
                                $T252_Id=0;
                                $interacaoInterna = false;
                                $horasAtendimento = false;

                                foreach ($action as $chave => $actions) {
                                    $acao = (array) $actions;

                                    //Verificando se detalhe do card ja foi inserida no sistema e não faz parte do checklist
                                    $idInteracao = $acao["id"];
                                    $type = $acao["type"];

                                    $sqlT273 = mysql_query("SELECT T273_Id FROM T273 WHERE T273_Id_Interacao='{$idInteracao}'");
                                    $rowsT273 = mysql_num_rows($sqlT273);

                                    if(($rowsT273<=0) && !(stripos($type,"Check"))){

                                        $dataInteracao = $acao["date"];
                                        $dataInteracao = str_replace("T", " ", $dataInteracao);
                                        $date = explode(".", $dataInteracao);
                                        $dataInteracao = $date[0];

                                        //Validando qual usuario vai ser inrido o comentario no chamado
                                        if(!empty($g['C031']['C007PadraoRetornoCliente'])){
                                            $idMemberCreator = $g['C031']['C007PadraoRetornoCliente'];
                                            $sqlC007 = mysql_query("SELECT C007_Id,C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['C031']['C007PadraoRetornoCliente']}'");
                                            $C007 = mysql_fetch_array($sqlC007);
                                        }else{
                                            $idMemberCreator = $acao["idMemberCreator"];
                                            $sqlC007 = mysql_query("SELECT C007_Id,C007_Primeiro_Nome FROM C007 WHERE C007_Id_Usuario_Trello='{$acao["idMemberCreator"]}'");
                                            $C007 = mysql_fetch_array($sqlC007);
                                        }

                                        $data = (array) $acao["data"];

                                        //tratando os anexos que vem nas ações
                                        $attachments = (array)$data["attachment"];
                                        $nomeAnexo = mysql_real_escape_string($attachments["name"]);
                                        $linkAnexo = mysql_real_escape_string($attachments["url"]);

                                        //pegando os comentarios que vem nas ações
                                        $comment = mysql_real_escape_string($data["text"]);

                                        //tratando a mudança de lista do card
                                        $listAntes = (array) $data["listBefore"];
                                        $nomeListAntes = $listAntes["name"];
                                        $idListAntes = $listAntes["id"];

                                        $sqlListaAntes = mysql_query("SELECT D167_Id,ifnull(D167_Horas_Validade,'00:00:00') as D167_Horas_Validade,D167_Nome_Lista from D167 where D167_Id_Lista_Trello='{$idListAntes}'");
                                        $Lista_Antes =mysql_fetch_array($sqlListaAntes);

                                        $listDepois = (array) $data["listAfter"];
                                        $nomeListDepois = $listDepois["name"];
                                        $idListDepois = $listDepois["id"];

                                        $sqlListaDepois = mysql_query("SELECT D167_Id,ifnull(D167_Horas_Validade,'00:00:00') as D167_Horas_Validade,D167_Nome_Lista from D167 where D167_Id_Lista_Trello='{$idListDepois}'");
                                        $Lista_Depois = mysql_fetch_array($sqlListaDepois);

                                        if($Lista_Depois['D167_Id']>0 && $Lista_Depois['D167_Horas_Validade']!="00:00:00" && !empty($Lista_Depois['D167_Horas_Validade'] && !$horasAtendimento)){
                                            $horasAtendimento = true;
                                            mysql_query("UPDATE T234 SET T234_Horas_Atendimento='{$Lista_Depois['D167_Horas_Validade']}' WHERE T234_Id='{$T234['T234_Id']}'");
                                            log("UPDATE T234 SET T234_Horas_Atendimento='{$Lista_Depois['D167_Horas_Validade']}' WHERE T234_Id='{$T234['T234_Id']}'");
                                        }
                                        if($D166["D166_Flag_Processar_Feedback"]=="N"){
                                            continue;
                                        }

                                        $sqlT273 = mysql_query("SELECT T273_Id FROM T273 WHERE T273_Id_Interacao='{$idInteracao}'");
                                        $rowsT273 = mysql_num_rows($sqlT273);

                                        //Inserindo detalhes no chamado
                                        $insert  =  "INSERT INTO T273 (";
                                        $insert .= "T273_T234_Id,";
                                        $insert .= "T273_C007_Id,";
                                        $insert .= "T273_Id_Interacao,";
                                        $insert .= "T273_Tipo,";
                                        $insert .= "T273_Data_Interacao,";
                                        $insert .= "T273_Descricao,";
                                        $insert .= "T273_Texto,";
                                        $insert .= "T273_Link_Anexo,";
                                        $insert .= "T273_D167_Antes,";
                                        $insert .= "T273_D167_Depois";
                                        $insert .= ")VALUES(";
                                        $insert .= "'{$T234['T234_Id']}',";
                                        $insert .= "'{$C007['C007_Id']}',";
                                        $insert .= "'{$idInteracao}',";
                                        $insert .= "'{$type}',";
                                        $insert .= "'{$dataInteracao}',";
                                        $insert .= "'',";
                                        $insert .= "'{$comment}',";
                                        $insert .= "'{$linkAnexo}',";
                                        $insert .= "'{$Lista_Antes['D167_Id']}',";
                                        $insert .= "'{$Lista_Depois['D167_Id']}'";
                                        $insert .= ")";

                                        mysql_query($insert);
                                        $erro = mysql_error();
                                        if(!empty($erro)){
                                            log("Erro mysql insert T273:".$erro);
                                            $result.="<br>Erro ao inserir as interações do chamado {$T234['T234_Id']}<br>";
                                        }

                                        //Verificando se detalhe é comentario(interação)
                                        if(!empty($comment)){
                                            $horasDesenvolvimento = stristr($comment, "#HORAS");
                                            if(!empty($horasDesenvolvimento)){
                                                $horasDesenvolvimento = trim(str_ireplace("#HORAS", "", $horasDesenvolvimento));
                                                $palavras = explode(" ", $horasDesenvolvimento);
                                                $horasDesenvolvimento = trim($palavras[0]);

                                                mysql_query("UPDATE T234 SET T234_Horas_Desenvolvimento='{$horasDesenvolvimento}' WHERE T234_Id='{$T234['T234_Id']}'");
                                            }

                                            $finalizarChamado = stristr($comment, "#FINALIZAR");
                                            if (empty($finalizarChamado)) {
                                                $finalizarChamado = stristr($comment, "#FINALIZADO");
                                            }
                                            if (!empty($finalizarChamado)) {
                                                require_once 'prog/outros/registroSuporte/registroSuporte.php';
                                                finalizarChamadoAbandonado($T234['T234_Id'], "", "63");
                                                $retorno = $this->arquivarCard($T234['T234_Id'],true);
                                                $result .= '** FINALIZAR **';
                                            }

                                            $comentario = stristr($comment, "#CLIENTE");
                                            $comentarioInterno = stristr($comment, "#INTERNO");

                                            $comentarioVoltar = stristr($comment, "#VOLTAR");
                                            $comentario = trim(str_ireplace("#VOLTAR", " ", $comentario));

                                            if(!empty($comentario)){
                                                $comentario = trim(str_ireplace("#HORAS", " ", $comentario));
                                                $comentario = trim(str_ireplace("#CLIENTE", " ", $comentario));
                                                $comentario = trim(str_ireplace("#FINALIZAR", " ", $comentario));

                                                //Inserindo interação
                                                $insert  =  "INSERT INTO T252 (";
                                                $insert .= "T252_T234_Id,";
                                                if($T234['T234_Origem_Chamado']=="I"){
                                                    $insert .= "T252_C007_Id_Cliente,";
                                                }else{
                                                    $insert .= "T252_C007_Id,";
                                                }
                                                $insert .= "T252_Usuario,";
                                                $insert .= "T252_Descricao,";
                                                $insert .= "T252_Data_Hora,";
                                                $insert .= "T252_Flag_Lido,";
                                                $insert .= "T252_Flag_Finalizacao,";
                                                $insert .= "T252_Flag_Feedback,";
                                                $insert .= "T252_Detalhe_Trello_Id";
                                                $insert .= ")VALUES(";
                                                $insert .= "'{$T234['T234_Id']}',";
                                                $insert .= "'{$C007['C007_Id']}',";
                                                $insert .= "'{$C007['C007_Primeiro_Nome']}',";
                                                $insert .= "'{$comentario}',";
                                                $insert .= "NOW(),";
                                                $insert .= "'N',";
                                                $insert .= "'N',";
                                                $insert .= "'N',";
                                                $insert .= "'{$idInteracao}'";
                                                $insert .= ")";
                                                mysql_query($insert);
                                                $erro = mysql_error();
                                                
                                                if(!empty($erro)){
                                                    log("Erro mysql insert T252:".$erro);
                                                    $result.= "<br>Erro ao inserir comentário no chamado {$T234['T234_Id']}<br>";
                                                } else {
                                                    $result.= "<br><font style='font-weight:bold'>#CLIENTE - {$T234['T234_Id']}</font> - {$comentario}<br>";
                                                }

                                                $T252_Id = $g['mysqlLastId'];

                                            } else if(!empty($comentarioInterno)){
                                                $comentarioInterno = trim(str_ireplace("#INTERNO", " ", $comentarioInterno));
                                                $interacaoInterna = true;
                                                $result.= "<br><font style='color:blue;font-weight:bold'>#INTERNO - {$T234['T234_Id']}</font> - {$comentarioInterno} - <b>Chamado retornado para triagem</b><br>";
                                            }

                                        }

                                        if(!empty($comentarioVoltar)){
                                            //ID DA LISTA DA CONFIGURAÇÂO NO CRIAR CARD NO TRELLO
                                            $D167_Id =  $g['C031']['TrelloListaCardVoltar'];
                                            $sqlD167 = mysql_query("SELECT D166_Id,D166_Id_Quadro_Trello,D167_Id_Lista_Trello FROM D167 LEFT JOIN D166 ON D167_D166_Id=D166_Id WHERE D167_Id={$D167_Id}");
                                            $D167 = mysql_fetch_array($sqlD167);

                                            //mudando o card para a lista de analise do quadro
                                            $ch = curl_init("{$urlApi}cards/{$idCard}?idList={$D167['D167_Id_Lista_Trello']}&idBoard={$D167['D166_Id_Quadro_Trello']}&pos=bottom&closed=false&key={$key}&token={$token}");
                                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                            curl_setopt($ch, CURLOPT_HEADER, 0);
                                            $data = curl_exec($ch);
                                            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                            curl_close($ch);

                                            $mudarLista = (array) json_decode($data);
                                            log("#VOLTAR:".$data);

                                            if($respostaHttp==200){
                                                mysql_query("UPDATE T234 SET T234_D166_Id='{$D167['D166_Id']}',T234_D167_Id='{$D167_Id}' where T234_Id='{$T234['T234_Id']}'");
                                            }
                                        }

                                        $C007_Id = $C007['C007_Id'];

                                        //Validando se existe anexo para ser inserido junto com a interação
                                        if(!empty($linkAnexo)){
                                            $link = explode("/", $linkAnexo);
                                            $arquivo = trim($link[count($link)-1]);
                                            $extensao = stristr($arquivo, ".");
                                            $extensao =  strtolower(str_ireplace(".", "", $extensao));
                                            $extensoesIcones='doc,docx,pdf,csv,txt,xls,xlsx,xml,zip';
                                            $extensoesImagens='png,jpeg,jpg,gif,bmp';
                                        
                                            if(stristr($extensoesIcones, $extensao)){
                                                $urlImagem = "{$confUsuario["urlRaiz"]}hardness3/static/img/format-{$extensao}-file.png";
                                                $comentario_anexo[$C007_Id].= "<div style='width:60px;margin-top:5px;'><a href='{$linkAnexo}' target='_blank'><img src='$urlImagem' id='trello' width='60px'></a></div>";
                                            } else if(stristr($extensoesImagens, $extensao)) {
                                                $result.= "<br>Erro chamado {$T234['T234_Id']} T252_Id = {$T252_Id} {$linkAnexo}";
                                                $comentario_anexo[$C007_Id].= "<div style='margin-top:5px;'><img src='{$linkAnexo}'></div>";
                                            }
                                        }

                                        if(!empty($comentario_anexo[$C007_Id]) && $T252_Id>0){
                                            mysql_query("UPDATE T252 SET T252_Descricao=Concat(T252_Descricao,\" $comentario_anexo[$C007_Id] \"),T252_Detalhe_Trello_Id='{$idInteracao}' where T252_Id='$T252_Id'");

                                            $erro = mysql_error();
                                            if(!empty($erro)){
                                                log("Erro mysql update T252:".$erro);
                                                $result.= "<br>Erro ao fazer update no comentário no chamado {$T234['T234_Id']}<br>";
                                            }
                                            $comentario_anexo='';
                                            // $T252_Id=0;
                                        }                                                        

                                    }    
                                }

                                if ($interacaoInterna === true){
                                    mysql_query("update T234 set T234_Flag_Triagem = 'S' where T234_Id='{$T234['T234_Id']}'");
                                    $erro = mysql_error();
                                    if(!empty($erro)){
                                        log("Erro myssql enviando chamado para triagem:".$erro);
                                        $result.= "<br>Erro ao retornar o chamado {$T234['T234_Id']} para triagem<br>";
                                    }
                                }
                            }
                        }else{
                            $result.= "<br>- Chamado não encontrado Id {$idCard} - {$nomeCard}";
                        }
                   }
                }
            }
        }

        if($sucesso>0){
            $result.="<br>{$sucesso} chamados foram sincronizados com o trello.<br>";
        }

        if(empty($result)){
            $result.="<br>Não foram realizadas novas inserções.<br>";
        }  

        if($organizations==$organizacao){
         /*   $return = $this->SincronizarChamadosPacote();  
            log("retorno:$retorno"); 
            $result.=$return;      */ 
        }


        $retorno[0] = $resultado;
        $retorno[1] = $result;

        return $retorno;
    }

   
    public function atualizarDetalhesChamadoTrello($T234_Id,$idCard = false)
    {
        global $g,$confUsuario,$sys;

        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];

        //Criar arquivo texto logTxt
        $Dir = $g['pathDados'] . 'tmp/';
        is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}Trello-{$T234_Id}.txt";
        //@unlink($arquivoNome);
        $h = fopen($arquivoNome, "a");
              
        $retorno = array();
        $resultado = true;
        $result = "";    

        //BUSCANDO INTERAÇÕES AINDA NÃO ENVIADOS PARA TRELLO
        $sqlT252 = mysql_query("SELECT T252_Usuario,T252_Data_Hora,T252_Descricao,D024_Nome_Empresa,T234_IdCard,T252_Id,T234_D167_Id,D167_Mover_Lista_Id,T234_Flag_Tipo_OS,T234_Origem_chamado
                                FROM T252 
                                LEFT JOIN T234 on T252_T234_Id=T234_Id 
                                LEFT JOIN D167 ON T234_D167_Id=D167_Id
                                LEFT JOIN D024 ON T234_D024_Id=D024_Id 
                                WHERE T252_T234_Id={$T234_Id}  
                                AND T252_Detalhe_Trello_Id=''");
        $erro = mysql_error();

        if($erro){
            $retorno[0] = false;
            $retorno[1] = "Erro mysql select T252:".$erro;
            return $retorno;
        }

        $D167_Id =  $g['C031']['TrelloListaCardVoltar'];
        if($D167_Id<=0){
            $retorno[0] = false;
            $retorno[1] = "Para enviar as interações e anexos para o trello é necessário selecionar 'Lista voltar trello' em configurações.";
            return $retorno;
        }
        
        //CASO NÃO TENHA NENHUMA INTERAÇÃO VERIFICA SE EXISTEM ANEXOS NOVOS E ENVIA PARA O TRELLO
        if(mysql_num_rows($sqlT252)<=0){
            $retornoAnexos = $this->enviarAnexos($T234_Id,$idCard);
            $this->importarDetalhes($T234_Id,$idCard);
            
            //Fechar e escrever no log txt
            $logTxt = "Retorno Anexo no enviar detalhes:".$retornoAnexos;
            $r = fwrite($h, $logTxt);
            fclose($h);

            mysql_query("UPDATE T234 SET T234_Flag_Triagem='N' where T234_Id={$T234_Id}");
            
            $retorno[0] = false;
            $retorno[1] = "Não existem interações para atualizar.<br>".$retornoAnexos;

            return $retorno;
        } 
      
        $logTxt = "Enviando as interações \r\n";
        $r = fwrite($h, $logTxt);

        while($T252 = mysql_fetch_array($sqlT252)){
            //TRATANDO DADOS DO ENVIO PARA A CRIAÇÃO DO CARD

            $idCard = $T252['T234_IdCard'];
            $T234_Flag_Tipo_OS = $T252['T234_Flag_Tipo_OS']; 

            $T252_Data_Hora = gcorrigeData($T252["T252_Data_Hora"]);

            //VERIFICANDO SE O CHAMADO É DO SUPORTE INTERNO OU DO SUPORTE TECNICO PARA MOSTRAR O NOME DA EMPRESA QUE SOLICITOU
            if($T252["T234_Origem_Chamado"]=="I"){
                $cabecalhoTexto = $T252_Data_Hora." - ". $T252["T252_Usuario"] . " - " . $T234_Id . chr(13) . chr(10);
            }else{
                $cabecalhoTexto = $T252_Data_Hora." - ". $T252["T252_Usuario"] . " - " .substr($T252["D024_Nome_Empresa"],0,17) . " - " . $T234_Id . chr(13) . chr(10);
            }

            //TRATANDO O TEXTO DE DESCRIÇÃO DO CHAMADO
            $texto = $cabecalhoTexto  . $T252['T252_Descricao']; 
            $textoSemUrlencode = $texto;
            $logTxt = "\r\n \r\n Texto: ".$texto."\r\n";
            $r = fwrite($h, $logTxt);
            $texto = strip_tags($texto);
            if ((mb_detect_encoding($texto)=='UTF-8') && (!mb_detect_encoding($texto,'UTF-8',true))) {
               $texto = utf8_encode($texto);
            }
            $texto = urlencode($texto);

            //ENVIANDO A INTERAÇÃO PARA O TRELLO
            $ch = curl_init("{$urlApi}cards/{$T252['T234_IdCard']}/actions/comments?text=$texto&key={$key}&token={$token}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $resposta =  (array)json_decode($data);

            $idDetalhe = $resposta['id'];

            if($respostaHttp!=200){
                $resultado=false;
                $logTxt = "retorno interação: {$respostaHttp} \r\n";
                $logTxt .= "retorno interação: ".$data ."\r\n";
                $r = fwrite($h, $logTxt);
                $result.="Erro ao enviar a interação: $textoSemUrlencode<br>";
            }else{
                $resultado=true;
                mysql_query("UPDATE T252 SET T252_Detalhe_Trello_Id='{$idDetalhe}' where T252_Id='{$T252["T252_Id"]}'");
            }

            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->loadHTML($T252['T252_Descricao']);                        
            $links = $dom->getElementsByTagName('img');

            //SE FOI INSERIDO UMA IMAGEM AO COMENTARIO DO CHAMADO INSERIDO PELO FORMULARIO DO JAVASCRIPT E NÃO POR ANEXOS
            //RECUPERA O LINK DA IMAGEM E ADICIONA COMO ANEXO NO CARD DO TRELLO
            $contAnexos=0;
            foreach ($links as $link) {
                $contAnexos++;
                $logTxt = "Enviando anexo na mensagem: \r\n";
                $r = fwrite($h, $logTxt);
                $nomeImagem = "Anexo_".$T252["T252_Id"]."_".$contAnexos;                
                if ((mb_detect_encoding($nomeImagem)=='UTF-8') && (!mb_detect_encoding($nomeImagem,'UTF-8',true))) {
                    $nomeImagem = utf8_encode($nomeImagem);
                }
                $nomeImagem = urlencode($nomeImagem);
               
                $link = $link->getAttribute('src').PHP_EOL;
                if ((mb_detect_encoding($link)=='UTF-8') && (!mb_detect_encoding($link,'UTF-8',true))) {
                    $link = utf8_encode($link);
                }
                $link = urlencode($link);
                $logTxt = "Link envio anexo: {$urlApi}cards/{$T252['T234_IdCard']}/attachments?name={$nomeImagem}&url={$link}&key={$key}&token={$token} \r\n ";
                $r = fwrite($h, $logTxt);
                $ch = curl_init("{$urlApi}cards/{$T252['T234_IdCard']}/attachments?name={$nomeImagem}&url={$link}&key={$key}&token={$token}");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $data = curl_exec($ch);
                $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if($respostaHttp!=200){
                    $logTxt= "Erro ao enviar imagem $link para o trello. \r\n";
                    $r = fwrite($h, $logTxt);
                    $result.="Erro ao enviar imagem $link para o trello.<br>";
                }
            }  
        }

        $etiqueta = "";
        if($T234_Flag_Tipo_OS=="M"){
            $etiqueta = "green";
        }else if($T234_Flag_Tipo_OS=="D"){
            $etiqueta = "yellow";
        }else if($T234_Flag_Tipo_OS=="B"){
            $etiqueta = "green";
        }else{
            $etiqueta = "red";
        }

        //ENVIAR A ETIQUETA COM A COR DE ACORDO COM O TIPO DE CHAMADO ABERTO
        $ch = curl_init("{$urlApi}cards/{$idCard}/labels?color={$etiqueta}&key={$key}&token={$token}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $sqlD167 = mysql_query("SELECT D167_Id_Lista_Trello,D166_Id_Quadro_Trello,D166_Id,D167_Id,D166_Flag_Atendimento FROM D167 LEFT JOIN D166 ON D167_D166_Id=D166_Id WHERE D167_Id={$D167_Id}");
        $D167 = mysql_fetch_array($sqlD167);

        if(mysql_num_rows($sqlD167)>0){

           //MUDANDO O CARD PARA A LISTA CONFIGURADA NO LISTA MOVER CARTÃO NAS CONFIGURAÇÕES DO TRELLO
            $ch = curl_init("{$urlApi}cards/{$idCard}?idList={$D167['D167_Id_Lista_Trello']}&idBoard={$D167['D166_Id_Quadro_Trello']}&pos=bottom&closed=false&key={$key}&token={$token}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($respostaHttp!=200){
                $result.="Erro ao mudar o card de lista<br> Retorno trello:".$data;
            }else{
                
                $clausulaFlagAtendimento = "";
                if($D167['D166_Flag_Atendimento']=="S"){
                    $clausulaFlagAtendimento = ",T234_Flag_Finalizado='A'";
                }else{
                    $clausulaFlagAtendimento = ",T234_Flag_Finalizado='N'";
                }

               mysql_query("UPDATE T234 SET T234_Flag_Arquivado='N',T234_D166_Id='{$D167['D166_Id']}',T234_D167_Id='{$D167['D167_Id']}'{$clausulaFlagAtendimento} WHERE T234_Id={$T234_Id}");
            }     
        }

        $retornoAnexos = $this->enviarAnexos($T234_Id,$idCard);
        $this->importarDetalhes($T234_Id,$idCard);
       
        //Fechar e escrever no log txt
        $logTxt = "Retorno Anexo no enviar detalhes:".$retornoAnexos;
        $r = fwrite($h, $logTxt);
        fclose($h);

        if(empty($result)){
            $result = "Informações atualizadas com sucesso!<br>".$retornoAnexos;
            mysql_query("UPDATE T234 SET T234_Flag_Triagem='N' where T234_Id={$T234_Id}");
        }

        $retorno[0] = $resultado;
        $retorno[1] = $result;

        return $retorno;
    }    

    public function arquivarCard($T234_Id,$arquivar,$usuarioTrello=false)
    {
        global $g,$confUsuario;

        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];

        if(isset($usuarioTrello) && $usuarioTrello>0){
            $sqlC007 = mysql_query("SELECT C007_Id,C007_Key_Trello,C007_Token_Trello,C007_Nome from C007 where C007_Id={$usuarioTrello}");
            $rC007 = mysql_fetch_array($sqlC007);
            $token = $rC007["C007_Token_Trello"];
            $key = $rC007["C007_Key_Trello"];
        }

        $retorno = array();
        $resultado = true;
        $result = "";
        
        //SELECIONANDO OS DADOS DO CHAMADO        
        $sqlT234 = mysql_query("SELECT T234_Id,T234_IdCard from T234  where T234_Id={$T234_Id}");
        $rT234 = mysql_fetch_array($sqlT234);

        if(!empty($rT234['T234_IdCard'])){

            //ARQUIVANDO O CARD NO TRELLO
            $ch = curl_init("{$urlApi}cards/{$rT234['T234_IdCard']}?closed=$arquivar&key={$key}&token={$token}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($respostaHttp==200){
                $result = "<br>Card arquivado com sucesso!";
                mysql_query("UPDATE T234 SET T234_Flag_Arquivado='S' WHERE T234_Id={$T234_Id}");
            }else{
                $resultado = false;
                $result = "<br>Erro ao arquivar o card:".$data;
            }

        }else{
            $resultado = false;
            $result =  "<br>Chamado não tem card criado no trello.";
        }

        $retorno[0] = $resultado;
        $retorno[1] = $result;

        return $retorno;

    }

    public function enviarAnexos($T234_Id,$idCard){
        global $g,$confUsuario;
        log("entrou no enviar anexos");
        //Criar arquivo texto logTxt
        $Dir = $g['pathDados'] . 'tmp/';
        is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}Trello-{$T234_Id}.txt";
        //@unlink($arquivoNome);
        $h = fopen($arquivoNome, "a");
      
        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];

        $retornoAnexos="";
        $contAnexos = 0;

        $sqlT253 = mysql_query("SELECT T253_Nome_Arquivo,T253_Caminho_Arquivo,T253_Id,T253_Link_Arquivo FROM T253 WHERE T253_T234_Id = '{$T234_Id}' AND T253_IdAnexo='' ");

        if(mysql_num_rows($sqlT253)<=0){
            return "<br>Não há novos anexos para enviar.<br>";
        } 

        while($rowT253 = mysql_fetch_array($sqlT253)){
            $contAnexos++;

            $urlOrigem = explode("//",$rowT253['T253_Link_Arquivo']);

            $linkAnexo = trim($urlOrigem[0]."//".$urlOrigem[1]."/hardness3/".$rowT253['T253_Caminho_Arquivo']);
           
            //LOG txt
            $logTxt=" Enviar Anexos tabela T253\r\nUrl Origem: $urlOrigem\r\nlinkAnexo: $linkAnexo\r\nurl Envio: {$urlApi}cards/{$idCard}/attachments?name={$rowT253['T253_Nome_Arquivo']}&url={$linkAnexo}<br>";
            $r = fwrite($h, $logTxt);

            if ((mb_detect_encoding($linkAnexo)=='UTF-8') && (!mb_detect_encoding($linkAnexo,'UTF-8',true))) {
                $linkAnexo = utf8_encode($linkAnexo);
            }
            $linkAnexo = urlencode($linkAnexo);

            $nomeArquivo = $rowT253['T253_Nome_Arquivo'];
            if ((mb_detect_encoding($nomeArquivo)=='UTF-8') && (!mb_detect_encoding($nomeArquivo,'UTF-8',true))) {
                $nomeArquivo = utf8_encode($nomeArquivo);
            }
            $nomeArquivo = urlencode($nomeArquivo);

            $ch = curl_init("{$urlApi}cards/{$idCard}/attachments?name={$nomeArquivo}&url={$linkAnexo}&key={$key}&token={$token}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $anexos = (array)json_decode($data);

            $idAnexo = $anexos['id'];

            log("entrou no enviar anexos retorno:$data");

            if($respostaHttp==200){
                $retornoAnexos.="<br><font color='green'>Anexo ".$rowT253['T253_Nome_Arquivo']." enviado.</font><br>";
                mysql_query("UPDATE T253 SET T253_IdAnexo='{$idAnexo}' WHERE T253_Id={$rowT253['T253_Id']}");
            }else{
                $retornoAnexos.="<br><font color='red'>Anexo ".$rowT253['T253_Nome_Arquivo']." não enviado.</font><br>";
            }
        }

        $logTxt="Retorno Anexos na função anexos: $retornoAnexos\r\n";
        $r = fwrite($h, $logTxt);
        fclose($h);
        return $retornoAnexos;
    }

    public function importarDetalhes($T234_Id,$idCard)
    {
        global $g,$confUsuario;

        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];

        //importando e inserindo no banco as interações do card do trello
        $ch = curl_init("{$urlApi}cards/{$idCard}/actions?key={$key}&token={$token}&filter=all");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $action = (array) json_decode($data);

        foreach ($action as $chave => $actions) {
            $acao = (array) $actions;
            log("entrou foreach");
            //pegando os dados das interações do json
            $idInteracao = $acao["id"];
            $type = $acao["type"];

            $sqlT273 = mysql_query("SELECT T273_Id FROM T273 WHERE T273_Id_Interacao='{$idInteracao}'");
            $rowsT273 = mysql_num_rows($sqlT273);

            if(($rowsT273<=0) && !(stripos($type,"Check"))){
                log("entrou no if");
                $dataInteracao = $acao["date"];
                $dataInteracao = str_replace("T", " ", $dataInteracao);
                $date = explode(".", $dataInteracao);
                $dataInteracao = $date[0];

                $idMemberCreator = $acao["idMemberCreator"];
                //select nos cartoes
                $sqlC007 = mysql_query("SELECT C007_Id FROM C007 WHERE C007_Id_Usuario_Trello='{$idMemberCreator}'");
                $C007 = mysql_fetch_array($sqlC007);

                $data = (array) $acao["data"];

                $attachments = (array)$data["attachment"];
                $nomeAnexo = mysql_real_escape_string($attachments["name"]);
                $linkAnexo = mysql_real_escape_string($attachments["url"]);
                log("link anexo: $linkAnexo");

                $comment = mysql_real_escape_string($data["text"]);

                $sqlT273 = mysql_query("SELECT T273_Id FROM T273 WHERE T273_Id_Interacao='{$idInteracao}'");
                $rowsT273 = mysql_num_rows($sqlT273);

                $insert  =  "INSERT INTO T273 (";
                $insert .= "T273_T234_Id,";
                $insert .= "T273_C007_Id,";
                $insert .= "T273_Id_Interacao,";
                $insert .= "T273_Tipo,";
                $insert .= "T273_Data_Interacao,";
                $insert .= "T273_Descricao,";
                $insert .= "T273_Texto,";
                $insert .= "T273_Link_Anexo";
                $insert .= ")VALUES(";
                $insert .= "'{$T234_Id}',";
                $insert .= "'{$C007['C007_Id']}',";
                $insert .= "'{$idInteracao}',";
                $insert .= "'{$type}',";
                $insert .= "'{$dataInteracao}',";
                $insert .= "'',";
                $insert .= "'{$comment}',";
                $insert .= "'{$linkAnexo}'";
                $insert .= ")";

                log("MYSQL:".$insert);
                mysql_query($insert);
                $erro = mysql_error();
                if(!empty($erro)){
                    log("Erro mysql insert T273:".$erro);
                    $result.="<br>Erro ao inserir as interações do chamado {$T234_Id}";
                }
            }
        }
        return true;
    }

    public function SincronizarChamadosPacote()
    {
        global $g, $confUsuario;

        include($confUsuario["pathRaiz"].'/bibliotecas/webservices/trello/BuscarTokenTrello.php');
        $urlApi = $g['C031']['TrelloUrlApi'];
      
        $sqlT234 = mysql_query("SELECT T234_Id,T234_T272_Id,T234_IdCard FROM T234 LEFT JOIN D166 ON T234_D166_Id=D166_Id WHERE T234_T272_Id!=0 AND D166_T272_Id=0");
        $numT234 = mysql_num_rows($sqlT234);

        while($T234 = mysql_fetch_array($sqlT234)){

            $sqlD166 = mysql_query("SELECT D166_Id,D166_Grupo_Suporte,D167_Id,D166_Id_Quadro_Trello,D167_Nome_Lista,D167_Id_Lista_Trello FROM D166 LEFT JOIN D167 ON D166_Lista_Analise_Id=D167_Id WHERE D166_T272_Id='{$T234['T234_T272_Id']}'");
            $D166 = mysql_fetch_array($sqlD166);

            $ch = curl_init("{$urlApi}cards/{$T234['T234_IdCard']}?idList={$D166['D167_Id_Lista_Trello']}&idBoard={$D166['D166_Id_Quadro_Trello']}&pos=bottom&closed=false&key={$key}&token={$token}");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $mudarLista = (array) json_decode($data);

            if($respostaHttp==200){
               mysql_query("UPDATE T234 SET T234_D166_Id='{$D166['D166_Id']}',T234_D167_Id='{$D166['D167_Id']}' WHERE T234_Id='{$T234['T234_Id']}'");
            }   
       }
    }

    public function AtualizarChamadosTrello()
    {
        global $g, $confUsuario;

        require_once('bibliotecas/webservices/trello/apiTrello.php');
		$Trello = new Trello();
		$erros = "";
		$enviados = 0;
		$naoEnviados = 0;
        if(empty($g['C031']['TrelloUrlApi'])){
            $mensagem = "Não existe configuração para o trello nesse banco de dados.";
        }else{
            $sqlT234 = mysql_query("SELECT T234_Id,T234_IdCard,D167_Flag_Enviar_Detalhes,D167_Nome_Lista,D166_Grupo_Suporte,T234_Flag_Arquivado 
								FROM T234 
								Left join D167 on T234_D167_Id=D167_Id 
								left join D166 on T234_D166_Id=D166_Id
								WHERE T234_Flag_Triagem = 'S' AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Finalizado != 'S'");
            if(mysql_num_rows($sqlT234)<=0){
                $mensagem = "Nenhum chamado se encontra em triagem.";
            }else{
                while($T234 = mysql_fetch_array($sqlT234)){

                    if(empty($T234['T234_IdCard'])){
                        $retorno = $this->envioDeCard($T234['T234_Id']);
                    }else{
                        $retorno = $this->atualizarDetalhesChamadoTrello($T234['T234_Id']);
                    }

                    if($retorno[0]===false){
                        $erros.="<br>Chamado {$T234['T234_Id']} - Erro:".$retorno[1];
                        $naoEnviados++;
                    }else{
                        $enviados++;
                    }
                }	

                if(!empty($erros)){
                    $erros = "<br> Chamados não enviados: <b>{$naoEnviados}</b> <br><br> Houveram erros no envio dos chamados abaixo: <br>".$erros; 
                }

                $mensagem = "Chamados enviados com sucesso: <b>{$enviados}</b>{$erros}";
            }
        }
        return $mensagem;
    }





}
?>