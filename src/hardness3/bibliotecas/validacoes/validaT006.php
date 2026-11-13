<?php
namespace hardness;

    log("entrou validaT006");
    $T006 = mysql_query("select T006_Id,
                                T006_D009_Id,                
                                T006_T005_Id,                         
                                T006_D005_Id,       
                                T006_Descricao_Produto,         
                                D001_Quantidade_Tributavel,
                                D001_Codigo_Produto,
                                D006_Flag_Unidade_Tributavel,
                                D005_D037_Id_Tributavel,
                                D005_Classificacao_Fiscal,
                                T006_Numero_Pedido_Compra,  
                                T006_Valor_Total_Frete,
                                T006_C004_Id_Estoque,
                                T005_C004_Id,
                                T006_T006_Id_Origem,
                                T006_Quantidade,                            
                                T006_Classificacao_Fiscal,
                                D006_Flag_Estoque,
                                D006_Codigo_CFOP,
                                C004_Flag_Empresa_Simples,
                                T006_Valor_Preco_Sem_Desconto_Unitario                          
                           from T006
                      left join D009 on D009_Id=T006_D009_Id
                      left join D049 on D049_Id=D009_D049_Id
                      left join D001 on D001_Id=D049_D001_Id
                      left join D006 on D006_Id=T006_D006_Id
                      left join D005 on D005_Id=T006_D005_Id
                      left join T005 on T005_Id=T006_T005_Id
                      left join C004 on C004_Id=T005_C004_Id
                          where T006_T005_Id='{$T005_Id}'");
    $freteProdutos = 0;
    $verificaCancelado = false;
    $return = '';

    while($mT006 = mysql_fetch_array($T006)){               

        $freteProdutos += $mT006['T006_Valor_Total_Frete'];   

        
        if(empty($mT006['T006_Classificacao_Fiscal']) || ($mT006['T006_D005_Id'] <= 0)){         
            $return .= "01 - Código {$mT006['D001_Codigo_Produto']}: Falta NCM.\n"; 
        }        
         
        // verifica se será utilizado os valores tributários
        if($mT006['D006_Flag_Unidade_Tributavel'] == 'S'){
            if(empty($mT006['D005_D037_Id_Tributavel']) || $mT006['D005_D037_Id_Tributavel'] <=0){ 
                $return .= "02 - Código {$mT006['D001_Codigo_Produto']}: A Unidade Tributável no cadastro da NCM " . $mT006['D005_Classificacao_Fiscal']. " deve ser preenchida.\n";
            }
            if($mT006['D001_Quantidade_Tributavel'] <= 0){ 
                $return .= "03 - Código {$mT006['D001_Codigo_Produto']}: A Quantidade Tributável no cadastro do produto deve ser preenchida.\n";  
            }
        }

        if(strlen(trim($mT006['T006_Numero_Pedido_Compra'])) > 15 ){ 
            $return .= "04 - Código {$mT006['D001_Codigo_Produto']}: Num. OC do produto deve possuir menos de 15 caracteres.\n";
        }
    
        $T006_C004_Id_Estoque = $mT006['T006_C004_Id_Estoque'] > 0 ? $mT006['T006_C004_Id_Estoque'] : $mT006['T005_C004_Id'];
        if($T006_C004_Id_Estoque != $mT006['T005_C004_Id'] && $mT006['T006_T005_Id'] == $T005_Id){
            $sqlPedidoGerado = mysql_query("select T006_Id 
                                            from T006 
                                       left join T005 ON T005_Id = T006_T005_Id 
                                           where T006_T006_Id_Origem = '{$mT006['T006_Id']}' 
                                             and T005_Flag_Status != '8'");

            if(mysql_num_rows($sqlPedidoGerado) <= 0){
                $verificaCancelado = true;                        
            }
        }                                          

        if ($mT006['D006_Flag_Estoque'] != 'N' && $mT006['T006_Quantidade'] > 0 && $mT006['T006_D009_Id'] == 0) { 
            $return .= "05 - Código {$mT006['D001_Codigo_Produto']}: CFOP {$mT006['D006_Codigo_CFOP']} movimenta estoque e tem itens que não possuem cadastro no sistema, verifique. \n";
        }    

        if($mT006['T006_Valor_Preco_Sem_Desconto_Unitario'] <=0){
            $return .= "06 - Código {$mT006['D001_Codigo_Produto']}: Necessário informar o valor do produto no pedido. \n";
        }

        $caracteres = strLen($mT006['T006_Descricao_Produto']);
        if($caracteres > 120){            
            $return .= "07 - Código {$mT006['D001_Codigo_Produto']}: Descrição do produto possui {$caracteres} caracteres, permitido 120 caracteres, verifique. \n";
        }
    }

    $T005 = mysql_query("select T005_Valor_Frete,
                                T005_Flag_Frete
                           from T005
                          where T005_Id = '{$T005_Id}'");
    $mT005 = mysql_fetch_array($T005);

    $freteProdutos = round($freteProdutos,2);
    log("freteProdutos ".$freteProdutos);
    if($mT005['T005_Valor_Frete'] != $freteProdutos && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT005['T005_Flag_Frete'] == 0){ 
        $return .= "07 - A soma do frete dos produtos é diferente do valor do frete no cabeçalho do pedido.\n";
    }

    if($verificaCancelado === true){ 
        $return .= "08 - Não foi possível gerar a nota fiscal, existem pedidos externos que estão cancelados, verifique!\n";
    }

    if(!empty($return)){
        return $return;
    }
