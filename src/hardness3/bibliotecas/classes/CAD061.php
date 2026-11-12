<?php
namespace hardness;
definirClasse('CAD061');
class CAD061_
{
    /**
     * incluirProduto
     *
     * @param Form $form
     * @param String $tipo
     * @return String/bool
     */
    public function incluirProduto($form, $tipo = null)
    {

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();

        global $g, $D158_Id;

        $D158_D009_Id              = $form->campoValorEnviado('D158_D009_Id');
        $D158_D005_Id              = $form->campoValorEnviado('D158_D005_Id');
        $D158_D006_Id              = $form->campoValorEnviado('D158_D006_Id');
        $D158_Quantidade           = $form->campoValorEnviado('D158_Quantidade');
        $D158_Flag_Destino_Produto = $form->campoValorEnviado('D158_Flag_Destino_Produto');
        $D158_Valor_Preco_Unitario = $form->campoValorEnviado('D158_Valor_Preco_Sem_Desconto_Unitario');
        $D158_D037_Id              = $form->campoValorEnviado('D158_D037_Id');
        $D158_Descricao_Produto    = mysql_real_escape_string($form->campoValorEnviado('D158_Descricao_Produto'));
        $D158_Codigo_Produto       = $form->campoValorEnviado('D158_Codigo_Produto');
        $D158_IPV                  = $form->campoValorEnviado('D158_IPV');
        $D157_Id                   = $form->campoValorEnviado('D158_D157_Id');
        $D158_Codigo_Substituto    = $form->campoValorEnviado('D158_Codigo_Substituto');
        $D158_Numero_Pedido_Compra = $form->campoValorEnviado('D158_Numero_Pedido_Compra');
        $D158_Item_Pedido_Compra   = $form->campoValorEnviado('D158_Item_Pedido_Compra');
        $D158_Observacao           = $form->campoValorEnviado('D158_Observacao');
        $D158_Observacao_Entrega   = $form->campoValorEnviado('D158_Observacao_Entrega');
        $D158_Aliquota_IPI         = $form->campoValorEnviado('D158_Aliquota_IPI');
        $idVeiculoPedido           = $form->campoValorEnviado('idVeiculoPedido');

        $D158_D022_Id                 = $form->campoValorEnviado('D158_D022_Id');
        $D158_D144_Id                 = $form->campoValorEnviado('D158_D144_Id');
        $D158_T208_Id                 = $form->campoValorEnviado('D158_T208_Id');
        $D158_Hora_Entrega            = $form->campoValorEnviado('D158_Hora_Entrega');
        $D158_Percentual_Desconto     = gCorrigeNumeroInverte($form->campoValorEnviado('D158_Percentual_Desconto'));
        //$NcmProduto=$form->campoValorEnviado('NcmProduto');
        //$D158_Percentual_Desconto=0;
        $D158_Valor_Desconto_Unitario = 0;
        $D158_Id                      = '';

        $D158_Quantidade           = gCorrigeNumeroInverte($D158_Quantidade);
        $D158_Valor_Preco_Unitario = gCorrigeNumeroInverte($D158_Valor_Preco_Unitario);

        $Aliquotas = $VEN001->retornaAliquotas($D158_D009_Id, false, $D157_Id);

        $D001  = mysql_query("SELECT D001_Id,
                  D082_Id,
                  D001_Codigo_Produto,
                  D049_Origem_Mercadoria,
                  D001_Flag_Tipo,
                  D049_Flag_Nacional_Importado,
                  D001_Cubagem_Unitaria,
                  D001_Flag_Pre_Cadastro,
                  T001_Id,
                  D009_Quantidade_OC_3(D009_Id,0) as D009_Quantidade_OC
               FROM D001
            LEFT JOIN D049 ON D049_D001_Id=D001_Id
            LEFT JOIN D009 ON D009_D049_Id=D049_Id
            LEFT JOIN D082 ON D082_Id=D049_D082_Id
            LEFT JOIN T001 ON T001_Id = D009_T001_Id
              WHERE D009_Id='$D158_D009_Id'");
        $mD001 = mysql_fetch_array($D001);

        if ($mD001['D001_Flag_Pre_Cadastro'] == 'S' && $g['C031']['permitePedidoProdutoPreCadastro'] == 'N') {
            return "Não foi possível incluir o produto! Este produto está em Pré-Cadastro.";
        }

        $D158_D001_Id        = $mD001['D001_Id'];
        $D158_Codigo_Produto = $mD001['D001_Codigo_Produto'];

        $C007  = mysql_query("SELECT *
               FROM C007
              WHERE C007_Id={$g['usuarioAtual']}");
        $mC007 = mysql_fetch_array($C007);

        $D158  = mysql_query("SELECT D158_Item
                  FROM D158
                 WHERE D158_D157_Id='$D157_Id'
                order by D158_Item desc
                 limit 1");
        $mD158 = mysql_fetch_array($D158);

        $D158_Item = $mD158['D158_Item'] + 1;

        $cClausula = " SELECT ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D049_Id, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " D009_Flag_Preco_Tabelado, D009_Quantidade_Estoque_Liquido ";
        $cClausula .= " FROM D001 ";
        $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D005 ON D001_D005_Id=D005_Id ";
        $cClausula .= " WHERE D009_Id='$D158_D009_Id' ";
        $D158  = mysql_query($cClausula);
        $mD158 = mysql_fetch_array($D158);

        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro consulta custo produto: $erro";
        }

        if (($mD158['D009_Id']) <= 0) {
            return "Erro: Produto não encontrado";
        }

        $cClausula = " SELECT *, ";
        $cClausula .= " D053_ST_ICMS_Interno, D053_ST_ICMS_Inter_Estadual, D053_Flag_ST, D018_Id, C004_D018_Id, C004_CFOP_ST_Venda_Dentro_Estado, C004_CFOP_ST_Venda_Fora_Estado, D006_Flag_Venda_Compra_Outros ";
        $cClausula .= " FROM D157 ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=D157_D024_Id ";
        $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
        $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=D157_C004_Id ";
        $cClausula .= " LEFT JOIN C004 ON C004_Id=D157_C004_Id ";
        $cClausula .= " LEFT JOIN D006 ON D006_Id=D157_D006_Id ";
        $cClausula .= " WHERE D157_Id='$D157_Id' ";
        $D157  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "$cErro<br />$cClausula";
            exit;
        }
        $mD157 = mysql_fetch_array($D157);

        $D024_Id = $mD157['D157_D024_Id'];

        // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
        $listPreco            = mysql_query("SELECT D157_Flag_Lista_Preco, D157_D024_Id, D157_C004_Id, D157_Flag_ACP, D157_C004_Id_Estoque FROM D157 WHERE D157_Id='{$D157_Id}'");
        $mLista_Preco         = mysql_fetch_array($listPreco);
        $D024_Id              = $mLista_Preco['D157_D024_Id'];
        $D158_D006_Id         = $VEN001->retornaCFOP($mLista_Preco['D157_D024_Id'], $D158_D009_Id, $mLista_Preco['D157_C004_Id'], $D158_Flag_Destino_Produto, false, false, false, $mLista_Preco['D157_C004_Id_Estoque']);
        $demonstrativoCFOP    = $VEN001->retornaCFOP($mLista_Preco['D157_D024_Id'], $D158_D009_Id, $mLista_Preco['D157_C004_Id'], $D158_Flag_Destino_Produto, false, true, false, $mLista_Preco['D157_C004_Id_Estoque']);
        $demonstrativoCFOP    = mysql_real_escape_string($demonstrativoCFOP);

        $Aliquotas            = $VEN001->retornaAliquotas($D158_D009_Id, false, $D157_Id, false, false, false, false, $D158_D006_Id);

        $demonstrativoT001    = $mD001['T001_Id'];

        //Método retornaPrecoVenda atualizado com apenas um demonstrativo
        $dadosPrecoVenda         = $CAD002->retornaPrecoVenda($mD158['D009_Id'], $mLista_Preco['D157_Flag_Lista_Preco'], $D024_Id, false, $D158_D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $Preco_Tabelado          = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        $custoUnitarioProduto    = $dadosPrecoVenda[4];

        if ($D158_Valor_Preco_Unitario <= 0) {
            $D158_Valor_Preco_Unitario = $Preco_Tabelado;
        }

        if ($D158_Percentual_Desconto > 0) {
            $D158_Valor_Preco_Unitario = $D158_Valor_Preco_Unitario - ($D158_Valor_Preco_Unitario * ($D158_Percentual_Desconto / 100));
            if ($Preco_Tabelado > 0) {
                $D158_Percentual_Desconto     = ((1 - ($D158_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $D158_Valor_Desconto_Unitario = (($Preco_Tabelado * ($D158_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $D158_Valor_Preco_Unitario;
            }
        } else {
            if ($Preco_Tabelado > 0) {
                $D158_Percentual_Desconto     = ((1 - ($D158_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $D158_Valor_Desconto_Unitario = (($Preco_Tabelado * ($D158_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $D158_Valor_Preco_Unitario;
            }
        }

        // Buscando o percentual de tributos (lei transparências)
        $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
        $mD149 = mysql_fetch_array($D149);

        $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$D158_D006_Id}'");
        $mD006 = mysql_fetch_array($D006);

        if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
            $D158_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
        } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
            $D158_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
        } else {
            $D158_Percentual_Trib_Transparencia = 0;
        }

        if ($Aliquotas['D005_Id'] == '') {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($T004_D005_Id);
        } else {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        }

        $precoTabela                     = $CAD002->cad002RetornaPrecoTabela($mD158['D009_Id'], $mLista_Preco['D157_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['D157_Flag_ACP'], $D158_D006_Id, $Aliquotas['Aliquota_ICMS'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI']);
        $demonstrativoPrecoTabela        = $precoTabela[1];
        $valorPrecoTabela                = gCorrigeNumeroInverte($precoTabela[0]);

        $precoCompra                     = $CAD002->cad002RetornaPrecoUltimaCompra($mD158['D009_Id'], $mLista_Preco['D157_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['D157_Flag_ACP'], $D158_D006_Id, $Aliquotas['Aliquota_ICMS'], 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI']);
        $demonstrativo                   = $precoCompra[1];
        $valorPrecoCompra                = gCorrigeNumeroInverte($precoCompra[0]);

        $demonstrativoCustoTabela        = $CAD002->cad002demonstrativoCustoTabela($mD158['D009_Id']);

        $sqlD009Transf = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$mD158['D049_Id']}' AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}'");
        $resD009Transf = mysql_fetch_array($sqlD009Transf);
        $dadosPrecoTransferencia         = $CAD002->cad002RetornaPrecoTransferencia($resD009Transf['D009_Id'], $g['C031']['C004PrecoTransferenciaGridProdutos'], $g['C004']['C004_D024_Id'], $D157_Id);
        $valorPrecoTransferencia         = gCorrigeNumeroInverte($dadosPrecoTransferencia[0]);
        $demonstrativoPrecoTransferencia = $dadosPrecoTransferencia[1];
        $valorCustoTransferencia         = $dadosPrecoTransferencia[4];
        $origemProdutoTransferencia      = $dadosPrecoTransferencia[5];

        // Demonstrativo
        $demonstrativo                   = mysql_real_escape_string(serialize($demonstrativo));
        $demonstrativoPrecoTabela        = mysql_real_escape_string(serialize($demonstrativoPrecoTabela));
        $demonstrativoPrecoTransferencia = mysql_real_escape_string(serialize($demonstrativoPrecoTransferencia));
        $demonstrativoNCM                = mysql_real_escape_string($demonstrativoNCM);
        $demonstrativoCustoTabela        = mysql_real_escape_string($demonstrativoCustoTabela);

        $cClausula = " INSERT INTO D158 (";
        $cClausula .= " D158_C004_Id, ";
        $cClausula .= " D158_Quantidade_Estoque, ";
        $cClausula .= " D158_Item, ";
        $cClausula .= " D158_Codigo_Produto, ";
        $cClausula .= " D158_Codigo_Substituto, ";
        $cClausula .= " D158_Descricao_Produto, ";
        $cClausula .= " D158_D157_Id, ";
        $cClausula .= " D158_D024_Id, ";
        $cClausula .= " D158_D009_Id, ";
        $cClausula .= " D158_D037_Id, ";
        $cClausula .= " D158_D082_Id, ";
        $cClausula .= " D158_Quantidade, ";
        $cClausula .= " D158_Valor_Custo_Unitario, ";
        $cClausula .= " D158_Valor_Preco_Unitario, ";
        $cClausula .= " D158_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " D158_Valor_Preco_Original, ";
        $cClausula .= " D158_Valor_Preco_Original_Bruto, ";
        $cClausula .= " D158_Percentual_Desconto, ";
        $cClausula .= " D158_Valor_Desconto_Unitario, ";
        $cClausula .= " D158_Percentual_Desconto_Bruto, ";
        $cClausula .= " D158_Valor_Desconto_Unitario_Bruto, ";
        $cClausula .= " D158_IPV, ";
        $cClausula .= " D158_Peso_Unitario, ";
        $cClausula .= " D158_Peso_Bruto, ";
        $cClausula .= " D158_D005_Id,";
        $cClausula .= " D158_Classificacao_Fiscal,";
        $cClausula .= " D158_D006_Id,";
        $cClausula .= " D158_Aliquota_IPI,";
        $cClausula .= " D158_Aliquota_ICMS,";
        $cClausula .= " D158_Percentual_Reducao_ICMS,";
        $cClausula .= " D158_Percentual_Reducao_ICMS_ST,";
        $cClausula .= " D158_ST_VA,";
        $cClausula .= " D158_Flag_ST,";
        $cClausula .= " D158_Flag_Situacao_Tributaria,";
        $cClausula .= " D158_ICMS_Inter_Estadual,";
        $cClausula .= " D158_ST_ICMS_Interno,";
        $cClausula .= " D158_ICMS_Inter_Estadual_MVA,";
        $cClausula .= " D158_ST_ICMS_Interno_MVA,";
        $cClausula .= " D158_Flag_Isento_PIS,";
        $cClausula .= " D158_Aliquota_PIS,";
        $cClausula .= " D158_Situacao_Tributaria_PIS,";
        $cClausula .= " D158_Flag_Isento_COFINS,";
        $cClausula .= " D158_Aliquota_COFINS, ";
        $cClausula .= " D158_Flag_Preco_Especial, ";
        $cClausula .= " D158_Situacao_Tributaria_COFINS, ";
        $cClausula .= " D158_Situacao_Tributaria_IPI, ";
        $cClausula .= " D158_D059_Id, ";
        $cClausula .= " D158_Observacao, ";
        $cClausula .= " D158_Numero_Pedido_Compra, ";
        $cClausula .= " D158_D022_Id, ";
        $cClausula .= " D158_T208_Id, ";
        $cClausula .= " D158_Hora_Entrega, ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " D158_Item_Pedido_Compra, ";
            $cClausula .= " D158_Valor_Preco_Bruto, ";
        } else {
            $cClausula .= " D158_Item_Pedido_Compra, ";
        }
        $cClausula .= " D158_Cubagem_Unitaria, ";
        $cClausula .= " D158_Percentual_Trib_Transparencia, ";
        $cClausula .= " D158_D144_Id, ";
        $cClausula .= " D158_Demonstrativo_Preco, ";
        $cClausula .= " D158_Demonstrativo_Preco_Tabela, ";
        $cClausula .= " D158_Demonstrativo_Preco_Transferencia, ";
        $cClausula .= " D158_Valor_Preco_Compra, ";
        $cClausula .= " D158_Valor_Preco_Tabela, ";
        $cClausula .= " D158_Valor_Preco_Transferencia, ";
        $cClausula .= " D158_Valor_Custo_Transferencia, ";
        $cClausula .= " D158_Flag_Origem_Produto_Transferencia, ";
        $cClausula .= " D158_T001_Id, ";
        $cClausula .= " D158_Flag_Destino_Produto, ";
        $cClausula .= " D158_Observacao_Entrega, ";
        $cClausula .= " D158_D006_Id_Info, ";
        $cClausula .= " D158_D005_Id_Info, ";
        $cClausula .= " D158_Demonstrativo_Custo_Tabela, ";
        $cClausula .= " D158_Quantidade_OC, ";
        $cClausula .= " D158_Demonstrativo_Preco_Venda, ";
        $cClausula .= " D158_Aliquota_IRPJ, ";
        $cClausula .= " D158_Flag_Isento_IRPJ,";
        $cClausula .= " D158_Aliquota_CSLL, ";
        $cClausula .= " D158_Flag_Isento_CSLL";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '{$mD158['D009_Quantidade_Estoque_Liquido']}', ";
        $cClausula .= " '$D158_Item', ";
        $cClausula .= " '$D158_Codigo_Produto', ";
        $cClausula .= " '$D158_Codigo_Substituto', ";
        $cClausula .= " '$D158_Descricao_Produto', ";
        $cClausula .= " '$D157_Id', ";
        $cClausula .= " '$D024_Id', ";
        $cClausula .= " '{$mD158['D009_Id']}', ";
        $cClausula .= " '$D158_D037_Id', ";
        $cClausula .= " '{$Aliquotas['D082_Id']}', ";
        $cClausula .= " '$D158_Quantidade', ";
        $cClausula .= " '{$custoUnitarioProduto}', ";
        //Se o produto for incluido pelo coletor, o valor unit. deve ser zero
        if ($tipo == 2) {
            $cClausula .= " '0', ";
        } else {
            $cClausula .= " '$D158_Valor_Preco_Unitario', ";
        }
        $cClausula .= " '$D158_Valor_Preco_Unitario', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '$D158_Percentual_Desconto', ";
        $cClausula .= " '$D158_Valor_Desconto_Unitario', ";
        $cClausula .= " '$D158_Percentual_Desconto', ";
        $cClausula .= " '$D158_Valor_Desconto_Unitario', ";
        $cClausula .= " '$D158_Valor_Preco_Unitario/{$Aliquotas['Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if ($Aliquotas['D005_Id'] == '') {
            $cClausula .= " '{$D158_D005_Id}',";
        } else {
            $cClausula .= " '{$Aliquotas['D005_Id']}',";
        }
        $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " '{$D158_D006_Id}',";
        //$cClausula .= " '{$mD158['Aliquota_IPI']}',";
        if ($D158_Aliquota_IPI <= 0) {
            $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
        } else {
            $cClausula .= " '{$D158_Aliquota_IPI}',";
        }
        $cClausula .= " '{$Aliquotas['Aliquota_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
        $cClausula .= " '{$Aliquotas['ST_VA']}',";
        $cClausula .= " '{$Aliquotas['Flag_ST']}',";
        $cClausula .= " '{$Aliquotas['Flag_Situacao_Tributaria']}',";
        $cClausula .= " '{$Aliquotas['ICMS_Inter_Estadual']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_PIS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_PIS']}',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_PIS']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_COFINS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_COFINS']}', ";
        $cClausula .= " '',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_COFINS']}',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_IPI']}',";
        $cClausula .= " '{$Aliquotas['D059_Id']}',";
        $cClausula .= " '$D158_Observacao', ";
        $cClausula .= " '$D158_Numero_Pedido_Compra', ";
        $cClausula .= " '$D158_D022_Id', ";
        $cClausula .= " '$D158_T208_Id', ";
        $cClausula .= " '$D158_Hora_Entrega', ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " '$D158_Item_Pedido_Compra', ";
            $cClausula .= " '$D158_Valor_Preco_Unitario', ";
        } else {
            $cClausula .= " '$D158_Item_Pedido_Compra', ";
        }
        $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
        $cClausula .= " '{$D158_Percentual_Trib_Transparencia}', ";
        $cClausula .= " '{$D158_D144_Id}', ";
        $cClausula .= " '{$demonstrativo}', ";
        $cClausula .= " '{$demonstrativoPrecoTabela}', ";
        $cClausula .= " '{$demonstrativoPrecoTransferencia}', ";
        $cClausula .= " '{$valorPrecoCompra}', ";
        $cClausula .= " '{$valorPrecoTabela}', ";
        $cClausula .= " '{$valorPrecoTransferencia}', ";
        $cClausula .= " '{$valorCustoTransferencia}', ";
        $cClausula .= " '{$origemProdutoTransferencia}', ";
        $cClausula .= " '{$demonstrativoT001}', ";
        $cClausula .= " '{$D158_Flag_Destino_Produto}', ";
        $cClausula .= " '{$D158_Observacao_Entrega}', ";
        $cClausula .= " '{$demonstrativoCFOP}', ";
        $cClausula .= " '{$demonstrativoNCM}', ";
        $cClausula .= " '{$demonstrativoCustoTabela}', ";
        $cClausula .= " '{$mD001['D009_Quantidade_OC']}', ";
        $cClausula .= " '{$demonstrativoPrecoVenda}', ";
        $cClausula .= " '{$Aliquotas['Aliquota_IRPJ']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_IRPJ']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_CSLL']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_CSLL']}'";
        $cClausula .= " ); ";

        mysql_query($cClausula);
        $D158_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro<br />$cClausula";
        } else {
            // Processa estoque por locação
            $CAD002->processaEstoqueLiquidoPorLocacao($mD158['D009_Id']);

            mysql_query("UPDATE T189 SET T189_Flag_Status = 'N' WHERE T189_D158_Id = '{$g['mysqlLastId']}'");
            if ($g['C031']['controleProdutoPedido'] == 'S') {
                if ($tipo == 1) {
                    $this->atualizarControleProduto($D158_Id);
                    $quantidadeSeparada = '';
                } else if ($tipo == 2) {
                    $this->inserirControleProduto($D158_Id);
                    $quantidadeSeparada = $D158_Quantidade;
                }
                $T066 = mysql_query("SELECT * FROM T066 WHERE T066_D009_Id ='{$mD158['D009_Id']}'");
                $res  = mysql_fetch_array($T066);
                $sql  = "INSERT INTO T185(
                                    T185_D158_Id,
                                    T185_T066_Id,
                                    T185_Qtd_Para_Separar,
                                    T185_Qtd_Separado
                                  )VALUES(
                                    '{$D158_Id}',
                                    '{$res['T066_Id']}',
                                    '{$D158_Quantidade}',
                                    '{$quantidadeSeparada}'
                                  )";
                mysql_query($sql);
            }

            if (!empty($D158_Codigo_Substituto)) {
                /*
                $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor FROM D083 WHERE D083_D001_Id='{$mD158['D001_Id']}' AND D083_D049_Id='{$mD158['D049_Id']}' AND D083_D024_Id='$D024_Id'");
                if (mysql_num_rows($D083) <= 0) {
                    mysql_query("INSERT INTO D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mD158['D049_Id']}','{$mD158['D001_Id']}','$D024_Id',curdate(),'S','$D158_Codigo_Substituto')");
                } else {
                    $mD083 = mysql_fetch_array($D083);
                    mysql_query("UPDATE D083 SET D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$D158_Codigo_Substituto' WHERE D083_Id='{$mD083['D083_Id']}'");
                }
                */
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                $CAD002->definirCodigoFornecedor($mD158['D009_Id'], $D024_Id, $D158_Codigo_Substituto, '', true, false);
            }
            //$this->atualizarAliquotasPedidos($D157_Id, false, $D158_Id);
            $cSql = mysql_query("call D158_Gravar_Totalizacao_4('$D158_Id');");
            $cSql = mysql_query("call D157_Gravar_Totalizacao_4('$D157_Id');");
        }

        // caso o produto possua serviços associados, insere estes serviços na T147
        $result  = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$D158_D009_Id}'");
        $retorno = mysql_fetch_array($result);
        $D001_Id = $retorno['D001_Id'];
        $result  = mysql_query("SELECT * FROM D135 LEFT JOIN D112 ON D112_Id=D135_D112_Id WHERE D135_D001_Id='{$D001_Id}'");

        if (mysql_num_rows($result) > 0) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            while ($D135 = mysql_fetch_array($result)) {
                $cClausula = " INSERT INTO T147 (";
                $cClausula .= " T147_D157_Id, ";
                $cClausula .= " T147_D112_Id, ";
                $cClausula .= " T147_D110_Id, ";
                $cClausula .= " T147_D158_Id, ";
                $cClausula .= " T147_Titulo, ";
                $cClausula .= " T147_Descricao, ";
                $cClausula .= " T147_Tempo_Estimado, ";
                $cClausula .= " T147_Quantidade, ";
                $cClausula .= " T147_Status, ";
                $cClausula .= " T147_Valor_Unitario, ";
                $cClausula .= " T147_Valor_Total, ";
                $cClausula .= " T147_D116_Id ";
                $cClausula .= " ) VALUES ( ";
                $cClausula .= " '$D157_Id', ";
                $cClausula .= " '{$D135['D135_D112_Id']}', ";
                $cClausula .= " '{$D135['D112_D110_Id']}', ";
                $cClausula .= " '$D158_Id', ";
                $cClausula .= " '{$D135['D112_Titulo']}', ";
                $cClausula .= " '{$D135['D112_Descricao']}', ";
                $cClausula .= " '{$D135['D112_Tempo_Estimado']}', ";
                $cClausula .= " '1', ";
                $cClausula .= " 'P', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$idVeiculoPedido}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
                
                $T147_Id = $g['mysqlLastId'];
                
                $sql    = "SELECT * FROM T147 WHERE T147_Id='{$T147_Id}'";
                $result = mysql_query($sql);
                $linha  = mysql_fetch_array($result);
                
                $sql     = "SELECT * FROM T171 WHERE T171_D116_Id='{$linha['T147_D116_Id']}' AND T171_D157_Id='{$linha['T147_D157_Id']}'";
                $result2 = mysql_query($sql);
                if (mysql_num_rows($result2) == 0) {
                    $sql = "INSERT INTO T171
  								(T171_D116_Id, T171_D157_Id, T171_T147_Id)
        						VALUES
          						('{$linha['T147_D116_Id']}', '{$linha['T147_D157_Id']}', '{$T147_Id}')";
                    
                    mysql_query($sql);
                    
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
                    $sql = "INSERT INTO T148 (T148_T147_Id, T148_Titulo, T148_Descricao) VALUES ('" . mysql_real_escape_string($T147_Id) . "', '"
                    //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                        . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    $sql = "INSERT INTO T149 (T149_D037_Id, T149_D009_Id, T149_T147_Id, T149_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T147_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                
            }
            mysql_query("COMMIT");
        }
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            mysql_query("UPDATE D158 SET D158_Valor_Preco_Sem_Desconto_Unitario = '{$D158_Valor_Preco_Unitario}' WHERE D158_Id = '{$D158_Id}'");
            mysqli_query("CALL D158_Gravar_Totalizacao_4('{$D158_Id}')");
            mysql_query("UPDATE D158 SET D158_Valor_Preco_Sem_Desconto_Unitario = D158_Valor_Preco_Sem_Desconto_Unitario/((D158_Valor_Preco_Sem_Desconto_Unitario+(D158_Valor_IPI/D158_Quantidade)+(D158_Valor_ICMS_Substituicao_Retencao/D158_Quantidade))/D158_Valor_Preco_Sem_Desconto_Unitario) WHERE D158_Id = '{$D158_Id}'");
            mysqli_query("CALL D158_Gravar_Totalizacao_4('{$D158_Id}')");
        }
        return TRUE;
    
    }

    /**
     * excluirProduto
     *
     * @param String $D158_Id
     * @param String $D157_Id
     * @return String/bool
     */     
    public function excluirProduto($D158_Id, $D157_Id)
    {

        global $g;
        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $C007 = mysql_fetch_array($C007);
        $sql  = "SELECT D157_Id FROM D157 WHERE D157_Id = '{$D157_Id}' AND (D157_Flag_Status>0 AND D157_Flag_Status!='' AND D157_Flag_Status IS NOT NULL AND D157_Flag_Status!=14 AND D157_Flag_Status!=15)";
        $res  = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0 && $C007['C007_Flag_Vendedor'] != 'D' && $C007['C007_Flag_Vendedor'] != 'S') {
            return 'Este pedido já foi faturado.';
        }
        
        // Verifica se existem produtos separados no estoque
        $cSql_D158Estoque = mysql_query("SELECT D158_Id FROM D158 WHERE D158_Id = '{$D158_Id}' AND D158_Quantidade_Separacao > 0;");
        if (mysql_num_rows($cSql_D158Estoque) > 0) {
            return "Não pode ser excluido porque o produto já está separado no estoque.<br/>Por favor, verifique com o setor de expedição.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $result = mysql_query("SELECT * FROM T147 WHERE T147_D158_Id='{$D158_Id}'");
        if (mysql_num_rows($result) > 0) {
            while ($T147 = mysql_fetch_array($result)) {
                $this->excluirServico($T147['T147_Id']);
            }
        }

        $sql = "SELECT D158_D009_Id FROM D158 WHERE D158_Id = '{$D158_Id}'";
        $res = mysql_query($sql);
        $mD158 = mysql_fetch_assoc($res);
        //echo "DELETE FROM D158 WHERE D158_Id='{$D158_Id}'";
        log("DELETE FROM D158 WHERE D158_Id='{$D158_Id}'");
        mysql_query("DELETE FROM D158 WHERE D158_Id='{$D158_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'falha na exclusão do produto...';
        }
        
        // Processa estoque por locação
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        //$CAD002->processaEstoqueLiquidoPorLocacao($mD158['D158_D009_Id']);
        
        //mysql_query("call D157_Gravar_Totalizacao_4({$D157_Id});");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
          //  return 'Erro no D157_Gravar_Totalização' . mysql_error();
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
        
    
    }

    /**
     * atualizarParcelas
     *
     * @param Form $form
     * @return String/bool
     */    
    public function atualizarParcelas($form)
    {

        global $g;
        
        $D157_Id                = $form->campoValorEnviado('D157_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D027_Id                = $form->campoValorEnviado('Portador_D027_Id');
        $primeiroVencimentoTipo = $form->campoValorEnviado('selecionaPrimeiroVencimento');
        $diasVctoTipo           = $form->campoValorEnviado('selecionaDias');
        $campoUsarFreteParcela  = strtoupper($form->campoValorEnviado('usarFreteParcela'));
        $campoUsarSTParcela     = strtoupper($form->campoValorEnviado('usarSTParcela'));
        $campoUsarIPIParcela    = strtoupper($form->campoValorEnviado('usarIPIParcela'));
        
        if (($primeiroVencimentoData != "") || (!empty($primeiroVencimentoData))) {
            $primeiroVencmentoValor = $primeiroVencimentoData;
        } else if (($primeiroVencimentoNum != "") || (!empty($primeiroVencimentoNum))) {
            $primeiroVencmentoValor = $primeiroVencimentoNum;
        }
        
        if (($IntervalorDias != "") || (!empty($IntervalorDias))) {
            $diasVctoValor = $IntervalorDias;
        } else if (($diaFixo != "") || (!empty($diaFixo))) {
            $diasVctoValor = $diaFixo;
        }
        if ($diasVctoTipo == "I") {
            $diasVctoTipo = "Intervalo";
        } else if ($diasVctoTipo == "F") {
            $diasVctoTipo = "Dia Fixo";
        }
        
        $cSql_D157 = mysql_query("SELECT * FROM D157 WHERE D157_Id='{$D157_Id}'");
        $mD157     = mysql_fetch_array($cSql_D157);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("DELETE D159 FROM D159 WHERE D159_D157_Id='{$D157_Id}'");
            if (mysql_error()) {
                return mysql_error();
            } else {
                return true;
            }
        }
        
        $cSql_D159 = mysql_query("SELECT * FROM D159 LEFT JOIN D027 ON D027_Id=D159_D027_Id WHERE D159_D157_Id='{$D157_Id}'");
        while ($mD159 = mysql_fetch_array($cSql_D159)) {
            $mPrazoAntigo[$mD159['D159_Numero_Parcela']]    = $mD159['D159_Prazos_Dias'];
            $mPortadorAntigo[$mD159['D159_Numero_Parcela']] = $mD159['D159_D027_Id'];
        }
        
        mysql_query("DELETE D159 FROM D159 WHERE D159_D157_Id='{$D157_Id}'");
        
        
        $Valor_Total_IPI       = $mD157['D157_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mD157['D157_Valor_Total'];

        //Global descontinuada 06/01/2015
        //$usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
        if ($campoUsarFreteParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mD157['D157_Valor_Frete']);
        }
        
        if ($campoUsarSTParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mD157['D157_Valor_ICMS_Substituicao_Retencao']);
        }
        if ($campoUsarIPIParcela == "ON") {
            $Valor_Total_Orcamento = ($Valor_Total_Orcamento - $mD157['D157_Valor_Total_IPI']);
        }
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
            //$Valor_Total_Titulo = ( ( round($Valor_Titulo_Parcela,2) * $Total_Parcelas ) + $Valor_Total_IPI );
            
            // verificar se deve adicionar juro na parcela 
            $C004              = mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
            $mC004             = mysql_fetch_array($C004);
            $tipoJuros         = $mC004['C004_Tipo_Juros'];
            $taxaJuros         = $mC004['C004_Taxa_Juros'];
            $taxaJurosParcela  = 0.00;
            $valorJurosParcela = 0.00;
            $parcelasIsentas   = $mC004['C004_Parcelas_Isentas_Juros'];
            if ($Total_Parcelas > $parcelasIsentas) {
                $taxaJurosParcela = $taxaJuros;
                if ($taxaJuros > 0.00) {
                    if ($tipoJuros == 'S')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosSimples($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    else if ($tipoJuros == 'C')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosCompostos($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    $valorJurosParcela = $Valor_Titulo_Parcela - ($Valor_Total_Orcamento / $Total_Parcelas);
                } else {
                    $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
                }
            } else {
                $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
            }
            
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas));
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if (($Valor_Total_Titulo != $Valor_Total_Orcamento) && ($Total_Parcelas <= $parcelasIsentas)) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        // se foi definido um intervalo de dias
        if ($IntervalorDias > 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela  
            $acrescimo = 0;
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime(date("Y-m-d"));
                        $date2            = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->format('%R%a');
                        $acrescimo        = $interval->format('%R%a');
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                
                if ($x == 1 && $campoUsarFreteParcela == "ON") {
                    $Valor_Titulo += $mD157['D157_Valor_Frete'];
                }                
                if ($x == 1 && $campoUsarSTParcela == "ON") {
                    $Valor_Titulo += $mD157['D157_Valor_ICMS_Substituicao_Retencao'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "ON") {
                    $Valor_Titulo += $mD157['D157_Valor_Total_IPI'];
                }
                
                $cClausula = "INSERT INTO D159 (";
                $cClausula .= "D159_D157_Id,";
                $cClausula .= "D159_D027_Id,";
                $cClausula .= "D159_Numero_Parcela,";
                $cClausula .= "D159_Prazos_Dias,";
                $cClausula .= "D159_Data_Vencimento,";
                $cClausula .= "D159_Taxa_Juros,";
                $cClausula .= "D159_Valor_Juros,";
                $cClausula .= "D159_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$D157_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                log($cClausula . '<br />');
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
            }
            // ou se foi colocado um dia fixo   
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes = date("m");
            $ano = date("Y");
            
            $diaFixoOriginal = $diaFixo;
            
            $validou = checkdate($mes, $diaFixo, $ano);
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = gCorrigeDataInverte($primeiroVencimentoData);
                    }
                    $dataPrazoDias = $dataVencimento;
                    // looping feito para quando o dia fixo é menor do que o intervalo dias da primeira parcela e fica no mesmo mes
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";
                } else {
                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (D159_Prazos_Dias)
                    $tempo          = $numMeses * 30;
                    $tempo          = "+$tempo days";
                    $date           = strtotime($tempo, strtotime($dataVencimento));
                    
                    $diaFixo = $diaFixoOriginal;
                    $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    //echo date("Y-m-d", $date) . ' -- ' . $numMeses . ' ---- ' . date("m", $date),$diaFixo,date("Y", $date) . "<br />";
                    while (!$validou) {
                        $diaFixo--;
                        $validou = checkdate(date("m", $date), $diaFixo, date("Y", $date));
                    }
                    
                    $dataPrazoDias = date("Y-m-" . $diaFixo, $date);
                    $numMeses      = $x - 1;
                }
                
                if ($x == 1 && $campoUsarFreteParcela == "ON") {
                    $Valor_Titulo += $mD157['D157_Valor_Frete'];
                }                
                if ($x == 1 && $campoUsarSTParcela == "ON") {
                    $Valor_Titulo += $mD157['D157_Valor_ICMS_Substituicao_Retencao'];
                }
                if ($x == 1 && $campoUsarIPIParcela == "ON") {
                    $Valor_Titulo += $mD157['D157_Valor_Total_IPI'];
                }
                
                $cClausula = "INSERT INTO D159 (";
                $cClausula .= "D159_D157_Id,";
                $cClausula .= "D159_D027_Id,";
                $cClausula .= "D159_Numero_Parcela,";
                $cClausula .= "D159_Data_Vencimento,";
                $cClausula .= "D159_Prazos_Dias,";
                $cClausula .= "D159_Taxa_Juros,";
                $cClausula .= "D159_Valor_Juros,";
                $cClausula .= "D159_Valor_Parcela";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$D157_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$dataPrazoDias',";
                //$cClausula.="DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias',current_date()),";
                $cClausula .= "'$taxaJurosParcela',";
                $cClausula .= "'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
            }
        }
        $sqlD157    = <<<EOT
            UPDATE D157 SET
            D157_Pgto_Parcelas='{$Total_Parcelas}',
            D157_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencimentoTipo}',
            D157_Pgto_Primeiro_Vcto_Valor='{$primeiroVencmentoValor}',
            D157_Pgto_Dias_Vcto_Tipo='{$diasVctoTipo}',
            D157_Pgto_Dias_Vcto_Valor='{$diasVctoValor}',
            D157_Pgto_Portador='{$D027_Id}',
            D157_Pgto_Data=CURRENT_TIMESTAMP(),
            D157_Pgto_C007_Id='{$g['usuarioAtual']}'
            WHERE D157_Id='{$D157_Id}'
