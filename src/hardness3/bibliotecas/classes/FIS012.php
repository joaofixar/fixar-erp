<?php
namespace hardness {
    set_time_limit(0);
    require_once("old/lib/funcoes.php");
    definirClasse('FIS012');
    class FIS012_
    {
        public function gerarMDFeCabecalho(){
            global $g;
            $arrayRetorno = array();
            $qtdeNotas = 0;
            $valorCarga = 0;
            $pesoBrutoTotal = 0;
            $ufCarregamento = NULL;
            $count = 0;
            //verificar as notas que estão selecionadas e não possuem vínculo com algum MDf-e
            $T007 = mysql_query("SELECT T007_Numero_Nota_Fiscal, 
                                        T007_Id, 
                                        T284_T276_Id,
                                        T007_Cliente_Estado,
                                        T007_Cliente_Cidade,
                                        D024_D018_Id,
                                        D024_D020_Id,
                                        D148_Entrega.D148_D020_Id AS D148_Entrega_D020_Id,
                                        D148_Entrega.D148_D018_Id AS D148_Entrega_D018_Id,
                                        D148_Redespacho.D148_D020_Id AS D148_Redespacho_D020_Id,
                                        D148_Redespacho.D148_D018_Id AS D148_Redespacho_D018_Id,
                                        D018_UF,
                                        D020_Nome_Cidade,
                                        D018_Codigo_IBGE,
                                        D020_Codigo_IBGE,                                                                               
                                        T007_Valor_Total,
                                        T007_Total_Peso_Bruto,
                                        T007_Peso_Bruto,
                                        T007_Flag_Marcado,
                                        COALESCE(T007_D148_Id_Entrega, 0) AS UF_Entrega,
                                        COALESCE(T007A_D148_Id_Redespacho, 0) AS UF_Redespacho,
                                        T276_Flag_Cancelada,
                                        T276_Flag_Encerrada
                                   FROM T007
                              LEFT JOIN D024 ON T007_D024_Id = D024_Id
                              LEFT JOIN D148 AS D148_Entrega ON T007_D148_Id_Entrega = D148_Entrega.D148_Id
                              LEFT JOIN D020 ON D024_D020_Id = D020_Id
                              LEFT JOIN D018 ON D024_D018_Id = D018_Id     
                              LEFT JOIN T284 ON T284_T007_Id = T007_Id 
                              LEFT JOIN T276 ON T284_T276_Id = T276_Id
                              LEFT JOIN T007A ON T007A_T007_Id = T007_Id
                              LEFT JOIN D148 AS D148_Redespacho ON T007A_D148_Id_Redespacho = D148_Redespacho.D148_Id
                                  WHERE (T007_Flag_Marcado = 'S')
                                    AND (T276_Flag_Cancelada IS NULL OR T276_Flag_Cancelada != 'N')
                                    AND (T276_Flag_Encerrada IS NULL OR T276_Flag_Encerrada != 'S')
                                    AND (T007_C004_Id='{$g['empresaAtual']}' AND T007_Flag_Emitida_Recebida!='R')
                               ORDER BY UF_Entrega DESC");
            if (mysql_num_rows($T007) > 0){
                $qtdeNotas = mysql_num_rows($T007);
                while($mT007 = mysql_fetch_array($T007)){
                    //Se houver um Mdf-e vinculado às notas e o Mdf-e não estiver cancelado
                    if(!empty($mT007['T284_T276_Id']) && $mT007['T276_Flag_Cancelada'] == 'N'){
                        return "Existe(m) nota(s) vinculada(s) com algum MDF-e";
                    }

                    //se houver endereço de entrega cadastrado
                    if($mT007['UF_Redespacho'] > 0){ 
                        $D148 = mysql_query("SELECT D018_UF, 
                                                    D018_Id,
                                                    D020_Nome_Cidade,
                                                    D020_Id
                                               FROM D018
                                          LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                          LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                              WHERE D148_Id='{$mT007['UF_Redespacho']}'
                                                AND D018_Id='{$mT007['D148_Redespacho_D018_Id']}'
                                                AND D020_Id='{$mT007['D148_Redespacho_D020_Id']}'");
                        $mD148 = mysql_fetch_array($D148);
                        $Id_UF_Descarregamento = $mD148['D018_Id'];
                        $sigla_UF_Descarregamento = $mD148['D018_UF']; 
                        $Id_Cidade_Descarregamento = $mD148['D020_Id'];
                        $nomeCidadeDescarregamento = $mD148['D020_Nome_Cidade'];
                        if($count == 0){
                            $UFtemporaria = $mD148['D018_UF'];
                            $count++;
                        }  
                    }else if($mT007['UF_Entrega'] > 0){ 
                        log("POSSUE ENDEREÇO DE ENTREGA CADASTRADO");
                        $D148 = mysql_query("SELECT D018_UF, 
                                                    D018_Id,
                                                    D020_Nome_Cidade,
                                                    D020_Id
                                               FROM D018
                                          LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                          LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                              WHERE D148_Id='{$mT007['UF_Entrega']}'
                                                AND D018_Id='{$mT007['D148_Entrega_D018_Id']}'
                                                AND D020_Id='{$mT007['D148_Entrega_D020_Id']}'");
                        $mD148 = mysql_fetch_array($D148);
                        $Id_UF_Descarregamento = $mD148['D018_Id'];
                        $sigla_UF_Descarregamento = $mD148['D018_UF']; 
                        $Id_Cidade_Descarregamento = $mD148['D020_Id'];
                        $nomeCidadeDescarregamento = $mD148['D020_Nome_Cidade'];
                        if($count == 0){
                            $UFtemporaria = $mD148['D018_UF'];
                            $count++;
                        }                 
                    }else{ //se não houver endereço de entrega, pega do cadastro do cliente 
                        $Id_UF_Descarregamento = $mT007['D024_D018_Id'];
                        $sigla_UF_Descarregamento = $mT007['D018_UF'];
                        $Id_Cidade_Descarregamento = $mT007['D024_D020_Id'];
                        $nomeCidadeDescarregamento = $mT007['D020_Nome_Cidade'];
                        if($count == 0){
                            $UFtemporaria = $mT007['D018_UF'];
                            $count++;
                        }
                    }

                    log("sigla_UF_Descarregamento: ".$sigla_UF_Descarregamento);
                    log("UFtemporaria: ".$UFtemporaria);
                    log("CodigoUFDescarregamento: ".$Id_UF_Descarregamento);
                    log("CodigoCidadeDescarregamento: ".$Id_Cidade_Descarregamento);
                    log("nomeCidadeDescarregamento: ".$nomeCidadeDescarregamento);

                    // Retirada validação 29/03/2019 pois existiam clientes que entregavam em UF diferentes da do cliente
                    /*if($sigla_UF_Descarregamento != $UFtemporaria){
                        log("UF DESCARREGAMENTO: ".$sigla_UF_Descarregamento);
                        log("UF TEMPORARIA: ".$UFtemporaria);
                        return "Existe(m) nota(s) com UF diferente. Verifique a UF dos endereços de entrega se existirem";
                    }*/

                    // Será utilizado o preço que estiver preenchido no formulário, se ele for maior que 0
                    if($arrayDataT007['T007_Peso_Bruto'] > 0){
                        $T276_Peso_Bruto_Total_Carga = gCorrigeNumero($arrayDataT007['T007_Peso_Bruto']);
                    }else{
                        $T276_Peso_Bruto_Total_Carga = gCorrigeNumero($arrayDataT007['T007_Total_Peso_Bruto']);
                    }

                    $valorCarga += $mT007['T007_Valor_Total'];
                    $pesoBrutoTotal += $T276_Peso_Bruto_Total; 
                }
                $C004 = mysql_query("SELECT C004_Id, 
                                        C004_D020_Id,
                                        C004_D018_Id,
                                        C004_D170_Id,
                                        C004_D172_Id,
                                        C004_Nome_Empresa,
                                        D020_Nome_Cidade,
                                        C004_Uf,
                                        C004_Cnpj,
                                        C004_Inscricao_Estadual,
                                        C004_Logradouro,
                                        C004_Numero,
                                        C004_Complemento,
                                        C004_Bairro,
                                        C004_Cep,
                                        C004_Ambiente_MDFe,
                                        C004_Flag_Emitente,
                                        C004_Flag_Transportador,
                                        C004_Versao_MDFe,
                                        C004_Tipo_Carga_Padrao,
                                        C004_Tipo_Transporte_Padrao,
                                        D018_UF,
                                        D018_Codigo_IBGE,
                                        D020_Codigo_IBGE,
                                        D170_D109_Id
                                   FROM C004
                              LEFT JOIN D018 ON D018_Id=C004_D018_Id
                              LEFT JOIN D020 ON D020_Id=C004_D020_Id
                              LEFT JOIN D170 ON D170_Id=C004_D170_Id
                                  WHERE C004_Id='{$g['empresaAtual']}'");
                $mC004 = mysql_fetch_array($C004);

                //motorista padrão
                if(!empty($mC004['C004_D170_Id']) || $mC004['C004_D170_Id'] != 0){
                    $motoristaPadrao = $mC004['D170_D109_Id'];
                }

                //inserir seguradora padrão
                $idSeguradoraPadrao = $mC004['C004_D172_Id'];

                //-- OBSERVACAO: DESENVOLVER SCRIPT PARA MUDAR O NUMERO DE SERIE CASO O TOTAL DE NOTAS ULTRAPASSE 999.999.999 (nossa!!!)
                if ($mC004['C004_Ambiente_MDFe'] == '3') {
                    $mC004['C004_Ambiente_MDFe'] = '1';
                    $nNumero_Serie              = 900;
                } else {
                    $nNumero_Serie = 1;
                }

                $numeroMdfe = mysql_query("SELECT coalesce(T276_Numero_MDFe,0) as T276_Numero_MDFe 
                                                 FROM T276
                                                WHERE T276_C004_Id='{$g['empresaAtual']}'
                                             ORDER BY round(T276_Numero_MDFe) DESC LIMIT 0,1;");
                $mT276Numero = mysql_fetch_array($numeroMdfe);
                $Proximo_Numero = $mT276Numero['T276_Numero_MDFe'] + 1;

                //modal sempre receberá 1 pois´por enquanto o mdf-e só atende o modal rodoviário
                $modal = 1;

                mysql_query("LOCK T276");
                mysql_query("LOCK T283");
                mysql_query("LOCK T282");
                mysql_query("LOCK T284");
                mysql_query("LOCK T279");
                mysql_query("LOCK T278");
                mysql_query("LOCK T281");
                mysql_query("START TRANSACTION");

                //inserir cabeçalho MDf-e - devolver ID com o mysqlLastId
                $cClausula = "INSERT INTO T276";
                $cClausula .= "(T276_Ambiente_MDFe, ";
                $cClausula .= "T276_Serie, ";
                $cClausula .= "T276_Numero_MDFe, ";
                $cClausula .= "T276_Flag_Modalidade_Transporte, ";
                $cClausula .= "T276_Data_Emissao, ";
                $cClausula .= "T276_Hora_Emissao, ";
                $cClausula .= "T276_C004_Id, ";
                $cClausula .= "T276_Flag_Emitente, ";
                $cClausula .= "T276_Emitente_CNPJ, ";
                $cClausula .= "T276_Emitente_Inscricao_Estadual, ";
                $cClausula .= "T276_Emitente_Nome, ";
                $cClausula .= "T276_Emitente_Nome_Fantasia, ";
                $cClausula .= "T276_Emitente_Rua, ";
                $cClausula .= "T276_Emitente_Numero, ";
                $cClausula .= "T276_Emitente_Cidade_IBGE, ";//código IBGE da cidade da empresa emitente
                $cClausula .= "T276_Emitente_Cidade, ";//Nome cidade da empresa emitente
                $cClausula .= "T276_Emitente_CEP, ";
                $cClausula .= "T276_Emitente_UF, ";//Sigla UF da empresa emitente
                $cClausula .= "T276_Quantidade_NFe, ";
                $cClausula .= "T276_Valor_Carga, ";
                $cClausula .= "T276_Peso_Bruto_Total_Carga, ";
                $cClausula .= "T276_Emitente_Bairro, ";
                $cClausula .= "T276_D109_Id, ";
                $cClausula .= "T276_C007_Id, ";
                $cClausula .= "T276_Emitente_UF_IBGE, ";//Código IBGE da UF da empresa emitente
                $cClausula .= "T276_D018_Id_Descarregamento, "; //ID da UF da nota
                $cClausula .= "T276_D018_Id_Carregamento, "; //ID da UF da empresa emitente
                $cClausula .= "T276_D020_Id_Carregamento, "; //ID da Cidade da empresa emitente
                $cClausula .= "T276_Fuso_Horario_Emissao ";
                $cClausula .= " ) values (";
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Ambiente_MDFe']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($nNumero_Serie) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($Proximo_Numero) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($modal) . "', ";
                $cClausula .= "CURDATE(),";
                $cClausula .= "CURTIME(),";
                $cClausula .= " '" . mysql_real_escape_string($g['empresaAtual']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Flag_Emitente']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Cnpj']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Inscricao_Estadual']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', "; // razao social
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', "; // nome fantasia
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Logradouro']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Numero']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['D020_Codigo_IBGE']) . "', ";//código IBGE da cidade da empresa emitente
                $cClausula .= " '" . mysql_real_escape_string($mC004['D020_Nome_Cidade']) . "', ";//Nome cidade da empresa emitente
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Cep']) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($mC004['D018_UF']) . "', ";//Sigla UF da empresa emitente
                $cClausula .= " '" . mysql_real_escape_string($qtdeNotas) . "', ";
                $cClausula .= " '" . mysql_real_escape_string($valorCarga) . "', "; //somar o valor da carga de todas as notas
                $cClausula .= " '" . mysql_real_escape_string($pesoBrutoTotal) . "', "; //somar o peso bruto total carga de todas as notas
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Bairro']) . "', "; 
                $cClausula .= " '" . mysql_real_escape_string($motoristaPadrao) . "', "; 
                $cClausula .= " '" . mysql_real_escape_string($g['usuarioAtual']) . "', "; 
                $cClausula .= " '" . mysql_real_escape_string($mC004['D018_Codigo_IBGE']) . "', "; //Código IBGE da UF da empresa emitente
                $cClausula .= " '" . mysql_real_escape_string($Id_UF_Descarregamento) . "', ";//ID da UF da nota
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_D018_Id']) . "', ";//ID da UF da empresa emitente
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_D020_Id']) . "', ";//ID da Cidade da empresa emitente
                $cClausula .= " '" . date("P") . "' ";
                $cClausula .= " ) "; 
                log("sqlInsert T276: ".$cClausula);

                mysql_query($cClausula);
                $cErro= mysql_error();
                if(!empty($cErro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao dar insert na T276= ".$cErro;
                }
                $T276_Id = $g['mysqlLastId'];
                log("T276_Id= ".$T276_Id);
                $arrayRetorno['T276_Id'] = $T276_Id;

                //inserir veículo padrão - T283 (modal rodoviário)
                if(!empty($mC004['C004_D170_Id'])){
                    $D170 = mysql_query("SELECT * FROM D170 left join D018 on D018_Id=D170_D018_Id where D170_Id='{$mC004['C004_D170_Id']}'");
                    $mD170 = mysql_fetch_array($D170);
                    $T283 = "INSERT INTO T283(";
                    $T283 .= "T283_Placa, ";
                    $T283 .= "T283_Renavam, ";
                    $T283 .= "T283_Tara, ";
                    $T283 .= "T283_Capacidade_Kg, ";
                    $T283 .= "T283_Capacidade_M3, ";
                    $T283 .= "T283_Flag_Rodado, ";
                    $T283 .= "T283_Flag_Carroceria, ";
                    $T283 .= "T283_UF_Licenciamento, ";
                    $T283 .= "T283_D170_Id, ";
                    $T283 .= "T283_Versao_Modal, ";
                    $T283 .= "T283_T276_Id, ";
                    $T283 .= "T283_Identificacao_Veiculo, ";
                    $T283 .= "T283_Flag_Carro_Proprio, ";
                    $T283 .= "T283_CNPJ_Proprietario, ";
                    $T283 .= "T283_Proprietario_Veiculo, ";
                    $T283 .= "T283_Inscricao_Estadual_Proprietario, ";
                    $T283 .= "T283_Categoria_Veiculo, ";
                    $T283 .= "T283_D018_Id ";
                    $T283 .= " )VALUES( ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Placa']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Renavam']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Tara']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Capacidade_Kg']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Capacidade_M3']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Flag_Rodado']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Flag_Carroceria']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D018_UF']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mC004['C004_D170_Id']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($modal) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Identificacao_Veiculo']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Flag_Carro_Proprio']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mC004['C004_Cnpj']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mC004['C004_Inscricao_Estadual']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_Categoria']) . "', ";
                    $T283 .= " '" . mysql_real_escape_string($mD170['D170_D018_Id']) . "' ";
                    $T283 .= " ) ";
                    mysql_query($T283);
                    log("sqlInsert T283: ".$T283);
                    $cErro = mysql_error();
                    if(!empty($cErro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao dar insert na T283: ".$cErro;        
                    } 
                }

                //inserir condutor padrão - T278
                if(!empty($motoristaPadrao)){
                    $D109 = mysql_query("SELECT D109_Id, D109_Nome,D109_Cpf FROM D109 where D109_Id='{$mC004['D170_D109_Id']}'");
                    $mD109 = mysql_fetch_array($D109);
                    $T278 = "INSERT INTO T278(";
                    $T278 .= "T278_D109_Id, ";
                    $T278 .= "T278_T276_Id, ";
                    $T278 .= "T278_Nome, ";
                    $T278 .= "T278_CPF ";
                    $T278 .= " ) VALUES (";
                    $T278 .= " '" . mysql_real_escape_string($mC004['D170_D109_Id']) . "', ";
                    $T278 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                    $T278 .= " '" . mysql_real_escape_string($mD109['D109_Nome']) . "', ";
                    $T278 .= " '" . mysql_real_escape_string($mD109['D109_Cpf']) . "' ";
                    $T278 .= " ) ";
                    mysql_query($T278);
                    $cErro = mysql_error();
                    if(!empty($cErro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao dar insert na T278: ".$cErro;        
                    } 
                }

                //inserir seguradora padrão - T279
                if($mC004['C004_D172_Id'] > 0){
                    $D172 = mysql_query("SELECT * FROM D172 where D172_Id='{$mC004['C004_D172_Id']}'");
                    $mD172 = mysql_fetch_array($D172);
                    $T279 = "INSERT INTO T279(";
                    $T279 .= "T279_T276_Id, ";
                    $T279 .= "T279_D172_Id, ";
                    $T279 .= "T279_Seguradora_Nome, ";
                    $T279 .= "T279_Seguradora_CNPJ ";
                    $T279 .= " ) VALUES (";
                    $T279 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                    $T279 .= " '" . mysql_real_escape_string($mC004['C004_D172_Id']) . "', ";
                    $T279 .= " '" . mysql_real_escape_string($mD172['D172_Nome']) . "', ";
                    $T279 .= " '" . mysql_real_escape_string($mD172['D172_CNPJ']) . "' ";
                    $T279 .= " ) ";
                    mysql_query($T279);
                    $cErro = mysql_error();
                    if(!empty($cErro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao dar insert na T279: ".$cErro;        
                    } 
                }

                if(!empty($mT007['T284_T276_Id']) && $mT007['T276_Flag_Cancelada'] == 'S'){//Se houver um Mdf-e vinculado às notas e ele estiver cancelado dou um UPDATE na T284
                    //UPDATE notas selecionadas- T284 (notas MDf-e)
                    if($qtdeNotas > 0){
                        $notaT007 = mysql_query("SELECT T007_Id, 
                                                        T284_Id,
                                                        T007_D024_Id,
                                                        D024_Nome_Empresa,
                                                        D024_Nome_Fantasia,
                                                        T007_Cliente_Razao,
                                                        T007_Flag_ACP,
                                                        T007_Numero_Nota_Fiscal,  
                                                        T007_Valor_Total,
                                                        D020_Codigo_IBGE,
                                                        D148_Entrega.D148_D018_Id AS D148_Entrega_D018_Id,
                                                        D148_Entrega.D148_D020_Id AS D148_Entrega_D020_Id,
                                                        D148_Redespacho.D148_D018_Id AS D148_Redespacho_D018_Id,
                                                        D148_Redespacho.D148_D020_Id AS D148_Redespacho_D020_Id,
                                                        D018_UF,
                                                        D024_D020_Id,
                                                        D024_D018_Id,
                                                        D020_Nome_Cidade,
                                                        T007_Total_Peso_Bruto,
                                                        T007_Peso_Bruto,
                                                        T007_Chave_Acesso_Nfe,
                                                        COALESCE(T007_D148_Id_Entrega, 0) AS UF_Entrega,
                                                        COALESCE(T007A_D148_Id_Redespacho, 0) AS UF_Redespacho,
                                                        T276_Flag_Cancelada,
                                                        T276_Flag_Encerrada
                                                   FROM T007
                                              LEFT JOIN D024 ON T007_D024_Id = D024_Id
                                              LEFT JOIN D148 AS D148_Entrega ON T007_D148_Id_Entrega = D148_Entrega.D148_Id
                                              LEFT JOIN D020 ON D024_D020_Id = D020_Id
                                              LEFT JOIN D018 ON D024_D018_Id = D018_Id     
                                              LEFT JOIN T284 ON T284_T007_Id = T007_Id 
                                              LEFT JOIN T276 ON T284_T276_Id = T276_Id
                                              LEFT JOIN T007A ON T007A_T007_Id = T007_Id
                                              LEFT JOIN D148 AS D148_Redespacho ON T007A_D148_Id_Redespacho = D148_Redespacho.D148_Id
                                                  WHERE (T007_Flag_Marcado = 'S')
                                                    AND (T276_Flag_Cancelada IS NULL OR T276_Flag_Cancelada != 'N')
                                                    AND (T276_Flag_Encerrada IS NULL OR T276_Flag_Encerrada != 'S')
                                                    AND (T007_C004_Id='{$g['empresaAtual']}' AND T007_Flag_Emitida_Recebida!='R')
                                               ORDER BY UF_Entrega DESC");
                        while($rowT007 = mysql_fetch_array($notaT007)){
                            $T284_Id = $rowT007['T284_Id'];
                            //pega o valor da uf e da cidade de entrega, se houver endereço cadastrado
                            if($rowT007['UF_Redespacho'] > 0){ 
                                log("VAI SALVAR O ENDEREÇO DE REDESPACHO");
                                $D148 = mysql_query("SELECT D018_UF, 
                                                            D018_Id,
                                                            D020_Nome_Cidade,
                                                            D020_Id,
                                                            D020_Codigo_IBGE
                                                       FROM D018
                                                  LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                                  LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                                      WHERE D148_Id='{$rowT007['UF_Redespacho']}'
                                                        AND D018_Id='{$rowT007['D148_Redespacho_D018_Id']}'
                                                        AND D020_Id='{$rowT007['D148_Redespacho_D020_Id']}'");
                                $rowD148 = mysql_fetch_array($D148);
                                $D018_UF_Descarregamento = $rowD148['D018_UF'];
                                $D018_Id_Descarregamento = $rowD148['D018_Id'];
                                $D020_Id_Descarregamento = $rowD148['D020_Id'];
                                $D020_Nome_Cidade_Descarregamento = $rowD148['D020_Nome_Cidade']; 
                                $D020_IBGE_Cidade_Descarregamento = $rowD148['D020_Codigo_IBGE'];                 
                            }else if($rowT007['UF_Entrega'] > 0){ 
                                log("VAI SALVAR O ENDEREÇO DE ENTREGA");
                                $D148 = mysql_query("SELECT D018_UF, 
                                                            D018_Id,
                                                            D020_Nome_Cidade,
                                                            D020_Id,
                                                            D020_Codigo_IBGE
                                                       FROM D018
                                                  LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                                  LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                                      WHERE D148_Id='{$rowT007['UF_Entrega']}'
                                                        AND D018_Id='{$rowT007['D148_Entrega_D018_Id']}'
                                                        AND D020_Id='{$rowT007['D148_Entrega_D020_Id']}'");
                                $rowD148 = mysql_fetch_array($D148);
                                $D018_UF_Descarregamento = $rowD148['D018_UF'];
                                $D018_Id_Descarregamento = $rowD148['D018_Id'];
                                $D020_Id_Descarregamento = $rowD148['D020_Id'];
                                $D020_Nome_Cidade_Descarregamento = $rowD148['D020_Nome_Cidade']; 
                                $D020_IBGE_Cidade_Descarregamento = $rowD148['D020_Codigo_IBGE'];                 
                            }else{ //se não houver endereço de entrega, pega do cadastro do cliente
                                log("VAI SALVAR A UF DO CADASTRO DE CLIENTE");
                                $D018_Id_Descarregamento = $rowT007['D024_D018_Id'];
                                $D018_UF_Descarregamento = $rowT007['D018_UF'];
                                $D020_Id_Descarregamento = $rowT007['D024_D020_Id'];
                                $D020_Nome_Cidade_Descarregamento = $rowT007['D020_Nome_Cidade'];
                                $D020_IBGE_Cidade_Descarregamento = $rowT007['D020_Codigo_IBGE'];
                            }

                            log("D018_UF_Descarregamento: ".$D018_UF_Descarregamento);
                            log("D018_Id_Descarregamento: ".$D018_Id_Descarregamento);
                            log("D020_Id_Descarregamento: ".$D020_Id_Descarregamento);
                            log("D020_Nome_Cidade_Descarregamento: ".$D020_Nome_Cidade_Descarregamento);
                            log("D020_IBGE_Cidade_Descarregamento: ".$D020_IBGE_Cidade_Descarregamento);

                            $flag_Tipo_Documento = '2'; //Tipo 1= CT-e ; Tipo 2= NF-e

                            // Será utilizado o preço que estiver preenchido no formulário, se ele for maior que 0
                            if($rowT007['T007_Peso_Bruto'] > 0){
                                $T284_Peso_Bruto_Total_Carga = mysql_real_escape_string($rowT007['T007_Peso_Bruto']);
                            }else{
                                $T284_Peso_Bruto_Total_Carga = mysql_real_escape_string($rowT007['T007_Total_Peso_Bruto']);
                            }

                            $T284 = "UPDATE T284 SET ";
                            $T284 .= " T284_Peso_Bruto_Total = '" . $T284_Peso_Bruto_Total_Carga . "', ";
                            $T284 .= "T284_Valor_Carga = '" . mysql_real_escape_string($rowT007['T007_Valor_Total']) . "', ";
                            $T284 .= "T284_Numero_NFe = '" . mysql_real_escape_string($rowT007['T007_Numero_Nota_Fiscal']) . "', ";
                            $T284 .= "T284_T276_Id = '" . mysql_real_escape_string($T276_Id) . "', ";
                            $T284 .= "T284_T007_Id = '" . mysql_real_escape_string($rowT007['T007_Id']) . "', ";
                            $T284 .= "T284_D020_Id = '" . mysql_real_escape_string($D020_Id_Descarregamento) . "', "; //Id da cidade da nota (endereço entrega ou cadastro cliente)
                            $T284 .= "T284_D018_Id = '" . mysql_real_escape_string($D018_Id_Descarregamento) . "', "; //Id da UF da nota (endereço entrega ou cadastro cliente)
                            $T284 .= "T284_Cidade_Descarregamento = '" . mysql_real_escape_string($D020_Nome_Cidade_Descarregamento) . "', ";//Nome da cidade da nota
                            $T284 .= "T284_Cidade_Descarregamento_Codigo_IBGE = '" . mysql_real_escape_string($D020_IBGE_Cidade_Descarregamento) . "', ";//Código IBGE da cidade da nota
                            $T284 .= "T284_Flag_Tipo_Documento = '" . mysql_real_escape_string($flag_Tipo_Documento) . "', ";
                            $T284 .= "T284_Destinatario_Nome = '" . mysql_real_escape_string($rowT007['D024_Nome_Empresa']) . "', ";
                            $T284 .= "T284_Chave_De_Acesso_NFe = '" . mysql_real_escape_string($rowT007['T007_Chave_Acesso_Nfe']) . "' ";
                            $T284 .= "WHERE T284_Id='".$T284_Id."'";
                            log("sqlUPDATE T284: ".$T284);
                            mysql_query($T284);
                            $cErro = mysql_error();
                            if(!empty($cErro)){
                                mysql_query("ROLLBACK");
                                return "Erro ao dar UPDATE na T284: ".$cErro;        
                            } 

                            // Comentado pois esses campos não estão mais sendo utilizados
                            /*if($mC004['C004_Tipo_Transporte_Padrao'] > 0){
                                //inserir tipo de transporte padrão
                                $T281 = "INSERT INTO T281(";
                                $T281 .= "T281_Flag_Unidade_Transporte, ";
                                $T281 .= "T281_T284_Id ";
                                $T281 .= " ) VALUES (";
                                $T281 .= " '" . mysql_real_escape_string($mC004['C004_Tipo_Transporte_Padrao']) . "', ";
                                $T281 .= " '" . mysql_real_escape_string($T284_Id) . "' ";
                                $T281 .= " ) ";
                                log("sql insert T281: ".$T281);
                                mysql_query($T281);
                                $T281_Id = $g['mysqlLastId'];
                                $cErro = mysql_error();
                                if(!empty($cErro)){
                                    mysql_query("ROLLBACK");
                                    return "Erro ao dar insert na T281: ".$cErro;        
                                } 

                                if($mC004['C004_Tipo_Carga_Padrao'] > 0){
                                    //inserir tipo de carga padrão
                                    $T282 = "INSERT INTO T282(";
                                    $T282 .= "T282_Flag_Unidade_Carga, ";
                                    $T282 .= "T282_T281_Id ";
                                    $T282 .= " ) VALUES (";
                                    $T282 .= " '" . mysql_real_escape_string($mC004['C004_Tipo_Carga_Padrao']) . "', ";
                                    $T282 .= " '" . mysql_real_escape_string($T281_Id) . "' ";
                                    $T282 .= " ) ";
                                    log("sql insert T282: ".$T282);
                                    mysql_query($T282);
                                    $cErro = mysql_error();
                                    if(!empty($cErro)){
                                        mysql_query("ROLLBACK");
                                        return "Erro ao dar insert na T282: ".$cErro;        
                                    }  
                                }
                            }*/
                            mysqli_query("UPDATE T007 SET T007_Flag_Marcado='N' WHERE T007_Id='{$rowT007['T007_Id']}'");
                        }
                    }
                }else{ //Se não houver um Mdf-e vinculado às notas e ele não estiver cancelado dou um INSERT na T284
                    if($qtdeNotas > 0){
                        $notaT007 = mysql_query("SELECT T007_Id, 
                                                        T007_D024_Id,
                                                        D024_Nome_Empresa,
                                                        D024_Nome_Fantasia,
                                                        T007_Cliente_Razao,
                                                        T007_Flag_ACP,
                                                        T007_Numero_Nota_Fiscal,  
                                                        T007_Valor_Total,
                                                        D020_Codigo_IBGE,
                                                        D148_Entrega.D148_D018_Id AS D148_Entrega_D018_Id,
                                                        D148_Entrega.D148_D020_Id AS D148_Entrega_D020_Id,
                                                        D148_Redespacho.D148_D018_Id AS D148_Redespacho_D018_Id,
                                                        D148_Redespacho.D148_D020_Id AS D148_Redespacho_D020_Id,
                                                        D018_UF,
                                                        D024_D020_Id,
                                                        D024_D018_Id,
                                                        D020_Nome_Cidade,
                                                        T007_Total_Peso_Bruto,
                                                        T007_Peso_Bruto,
                                                        T007_Chave_Acesso_Nfe,
                                                        COALESCE(T007_D148_Id_Entrega, 0) AS UF_Entrega,
                                                        COALESCE(T007A_D148_Id_Redespacho, 0) AS UF_Redespacho,
                                                        T276_Flag_Cancelada,
                                                        T276_Flag_Encerrada
                                                   FROM T007
                                              LEFT JOIN D024 ON T007_D024_Id = D024_Id
                                              LEFT JOIN D148 AS D148_Entrega ON T007_D148_Id_Entrega = D148_Entrega.D148_Id
                                              LEFT JOIN D020 ON D024_D020_Id = D020_Id
                                              LEFT JOIN D018 ON D024_D018_Id = D018_Id     
                                              LEFT JOIN T284 ON T284_T007_Id = T007_Id 
                                              LEFT JOIN T276 ON T284_T276_Id = T276_Id
                                              LEFT JOIN T007A ON T007A_T007_Id = T007_Id
                                              LEFT JOIN D148 AS D148_Redespacho ON T007A_D148_Id_Redespacho = D148_Redespacho.D148_Id
                                                  WHERE (T007_Flag_Marcado = 'S')
                                                    AND (T276_Flag_Cancelada IS NULL OR T276_Flag_Cancelada != 'N')
                                                    AND (T276_Flag_Encerrada IS NULL OR T276_Flag_Encerrada != 'S')
                                                    AND (T007_C004_Id='{$g['empresaAtual']}' AND T007_Flag_Emitida_Recebida!='R')
                                               ORDER BY UF_Entrega DESC");
                        while($rowT007 = mysql_fetch_array($notaT007)){
                            //pega o valor da uf e da cidade de entrega, se houver endereço cadastrado
                            if($rowT007['UF_Redespacho'] > 0){ //se houver endereço de redespacho cadastrado
                                log("VAI SALVAR O ENDEREÇO DE REDESPACHO");
                                $D148 = mysql_query("SELECT D018_UF, 
                                                            D018_Id,
                                                            D020_Nome_Cidade,
                                                            D020_Id,
                                                            D020_Codigo_IBGE
                                                       FROM D018
                                                  LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                                  LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                                      WHERE D148_Id='{$rowT007['UF_Redespacho']}'
                                                        AND D018_Id='{$rowT007['D148_Redespacho_D018_Id']}'
                                                        AND D020_Id='{$rowT007['D148_Redespacho_D020_Id']}'");

                                $rowD148 = mysql_fetch_array($D148);
                                $D018_UF_Descarregamento = $rowD148['D018_UF'];
                                $D018_Id_Descarregamento = $rowD148['D018_Id'];
                                $D020_Id_Descarregamento = $rowD148['D020_Id'];
                                $D020_Nome_Cidade_Descarregamento = $rowD148['D020_Nome_Cidade']; 
                                $D020_IBGE_Cidade_Descarregamento = $rowD148['D020_Codigo_IBGE'];                 
                            }else if($rowT007['UF_Entrega'] > 0){ //se houver endereço de entrega cadastrado
                                log("VAI SALVAR O ENDEREÇO DE ENTREGA");
                                $D148 = mysql_query("SELECT D018_UF, 
                                                            D018_Id,
                                                            D020_Nome_Cidade,
                                                            D020_Id,
                                                            D020_Codigo_IBGE
                                                       FROM D018
                                                  LEFT JOIN D020 ON D020_D018_Id=D018_Id
                                                  LEFT JOIN D148 ON D148_D018_Id=D018_Id
                                                      WHERE D148_Id='{$rowT007['UF_Entrega']}'
                                                        AND D018_Id='{$rowT007['D148_Entrega_D018_Id']}'
                                                        AND D020_Id='{$rowT007['D148_Entrega_D020_Id']}'");

                                $rowD148 = mysql_fetch_array($D148);
                                $D018_UF_Descarregamento = $rowD148['D018_UF'];
                                $D018_Id_Descarregamento = $rowD148['D018_Id'];
                                $D020_Id_Descarregamento = $rowD148['D020_Id'];
                                $D020_Nome_Cidade_Descarregamento = $rowD148['D020_Nome_Cidade']; 
                                $D020_IBGE_Cidade_Descarregamento = $rowD148['D020_Codigo_IBGE'];                 
                            }else{ //se não houver endereço de entrega, pega do cadastro do cliente
                                log("VAI SALVAR A UF DOP CADASTRO DE CLIENTE");
                                $D018_Id_Descarregamento = $rowT007['D024_D018_Id'];
                                $D018_UF_Descarregamento = $rowT007['D018_UF'];
                                $D020_Id_Descarregamento = $rowT007['D024_D020_Id'];
                                $D020_Nome_Cidade_Descarregamento = $rowT007['D020_Nome_Cidade'];
                                $D020_IBGE_Cidade_Descarregamento = $rowT007['D020_Codigo_IBGE'];
                            }

                            log("D018_UF_Descarregamento: ".$D018_UF_Descarregamento);
                            log("D018_Id_Descarregamento: ".$D018_Id_Descarregamento);
                            log("D020_Id_Descarregamento: ".$D020_Id_Descarregamento);
                            log("D020_Nome_Cidade_Descarregamento: ".$D020_Nome_Cidade_Descarregamento);
                            log("D020_IBGE_Cidade_Descarregamento: ".$D020_IBGE_Cidade_Descarregamento);

                            $flag_Tipo_Documento = '2'; //Tipo 1= CT-e ; Tipo 2= NF-e

                            // Será utilizado o preço que estiver preenchido no formulário, se ele for maior que 0
                            if($rowT007['T007_Peso_Bruto'] > 0){
                                $T284_Peso_Bruto_Total_Carga = mysql_real_escape_string($rowT007['T007_Peso_Bruto']);
                            }else{
                                $T284_Peso_Bruto_Total_Carga = mysql_real_escape_string($rowT007['T007_Total_Peso_Bruto']);
                            }

                            $T284 = "INSERT INTO T284(";
                            $T284 .= "T284_Peso_Bruto_Total, ";
                            $T284 .= "T284_Valor_Carga, ";
                            $T284 .= "T284_Numero_NFe, ";
                            $T284 .= "T284_T276_Id, ";
                            $T284 .= "T284_T007_Id, ";
                            $T284 .= "T284_D020_Id, "; //Id da cidade da nota (endereço entrega ou cadastro cliente)
                            $T284 .= "T284_D018_Id, "; //Id da UF da nota (endereço entrega ou cadastro cliente)
                            $T284 .= "T284_Cidade_Descarregamento, ";//Nome da cidade da nota
                            $T284 .= "T284_Cidade_Descarregamento_Codigo_IBGE, ";//Código IBGE da cidade da nota
                            $T284 .= "T284_Flag_Tipo_Documento, ";
                            $T284 .= "T284_Destinatario_Nome, ";
                            $T284 .= "T284_Chave_De_Acesso_NFe ";
                            $T284 .= " ) VALUES (";
                            $T284 .= " '" . $T284_Peso_Bruto_Total_Carga . "', ";
                            $T284 .= " '" . mysql_real_escape_string($rowT007['T007_Valor_Total']) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($rowT007['T007_Numero_Nota_Fiscal']) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($rowT007['T007_Id']) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($D020_Id_Descarregamento) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($D018_Id_Descarregamento) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($D020_Nome_Cidade_Descarregamento) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($D020_IBGE_Cidade_Descarregamento) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($flag_Tipo_Documento) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($rowT007['D024_Nome_Empresa']) . "', ";
                            $T284 .= " '" . mysql_real_escape_string($rowT007['T007_Chave_Acesso_Nfe']) . "' ";
                            $T284 .= " ) ";
                            log("sqlInsert T284: ".$T284);
                            mysql_query($T284);
                            $T284_Id = $g['mysqlLastId'];
                            $cErro = mysql_error();
                            if(!empty($cErro)){
                                mysql_query("ROLLBACK");
                                return "Erro ao dar insert na T284: ".$cErro;        
                            } 


                           /* if($mC004['C004_Tipo_Transporte_Padrao'] > 0){
                                //inserir tipo de transporte padrão
                                $T281 = "INSERT INTO T281(";
                                $T281 .= "T281_Flag_Unidade_Transporte, ";
                                $T281 .= "T281_T284_Id ";
                                $T281 .= " ) VALUES (";
                                $T281 .= " '" . mysql_real_escape_string($mC004['C004_Tipo_Transporte_Padrao']) . "', ";
                                $T281 .= " '" . mysql_real_escape_string($T284_Id) . "' ";
                                $T281 .= " ) ";
                                log("sql insert T281: ".$T281);
                                mysql_query($T281);
                                $T281_Id = $g['mysqlLastId'];
                                $cErro = mysql_error();
                                if(!empty($cErro)){
                                    mysql_query("ROLLBACK");
                                    return "Erro ao dar insert na T281: ".$cErro;        
                                } 

                                if($mC004['C004_Tipo_Carga_Padrao'] > 0){
                                    //inserir tipo de carga padrão
                                    $T282 = "INSERT INTO T282(";
                                    $T282 .= "T282_Flag_Unidade_Carga, ";
                                    $T282 .= "T282_T281_Id ";
                                    $T282 .= " ) VALUES (";
                                    $T282 .= " '" . mysql_real_escape_string($mC004['C004_Tipo_Carga_Padrao']) . "', ";
                                    $T282 .= " '" . mysql_real_escape_string($T281_Id) . "' ";
                                    $T282 .= " ) ";
                                    log("sql insert T282: ".$T282);
                                    mysql_query($T282);
                                    $cErro = mysql_error();
                                    if(!empty($cErro)){
                                        mysql_query("ROLLBACK");
                                        return "Erro ao dar insert na T282: ".$cErro;        
                                    }  
                                }
                            }*/
                            mysqli_query("UPDATE T007 SET T007_Flag_Marcado='N' WHERE T007_Id='{$rowT007['T007_Id']}'");
                        }
                    }
                }
            }  
            mysql_query("call T276_Gravar_Totalizacao('{$T276_Id}')");
            mysql_query("COMMIT");
            mysql_query("UNLOCK ALL");
            return $arrayRetorno;
        }

        /* Raquel 03/01/2019*/
        /**
         * incluirMDFe
         * programador: Lígia Nakazato
         * @param Form $form
         * @param String $T276_Id
         * @return String/bool
         */   
        public function incluirMDFe($form, $T276_Id){
            global $g;
            $empresaEmitente      = $form->campoValorEnviado('T276_Emitente_Nome_Fantasia');
            $tipoEmitente         = $form->campoValorEnviado('T276_Flag_Emitente');
            $tipoTransportador    = $form->campoValorEnviado('T276_Flag_Transportador');
            $modalForm            = $form->campoValorEnviado('T276_Flag_Modalidade_Transporte');
            $ufCarregamento       = $form->campoValorEnviado('T276_D018_Id_Carregamento');
            $cidadeCarregamento   = $form->campoValorEnviado('T276_D020_Id_Carregamento');
            $ufDescarregamento    = $form->campoValorEnviado('T276_D018_Id_Descarregamento');
            $dataInicioViagem     = troca_data_amd($form->campoValorEnviado('T276_Data_Inicio_Viagem'));
            $horaInicioViagem     = $form->campoValorEnviado('T276_Hora_Inicio_Viagem');

            log("empresa emitente: ".$empresaEmitente);
            log("tipo emitente: ".$tipoEmitente);
            log("tipo transportador: ".$tipoTransportador);
            log("modal: ".$modalForm);
            log("UF carregamento: ".$ufCarregamento);
            log("cidade carregamento: ".$cidadeCarregamento);
            log("UF descarregamento: ".$ufDescarregamento);
            log("dataInicioViagem: ".$dataInicioViagem);
            log("horaInicioViagem: ".$horaInicioViagem);

            //$T007_Data_Saida   = troca_data_amd($T007_Data_Saida);

            $C004 = mysql_query("SELECT C004_Id, 
                                        C004_D020_Id,
                                        C004_D018_Id,
                                        C004_D170_Id,
                                        C004_D172_Id,
                                        C004_Nome_Empresa,
                                        D020_Nome_Cidade,
                                        C004_Uf,
                                        C004_Cnpj,
                                        C004_Inscricao_Estadual,
                                        C004_Logradouro,
                                        C004_Numero,
                                        C004_Complemento,
                                        C004_Bairro,
                                        C004_Cep,
                                        C004_Ambiente_MDFe,
                                        C004_Flag_Emitente,
                                        C004_Flag_Transportador,
                                        C004_Versao_MDFe,
                                        C004_Tipo_Carga_Padrao,
                                        C004_Tipo_Transporte_Padrao,
                                        D018_UF,
                                        D018_Codigo_IBGE,
                                        D020_Codigo_IBGE,
                                        D170_D109_Id
                                   FROM C004
                              LEFT JOIN D018 ON D018_Id=C004_D018_Id
                              LEFT JOIN D020 ON D020_Id=C004_D020_Id
                              LEFT JOIN D170 ON D170_Id=C004_D170_Id
                                  WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004 = mysql_fetch_array($C004);

            //motorista padrão
            if(!empty($mC004['C004_D170_Id']) || $mC004['C004_D170_Id'] != 0){
                $motoristaPadrao = $mC004['D170_D109_Id'];
            }

            //inserir seguradora padrão
            $idSeguradoraPadrao = $mC004['C004_D172_Id'];

            //-- OBSERVACAO: DESENVOLVER SCRIPT PARA MUDAR O NUMERO DE SERIE CASO O TOTAL DE NOTAS ULTRAPASSE 999.999.999 (nossa!!!)
            if ($mC004['C004_Ambiente_MDFe'] == '3') {
                $mC004['C004_Ambiente_MDFe'] = '1';
                $nNumero_Serie              = 900;
            } else {
                $nNumero_Serie = 1;
            }

            $numeroMdfe = mysql_query("SELECT coalesce(T276_Numero_MDFe,0) as T276_Numero_MDFe 
                                         FROM T276
                                        WHERE T276_C004_Id='{$g['empresaAtual']}'
                                     ORDER BY round(T276_Numero_MDFe) DESC LIMIT 0,1;");
            $mT276Numero = mysql_fetch_array($numeroMdfe);
            $Proximo_Numero = $mT276Numero['T276_Numero_MDFe'] + 1;

            //modal sempre receberá 1 pois´por enquanto o mdf-e só atende o modal rodoviário
            $modal = 1;

            mysql_query("LOCK T276");
            mysql_query("LOCK T283");
            mysql_query("LOCK T282");
            mysql_query("LOCK T284");
            mysql_query("LOCK T279");
            mysql_query("LOCK T278");
            mysql_query("LOCK T281");
            mysql_query("START TRANSACTION");


            //inserir cabeçalho MDf-e - devolver ID com o mysqlLastId
            $cClausula = "INSERT INTO T276";
            $cClausula .= "(T276_Ambiente_MDFe, ";
            $cClausula .= "T276_Serie, ";
            $cClausula .= "T276_Numero_MDFe, ";
            $cClausula .= "T276_Flag_Modalidade_Transporte, ";
            $cClausula .= "T276_Data_Emissao, ";
            $cClausula .= "T276_Hora_Emissao, ";
            $cClausula .= "T276_Data_Inicio_Viagem, ";
            $cClausula .= "T276_Hora_Inicio_Viagem, ";
            $cClausula .= "T276_C004_Id, ";
            $cClausula .= "T276_Flag_Emitente, ";
            $cClausula .= "T276_Emitente_CNPJ, ";
            $cClausula .= "T276_Emitente_Inscricao_Estadual, ";
            $cClausula .= "T276_Emitente_Nome, ";
            $cClausula .= "T276_Emitente_Nome_Fantasia, ";
            $cClausula .= "T276_Emitente_Rua, ";
            $cClausula .= "T276_Emitente_Numero, ";
            $cClausula .= "T276_Emitente_Cidade_IBGE, ";// código IBGE da cidade da empresa
            $cClausula .= "T276_Emitente_Cidade, ";// nome cidade da empresa
            $cClausula .= "T276_Emitente_CEP, ";
            $cClausula .= "T276_Emitente_UF, ";// sigla uf da empresa
            $cClausula .= "T276_Emitente_Bairro, ";
            $cClausula .= "T276_D109_Id, ";
            $cClausula .= "T276_C007_Id, ";
            $cClausula .= "T276_Emitente_UF_IBGE, ";//código IBGE da UF da empresa
            $cClausula .= "T276_D018_Id_Carregamento, "; //código UF da empresa
            $cClausula .= "T276_D020_Id_Carregamento, "; //código Cidade da empresa
            $cClausula .= "T276_Fuso_Horario_Emissao, ";
            $cClausula .= "T276_D018_Id_Descarregamento ";
            $cClausula .= " ) values (";
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Ambiente_MDFe']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($nNumero_Serie) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($Proximo_Numero) . "', ";
            if(!empty($modalForm)){
                $cClausula .= " '" . mysql_real_escape_string($modalForm) . "', ";    
            }else{
                $cClausula .= " '" . mysql_real_escape_string($modal) . "', ";
            }
            $cClausula .= "CURDATE(),";
            $cClausula .= "CURTIME(),";
            $cClausula .= " '" . mysql_real_escape_string($dataInicioViagem) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($horaInicioViagem) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($g['empresaAtual']) . "', ";
            if(!empty($tipoEmitente)){
                $cClausula .= " '" . mysql_real_escape_string($tipoEmitente) . "', ";
            }else{
                $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Flag_Emitente']) . "', ";
            }
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Cnpj']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Inscricao_Estadual']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', "; // razao social
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', "; // nome fantasia
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Logradouro']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Numero']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($mC004['D020_Codigo_IBGE']) . "', ";// código IBGE da cidade da empresa
            $cClausula .= " '" . mysql_real_escape_string($mC004['D020_Nome_Cidade']) . "', ";// nome cidade da empresa
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Cep']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($mC004['D018_UF']) . "', ";// sigla uf da empresa
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_Bairro']) . "', "; 
            $cClausula .= " '" . mysql_real_escape_string($motoristaPadrao) . "', "; 
            $cClausula .= " '" . mysql_real_escape_string($g['usuarioAtual']) . "', "; 
            $cClausula .= " '" . mysql_real_escape_string($mC004['D018_Codigo_IBGE']) . "', "; 
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_D018_Id']) . "', ";
            $cClausula .= " '" . mysql_real_escape_string($mC004['C004_D020_Id']) . "', ";
            $cClausula .= " '" . date("P") . "', ";
            $cClausula .= " '" . mysql_real_escape_string($ufDescarregamento) . "' ";
            $cClausula .= " ) "; 
            log("sqlInsert T276: ".$cClausula);

            mysql_query($cClausula);
            $cErro= mysql_error();
            if(!empty($cErro)){
                mysql_query("ROLLBACK");
                return "Erro ao dar insert na T276= ".$cErro;
            }
            $T276_Id = $g['mysqlLastId'];
            log("T276_Id= ".$T276_Id);
            $arrayRetorno['T276_Id'] = $T276_Id;
            mysql_query("call T276_Gravar_Totalizacao('{$T276_Id}')");

            //inserir veículo padrão - T283 (modal rodoviário)
            if(!empty($mC004['C004_D170_Id'])){
                $D170 = mysql_query("SELECT * FROM D170 left join D018 on D018_Id=D170_D018_Id where D170_Id='{$mC004['C004_D170_Id']}'");
                $mD170 = mysql_fetch_array($D170);
                $T283 = "INSERT INTO T283(";
                $T283 .= "T283_Placa, ";
                $T283 .= "T283_Renavam, ";
                $T283 .= "T283_Tara, ";
                $T283 .= "T283_Capacidade_Kg, ";
                $T283 .= "T283_Capacidade_M3, ";
                $T283 .= "T283_Flag_Rodado, ";
                $T283 .= "T283_Flag_Carroceria, ";
                $T283 .= "T283_UF_Licenciamento, ";
                $T283 .= "T283_D170_Id, ";
                $T283 .= "T283_Versao_Modal, ";
                $T283 .= "T283_T276_Id, ";
                $T283 .= "T283_Identificacao_Veiculo, ";
                $T283 .= "T283_Flag_Carro_Proprio, ";
                $T283 .= "T283_CNPJ_Proprietario, ";
                $T283 .= "T283_Proprietario_Veiculo, ";
                $T283 .= "T283_Inscricao_Estadual_Proprietario, ";
                $T283 .= "T283_Categoria_Veiculo, ";
                $T283 .= "T283_D018_Id ";
                $T283 .= " )VALUES( ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Placa']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Renavam']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Tara']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Capacidade_Kg']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Capacidade_M3']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Flag_Rodado']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Flag_Carroceria']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D018_UF']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mC004['C004_D170_Id']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($modal) . "', ";
                $T283 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Identificacao_Veiculo']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Flag_Carro_Proprio']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mC004['C004_Cnpj']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mC004['C004_Inscricao_Estadual']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_Categoria']) . "', ";
                $T283 .= " '" . mysql_real_escape_string($mD170['D170_D018_Id']) . "' ";
                $T283 .= " ) ";
                mysql_query($T283);
                log("sqlInsert T283: ".$T283);
                $cErro = mysql_error();
                if(!empty($cErro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao dar insert na T283: ".$cErro;        
                } 
            }

            log("MOTORISTA PADRAO: ".$motoristaPadrao);
            //inserir condutor padrão - T278
            if(!empty($motoristaPadrao)){
                $D109 = mysql_query("SELECT D109_Id, D109_Nome,D109_Cpf FROM D109 where D109_Id='{$mC004['D170_D109_Id']}'");
                $mD109 = mysql_fetch_array($D109);
                $T278 = "INSERT INTO T278(";
                $T278 .= "T278_D109_Id, ";
                $T278 .= "T278_T276_Id, ";
                $T278 .= "T278_Nome, ";
                $T278 .= "T278_CPF ";
                $T278 .= " ) VALUES (";
                $T278 .= " '" . mysql_real_escape_string($mC004['D170_D109_Id']) . "', ";
                $T278 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                $T278 .= " '" . mysql_real_escape_string($mD109['D109_Nome']) . "', ";
                $T278 .= " '" . mysql_real_escape_string($mD109['D109_Cpf']) . "' ";
                $T278 .= " ) ";
                mysql_query($T278);
                $cErro = mysql_error();
                if(!empty($cErro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao dar insert na T278: ".$cErro;        
                } 
            }

            log("MOTORISTA PADRAO: ".$mC004['C004_D172_Id']);
            //inserir seguradora padrão - T279
            if(!empty($mC004['C004_D172_Id'])){
                $D172 = mysql_query("SELECT * FROM D172 where D172_Id='{$mC004['C004_D172_Id']}'");
                $mD172 = mysql_fetch_array($D172);
                $T279 = "INSERT INTO T279(";
                $T279 .= "T279_T276_Id, ";
                $T279 .= "T279_D172_Id, ";
                $T279 .= "T279_Seguradora_Nome, ";
                $T279 .= "T279_Seguradora_CNPJ ";
                $T279 .= " ) VALUES (";
                $T279 .= " '" . mysql_real_escape_string($T276_Id) . "', ";
                $T279 .= " '" . mysql_real_escape_string($mC004['C004_D172_Id']) . "', ";
                $T279 .= " '" . mysql_real_escape_string($mD172['D172_Nome']) . "', ";
                $T279 .= " '" . mysql_real_escape_string($mD172['D172_CNPJ']) . "' ";
                $T279 .= " ) ";
                mysql_query($T279);
                $cErro = mysql_error();
                if(!empty($cErro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao dar insert na T279: ".$cErro;        
                } 
            }

            mysql_query("COMMIT");
            mysql_query("UNLOCK ALL");
            return $arrayRetorno;

        }

        public function alterarLocalDescarregamento($T276_Id,$D018_Id,$D020_Id){
            global $g;
            $sql = mysql_query("SELECT T276_D018_Id_Descarregamento,D018_UF FROM T276 LEFT JOIN D018 ON D018_Id=T276_D018_Id_Descarregamento WHERE T276_Id='{$T276_Id}'");
            $T276 = mysql_fetch_assoc($sql);
            if($T276['T276_D018_Id_Descarregamento']!=$D018_Id){
                $erro = "Não é permitido selecionar uma UF de descarregamento diferente da que está cadastrada no cabeçalho do MDF-e.";
                return $erro;
            }else{
                $sql = mysql_query("SELECT D020_Nome_Cidade,D020_Codigo_Cidade,D020_Codigo_IBGE FROM D020 WHERE D020_Id='{$D020_Id}'");
                $D020 = mysql_fetch_assoc($sql);

                $sql     = "UPDATE T284 SET T284_D020_Id='{$D020_Id}',
                                            T284_D018_Id='{$D018_Id}',
                                            T284_Cidade_Descarregamento='{$D020['D020_Nome_Cidade']}',
                                            T284_Cidade_Descarregamento_Codigo_IBGE='{$D020['D020_Codigo_IBGE']}',
                                            T284_Cidade_Descarregamento_Codigo='{$D020['D020_Codigo_Cidade']}'
                            WHERE T284_T276_Id='{$T276_Id}'";
                log("SQL:".$sql);
                mysql_query($sql);
            }

            $erro = mysql_error();
            if($erro){
                return $erro;
            }
            return true;
        }

        /**
         * cancelarMdfe
         *
         * @param Form $form
         * @param String $T276_Id
         * @return String/bool
         */        
        public function cancelarMdfe($form, $T276_Id){
            global $g;
            //-- ELIMINA O ENTER
            $T276_Observacao_Cancelamento = $form->campoValorEnviado('T276_Observacao_Cancelamento');
            $T276_Observacao_Cancelamento = str_replace("\r", "", $T276_Observacao_Cancelamento);
            $T276_Observacao_Cancelamento = str_replace("\n", " ", $T276_Observacao_Cancelamento);
            $T276_Observacao_Cancelamento = trim($T276_Observacao_Cancelamento);
            
            $T276_Observacao_Cancelamento = gLimpaAcentos($T276_Observacao_Cancelamento);
            if (empty($T276_Observacao_Cancelamento)) {
                return "Preencha o motivo do cancelamento!";
            }
            
            $cSql_T276 = mysql_query("SELECT T276_Versao_MDFe,
                                             T276_Numero_Protocolo_MDFe,
                                             T276_Chave_Acesso_MDFe,
                                             T276_Numero_Protocolo_Cancelamento_MDFe,
                                             T276_Emitente_UF,
                                             T276_Emitente_UF_IBGE,
                                             T276_Ambiente_MDFe,
                                             T276_Emitente_CNPJ,
                                             C004_Localizacao_Certificados,
                                             C004_Senha_Certificados,
                                             T276_C004_Id   
                                        FROM T276 
                                   LEFT JOIN C004 ON C004_Id=T276_C004_Id 
                                       WHERE T276_Id='{$T276_Id}'");
            $mT276     = mysql_fetch_array($cSql_T276);
            
            if ($mT276['T276_Versao_MDFe'] == '3.00') {
                //--  Class para gerar o arquivo da NFe VERSAO 1.10
                include $g['pathRaiz'] . "hardness/class/mdfe3.class.php";
            } 
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            
            //-- CANCELAMENTO DA NOTA FISCAL ELETRONICA
            if (strlen($T276_Observacao_Cancelamento) < 15) {
                mysql_query("ROLLBACK");
                return "Justificativa de cancelamento deve ter mais de 15 caracteres.";
            } elseif (strlen($T276_Observacao_Cancelamento) > 255) {
                mysql_query("ROLLBACK");
                return "Caracteres excedidos para justificativa de cancelamento. Ultrapassou 255 caracteres.";
            }
            
            if (empty($mT276['T276_Chave_Acesso_MDFe'])) {
                mysql_query("ROLLBACK");
                return "Este MDFe não possui chave de acesso, nao pode ser cancelado.";
            }

            if (empty($mT276['T276_Numero_Protocolo_MDFe'])) {
                mysql_query("ROLLBACK");
                return "Este MDF-e ainda não tem protocolo de autorização, não pode ser cancelado.";
            }
                        
            if ($g['C020']['C020_NFe_Treinamento'] == 'S' AND $mT276['T276_Ambiente_MDFe'] != '2') {
                return "MDFe esta definido como treinamento e o ambiente não é homologação, verifique!";
            }
            
            if ($g['C020']['C020_NFe_Treinamento'] == 'S') {
                $cClausula = " UPDATE T276 SET ";
                $cClausula .= " T276_Numero_Protocolo_Cancelamento_MDFe = T276_Chave_Acesso_MDFe, ";
                $cClausula .= " T276_Data_Hora_Cancelamento_Nfe = now() ";
                $cClausula .= " WHERE T276_Id='{$T276_Id}' ";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Ocorreu um erro ao gravar o retorno do Sefaz na nota fiscal\nPor favor entre em contato com o suporte \nErro:$cErro\nClausula:$cClausula";
                }
                //return $this->gravarCancelamento($T276_Id, $T276_Observacao_Cancelamento);
                return true;
            } else {
                //-- $Diretorio_MDFe é definido no arquivo include/pasta_usuario.inc
                $oMDFe = new classeMDFe();
                $oMDFe->setPastaArquivoXML($g['pathDadosAntigo']. "/mdfe/xml");
                $oMDFe->setUFEmitente($mT276['T276_Emitente_UF']);
                $oMDFe->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);
                $oMDFe->setSenhaCertificado($mT276['C004_Senha_Certificados']);
                $oMDFe->setCertificadoPEM($mT276['C004_Localizacao_Certificados'] . $mT276['T276_C004_Id'] . ".pem");
                $oMDFe->setIdentAmbiente($mT276['T276_Ambiente_MDFe']); //--  1-Produção / 2-Homologação
                $oMDFe->setTipoDocAuxiliar('1');
                $oMDFe->setT276Id($T276_Id);
                $oMDFe->setModeloCertificado($g['C020']['C020_NFe_Tipo_Certificado']);
                $oMDFe->setCertificadoHardness($g['pathDadosAntigo'] . "/../../principal/hardness.pem");
                $oMDFe->carregaCertP12($mT276['C004_Localizacao_Certificados'] . $mT276['T276_C004_Id'] . ".pfx");
                $oMDFe->setCnpjEmitente($mT276['T276_Emitente_CNPJ']);
                $oMDFe->setMotivoCancelamento($T276_Observacao_Cancelamento);
                
                if ($oMDFe->statusServico() === true) {
                    //-- CRIA O XML
                    if (!$erro = $oMDFe->recepcaoEvento($mT276['T276_Chave_Acesso_MDFe'], $mT276['T276_Numero_Protocolo_MDFe'], 'Cancelamento')) {
                        $logErro = $oMDFe->mErrorMsg;
                        $logErro = $logErro[0];
                        log("Msg Erro: ".$logErro);
                        $pos = strripos($logErro, "135");
                        log("strripos: ".$pos);
                        if($pos===false){
                            log("Entrou IF erro");
                            mysql_query("ROLLBACK");
                            return "Ocorreu um erro ao tentar gerar o xml do arquivo de cancelamento: {$logErro}";
                        }
                        log("Não entrou IF, procedeu cancelamento");
                    }

                    log("## EROOO = ".$erro);
                    
                    $cNumero_Protocolo = $oMDFe->nProt;
                    $Data_Recebimento  = $oMDFe->dhRecbto;
                    $Data_Recebimento  = str_replace("T", " ", $Data_Recebimento);
                    $Data_Recebimento  = substr($Data_Recebimento, 0, 19);
                    
                    $cClausula = " UPDATE T276 SET ";
                    $cClausula .= " T276_Numero_Protocolo_Cancelamento_MDFe = '$cNumero_Protocolo', ";
                    $cClausula .= " T276_Data_Hora_Recebimento_Cancelamento_MDFe = '$Data_Recebimento' ";
                    $cClausula .= " WHERE T276_Id='{$T276_Id}' ";
                    
                    mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Ocorreu um erro ao gravar o retorno do Sefaz no mdfe.<br/>Por favor entre em contato com o suporte <br/>Erro:$cErro<br/>Clausula:$cClausula";
                    }
                    $this->gravarCancelamento($T276_Id, $T276_Observacao_Cancelamento);
                } else {
                    mysql_query("ROLLBACK");
                    return "Problemas com os servidores do Sefaz: " . $oMDFe->getMotivo();
                }
            }
            mysql_query("COMMIT");
            return true;
        }

        /**
         * grava cancelamento
         *
         * @param String $T276_Id
         * @param String $T276_Observacao_Cancelamento
         * @return true
         */        
        public function gravarCancelamento($T276_Id, $T276_Observacao_Cancelamento){
            mysql_query("SET AUTOCOMMIT=1");
            $T276_Observacao_Cancelamento = mysql_real_escape_string($T276_Observacao_Cancelamento);

            //-- Cancelando MDF-e
            mysql_query("UPDATE T276 SET 
                                T276_Flag_Cancelada='S',
                                T276_Quantidade_NFe=0,
                                T276_Quantidade_CTe=0,
                                T276_Valor_Carga=0,
                                T276_Peso_Bruto_Total_Carga=0,
                                T276_Observacao_Cancelamento='{$T276_Observacao_Cancelamento}'
                        where T276_Id='{$T276_Id}'");
            if (mysql_error()){
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }
            return true;
        }
        /* Raquel 08/01/2019*/
        /**
         * finalizar MDFe
         *
         * @param String $T276_Id
         * @return true
         */        
        public function finalizarMDFe($T276_Id){
            global $g;

            if(empty($T276_Id)){
                return "Não encontrado o ID do MDF-e (T276_Id)";
            }

            // Validação dados gerais MDFe
            $T276 = mysql_query("SELECT T276_Versao_MDFe,
                            T276_Numero_Protocolo_MDFe,
                            T276_Flag_Cancelada,
                            T276_Flag_Encerrada,
                            T276_D018_Id_Carregamento,
                            T276_D018_Id_Descarregamento,
                            T276_Informacoes_Adicionais_Fisco,
                            T276_Informacoes_Adicionais_Contribuinte,
                            T276_Data_Inicio_Viagem,
                            T276_Data_Emissao,
                            T276_Numero_MDFe,
                            T276_UF_Percurso,
                            carregamentoUF.D018_UF as carregamentoUF,
                            descarregamentoUF.D018_UF as descarregamentoUF
                       FROM T276
                  LEFT JOIN D018 as carregamentoUF ON carregamentoUF.D018_Id=T276_D018_Id_Carregamento
                  LEFT JOIN D018 as descarregamentoUF ON descarregamentoUF.D018_Id=T276_D018_Id_Descarregamento
                      WHERE T276_Id='{$T276_Id}'
                    ");
            $mT276 = mysql_fetch_assoc($T276);
            if($mT276['T276_Versao_MDFe'] != '3.00'){
                return "Versão ".$mT276['T276_Versao_MDFe']." do MDF-e, no cadastro da empresa está incorreta.";
            }
            if(!empty($mT276['T276_Numero_Protocolo_MDFe']) || $mT276['T276_Flag_Cancelada'] == 'S' || $mT276['T276_Flag_Encerrada'] == 'S'){
                return "MDF-e já foi enviado, não é possível ser finalizado.";
            }
            if(empty($mT276['T276_D018_Id_Carregamento'])){
                return "Necessário preencher UF de carregamento, no cabeçalho do MDF-e.";       
            }
            if(empty($mT276['T276_D018_Id_Descarregamento'])){
                return "Necessário preencher UF de descarregamento, no cabeçalho do MDF-e.";       
            }
            if(strlen($mT276['T276_Informacoes_Adicionais_Fisco']) > 2000){
                return "Informações Interesse fisco, em adicionais, ultrapassou 2000 caracteres, necessário diminuir seu tamanho. Este campo esta com " . strlen($mT276['T276_Informacoes_Adicionais_Fisco']) . " caracteres.";
            }
            if(strlen($mT276['T276_Informacoes_Adicionais_Contribuinte']) > 5000){
                return "Informações Interesse contribuinte, em adicionais, ultrapassou 5000 caracteres, necessário diminuir seu tamanho. Este campo esta com " . strlen($mT276['T276_Informacoes_Adicionais_Contribuinte']) . " caracteres.";
            }
            if(strtotime($mT276['T276_Data_Inicio_Viagem']) < strtotime($mT276['T276_Data_Emissao'])){
                return "Data início viagem não pode ser anterior a data de emissão.";          
            }
            // Validação dos dados de percurso
            // A UF de descarregamento não pode estar no percurso e nem a UF de carregamento
            $ufPercurso = explode(",", $mT276['T276_UF_Percurso']);
            for($i=0 ; $i<count($ufPercurso); $i++){
                $UF = $ufPercurso[$i];
                log("UF PERCURSO: ".$ufPercurso[$i]);
                if(($UF == $mT276['carregamentoUF']) || ($UF == $mT276['descarregamentoUF'])){
                    return "UF de carregamento e descarregamento não devem estar inseridas na UF de percurso.";
                }        
            }
        

            // Verificar se foi inserido mais de um veículo de tração ou mais de 3 de Reboque
            $T283 = mysql_query("SELECT T283_Categoria_Veiculo, 
                                        COUNT(T283_Id) as total
                                   FROM T283
                                  WHERE T283_T276_Id='{$T276_Id}'
                               GROUP BY T283_Categoria_Veiculo");
            while($mT283 = mysql_fetch_array($T283)){
                if($mT283['T283_Categoria_Veiculo'] == 1 && $mT283['total'] > 3){
                    return "Podem ser cadastrados até 3 veículos de reboque no MDF-e.";
                }    
                if($mT283['T283_Categoria_Veiculo'] == 2 && $mT283['total'] != 1){
                    return "Deve ser cadastrado somente 1 veículo de tração no MDF-e.";
                }
            }
            
            // Validação dados da nota do MDFe
            $T284 = mysql_query("SELECT T284_Id,
                                        T284_Valor_Carga,
                                        T284_Peso_Bruto_Total,
                                        T284_Chave_De_Acesso_NFe,
                                        T284_Chave_De_Acesso_CTe,
                                        T284_Flag_Tipo_Documento,
                                        T284_Numero_NFe,
                                        T284_Peso_Bruto_Total,
                                        D018_Id,
                                        D018_UF
                                   FROM T284
                              LEFT JOIN T007 ON T007_Id=T284_T007_Id
                              LEFT JOIN D018 ON D018_Id=T284_D018_Id
                                  WHERE T284_T276_Id='{$T276_Id}'
                    ");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return 'Erro: ' . $cErro;
            }
            $resultNotasMDfe = '';
            $resultUFMDfe = '';
            if(mysql_num_rows($T284) <= 0){
                return "MDF-e não possui documentos(NF-e ou CT-e), não é possível finalizar.";
            }else{
                while($mT284 = mysql_fetch_array($T284)){

                    if($mT284['T284_Valor_Carga'] <= 0){
                        if($mT284['T284_Flag_Tipo_Documento'] == 1) {
                            $resultNotasMDfe .=" Valor do Conhecimento deve ser maior que zero.<br/>";
                        } elseif($mT284['T284_Flag_Tipo_Documento'] == 2) {
                            $resultNotasMDfe .=" Valor da Nota Fiscal ".$mT284['T284_Numero_NFe']." deve ser maior que zero.<br/>";
                        }
                    }
                    if($mT284['T284_Peso_Bruto_Total'] <= 0){
                        if($mT284['T284_Flag_Tipo_Documento'] == 1) {
                            $resultNotasMDfe .=" Peso do Conhecimento deve ser maior que zero.<br/>";
                        }elseif($mT284['T284_Flag_Tipo_Documento'] == 2) {
                            $resultNotasMDfe .=" Peso da Nota Fiscal ".$mT284['T284_Numero_NFe']." deve ser maior que zero.<br/>";
                        }
                    }

                    if($mT284['T284_Flag_Tipo_Documento'] == 2 && $mT284['T284_Chave_De_Acesso_NFe'] <= 0){
                        $resultNotasMDfe .=" Necessário preencher a chave de acesso da Nota Fiscal ".$mT284['T284_Numero_NFe'].".<br/>";
                    }elseif($mT284['T284_Flag_Tipo_Documento'] == 1 && $mT284['T284_Chave_De_Acesso_CTe'] <= 0) {
                        $resultNotasMDfe .=" Necessário preencher a chave de acesso do Conhecimento.<br/>";
                    }

                    if($mT284['D018_UF'] != $mT276['descarregamentoUF']){
                        if($mT284['T284_Flag_Tipo_Documento'] == 1) {
                            $resultNotasMDfe .= "UF ".$mT284['D018_UF']." do conhecimento, chave de acesso {$mT284['T284_Chave_De_Acesso_CTe']} é diferente da UF de descarregamento ".$mT276['descarregamentoUF']." do MDF-e <br/>";
                        } elseif($mT284['T284_Flag_Tipo_Documento'] == 2) {
                            $resultNotasMDfe .= "UF ".$mT284['D018_UF']." da nota  ".$mT284['T284_Numero_NFe']." é diferente da UF de descarregamento ".$mT276['descarregamentoUF']." do MDF-e <br/>";
                        }
                    }

                    // Documento NF-e
                    if ($mT284['T284_Flag_Tipo_Documento'] == 2) {
                        $notasMDFe = mysql_fetch_assoc(mysql_query("SELECT COUNT(T284_T276_Id) as total 
                                                                    FROM T276 
                                                                LEFT JOIN T284 ON T284_T276_Id=T276_Id  
                                                                    WHERE T284_Chave_De_Acesso_NFe='{$mT284['T284_Chave_De_Acesso_NFe']}' 
                                                                    AND T276_Flag_Cancelada='N'
                                                                GROUP BY T284_Chave_De_Acesso_NFe"));
                        if($notasMDFe['total'] > 1 ){
                            return "A nota ".$mT284['T284_Numero_NFe']."  já está vinculada a outro MDF-e";
                        }
                        //Documento CT-e
                    } elseif($mT284['T284_Flag_Tipo_Documento'] == 1) {
                        $notasMDFe = mysql_fetch_assoc(mysql_query("SELECT COUNT(T284_T276_Id) as total 
                                                                    FROM T276 
                                                                LEFT JOIN T284 ON T284_T276_Id=T276_Id  
                                                                    WHERE T284_Chave_De_Acesso_CTe='{$mT284['T284_Chave_De_Acesso_CTe']}' 
                                                                    AND T276_Flag_Cancelada='N'
                                                                GROUP BY T284_Chave_De_Acesso_CTe"));
                        if($notasMDFe['total'] > 1 ){
                           return "O conhecimento {$mT284['T284_Chave_De_Acesso_CTe']} já está vinculada a outro MDF-e";
                        }
                    }
 
                    if(!empty($resultNotasMDfe)){
                        $resultNotasMDfe .= "<br/>";
                    }
                }
            }
            if(!empty($resultNotasMDfe)){
                return $resultNotasMDfe;
            }

            // Insere o Numero do mdfe
            if(empty($mT276['T276_Numero_MDFe'])){
                $numeroMDFe = mysql_fetch_array(mysql_query("SELECT T276_Numero_MDFe FROM T276 ORDER BY ROUND(T276_Numero_MDFe) DESC LIMIT 0,1"));
                $proximoNumero = $numeroMDFe['T276_Numero_MDFe'];
                $proximoNumero++; 

                mysql_query("UPDATE T276 SET T276_Numero_MDFe='{$proximoNumero}' WHERE T276_id='{$T276_Id}'");
                $erro = mysql_error();
                if ($erro) {
                    mysql_query("ROLLBACK");
                    return $erro;
                }
            }
                mysql_query("UPDATE T276 SET T276_Flag_Travar_MDFe='S' WHERE T276_id='{$T276_Id}'"); 
                $erro = mysql_error();
                if ($erro) {
                    mysql_query("ROLLBACK");
                    return $erro;
                }
            return true;
        }

        /**
        * Excluir MDFe
        *
        * @param String $T276_Id
        * @return true
        **/        
        public function excluirMDFe($T276_Id){

            $T276 = mysql_query("SELECT T276_Id, 
                                        T276_Numero_Recibo_MDFe,
                                        T276_Numero_MDFe,
                                        T276_Numero_Protocolo_MDFe,
                                        T276_Chave_Acesso_MDFe
                                   FROM T276
                                  WHERE T276_Id='{$T276_Id}'
                                ");
            $mT276 = mysql_fetch_array($T276);

            if(!empty($mT276['T276_Numero_Protocolo_MDFe'])){
                return "Já existe Protocolo para o MDF-e " . $mT276['T276_Numero_MDFe'] . ", ele não pode ser excluído.";
            }

            if(!empty($mT276['T276_Numero_Recibo_MDFe'])){
                return "Já existe Recibo para o MDF-e " . $mT276['T276_Numero_MDFe'] . ", ele não pode ser excluído.";
            }

            if(!empty($mT276['T276_Chave_Acesso_MDFe'])){
                return "Já existe Chave de Acesso para o MDF-e " . $mT276['T276_Numero_MDFe'] . ", ele não pode ser excluído.";
            }

            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            // Deletando os dados do Lacre da do tipo de carga
            mysql_query("DELETE T287,
                                T288, 
                                T282,
                                T281,
                                T284 
                           FROM T284 
                              LEFT JOIN T281 ON T284_Id = T281_T284_Id 
                              LEFT JOIN T287 ON T287_T281_Id = T281_Id 
                              LEFT JOIN T282 ON T282_T281_Id = T281_Id
                              LEFT JOIN T288 ON T288_T282_Id = T282_Id
                            WHERE T284_T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            // Excluindo Veículos MDF-e
            mysql_query("DELETE T283 
                           FROM T283 
                          WHERE T283_T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            // Excluindo Motorista MDF-e
            mysql_query("DELETE T278 
                           FROM T278 
                          WHERE T278_T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            // Excluindo Lacres Modal Rodoviário
            mysql_query("DELETE T286 
                           FROM T286 
                          WHERE T286_T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            // Excluindo Dados da seguradora e de suas tabelas "filhas"
            mysql_query("DELETE D174,
                                T279 
                           FROM T279 
                      LEFT JOIN D174 ON D174_T279_Id = T279_Id
                          WHERE T279_T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            // Excluindo Lacres MDF-e
            mysql_query("DELETE T280 
                           FROM T280 
                          WHERE T280_T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            // Excluindo MDF-e
            mysql_query("DELETE T276 
                           FROM T276 
                          WHERE T276_Id = '{$T276_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            /*
            * INICIANDO AS VERIFICAÇÕES PARA VER SE REALMENTE EXCLUIU OS REGISTROS.
            */

            // Verificando as notas e suas tabelas correlacionadas
            $T284 = mysql_query("SELECT T284_Id
                                   FROM T284 
                              LEFT JOIN T281 ON T284_Id = T281_T284_Id 
                              LEFT JOIN T287 ON T287_T281_Id = T281_Id 
                              LEFT JOIN T282 ON T282_T281_Id = T281_Id
                              LEFT JOIN T288 ON T288_T282_Id = T282_Id 
                                  WHERE T284_T276_Id='{$T276_Id}'");
            $rT284 = mysql_num_rows($T284);

            if($rT284 > 0){
                mysql_query("ROLLBACK");
                return "Os registros referente as Notas do MDF-e (T284) não foram removidos. Por favor, contate o suporte técnico.";
            }

            // Veículos
            $T283 = mysql_query("SELECT T283_Id  
                                   FROM T283 
                                  WHERE T283_T276_Id='{$T276_Id}'");
            $rT283 = mysql_num_rows($T283);
            if($rT283 > 0){
                mysql_query("ROLLBACK");
                return "Os registros referente aos Veículos (T283) não foram removidos. Por favor, contate o suporte técnico.";
            }

            // Verificando os dados dos Motorista MDF-e
            $T278 = mysql_query("SELECT T278_Id  
                                   FROM T278 
                                  WHERE T278_T276_Id='{$T276_Id}'");
            $rT278 = mysql_num_rows($T278);
            if($rT278 > 0){
                mysql_query("ROLLBACK");
                return "Os registros referente aos Motoristas (T278) não foram removidos. Por favor, contate o suporte técnico.";
            }

            // Verificando os dados dos Lacres modal rodoviário
            $T286 = mysql_query("SELECT T286_Id  
                                   FROM T286 
                                  WHERE T286_T276_Id='{$T276_Id}'");
            $rT286 = mysql_num_rows($T286);
            if($rT286 > 0){
                mysql_query("ROLLBACK");
                return "Os registros referente aos Lacres do Modal Rodoviário (T286) não foram removidos. Por favor, contate o suporte técnico.";
            }

            // Verificando os dados da seguradora
            $T279 = mysql_query("SELECT T279_Id,
                                        D174_Id  
                                   FROM T279 
                              LEFT JOIN D174 ON D174_T279_Id = T279_Id
                                  WHERE T279_T276_Id='{$T276_Id}'");
            $rT279 = mysql_num_rows($T279);
            if($rT279 > 0){
                mysql_query("ROLLBACK");
                return "Os registros referente a seguradora (T279 e D174) não foram removidos. Por favor, contate o suporte técnico.";
            }

            // Verificando os Lacres do MDF-e
            $T280 = mysql_query("SELECT T280_Id FROM T280 WHERE T280_T276_Id='{$T276_Id}'");
            $rT280 = mysql_num_rows($T280);
            if($rT280 > 0){
                mysql_query("ROLLBACK");
                return "Os registros referente aos lacres do MDF-e (T280) não foram removidos. Por favor, contate o suporte técnico.";
            }

            // Verificando o MDF-e
            $T276 = mysql_query("SELECT T276_Id FROM T276 WHERE T276_Id='{$T276_Id}'");
            $rT276 = mysql_num_rows($T276);
            if($rT276 > 0){
                mysql_query("ROLLBACK");
                return "O registro referente ao MDF-e (T276) número {$T276_Id} não foi removido. Por favor, contato o suporte técnico.";
            }

            mysql_query("COMMIT");
            return true;
        }
    }
}
