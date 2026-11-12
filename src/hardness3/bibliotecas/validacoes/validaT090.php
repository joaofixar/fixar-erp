<?php
namespace hardness;
    
    $T090 = mysql_query("select D027_Flag_Boleto,                                                            
                                D007_Flag_Ativo,
                                ifnull(D027_D007_Id,0) as D027_D007_Id,
                                D027_Portador, 
                                T090_Id,
                                T090_D027_Id,
                                D007_Id,
                                T090_Valor_Parcela,
                                T090_Prazos_Dias,
                                T090_Valor_Parcela,
                                D006_Flag_Gera_Contas,
                                D007_Nosso_Numero,
                                D007_Nome_Banco,
                                D007_D062_Id 
                           from T090 
                      left join D027 on D027_Id=T090_D027_Id 
                      left join D007 on D007_Id=D027_D007_Id
                      left join T005 on T005_Id=T090_T005_Id
                      left join D006 on D006_Id=T005_D006_Id
                          where T090_T005_Id = '{$T005_Id}'
                       order by T090_Id");
    
    $return             = '';
    $valorParcelas      = 0;
    $ultimoPrazo        = -1;    
    $semPortador        = false;
    $portadorSemBanco   = false;
    $bancoInativo       = false;
    $prazoInconsistente = false;
    $validaNossoNumero  = false;
    $contaSemBanco      = false;
    $T090_Id            = 0;

    while($mT090 = mysql_fetch_array($T090)){         

        if ($mT090['D027_Flag_Boleto'] == 'S' && $mT090['D027_D007_Id'] == 0) {
            $portadorSemBanco = true;     
            $D027PortadorSemBanco = $mT090['D027_Portador'];     
        }
        
        if($mT090['D027_D007_Id'] > 0 && $mT090['D007_Flag_Ativo'] != 'S'){ 
            $bancoInativo = true;
            $D027_Portador = $mT090['D027_Portador'];
        }   
         
        //Para verificar o total das parcelas com o total do pedido
        $valorParcelas += $mT090['T090_Valor_Parcela'];    

        if ($ultimoPrazo > $mT090['T090_Prazos_Dias']) { 
            $prazoInconsistente = true;
            
        }
        $ultimoPrazo = $mT090['T090_Prazos_Dias'];
        
        if (empty($mT090['T090_D027_Id'])) {
            $semPortador = true;
        }         

        if($mT090['D006_Flag_Gera_Contas'] == 'R' && $mT090['D027_Flag_Boleto'] == 'S' && $validaNossoNumero === false){
            $sqlValidaNossoNumero = mysql_query("select T117_Nosso_Numero 
                                                   from T117 
                                              left join D007 on T117_D007_Id = D007_Id
                                                  where T117_Nosso_Numero = '{$mT090['D007_Nosso_Numero']}'
                                                    and D007_Id='{$mT090['D007_Id']}'
                                               order by T117_Nosso_Numero desc");
            if(!empty($mT090['D007_Nosso_Numero']) && mysql_num_rows($sqlValidaNossoNumero) > 0){
                $validaNossoNumero = true;
                $nossoNumero = $mT090['D007_Nosso_Numero'];
            }            
        }

        if ($mT090['D027_Flag_Boleto'] == 'S' && $mT090['D027_D007_Id'] > 0 && ($mT090['D007_D062_Id'] <=0 || $mT090['D007_D062_Id'] == '')) {
            $contaSemBanco         = true;  
            $portadorContaSemBanco = $mT090['D027_Portador'];
            $nomeContaSemBanco     = $mT090['D007_Nome_Banco'];     
        }
        
        // Para testar se existe T090_Id no erro nº 05
        $T090_Id = $mT090['T090_Id'];
    }
    
    if($portadorSemBanco === true){ 
        $return .= "01 - O portador ".$D027PortadorSemBanco." emite boleto e está sem uma conta definida.\n";
    }

    if($bancoInativo === true){ 
        $return .= "02 - O portador ".$D027_Portador." está com a conta bancária inativa, verifique com o setor financeiro.\n";
    }

    if($prazoInconsistente === true){
        $return .= "03 - Os prazos estão inconsistentes.\n";
    }

    if($semPortador === true){ 
        $return .= "04 - Existem parcelas sem portador.\n";
    }
    
    if ($T090_Id <= 0 && $g['c020']['C020_Pre_Pedido_Prazos_Obrigatorio'] == 'S') { 
        $return .= "05 - Você deve selecionar um prazo para prosseguir.\n";
    }

    if($validaNossoNumero === true){ 
        $return .= "06 - O nosso número " . $nossoNumero . " já está sendo utilizado, altere em bancos/caixas para o próximo número.\n";
    }

    if($contaSemBanco === true){ 
        $return .= "07 - O portador ". $portadorContaSemBanco. " está configurado para gerar boleto mas a conta ".$nomeContaSemBanco." não possui um banco definido.\n";
    }

    $T005 = mysql_query("select T005_Valor_Total
                           from T005
                          where T005_Id = '{$T005_Id}'");
    $mT005 = mysql_fetch_array($T005);

    if (number_format($mT005['T005_Valor_Total'], 2) != number_format($valorParcelas, 2) && $T090_Id > 0) { 
        $MsgVerificacao = "08 - O valor total das parcelas está divergente com o valor do pedido.\n";
        $return .= $MsgVerificacao;
    }

    if(!empty($return)){
        return $return;
    }