EOT;
        $resultD157 = mysql_query($sqlD157, false);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    
    }

    /**
     * calcularParcelaCombo
     *
     * @param String $parcela
     * @param String $valorTotal
     * @param String $tipoJuros
     * @param String $taxaJuros
     * @param String $parcelasIsentas
     * @return String
     */    
    public function calcularParcelaCombo($parcela, $valorTotal, $tipoJuros, $taxaJuros, $parcelasIsentas)
    {
        global $g;
        if ($parcela <= $parcelasIsentas) {
            return gCorrigeNumero($valorTotal / $parcela);
        } else {
            if ($tipoJuros == 'S') {
                return gJurosSimples($valorTotal, $taxaJuros, $parcela);
            } else if ($tipoJuros == 'C') {
                return gJurosCompostos($valorTotal, $taxaJuros, $parcela);
            }
        }
        
    }

    public function diasemana($data) {
        $ano =  substr("$data", 0, 4);
        $mes =  substr("$data", 5, -3);
        $dia =  substr("$data", 8, 9);

        $diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano));

        switch($diasemana) {
            case"0": $diasemana = "01"; break; // domingo
            case"1": $diasemana = "02"; break; // segunda
            case"2": $diasemana = "03"; break; // terca
            case"3": $diasemana = "04"; break; // quarta
            case"4": $diasemana = "05"; break; // quinta
            case"5": $diasemana = "06"; break; // sexta
            case"6": $diasemana = "07"; break; // sábado
        }

        return "$diasemana";
    }

    /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */
    public function processarPedidosPeriodicos($prazoInicial, $prazoFinal){
        global $g;
        // $dias, $extra

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        //Prazos (Datas e dias da semana)
        $dataInicial = $dataInicial_ = date("Y-m-d", strtotime("+{$prazoInicial} day", strtotime(date("Y-m-d"))));
        $dataFinal = date("Y-m-d", strtotime("+{$prazoFinal} day", strtotime(date("Y-m-d"))));

        $datas = array();
        while(strtotime($dataInicial_) <= strtotime($dataFinal)) {
            $datas[] = array('dia' => date("d", strtotime($dataInicial_)), 'diaSemana' => date("w", strtotime($dataInicial_))+1, 'data' => $dataInicial_);
            $dataInicial_ = date("Y-m-d", strtotime("+1 day", strtotime($dataInicial_)));
        }

        $pedidosPeriodicos = array();
        $sqlD157 = mysql_query("SELECT D157_Id, D157_Tipo_Periodicidade, D157_Periodicidade FROM D157 WHERE D157_C004_Id = '{$g['empresaAtual']}' AND TRIM(IFNULL(D157_Periodicidade,'')) != ''");
        //Verifica os pedidos que estão dentro do prazo
        while($rowD157 = mysql_fetch_assoc($sqlD157)){
            $D157_Periodicidade = explode(',', $rowD157['D157_Periodicidade']);

            foreach ($datas as $key => $data) {
                if($rowD157['D157_Tipo_Periodicidade'] == '1') {
                    // D157_Tipo_Periodicidade = 1 = dia/mês
                    foreach($D157_Periodicidade as $dia){
                        if($dia == $data['dia']){
                            $pedidosPeriodicos[] = array('D157_Id' => $rowD157['D157_Id'], 'D157_Data_Entrega' => $data['data']);
                        }
                    }
                } else {
                    // D157_Tipo_Periodicidade = 2 = dia/semana
                    foreach($D157_Periodicidade as $diaSemana){
                        if($diaSemana == $data['diaSemana']){
                            $pedidosPeriodicos[] = array('D157_Id' => $rowD157['D157_Id'], 'D157_Data_Entrega' => $data['data']);
                        }
                    }
                }
            }
        }

        //Seleciona os dados referentes ao processo de duplicação (Usuario/Empresa/Dias de Intervalo)
        $sqlT005 = mysql_query("SELECT * FROM T005 ORDER BY T005_Processo_Id DESC LIMIT 1");
        $resT005 = mysql_fetch_array($sqlT005); 
        $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
        $resC007 = mysql_fetch_array($sqlC007);
        $sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
        $resC004 = mysql_fetch_array($sqlC004);

        $lastProcessoId = $resT005['T005_Processo_Id'] + 1;

        //Loop dos pedidos
        foreach ($pedidosPeriodicos as $key => $pedido) {
            $D157 = "SELECT * FROM D157 WHERE D157_Id = '{$pedido['D157_Id']}' AND D157_C004_Id = '{$g['empresaAtual']}'";
            $AllT005 = mysql_query($D157);
            $mD157 = mysql_fetch_assoc($AllT005);

            $D157_Tipo_Periodicidade = ($mD157['D157_Tipo_Periodicidade'] == '1') ? 'DIA/MÊS' : 'DIA/SEMANA';
            $processoLog  = "Pedido periódico {$mD157['D157_Id']}<br />";
            $processoLog .= 'Usuario: '.$resC007['C007_Primeiro_Nome'].'('.$g['usuarioAtual'].')<br />';
            $processoLog .= 'Empresa: '.$resC004['C004_Nome_Abreviado'].'('.$g['empresaAtual'].')<br />';
            $processoLog .= "{$D157_Tipo_Periodicidade} - {$mD157['D157_Periodicidade']}<br />";
            $processoLog .= 'Data: '.date('d/m/Y', time()).' - Hora: '.date('H:i:s', time());

            $Data_Entrega = $pedido['D157_Data_Entrega'];

            $D157_Id_Antigo = $mD157['D157_Id'];
            $campos="";
            $valores="";
            foreach ($mD157 as $key=>$value) {
                $key = str_replace("D157", "T005", $key);
                if($key == 'T005_Id'){
                    continue;
                }elseif($key == 'T005_Tipo_Periodicidade'){
                    continue;
                }elseif($key == 'T005_Periodicidade'){
                    continue;
                }elseif($key == 'T005_Data_Emissao'){
                    $campos.=$key.", ";
                    $valores.= "CURDATE(),";
                }elseif($key == 'T005_Data_Entrega'){
                    $campos.=$key.", ";
                    $valores.= "'" . mysql_real_escape_string($Data_Entrega) . "',";
                }elseif($key == 'T005_Flag_Status'){
                    $campos.=$key.", ";
                    $valores.= "'0',";
                }elseif($key == 'T005_Processo_Id'){
                    $campos.=$key.", ";
                    $valores.= "'{$lastProcessoId}',";
                }elseif($key == 'T005_Processo_Log'){
                    $campos.=$key.", ";
                    $valores.= "'{$processoLog}',";
                }else{
                    $campos.=$key.", ";
                    $valores.= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT005 = "INSERT INTO T005 ({$campos}) VALUES ({$valores})";
            $resT005 = mysql_query($insT005);
            if(!$resT005){
                mysql_query("ROLLBACK");
                return "Erro 1: " . mysql_error();
            }
            $T005_Id_Novo = $g['mysqlLastId'];

            // Selecionando todos os itens do pedido
            $D158 = mysql_query("SELECT * FROM D158 WHERE D158_D157_Id='{$D157_Id_Antigo}'");

            while($mT006 = mysql_fetch_assoc($D158)){
                $campos="";
                $valores="";
                foreach ($mT006 as $key=>$value) {
                    $key = str_replace("D158", "T006", $key);
                    $key = str_replace("D157", "T005", $key);
                    if($key == 'T006_Id'){
                        continue;
                    }elseif($key == "T006_T005_Id"){
                        $campos.=$key.", ";
                        $valores.= "'" . mysql_real_escape_string($T005_Id_Novo) . "',";
                    } elseif ($key == "T006_C004_Id_Estoque") {
                        $campos .= $key . ", ";
                        $valores .= "NULL,";
                    }else{
                        $campos.=$key.", ";
                        $valores.= "'" . mysql_real_escape_string($value) . "',";
                    }
                }

                $campos = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insT006 = "INSERT INTO T006 ({$campos}) VALUES ({$valores})";
                $resT006 = mysql_query($insT006);
                $erro = mysql_error();
                if(!$resT006){
                    mysql_query("ROLLBACK");
                    return "Erro 2: " . $insT006." <br />Erro: ".$erro;
                }
            }
        }
        mysql_query("COMMIT");
        return true;
    }
}
