<?php
namespace hardness;

    $return = '';
    if (empty($T005_Id)) { 
        $return.= 'Não encontrado o Id do pedido' . $T005_Id;
    }

    $T005 = mysql_query("select T005_C004_Id,                                                              
                                T005_Flag_Frete,
                                T005_Valor_Frete,
                                D024_D018_Id,
                                D024_Flag_Transporta_Diferentes_UF,
                                D024_Cnpj,
                                D024_Cpf,
                                D024_Cep,
                                D024_Endereco,
                                D024_Bairro,
                                D024_Nome_Empresa,
                                D024_Flag_Pessoa_Fisica_Juridica,
                                D024_Flag_Contribuinte,             
                                D024_Nome_Fantasia,                 
                                D024_E_Mail,                        
                                D024_Codigo_Regime_Tributario,      
                                D024_Numero,
                                D024_D020_Id,
                                D024_D030_Id,                       
                                D024_DDD_Telefone_1,                
                                D024_Telefone_1,                    
                                T005_D022_Id,
                                D006_Flag_Gera_Contas,
                                D022_Nome_Empresa,
                                ufTransportadora.D018_UF as ufTransportadora,
                                ufEntrega.D018_UF as ufEntrega,
                                D006_D014_Id,                            
                                D006_D073_Id,                                               
                                T005_C007_Id_Vendedor_Interno,                        
                                T005_D006_Id,
                                T005_D027_Id,                            
                                T005_D027_Id_Entrada,
                                T005_Flag_ACP,
                                T005_Numero_Parcelas,
                                T005_Valor_Entrada,
                                T005_Valor_Total,
                                T005_Valor_Total_ICMS,
                                T005_Numero_Dias,
                                (T005_Valor_Total_IPI+T005_Valor_Total_ICMS+T005_Valor_ICMS_Substituicao_Retencao) as tributos,   
                                C004_Flag_Empresa_Simples,    
                                ufCliente.D018_UF as ufCliente,                     
                                cidadeCliente.D020_Flag_Suframa as FlagSuframaCidadeCliente,
                                cidadeCliente.D020_Id as idCidadeCliente,
                                D024_Flag_Isento_IPI,
                                D024_Flag_Isento_PIS_COFINS,                                
                                D024_Flag_Revenda_Consumidor,
                                D024_Flag_Isento_ICMS_Venda,
                                D024_Flag_Suframa,
                                D024_Numero_Suframa,
                                D030_Nome_Pais,
                                D006_Flag_Atualiza_Custo,
                                D006_Flag_Estoque,                                
                                D006_Flag_MMF,
                                D006_Flag_Zona_Franca,
                                D006_Id,      
                                T005_Peso_Liquido,
                                T005_Peso_Bruto,
                                T005_Quantidade_Volumes,
                                T005_Flag_Frete,
                                T005_Transportadora_Placa_Veiculo,
                                T005_Estado_Veiculo,
                                T005_D148_Id_Entrega,
                                D148_Nome as entregaNome,
                                D148_Rua as entregaRua,
                                D148_Numero as entregaNumero,
                                D148_Bairro as entregaBairro,
                                D148_Cnpj as entregaCnpj,
                                D148_Cpf as entregaCpf,
                                D148_D018_Id as entregaUfId,
                                D148_D020_Id as entregaCidadeId,
                                D148_D030_Id as entregaPaisId,
                                cidadeEntrega.D020_Nome_Cidade as entregaCidade,
                                ufEntrega.D018_UF as entregaUf
                           from T005
                      left join D024 on D024_Id=T005_D024_Id
                      left join D018 as ufCliente on ufCliente.D018_Id=D024_D018_Id
                      left join D020 as cidadeCliente on cidadeCliente.D020_Id=D024_D020_Id
                      left join D030 on D030_Id=D024_D030_Id
                      left join D006 on D006_Id=T005_D006_Id
                      left join C004 on C004_Id=T005_C004_Id
                      left join D022 on D022_Id=T005_D022_Id
                      left join D018 as ufTransportadora on ufTransportadora.D018_Id=D022_D018_Id
                      left join D020 as cidadeTransportadora on cidadeTransportadora.D020_Id=D022_D020_Id
                      left join D148 on D148_Id=T005_D148_Id_Entrega
                      left join D018 as ufEntrega on ufEntrega.D018_Id=D148_D018_Id
                      left join D020 as cidadeEntrega on cidadeEntrega.D020_Id=D148_D020_Id
                          where T005_Id = '{$T005_Id}'");

    $mT005 = mysql_fetch_array($T005);

    if ($g['c020']['C020_Caixa_Cupom_Fiscal'] == 'N' and $mT005['T005_Flag_ACP'] == 4) {
        $return.= "01 - Opção de cupom fiscal não esta habilitada.\n";
    }

    //VGF02
    if ($g['c020']['C020_NFe_Habilitada'] == 'S' and $mT005['T005_Flag_ACP'] == 1) {
        $return.= "02 - Você está tentando emitir uma nota fiscal normal e a opção de NF-e esta habilitada para esta empresa.\n";
    }
    
    if ($mT005['T005_Peso_Liquido'] > $mT005['T005_Peso_Bruto']) { 
        $return.= "03 - O peso líquido é maior que o peso bruto, informe corretamente.\n";
    }

    if((empty($mT005['T005_Quantidade_Volumes']) || $mT005['T005_Quantidade_Volumes'] <= 0) && $mT005['T005_Flag_ACP'] == '6'){ 
        $return.= "04 - Quantidade de volumes não informada, verifique na aba \"Frete\".\n";
    }

    if($mT005['T005_Flag_ACP'] != 8){ 

        if(empty($mT005['D024_Nome_Empresa'])){ 
            $return.= "05 - O nome da empresa no cadastro do cliente não está preenchido. \n";
        }

        // if(empty($mT005['D024_Nome_Fantasia'])){
        //     $return.= "06 - O nome fantasia não está preenchido.\n";
        // }

        // if(empty($mT005['D024_Flag_Contribuinte'])){
        //     $return.= "07 - O marcador do tipo de revenda não está preenchido.\n";
        // }

        if(empty($mT005['D024_Cnpj'])){
            if($mT005['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){ 
                $return.= "08 - O CNPJ no cadastro do cliente não está preenchido.\n";
            }
        }

        if(empty($mT005['D024_Cpf'])){
            if($mT005['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){ 
                $return.= "09 - O CPF no cadastro do cliente não está preenchido.\n";
            }
        }

        if(empty($mT005['D024_D018_Id'])){ 
            $return.= "10 - A UF no cadastro do cliente não está preenchida.\n";
        }

        if(empty($mT005['D024_Cep'])){ 
            $return.= "11 - O CEP no cadastro do cliente não está preenchido, caso não exista, preencher com  '00000-000'.\n";
        }

        if(empty($mT005['D024_Endereco'])){ 
            $return.= "12 - O endereço no cadastro do cliente não está preenchido.\n";
        }

        if(empty($mT005['D024_Bairro'])){ 
            $return.= "13 - O bairro no cadastro do cliente não está preenchido.\n";
        }

        // if(empty($mT005['D024_Codigo_Regime_Tributario'])){
        //     $return.= "14 - O do códig no cadastro do cliente o de regime tributário não está preenchido.";
        // }

        if(empty($mT005['D024_Numero'])){ 
            $return.= "15 - O número no cadastro do cliente não está preenchido.\n";
        }

        if(empty($mT005['D024_D020_Id'])){ 
            $return.= "16 - O cidade no cadastro do cliente não está preenchido.\n";
        }

        // if(empty($mT005['D024_D030_Id'])){
        //     $return.= "17 - O país no cadastro do cliente não está preenchido.";
        // }

        // if(empty($mT005['D024_DDD_Telefone_1']) && empty($mT005['D024_Telefone_1'])){
        //     $return.= "18 - O do telefone no cadastro do cliente one não está preenchido corretamente.";
        // }

        // if(empty($mT005['D024_E_Mail'])){
        //     $return.= "19 - O do e-mai no cadastro do cliente l não está preenchido.";
        // }
    }


    if($mT005['T005_Flag_Frete'] != 9 && $mT005['T005_D022_Id'] <= 0) { 
        $return.= "20 - O tipo do frete selecionado exige que seja informado uma transportadora.\n";
    }

    if (!empty($mT005['D022_Nome_Empresa'])){
        if (!empty($mT005['T005_Transportadora_Placa_Veiculo'])) {
            if (strlen($mT005['T005_Transportadora_Placa_Veiculo']) != 7) { 
                //VF06
                $return.= "21 - Placa do Veiculo esta incorreta, caso tenha preenchido, não coloque hífen (-) ou espaço.\n";
            }
            if (empty($mT005['T005_Estado_Veiculo'])) {
                //VF07
                $return.= "22 - Informe a UF do veículo.\n";
            }
        }
    } else {
        if (!empty($mT005['T005_Transportadora_Placa_Veiculo'])) { 
            //VF08
            $return.= "23 - Foi informado a placa do veículo, porém, não foi informado a transportadora.\n";
        }     
    }

    // Valida se pode transportar em diferentes uf
    if($mT005['T005_Flag_Frete'] != 9 AND $g['C031']['permiteTransportarDiferentesUF'] == 'N' AND $mT005['D024_Flag_Transporta_Diferentes_UF'] == 'N'){    //Ok
        if($mT005['ufTransportadora'] != $mT005['ufEntrega']){
            $return.= "24 - UF ".$mT005['ufTransportadora']." da transportadora é diferente da UF ".$mT005['ufEntrega']." do endereço de entrega, verifique \n";
        }
    }

    if($mT005['tributos']>0 && $mT005['T005_Flag_ACP']=='3'){ 
        $return.= "25 - Não é permitido tributos para uma ACP.\n";
    }

    if(($mT005['D006_Flag_Gera_Contas'] == 'P' || $mT005['D006_Flag_Gera_Contas'] == 'R') && (empty($mT005['D006_D014_Id']) || $mT005['D006_D014_Id'] <= 0)){ //ok
        $tipoConta = ($mT005['D006_Flag_Gera_Contas'] == 'P') ? 'pagar' : 'receber';
        $return.= "27 - A CFOP gera contas a {$tipoConta} e está sem uma conta definida, verifique o cadastro da CFOP\n";
    }

    if($mT005['D006_Flag_Gera_Contas'] == 'P' && (empty($mT005['D006_D073_Id']) || $mT005['D006_D073_Id'] <= 0)) { //ok
        $return.= "28 - A CFOP gera contas a pagar e está sem um centro de custo definido, verifique o cadastro da CFOP.\n";
    } 

    if ($mT005['T005_D006_Id'] == 0) { 
        $return.= "29 - Pedido está sem CFOP definida.\n";
    }
    
    if (empty($mT005['T005_C007_Id_Vendedor_Interno']) and $g['C020']['C020_Pre_Pedido_Vendedor_Obrigatorio'] != 'N') { //Ok
        $return.= "30 - Vendedor Interno não foi gravado no pedido. \n";
    }
    
    if ($g['empresaAtual'] != $mT005['T005_C004_Id']) { 
        $return.= "31 - A Empresa selecionada não é a mesma do pedido, empresa Selecionada:" . $g['empresaAtual']."\n";
    }
    
    if ($mT005['T005_Valor_Total'] <= 0) {
        $return.= "32 - O Valor total do Pedido deve ser maior que 0.\n";
    } 

    if ($mT005['T005_Valor_Entrada'] > $mT005['T005_Valor_Total']) { 
        $return.= "33 - Valor da entrada é maior que o valor do pedido.\n";
    }

    if (($mT005['T005_Valor_Entrada'] > 0) and (empty($mT005['T005_D027_Id_Entrada'])) and ($mT005['T005_Flag_ACP'] != 2)) { 
        $return.= "34 - Faltou selecionar o Portador da entrada.\n";
    }

    if ((empty($mT005['T005_Numero_Dias'])) and ($mT005['T005_Numero_Parcelas'] > 1) and ($mT005['T005_Flag_ACP'] != 2)) { 
        $return.= "35 - Digite o Intervalo entre dias.\n";
    }

    if (($mT005['D030_Nome_Pais'] == 'BRASIL') and ($mT005['ufCliente'] == 'EX')) { 
        $return.= "36 - UF do cliente está definido como EX, verifique o cadastro do cliente.\n";
    }

    if (($mT005['T005_D006_Id'] > 0) and ($mT005['D006_Flag_Gera_Contas'] != 'N') and ($mT005['D006_Flag_Gera_Contas'] != 'P') and ($mT005['D006_Flag_Gera_Contas'] != 'R')) { 
        $return.= "37 - O campo \"Gera Contas\" no cadastro da CFOP está incorreto.\n";
    }

    if (($mT005['T005_D006_Id'] > 0) and ($mT005['D006_Flag_MMF'] != 'N') and ($mT005['D006_Flag_MMF'] != 'S')) { 
        $return.= "38 - O campo \"Min, Max e Frequência\" no cadastro da CFOP está incorreto. \n";
    }

    if (($mT005['T005_D006_Id'] > 0) and ($mT005['D006_Flag_Atualiza_Custo'] != 'N') and ($mT005['D006_Flag_Atualiza_Custo'] != 'S')) { 
        $return.= "39 - O campo \"Atualiza custo no cadastro\" da CFOP está incorreto. \n";
    }

    if ($mT005['D024_Flag_Revenda_Consumidor'] == 'S') { 
        
        //NAO PERMITE PROCESSEGUIR SE O CLIENTE NAO TEM NUMERO DO SUFRAMA OU NAO FOI SETADO FLAG SUFRAMA
        if ($mT005['FlagSuframaCidadeCliente'] == 'S' && empty($mT005['D024_Numero_Suframa']) && $mT005['D024_Flag_Suframa'] != 'N') {
            $return.= "40 -  Cliente não possui o número Suframa.\n";
        }
        
        //NAO PERMITE PROCESSEGUIR SE NAO FOR DEFINIDA EXCESSAO DO SUFRAMA
        if ($mT005['FlagSuframaCidadeCliente'] == 'S' && $mT005['D024_Flag_Suframa'] == '') {
            $return.= "41 -  Não foi definida a excessão do Suframa.\n";
        }
        
        //NAO PERMITE PROCESSEGUIR SE A CIDADE DO CLIENTE NAO PERTENCE AO SUFRAMA E ESTIVER DEFINIDO COM SUFRAMA
        if ($mT005['FlagSuframaCidadeCliente'] != 'S' && !empty($mT005['D024_Numero_Suframa'])) {
            $return.= "42 - Cidade do cliente não pertence ao Suframa e consta número no cadastro.\n";
        }
        
        //NAO PERMITE SELECIONAR NATUREZA ERRADA PARA SUFRAMA
        if ($mT005['D024_Flag_Suframa'] != 'S' && $mT005['D006_Flag_Zona_Franca'] == 'S') {
            $return.= "43 - Natureza selecionala é para zona franca de Manaus, selecione a natureza correte ou verifique o cadastro do cliente.\n";
        }
        if (($mT005['D024_Flag_Suframa'] == 'S') and ($mT005['D006_Flag_Zona_Franca'] == 'N')) {
            $return.= "44 - Natureza não é para zona frande de Manaus e cliente pertence ao suframa, selecione a natureza correte ou verifique o cadastro do cliente.\n";
        }
        
        if ($mT005['FlagSuframaCidadeCliente'] == 'S' && empty($mT005['D024_Numero_Suframa'])) {
            $return.= "45 -  Revenda e não possui o número Suframa.\n";
        }

        if ($mT005['D024_Flag_Suframa'] == 'S' && $mT005['D024_Flag_Isento_PIS_COFINS'] == 'N' && $mT005['D024_Flag_Isento_IPI'] == '2' && $mT005['D024_Flag_Isento_ICMS_Venda'] == 'N') {
            $return.= "46 - Cliente possui Suframa, é necessário informa no cadastro do cliente se é isento de ICMS, IPI ou PIS/CONFINS.\n";
        }

        if ($mT005['D024_Flag_Suframa'] == 'N' && ($mT005['D024_Flag_Isento_PIS_COFINS'] == 'S' || $mT005['D024_Flag_Isento_IPI'] == '1' || $mT005['D024_Flag_Isento_ICMS_Venda'] == 'S')) {
            $return.= "47 - Cliente não possui Suframa, deve ser definido no cadastro do cliente a isenção de impostos.\n";
        }
        if ($mT005['D024_Flag_Suframa'] == '' && ($mT005['D024_Flag_Isento_PIS_COFINS'] == 'S' || $mT005['D024_Flag_Isento_IPI'] == '1' || $mT005['D024_Flag_Isento_ICMS_Venda'] == 'S')) {
            $return.= "48 - Cliente não possui Suframa, deve ser definido no cadastro do cliente as isenções de impostos.\n";
        } 
    }

    // VERIFICA SE JÁ EXISTE NOTA FISCAL PARA O PEDIDO
    $sql = mysql_query("select T007_Numero_Nota_Fiscal,
                               T007_Data_Emissao,
                               T007_Empresa_Usuaria_Cnpj 
                          from T007 
                         where T007_T005_Id='{$T005_Id}' 
                           and T007_Flag_Cancelada!='S'  
                           and T007_Flag_Inutilizado!='S' 
                           and IFNULL(T007_Protocolo_Inutilizacao,'') = ''");

    if (mysql_num_rows($sql) > 0) {
        $return .= "49 - Este pedido já foi faturado: ";
        while ($mT007 = mysql_fetch_array($sql)) {
            $mT007['T007_Data_Emissao'] = troca_data_dma($mT007['T007_Data_Emissao']);
            $return .= "NF " . $mT007['T007_Numero_Nota_Fiscal'] . " no dia " . $mT007['T007_Data_Emissao'] . ", \n";
        }
        return $return;
        $cSql = mysql_query("UPDATE T005 SET T005_Flag_Status='4',T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),T005_Flag_Situacao=' ' WHERE T005_Id='{$T005_Id}'");
    }

    
    //VERIFICA SE O CAIXA ESTA ABERTO
    if ($g['c020']['C020_Caixa_Obrigatorio_Abrir_Fechar_Caixa'] == 'S') {
        log("caixa ".$g['c020']['C020_Caixa_Obrigatorio_Abrir_Fechar_Caixa']);
        $cClausula = " SELECT ";
        $cClausula .= " T119_D007_Id, ";
        $cClausula .= " T119_Data_Abertura, ";
        $cClausula .= " datediff(now(),T119_Data_Abertura) as Dias, ";
        $cClausula .= " D007_Numero_Agencia, ";
        $cClausula .= " D007_Numero_Conta ";
        $cClausula .= " FROM T119 ";
        $cClausula .= " LEFT JOIN D007 ON T119_D007_Id=D007_Id ";
        $cClausula .= " WHERE T119_Data_Fechamento='0000-00-00 00:00:00' ";
        $cSql_T119 = mysql_query($cClausula);
        echo mysql_error();
        $mT119 = mysql_fetch_array($cSql_T119);
        if (mysql_num_rows($cSql_T119) == 0) {
            $return .= "50 - Você deve fazer a abertura do caixa.\n";
        }
        if ($mT119['Dias'] > 0) {
            $return .= "51 - Você deve fazer o fechamento do caixa, pois a data de abertura é " . troca_datahora_dma($mT119['T119_Data_Abertura'])."\n";
        }
    }     

        error_log("vai comecar a validar o endereço de entrega : {$mT005['T005_D148_Id_Entrega']}");
    if ($mT005['T005_D148_Id_Entrega'] > 0) {
        error_log($mT005['entregaNome']);
        error_log("entregaRua: ".$mT005['entregaRua']);
        error_log("entregaNumero: ".$mT005['entregaNumero']);
        error_log("entregaBairro: ".$mT005['entregaBairro']);       
        error_log("entregaCnpj: ".$mT005['entregaCnpj']);
        error_log("entregaCpf: ".$mT005['entregaCpf']);
        error_log("entregaUfId: ".$mT005['entregaUfId']);
        error_log("entregaCidadeId: ".$mT005['entregaCidadeId']);
        error_log("entregaPaisId: ".$mT005['entregaPaisId']);
        $camposEntregaObrigatorios = array();

        if (empty($mT005['entregaNome'])) {
            $camposEntregaObrigatorios[] = 'nome';
        }
        if (empty($mT005['entregaCnpj']) && empty($mT005['entregaCpf'])) {
            $camposEntregaObrigatorios[] = 'CPF/CNPJ';
        }
        if (empty($mT005['entregaRua'])) {
            $camposEntregaObrigatorios[] = 'logradouro';
        }
        if (empty($mT005['entregaNumero'])) {
            $camposEntregaObrigatorios[] = 'numero';
        }
        if (empty($mT005['entregaBairro'])) {
            $camposEntregaObrigatorios[] = 'bairro';
        }
        if (empty($mT005['entregaCidadeId']) || empty($mT005['entregaCidade'])) {
            $camposEntregaObrigatorios[] = 'cidade';
        }
        if (empty($mT005['entregaUfId']) || empty($mT005['entregaUf'])) {
            $camposEntregaObrigatorios[] = 'UF';
        }
        if (empty($mT005['entregaPaisId'])) {
            $camposEntregaObrigatorios[] = 'pais';
        }

        if (!empty($camposEntregaObrigatorios)) {
            $return .= "52 - O endereço de entrega selecionado está com campos obrigatórios para NF-e não preenchidos: " . implode(', ', $camposEntregaObrigatorios) . ".\n";
        }
    }


    if(!empty($return)){
        return $return;
    }



