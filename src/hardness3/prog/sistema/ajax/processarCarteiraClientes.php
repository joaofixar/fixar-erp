<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

    if ($r_inicio==1){
        //verifica globais
        $sqlC031 = mysql_query("SELECT C031_Campo, 
                                       C031_Valor 
                                  FROM C031 
                                 WHERE C031_C004_Id = '1' 
                                   AND (C031_Campo = 'carteiraClienteDiasMaxContato' OR C031_Campo = 'carteiraClienteDiasMaxOrcamento' OR C031_Campo = 'carteiraClienteDiasMaxPedido' OR C031_Campo = 'carteiraClienteDiasMaxNotaFiscal')");

        while($rowCacheC031 = mysql_fetch_array($sqlC031)){
            switch ($rowCacheC031['C031_Campo']) {
                case 'carteiraClienteDiasMaxContato':
                    $cacheC031['diasAgenda'] = $rowCacheC031['C031_Valor'];
                break;
                case 'carteiraClienteDiasMaxOrcamento':
                    $cacheC031['diasOrcamento'] = $rowCacheC031['C031_Valor'];
                break;
                case 'carteiraClienteDiasMaxPedido':
                    $cacheC031['diasPedido'] = $rowCacheC031['C031_Valor'];
                break;
                case 'carteiraClienteDiasMaxNotaFiscal':
                    $cacheC031['diasNotaFiscal'] = $rowCacheC031['C031_Valor'];
                break;
            }
        }
 
        $sqlD024 = mysql_query("SELECT D024_Id, 
                                       IFNULL(D024_Data_Cadastro, '0000-00-00') as D024_Data_Cadastro, 
                                       IFNULL(D024_Data_Ativo, '0000-00-00') as D024_Data_Ativo 
                                  FROM D024 
                                 WHERE D024_Flag_Ativo='S'");
        $erroMysql = mysql_error();
        if (!empty($erroMysql)){
            $resposta['code'] = false;
            $resposta['data'] = "Erro consultando clientes: $erroMysql $cClausula";
        }

        $count = 0;
        while($rowCache = mysql_fetch_array($sqlD024)){
            $cache[$count]['D024_Id']= $rowCache['D024_Id'];
            $cache[$count]['D024_Data_Cadastro']= $rowCache['D024_Data_Cadastro'];
            $cache[$count]['D024_Data_Ativo']= $rowCache['D024_Data_Ativo'];
            $count++;
        }
        log("count= ".$count);

        $resposta['totalRegistros'] = count($cache);
        $resposta['etapas'] = round($resposta['totalRegistros'] / 10);
        $resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
        $resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
        $resposta['etapa'] = 1;
        $resposta['concluido'] = 0;

        @unlink("/tmp/cacheC031-{$g['usuarioAtual']}");
        $retornoC031 = file_put_contents("/tmp/cacheC031-{$g['usuarioAtual']}", serialize($cacheC031));
        if($retornoC031 === false){
            $resposta['code'] = false;
            $resposta['data'] = 'Erro ao criar variável C031 no cache';
        }
        @unlink("/tmp/cacheD024-{$g['usuarioAtual']}");
        @unlink("/tmp/cacheD024-resposta-{$g['usuarioAtual']}");
        $retorno = file_put_contents("/tmp/cacheD024-{$g['usuarioAtual']}", serialize($cache));
        if($retorno === false){
            $resposta['code'] = false;
            $resposta['data'] = 'Erro ao criar variável D024 no cache';
        }
    }else{
        $cacheC031 = unserialize(file_get_contents("/tmp/cacheC031-{$g['usuarioAtual']}"));
        $cache = unserialize(file_get_contents("/tmp/cacheD024-{$g['usuarioAtual']}"));
        $resposta = unserialize(file_get_contents("/tmp/cacheD024-resposta-{$g['usuarioAtual']}"));
        if(!$resposta){
            $resposta['code'] = false;
            $resposta['data'] = 'Erro ao recuperar variável D024 no cache resposta';
        }
    }

    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

    if($resposta['etapa'] >= $resposta['etapas']){
        $resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
    } else {
        $resposta['limit2'] += $resposta['registrosEtapa'] ;
    }

    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);
   
    $r_opcoes = json_decode($r_opcoes);

    if(!is_array($r_opcoes)) { 
        $opcoes = get_object_vars($r_opcoes);
    }

    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
        require_once("bibliotecas/classes/CAD001.php");
        $CAD001 = new CAD001();
        $processarCarteiraCliente = $CAD001->processarCarteiraCliente($cache[$i]['D024_Id'],$cacheC031);

        if($processarCarteiraCliente !== true){
            $resposta['code'] = false;
            $resposta['data'] = "Erro no Update inativo: $erroMysql $cClausula";
        }
        
/*         $ativarCliente = true;
        $observacaoInativo = '';
        $diasAgenda = $cacheC031['diasAgenda'];
        $diasOrcamento = $cacheC031['diasOrcamento'];
        $diasPedido = $cacheC031['diasPedido'];
        $diasNotaFiscal = $cacheC031['diasNotaFiscal'];

        if($cache[$i]['D024_Data_Cadastro'] == '0000-00-00'){
            $diasCadastro = 999;
        } else {
            $diasCadastro = round(abs(strtotime(date("Y-m-d"))-strtotime($cache[$i]['D024_Data_Cadastro']))/86400);
        }

        if($cache[$i]['D024_Data_Ativo'] == '0000-00-00'){
            $diasAtivo = 120;
        } else {
            $diasAtivo = round(abs(strtotime(date("Y-m-d"))-strtotime($cache[$i]['D024_Data_Ativo']))/86400);
        }

        if(!empty($diasNotaFiscal) && $diasNotaFiscal > 0){
            $sqlNotaFiscal = mysql_query("SELECT T007_Id 
                                        FROM T007 
                                   LEFT JOIN D024 ON D024_Id = T007_D024_Id 
                                       WHERE T007_D024_Id = '{$cache[$i]['D024_Id']}' 
                                         AND IFNULL(T007_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasNotaFiscal} DAY LIMIT 1");

            if(mysql_num_rows($sqlNotaFiscal) <= 0 && $diasCadastro >= $diasNotaFiscal && $diasAtivo >= $diasNotaFiscal){
                $ativarCliente = false;
                $observacaoInativo .= "Nota em $diasNotaFiscal dia(s)<br>";
            }
        }

        if(!empty($diasPedido) && $diasPedido > 0){
            $sqlPedido = mysql_query("SELECT T005_Id 
                                        FROM T005 
                                   LEFT JOIN D024 ON D024_Id = T005_D024_Id 
                                       WHERE T005_D024_Id = '{$cache[$i]['D024_Id']}' 
                                         AND IFNULL(T005_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasPedido} DAY LIMIT 1");
            if(mysql_num_rows($sqlPedido) <= 0 && $diasCadastro >= $diasPedido && $diasAtivo >= $diasPedido){
                $ativarCliente = false;
                $observacaoInativo .= "Pedido em $diasPedido dia(s)<br>";
            }
        }

        if(!empty($diasOrcamento) && $diasOrcamento > 0){
            $sqlOrcamento = mysql_query("SELECT T003_Id 
                                           FROM T003 
                                      LEFT JOIN D024 ON D024_Id = T003_D024_Id 
                                          WHERE T003_D024_Id = '{$cache[$i]['D024_Id']}' 
                                            AND IFNULL(T003_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasOrcamento} DAY LIMIT 1");
            if(mysql_num_rows($sqlOrcamento) <= 0 && $diasCadastro >= $diasOrcamento && $diasAtivo >= $diasOrcamento){
                $ativarCliente = false;
                $observacaoInativo .= "Orcamento em $diasOrcamento dia(s)<br>";
            }
        }

        if(!empty($diasAgenda) && $diasAgenda > 0){
            $sqlAgenda = mysql_query("SELECT D085_Id 
                                        FROM D085 
                                   LEFT JOIN D024 ON D024_Id = D085_D024_Id 
                                       WHERE D085_D024_Id = '{$cache[$i]['D024_Id']}' 
                                         AND IFNULL(D085_Data, '0000-00-00') > CURDATE() - INTERVAL {$diasAgenda} DAY LIMIT 1");
            if(mysql_num_rows($sqlAgenda) <= 0 && $diasCadastro >= $diasAgenda && $diasAtivo >= $diasAgenda){
                $ativarCliente = false;
                $observacaoInativo .= "Tarefa em $diasAgenda dia(s)";
            }
        }

        if($ativarCliente){
            mysqli_query("UPDATE D024 SET D024_Flag_Comercial = 'A', D024_Data_Inativo = '0000-00-00', D024_Observacao_Inativo = '' WHERE D024_Id = '{$cache[$i]['D024_Id']}' AND D024_Flag_Comercial != 'A'");
            $erroMysql = mysql_error();
            if (!empty($erroMysql)){
                $resposta['code'] = false;
                $resposta['data'] = "Erro no Update ativo: $erroMysql $cClausula";
            }
        } else {
            mysqli_query("UPDATE D024 SET D024_Flag_Comercial = 'I', D024_Data_Inativo = CURDATE(), D024_Observacao_Inativo = '$observacaoInativo' WHERE D024_Id = '{$cache[$i]['D024_Id']}' AND D024_Flag_Comercial != 'I'");
            $erroMysql = mysql_error();
            if (!empty($erroMysql)){
                $resposta['code'] = false;
                $resposta['data'] = "Erro no Update inativo: $erroMysql $cClausula";
            }
        }     */    
    }
    

    if($resposta['etapa'] >= $resposta['etapas']){
        @unlink("/tmp/cacheC031-{$g['usuarioAtual']}");
        @unlink("/tmp/cacheD024-{$g['usuarioAtual']}");
        @unlink("/tmp/cacheD024-resposta-{$g['usuarioAtual']}");
        $resposta['concluido'] = 1;
    } else {
        @unlink("/tmp/cacheD024-resposta-{$g['usuarioAtual']}");
        $resposta['etapa'] = $resposta['etapa'] + 1;
        file_put_contents("/tmp/cacheD024-resposta-{$g['usuarioAtual']}", serialize($resposta));
    }        


echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
