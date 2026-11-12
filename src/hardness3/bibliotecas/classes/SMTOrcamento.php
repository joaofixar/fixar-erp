<?php
namespace hardness;

definirClasse('SMTOrcamento');
class SMTOrcamento_ {
  public function OrcamentoPolux() {
    $dados  = $this->carregarDados();
    $layout = $this->montarLayout($dados);

    if (empty($layout)) {
      header('HTTP/1.0 400 Bad Request');

      $retorno['code']    = 400;
      $retorno['message'] = "Dados não localizados.";

      echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
    } else {
      echo $layout;
    }
  }

  function carregarDados() {
    global $g, $confUsuario;

    $T003_Id = $_GET['T003_Id'];
    $dados = array();

    #region -- DADOS DA EMPRESA
    $sqlEmpresa  = " SELECT C004.C004_Logo_Marca, ";
    $sqlEmpresa .=        " C004.C004_Nome_Empresa, ";
    $sqlEmpresa .=        " C004.C004_D024_Id, ";
    $sqlEmpresa .=        " C004.C004_DDD_Telefone, ";
    $sqlEmpresa .=        " C004.C004_Telefone, ";
    $sqlEmpresa .=        " C004.C004_Logradouro, ";
    $sqlEmpresa .=        " C004.C004_Numero, ";
    $sqlEmpresa .=        " C004.C004_Bairro, ";
    $sqlEmpresa .=        " C004.C004_E_Mail, ";
    $sqlEmpresa .=        " C004.C004_Site, ";
    $sqlEmpresa .=        " C004.C004_Cep, ";
    $sqlEmpresa .=        " C004.C004_Cnpj, ";
    $sqlEmpresa .=        " C004.C004_Inscricao_Estadual, ";
    $sqlEmpresa .=        " D020.D020_Nome_Cidade, ";
    $sqlEmpresa .=        " D020.D020_UF ";
    $sqlEmpresa .=   " FROM C004, D020 ";
    $sqlEmpresa .=  " WHERE C004.C004_Cidade = D020.D020_Codigo_IBGE ";
    $sqlEmpresa .=    " AND D020.D020_Id     = C004.C004_D020_Id ";
    $sqlEmpresa .=    " AND C004.C004_Id     = {$g['empresaAtual']} ";
    
    $queryEmpresa = mysql_query($sqlEmpresa);
    $dadosEmpresa = mysql_fetch_assoc($queryEmpresa);

    $empresaLogo         = empty($dadosEmpresa['C004_Logo_Marca']) ? "" : "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/" . $dadosEmpresa['C004_Logo_Marca'];
    $empresaLogoIso9001  = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/iso-9001.png";
    $empresaLogoIso14001 = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/iso-14001.png";
    $empresaLogoCE       = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/ce.png";
    $empresaLogoRoHS     = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/rohs.png";
    $empresaTelefone     = empty($dadosEmpresa['C004_DDD_Telefone']) ? $dadosEmpresa['C004_Telefone'] : "(" . $dadosEmpresa['C004_DDD_Telefone'] . ") " . $dadosEmpresa['C004_Telefone'];

    $dados['Empresa_Logo']            = $empresaLogo;
    $dados['Empresa_Logo_ISO9001']    = $empresaLogoIso9001;
    $dados['Empresa_Logo_ISO14001']   = $empresaLogoIso14001;
    $dados['Empresa_Logo_CE']         = $empresaLogoCE;
    $dados['Empresa_Logo_ROHS']       = $empresaLogoRoHS;
    $dados['Empresa_Nome']            = $dadosEmpresa['C004_Nome_Empresa'];
    $dados['Empresa_Endereco_Rua']    = $dadosEmpresa['C004_Logradouro'];
    $dados['Empresa_Endereco_Numero'] = $dadosEmpresa['C004_Numero'];
    $dados['Empresa_Endereco_Bairro'] = $dadosEmpresa['C004_Bairro'];
    $dados['Empresa_Endereco_Cidade'] = $dadosEmpresa['D020_Nome_Cidade'];
    $dados['Empresa_Endereco_Estado'] = $dadosEmpresa['D020_UF'];
    $dados['Empresa_Endereco_CEP']    = $dadosEmpresa['C004_Cep'];
    $dados['Empresa_CNPJ']            = $dadosEmpresa['C004_Cnpj'];
    $dados['Empresa_IE']              = $dadosEmpresa['C004_Inscricao_Estadual'];
    $dados['Empresa_Telefone']        = $empresaTelefone;
    $dados['Empresa_Email']           = $dadosEmpresa['C004_E_Mail'];
    $dados['Empresa_Site']            = $dadosEmpresa['C004_Site'];
    #endregion -- DADOS DA EMPRESA

    #region -- DADOS DA CAPA DO ORCAMENTO
    $sqlOrcamento  = " SELECT T003.T003_Id, ";
    $sqlOrcamento .=        " T003.T003_Nome_Cliente, ";
    $sqlOrcamento .=        " T003.T003_Data_Emissao, ";
    $sqlOrcamento .=        " T003.T003_Nome_Vendedor, ";
    $sqlOrcamento .=        " T003.T003_Validade_Proposta, ";
    $sqlOrcamento .=        " T003.T003_Flag_Frete, ";
    $sqlOrcamento .=        " T003.T003_Valor_Frete, ";
    $sqlOrcamento .=        " T003.T003_Valor_ST, ";
    $sqlOrcamento .=        " T003.T003_Peso_Total, ";
    $sqlOrcamento .=        " T003.T003_Valor_Total, ";
    $sqlOrcamento .=        " T003.T003_Valor_Total_Produtos, ";
    $sqlOrcamento .=        " T003.T003_Valor_Total_IPI, ";
    $sqlOrcamento .=        " T003.T003_Valor_Total_ICMS, ";
    $sqlOrcamento .=        " T003.T003_Valor_Total_PIS, ";
    $sqlOrcamento .=        " T003.T003_Valor_Total_COFINS, ";
    $sqlOrcamento .=        " T003.T003_Observacao, ";
    $sqlOrcamento .=        " T003.T003_Prazo_1, ";
    $sqlOrcamento .=        " T003A.T003A_Garantia, ";
    $sqlOrcamento .=        " D022.D022_Nome_Empresa, ";
    $sqlOrcamento .=        " D024.D024_Id, ";
    $sqlOrcamento .=        " D024.D024_Nome_Empresa, ";
    $sqlOrcamento .=        " D024.D024_E_Mail, ";
    $sqlOrcamento .=        " D024.D024_Cnpj, ";
    $sqlOrcamento .=        " D024.D024_Endereco, ";
    $sqlOrcamento .=        " D024.D024_Numero, ";
    $sqlOrcamento .=        " D024.D024_Bairro, ";
    $sqlOrcamento .=        " D024.D024_Inscricao_Estadual, ";
    $sqlOrcamento .=        " D024.D024_Cep, ";
    $sqlOrcamento .=        " D020.D020_Nome_Cidade, ";
    $sqlOrcamento .=        " D018.D018_UF, ";
    $sqlOrcamento .=        " D013.D013_Nome_Contato, ";
    $sqlOrcamento .=        " D013.D013_E_Mail, ";
    $sqlOrcamento .=        " D013.D013_Telefone_1, ";
    $sqlOrcamento .=        " T003.T003_D148_Id_Entrega, ";
    $sqlOrcamento .=        " D148_Ent.D148_Rua as Entrega_Rua, ";
    $sqlOrcamento .=        " D148_Ent.D148_Numero as Entrega_Numero, ";
    $sqlOrcamento .=        " D148_Ent.D148_Bairro as Entrega_Bairro, ";
    $sqlOrcamento .=        " D148_Ent.D148_Cep as Entrega_Cep, ";
    $sqlOrcamento .=        " T003.T003_D148_Id_Cobranca, ";
    $sqlOrcamento .=        " D148_Cob.D148_Rua as Cobranca_Rua, ";
    $sqlOrcamento .=        " D148_Cob.D148_Numero as Cobranca_Numero, ";
    $sqlOrcamento .=        " D148_Cob.D148_Bairro as Cobranca_Bairro, ";
    $sqlOrcamento .=        " D148_Cob.D148_Cep as Cobranca_Cep, ";
    $sqlOrcamento .=        " D021.D021_Nome_Empresa, ";
    $sqlOrcamento .=        " D021.D021_Telefone_1, ";
    $sqlOrcamento .=        " C007.C007_Nome, ";
    $sqlOrcamento .=        " C007.C007_Telefone, ";
    $sqlOrcamento .=        " (SELECT D027_Portador ";
    $sqlOrcamento .=           " FROM T089, D027 ";
    $sqlOrcamento .=          " WHERE D027_Id = T089_D027_Id ";
    $sqlOrcamento .=            " AND T089_T003_Id = 18 ";
    $sqlOrcamento .=          " LIMIT 1) AS FORMA_PAGAMENTO ";
    $sqlOrcamento .=   " FROM T003 ";
    $sqlOrcamento .=        " LEFT JOIN T003A         ON T003A.T003A_T003_Id        = T003.T003_Id";
    $sqlOrcamento .=        " LEFT JOIN D024          ON T003.T003_D024_Id          = D024.D024_Id";
    $sqlOrcamento .=        " LEFT JOIN D013          ON T003.T003_D013_Id          = D013.D013_Id";
    $sqlOrcamento .=        " LEFT JOIN D021          ON T003.T003_D021_Id          = D021.D021_Id";
    $sqlOrcamento .=        " LEFT JOIN C007          ON T003.T003_C007_Id          = C007.C007_Id";
    $sqlOrcamento .=        " LEFT JOIN D022          ON T003.T003_D022_Id          = D022.D022_Id";
    $sqlOrcamento .=        " LEFT JOIN D148 D148_Ent ON T003.T003_D148_Id_Entrega  = D148_Ent.D148_Id";
    $sqlOrcamento .=        " LEFT JOIN D148 D148_Cob ON T003.T003_D148_Id_Cobranca = D148_Cob.D148_Id";
    $sqlOrcamento .=        " LEFT JOIN D018          ON D024.D024_D018_Id          = D018.D018_Id";
    $sqlOrcamento .=        " LEFT JOIN D020          ON D024.D024_D020_Id          = D020.D020_Id";
    $sqlOrcamento .=  " WHERE T003_Id = {$T003_Id} ";

    $queryOrcamento = mysql_query($sqlOrcamento);
    $dadosOrcamento = mysql_fetch_assoc($queryOrcamento);

    $dados['Cliente_Nome']            = $dadosOrcamento['D024_Id'] . ' - ' . $dadosOrcamento['D024_Nome_Empresa'];
    $dados['Cliente_Empresa']         = $dadosOrcamento['D024_Nome_Empresa'];
    $dados['Cliente_IE']              = $dadosOrcamento['D024_Inscricao_Estadual'];
    $dados['Cliente_CNPJ']            = $dadosOrcamento['D024_Cnpj'];
    $dados['Cliente_Endereco_Rua']    = $dadosOrcamento['D024_Endereco'];
    $dados['Cliente_Endereco_Numero'] = $dadosOrcamento['D024_Numero'];
    $dados['Cliente_Endereco_Bairro'] = $dadosOrcamento['D024_Bairro'];
    $dados['Cliente_Endereco_CEP']    = $dadosOrcamento['D024_Cep'];
    $dados['Cliente_Endereco_Cidade'] = $dadosOrcamento['D020_Nome_Cidade'];
    $dados['Cliente_Endereco_Estado'] = $dadosOrcamento['D018_UF'];
    $dados['Contato_Nome']            = $dadosOrcamento['D013_Nome_Contato'];
    $dados['Contato_Email']           = $dadosOrcamento['D013_E_Mail'];
    $dados['Contato_Telefone']        = $dadosOrcamento['D013_Telefone_1'];
    $dados['Vendedor_Nome']           = $dadosOrcamento['C007_Nome'];
    $dados['Vendedor_Telefone']       = $dadosOrcamento['C007_Telefone'];
    $dados['Representante_Nome']      = $dadosOrcamento['D021_Nome_Empresa'];
    $dados['Representante_Telefone']  = $dadosOrcamento['D021_Telefone_1'];
    $dados['Orcamento_Numero']        = $dadosOrcamento['T003_Id'];
    $dados['Orcamento_Emissao']       = $this->formatarData($dadosOrcamento['T003_Data_Emissao']);
    $dados['Orcamento_Validade']      = $dadosOrcamento['T003_Validade_Proposta'];
    $dados['Orcamento_Observacao']    = $dadosOrcamento['T003_Observacao'];
    $dados['Orcamento_Garantia']      = (!empty($dadosOrcamento['T003A_Garantia']) ? $dadosOrcamento['T003A_Garantia'] : '12');
    $dados['Entrega_Tipo_Frete']      = $this->tipoFrete($dadosOrcamento['T003_Flag_Frete']);
    $dados['Entrega_Transportadora']  = $dadosOrcamento['D022_Nome_Empresa'];
    $dados['Entrega_Peso_Total']      = $this->formatarValor($dadosOrcamento['T003_Peso_Total']);
    $dados['Entrega_Endereco']        = $this->enderecoEntrega($dadosOrcamento);
    $dados['Pagamento_Prazo']         = $this->prazoPagamento($T003_Id);
    $dados['Pagamento_Forma']         = $dadosOrcamento['FORMA_PAGAMENTO'];
    $dados['Pagamento_Endereco']      = $this->enderecoPagamento($dadosOrcamento);
    $dados['Total_Produtos']          = $this->formatarValor($dadosOrcamento['T003_Valor_Total_Produtos']);
    $dados['Total_IPI']               = $this->formatarValor($dadosOrcamento['T003_Valor_Total_IPI']);
    $dados['Total_Frete']             = $this->formatarValor($dadosOrcamento['T003_Valor_Frete']);
    $dados['Total_ST']                = $this->formatarValor($dadosOrcamento['T003_Valor_ST']);
    $dados['Total_ICMS']              = $this->formatarValor($dadosOrcamento['T003_Valor_Total_ICMS']);
    $dados['Total_PIS']               = $this->formatarValor($dadosOrcamento['T003_Valor_Total_PIS']);
    $dados['Total_COFINS']            = $this->formatarValor($dadosOrcamento['T003_Valor_Total_COFINS']);
    $dados['Total_Valor']             = $this->formatarValor($dadosOrcamento['T003_Valor_Total']);
    #endregion -- DADOS DA CAPA DO ORCAMENTO

    #region -- DADOS DOS ITENS
    $dados['Itens'] = array();

    $sqlItem  = " SELECT D001.D001_Id, ";
    $sqlItem .=        " T004.T004_Item, ";
    $sqlItem .=        " T004.T004_Codigo_Produto, ";
    $sqlItem .=        " T004.T004_Descricao_Produto, ";
    $sqlItem .=        " T004.T004_Valor_Preco_Unitario, ";
    $sqlItem .=        " T004.T004_Quantidade, ";
    $sqlItem .=        " T004.T004_Aliquota_ICMS, ";
    $sqlItem .=        " T004.T004_Aliquota_IPI, ";
    $sqlItem .=        " T004.T004_Valor_Total_Preco, ";
    $sqlItem .=        " T004A.T004A_Data_Entrega, ";
    $sqlItem .=        " T004.T004_Codigo_Substituto,";
    $sqlItem .=        " T004.T004_Observacao_Entrega, ";
    $sqlItem .=        " T004.T004_Observacao, ";
    $sqlItem .=        " D005.D005_Classificacao_Fiscal ";
    $sqlItem .=   " FROM T003 ";
    $sqlItem .=        " LEFT JOIN T004  ON T004_T003_Id  = T003_Id ";
    $sqlItem .=        " LEFT JOIN T004A ON T004A_T004_Id = T004_Id ";
    $sqlItem .=        " LEFT JOIN D005  ON T004_D005_Id  = D005_Id ";
    $sqlItem .=        " LEFT JOIN D009  ON T004_D009_Id  = D009_Id ";
    $sqlItem .=        " LEFT JOIN D001  ON D009_D001_Id  = D001_Id ";
    $sqlItem .=  " WHERE T003_Id = {$T003_Id} ";
    $sqlItem .=  " ORDER BY T004.T004_Item ASC ";

    $queryItem = mysql_query($sqlItem);

    while ($dadosItem = mysql_fetch_assoc($queryItem)) {
      $item = array('Sequencia'       => $dadosItem['T004_Item'],
                    'Codigo'          => $dadosItem['T004_Codigo_Produto'],
                    'Descricao'       => $dadosItem['T004_Descricao_Produto'],
                    'NCM'             => $dadosItem['D005_Classificacao_Fiscal'],
                    'Valor_Unitario'  => $this->formatarValor($dadosItem['T004_Valor_Preco_Unitario']),
                    'Quantidade'      => intval($dadosItem['T004_Quantidade']),
                    'Aliquota_ICMS'   => $this->formatarValor($dadosItem['T004_Aliquota_ICMS']),
                    'Aliquota_IPI'    => $this->formatarValor($dadosItem['T004_Aliquota_IPI']),
                    'Valor_Total'     => $this->formatarValor($dadosItem['T004_Valor_Total_Preco']),
                    'Data_Entrega'    => $this->formatarData($dadosItem['T004A_Data_Entrega']),
                    'Codigo_Cliente'  => $dadosItem['T004_Codigo_Substituto'],
                    'Observacao'      => $dadosItem['T004_Observacao'],
                    'Prazo_Entrega'   => $dadosItem['T004_Observacao_Entrega'],
                    'Caracteristicas' => array()
                  );

        $sqlCaracteristicas  = " SELECT SMT010.SMT010_Caracteristicas, ";
        $sqlCaracteristicas .=        " SMT011_Valor_Caracteristica ";
        $sqlCaracteristicas .=   " FROM SMT010, SMT011 ";
        $sqlCaracteristicas .=  " WHERE SMT011.SMT011_Caracteristica_Produto = SMT010.SMT010_Id ";
        $sqlCaracteristicas .=    " AND SMT011.SMT011_D001_Id                = {$dadosItem['D001_Id']} ";

        $queryCaracteristicas = mysql_query($sqlCaracteristicas);
        while ($dadosCaracteristicas = mysql_fetch_assoc($queryCaracteristicas)) {
          $caracteristicas = array('Chave' => $dadosCaracteristicas['SMT010_Caracteristicas'],
                                   'Valor' => $dadosCaracteristicas['SMT011_Valor_Caracteristica'],
                                  );
          array_push($item['Caracteristicas'], $caracteristicas);
        }

        array_push($dados['Itens'], $item);
    }
    #endregion -- DADOS DOS ITENS

    if ((((is_null($dados['Orcamento_Numero'])) || (empty($dados['Orcamento_Numero'])))) || ((is_null($dados['Orcamento_Numero'])) || (empty($dados['Orcamento_Numero'])))) {
      return null;
    } else {
      return $dados;
    }
  }

  function montarLayout($dados) {
    $layout = '';

    if (is_null($dados)) {
      return '';
    }

    $layout .= "<!DOCTYPE html>";
    $layout .= "<html lang='pt-br'>";
    $layout .=   "<head>";
    $layout .=     "<title>Orçamento Polux</title>";
    $layout .=     "<meta charset='utf-8'>";
    $layout .=      $this->montarCSS();
    $layout .=   "</head>";
    $layout .=   "<body>";
    $layout .=     $this->montarCabecalho($dados);
    $layout .=     "<table>";
    $layout .=       "<thead>";
    $layout .=         "<tr><td>";
    $layout .=           "<div class='page-header-space'></div></td></tr>";
    $layout .=         "</td></tr>";
    $layout .=       "</thead>";
    $layout .=       "<tbody>";
    $layout .=         "<tr><td>";
    $layout .=          $this->montarTitulo($dados);
    $layout .=          $this->montarItemHeader();
    $layout .=          $this->montarItemDetail($dados);
    $layout .=          $this->montarTotalizador($dados);
    $layout .=          $this->montarTermo($dados);
    $layout .=         "</td></tr>";
    $layout .=       "</tbody>";
    $layout .=     "</table>";
    $layout .=   "</body>";
    $layout .= "</html>";

    header('Content-Type: html');

    return $layout;
  }

  function montarCSS() {
    $css  = "<style>";
    $css .=   ".grid-container { ";
    $css .=     "display: grid; ";
    $css .=     "gap: 0px 1px; ";
    $css .=   "}";
    $css .=   ".data-container { ";
    $css .=     "margin: 5px 15px 10px 15px; ";
    $css .=   "}";
    $css .=   ".page { ";
    $css .=     "font-family: Helvetica, Verdana, Arial, Sans-Serif;";
    $css .=     "min-width: 850px; ";
    $css .=     "max-width: 850px; ";
    $css .=     "page-break-inside: avoid;";
    $css .=   "}";
    $css .=   ".logo { ";
    $css .=     "padding: 20px;";
    $css .=     "width: 80%;";
    $css .=   "}";
    $css .=   ".logo-cert { ";
    $css .=     "padding: 10px;";
    $css .=     "display: block;";
    $css .=     "margin-left: auto;";
    $css .=     "margin-right: auto;";
    $css .=   "}";
    $css .=   ".item-header { ";
    $css .=     "text-transform: uppercase;";
    $css .=     "text-align: center;";
    $css .=     "background-color: grey;";
    $css .=     "color: white;";
    $css .=     "padding: 1px;";
    $css .=   "}";
    $css .=   ".text-title { ";
    $css .=     "font-size: 12px;";
    $css .=     "font-weight: bold;";
    $css .=   "}";
    $css .=   ".text-description { ";
    $css .=     "font-size: 10px;";
    $css .=     "font-weight: bold;";
    $css .=   "}";
    $css .=   ".text-content { ";
    $css .=     "font-size: 10px;";
    $css .=     "font-weight: normal;";
    $css .=   "}";
    $css .=   ".text-total { ";
    $css .=     "font-size: 13px;";
    $css .=     "font-weight: bold;";
    $css .=     "line-height: 30px;";
    $css .=   "}";
    $css .=   ".line-text { ";
    $css .=     "line-height: 11px;";
    $css .=   "}";
    $css .=   ".item-data { ";
    $css .=     "background-color: lightgrey;";
    $css .=     "padding: 1px;";
    $css .=     "color: black;";
    $css .=   "}";
    $css .=   ".item-lowercase { ";
    $css .=     "text-transform: lowercase;";
    $css .=   "}";
    $css .=   ".item-text { ";
    $css .=     "text-align: justify;";
    $css .=   "}";
    $css .=   ".container-text { ";
    $css .=     "margin-bottom: 10px;";
    $css .=   "}";
    $css .=   ".item-detail { ";
    $css .=     "margin-left: 95px;";
    $css .=     "line-height: 10px;";
    $css .=   "}";
    $css .=   ".page-break { ";
    $css .=     "page-break-after: always;";
    $css .=   "}";
    $css .=   ".signature { ";
    $css .=     "margin-top: 80px;";
    $css .=     "margin-bottom: 10px;";
    $css .=     "border-style: solid;";
    $css .=     "border-width: 0px 0px 1px 0px;";
    $css .=   "}";

    $css .=   ".page-header, .page-header-space {";
    $css .=   "  height: 110px;";
    $css .=   "}";
    $css .=   ".page-header {";
    $css .=   "  position: fixed;";
    $css .=   "  top: 0mm;";
    $css .=   "  width: 100%;";
    $css .=   "}";
    $css .=   "@media print {";
    $css .=   "  thead {display: table-header-group;} ";
    $css .=   "  body {margin: 0;}";
    $css .=   "}";


    $css .= "</style>";

    return $css;
  }

  function montarCabecalho($dados) {
    $cabecalho  = "<div class='page page-header'>";
    $cabecalho .=   "<div class='grid-container' style='grid-template-columns: 250px auto 250px; margin-bottom: 5px;'>";
    $cabecalho .=     "<div>";
    $cabecalho .=       "<img class='logo' src='{$dados['Empresa_Logo']}'>";
    $cabecalho .=     "</div>";
    $cabecalho .=     "<div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>{$dados['Empresa_Nome']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-content'>{$dados['Empresa_Endereco_Rua']}, {$dados['Empresa_Endereco_Numero']} - {$dados['Empresa_Endereco_Bairro']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-content'>{$dados['Empresa_Endereco_Cidade']} - {$dados['Empresa_Endereco_Estado']} - CEP: {$dados['Empresa_Endereco_CEP']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>CNPJ: </span><span class='text-content'>{$dados['Empresa_CNPJ']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>Inscrição Estudal: </span><span class='text-content'>{$dados['Empresa_IE']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>Telefone: </span><span class='text-content'>{$dados['Empresa_Telefone']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>E-Mail: </span><span class='text-content item-lowercase'>{$dados['Empresa_Email']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>Site: </span><span class='text-content item-lowercase'>{$dados['Empresa_Site']}</span></div>";
    $cabecalho .=     "</div>";
    $cabecalho .=     "<div>";
    $cabecalho .=       "<div><span style='font-size: 18px; font-weight: bold; line-height: 15px;'>ORÇAMENTO N° {$dados['Orcamento_Numero']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>Emissão: </span><span class='text-content'>{$dados['Orcamento_Emissao']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description' style='color: red;'>Validade: </span><span class='text-content' style='color: red;'>{$dados['Orcamento_Validade']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span>&nbsp;</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>Vendedor: </span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-content'>{$dados['Vendedor_Nome']}, {$dados['Vendedor_Telefone']}</span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-description'>Representante: </span></div>";
    $cabecalho .=       "<div class='line-text'><span class='text-content'>{$dados['Representante_Nome']}, {$dados['Representante_Telefone']}</span></div>";
    $cabecalho .=     "</div>";
    $cabecalho .=   "</div>";
    $cabecalho .= "</div>";

    return $cabecalho;
  }

  function montarTitulo($dados) {
    $titulo  = "<div class='page'>";
    $titulo .=   "<div class='item-header text-title'>";
    $titulo .=     "<span class='text-content'>Cliente</span>";
    $titulo .=   "</div>";
    $titulo .=   "<div class='grid-container data-container' style='grid-template-columns: auto 400px;'>";
    $titulo .=     "<div>";
    $titulo .=       "<div class='line-text'><span class='text-description'>{$dados['Cliente_Nome']}</span></div>";
    $titulo .=       "<div class='line-text'><span class='text-description'>CNPJ/CPF: </span><span class='text-content'>{$dados['Cliente_CNPJ']}</span></div>";
    $titulo .=       "<div class='line-text'><span class='text-description'>Inscrição Estadual: </span><span class='text-content'>{$dados['Cliente_IE']}</span></div>";
    $titulo .=     "</div>";
    $titulo .=     "<div>";
    $titulo .=       "<div class='line-text'><span class='text-description'>Contato: </span><span class='text-content'>{$dados['Contato_Nome']}</span></div>";
    $titulo .=       "<div class='line-text'><span class='text-description'>Telefone: </span><span class='text-content'>{$dados['Contato_Telefone']}</span></div>";
    $titulo .=       "<div class='line-text'><span class='text-description'>E-mail: </span><span class='text-content item-lowercase'>{$dados['Contato_Email']}</span></div>";
    $titulo .=     "</div>";
    $titulo .=   "</div>";
    $titulo .=   "<div class='grid-container data-container'>";
    $titulo .=     "<div class='line-text'><span class='text-description'>Prezado(a) {$dados['Contato_Nome']},</span></div>";
    $titulo .=     "<div class='line-text'><span class='text-description'>Agradecemos vossa consulta e temos a satisfação de apresentar nossa proposta para o fornecimento dos produtos abaixo relacionados, conforme segue:</span></div>";
    $titulo .=   "</div>";
    $titulo .= "</div>";

    return $titulo;
  }

  function montarItemHeader() {
    $data = array('Classe_CSS'     => 'item-header text-content',
                  'Item'           => 'It',
                  'Codigo'         => 'Código',
                  'Descricao'      => 'Descrição do Produto',
                  'NCM'            => 'NCM',
                  'Valor_Unitario' => 'Unitário (R$)',
                  'Quantidade'     => 'Qtde',
                  'Valor_ICMS'     => '% ICMS',
                  'Valor_IPI'      => '% IPI',
                  'Valor_Total'    => 'Total (R$)',
                  'Data_Entrega'   => 'Entrega'
                  );

    $itemHeader = $this->montarItemGrid($data);

    return $itemHeader;
  }

  function montarItemDetail($dados) {
    $items = "";
    foreach ($dados['Itens'] as $item) {
      $items .= "<div class='page' style='margin-bottom: 5px;'>";
      $data = array('Classe_CSS'     => 'item-data text-content',
                    'Item'           => "<div style='text-align: center;'>{$item['Sequencia']}</div>",
                    'Codigo'         => "<div style='text-align: center;'>{$item['Codigo']}</div>",
                    'Descricao'      => "<div style='font-weight: bold;'>{$item['Descricao']}</div>",
                    'NCM'            => "<div style='text-align: center;'>{$item['NCM']}</div>",
                    'Valor_Unitario' => "<div style='text-align: right;'>{$item['Valor_Unitario']}</div>",
                    'Quantidade'     => "<div style='text-align: center;'>{$item['Quantidade']}</div>",
                    'Valor_ICMS'     => "<div style='text-align: center;'>{$item['Aliquota_ICMS']}</div>",
                    'Valor_IPI'      => "<div style='text-align: center;'>{$item['Aliquota_IPI']}</div>",
                    'Valor_Total'    => "<div style='text-align: right;'>{$item['Valor_Total']}</div>",
                    'Data_Entrega'   => "<div style='text-align: center;'>{$item['Data_Entrega']}</div>",
                    );

      $items .= $this->montarItemGrid($data);

      if (!is_null($item['Codigo_Cliente']) && !empty($item['Codigo_Cliente'])) {
        $items .= "<div class='page'>";
        $items .=   "<div class='line-text item-detail'><span class='text-description'>Referência do cliente: </span><span class='text-content'>{$item['Codigo_Cliente']}</span></div>";
        $items .= "</div>";
      }
      if ($item['Caracteristicas'] > 0) {
        $items .= "<div class='page'>";
        $items .=   "<div class='line-text item-detail'><span class='text-description'>Descrição técnica: </span></div>";
        $items .= "</div>";

        foreach ($item['Caracteristicas'] as $item) {
          $items .= "<div class='page'>";
          $items .=   "<div class='line-text item-detail'><span class='text-content'>{$item['Chave']}: </span><span class='text-content'>{$item['Valor']}</span></div>";
          $items .= "</div>";
        }
      }

      if (!is_null($item['Observacao']) && !empty($item['Observacao'])) {
        $items .= "<div class='page'>";
        $items .=   "<div class='line-text item-detail'><span class='text-description'>Observação: </span><span class='text-content'>{$item['Observacao']}</span></div>";
        $items .= "</div>";
      }

      if (!is_null($item['Prazo_Entrega']) && !empty($item['Prazo_Entrega'])) {
        $items .= "<div class='page'>";
        $items .=   "<div class='line-text item-detail'><span class='text-description'>Prazo de fabricação: </span><span class='text-content'>{$item['Prazo_Entrega']} dias úteis.</span></div>";
        $items .= "</div>";
      }

      $items .= "</div>";
    }

    return $items;
  }

  function montarItemGrid($data) {
    $itemGrid  = "<div class='page'>";
    $itemGrid .=   "<div class='grid-container' style='grid-template-columns: 15px 70px auto 50px 75px 30px 45px 30px 75px 60px; column-gap: 2px;'>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Item']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Codigo']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Descricao']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['NCM']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Valor_Unitario']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Quantidade']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Valor_ICMS']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Valor_IPI']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Valor_Total']}</div>";
    $itemGrid .=     "<div class='{$data['Classe_CSS']}'>{$data['Data_Entrega']}</div>";
    $itemGrid .=   "</div>";
    $itemGrid .= "</div>";

    return $itemGrid;
  }

  function montarTotalizador($dados) {
    $totalizador  = "<div class='page' style='margin-top: 15px;'>";
    $totalizador .=   "<div class='grid-container' style='grid-template-columns: 420px auto 420px;'>";
    $totalizador .=     "<div>";
    $totalizador .=       "<div class='item-header text-title'>";
    $totalizador .=         "<span class='text-content'>Frete</span>";
    $totalizador .=       "</div>";
    $totalizador .=       "<div class='data-container'>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Frete: </span><span class='text-content'>{$dados['Entrega_Tipo_Frete']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Transportadora: </span><span class='text-content'>{$dados['Entrega_Transportadora']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Peso total: </span><span class='text-content'>{$dados['Entrega_Peso_Total']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Endereço de entrega: </span><span class='text-content'>{$dados['Entrega_Endereco']}</span></div>";
    $totalizador .=       "</div>";
    $totalizador .=       "<div class='item-header text-title'>";
    $totalizador .=         "<span class='text-content'>Pagamento</span>";
    $totalizador .=       "</div>";
    $totalizador .=       "<div class='data-container'>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Prazo de pagamento: </span><span class='text-content'>{$dados['Pagamento_Prazo']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Forma de pagamento: </span><span class='text-content'>{$dados['Pagamento_Forma']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-description'>Endereço de cobrança: </span><span class='text-content'>{$dados['Pagamento_Endereco']}</span></div>";
    $totalizador .=       "</div>";
    $totalizador .=     "</div>";

    $totalizador .=     "<div>";
    $totalizador .=     "</div>";
    
    $totalizador .=     "<div>";
    $totalizador .=       "<div class='item-header text-title'>";
    $totalizador .=         "<span class='text-content'>Tributação</span>";
    $totalizador .=       "</div>";
    $totalizador .=       "<div class='data-container'>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor total dos produtos: </span><span class='text-description'>R$ {$dados['Total_Produtos']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor total do IPI: </span><span class='text-description'>R$ {$dados['Total_IPI']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor do frete: </span><span class='text-description'>R$ {$dados['Total_Frete']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor do ST: </span><span class='text-description'>R$ {$dados['Total_ST']}</span></div>";
    $totalizador .=         "<div><span class='text-total'>Valor total da proposta: R$ {$dados['Total_Valor']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor do ICMS: </span><span class='text-description'>R$ {$dados['Total_ICMS']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor do PIS: </span><span class='text-description'>R$ {$dados['Total_PIS']}</span></div>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>Valor do COFINS: </span><span class='text-description'>R$ {$dados['Total_COFINS']}</span></div>";
    $totalizador .=       "</div>";
    $totalizador .=     "</div>";
    $totalizador .=   "</div>";
    $totalizador .=     "<div>";
    $totalizador .=       "<div class='item-header text-title'>";
    $totalizador .=         "<span class='text-content'>Observações adicionais da proposta</span>";
    $totalizador .=       "</div>";
    $totalizador .=       "<div class='data-container'>";
    $totalizador .=         "<div class='line-text'><span class='text-content'>{$dados['Orcamento_Observacao']}</span></div>";
    $totalizador .=       "</div>";
    $totalizador .=     "</div>";

    $totalizador .= "</div>";

    return $totalizador;
  }

  function montarTermo($dados) {
    $termo  = "<div class='page item-text'>";
    $termo .=   "<div>";
    $termo .=     "<div class='item-header text-title'>";
    $termo .=       "<span class='text-content'>Ensaios</span>";
    $termo .=     "</div>";
    $termo .=     "<div class='data-container'>";
    $termo .=       "<div class='line-text'><span class='text-description'>ROTINA: </span><span class='text-content'>Serão executados nos laboratórios da fábrica conforme Norma ABNT NBR 5356 -11, estando inclusos nos preços ofertados.</span></div>";
    $termo .=       "<div class='line-text'><span class='text-description'>TIPO e ESPECIAIS: </span><span class='text-content'>Quando solicitado, serão executados em nossa fábrica e cobrados por ensaio realizado. Eventuais prazos decorrentes da disponibilidade de laboratórios para a execução de ensaios de tipo deverão ser acrescidos no prazo final de entrega do equipamento.</span></div>";
    $termo .=     "</div>";
    $termo .=   "</div>";
    $termo .=   "<div>";
    $termo .=     "<div class='item-header text-title'>";
    $termo .=       "<span class='text-content'>Condições gerais de fornecimento</span>";
    $termo .=     "</div>";
    $termo .=     "<div class='grid-container' style='grid-template-columns: 420px auto 420px;'>";
    $termo .=     "<div class='data-container'>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>1. DA APLICABILIDADE E DA ACEITAÇÃO</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>As Condições Gerais de Vendas são consideradas como documento regente de todo e qualquer fornecimento de produtos, se aplicam tanto para entregas únicas quanto para o fornecimento contínuo. A confirmação do pedido dada pelo cliente por qualquer meio de comunicação representa a completa e irreversível aceitação das seguintes condições.</span></div>";
    $termo .=       "</div>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>2. DO PREÇO E CONDIÇÕES DE PAGAMENTO</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>2.1 Salvo condições de pagamento específicas acordadas na proposta, aplica-se a condição de pagamento à vista antecipado, as demais condições podem estar sujeitas a análise de crédito.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>2.2 Caso as partes pactuem pagamento diverso do mencionado na cláusula 2.1, o não pagamento até a data do vencimento conduz de imediato e sem aviso prévio a inadimplência por parte do cliente.</span></div>";
    $termo .=       "</div>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>3. DOS TRIBUTOS</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>3.1 O cliente fica responsável por encaminhar a declaração informando sua condição de contribuinte ou não do ICMS e sua inscrição estadual, bem como repassar informações relativas as particularidades tributárias das obrigações principais, como alíquotas diferenciadas, benefícios concedidos à sua atividade econômica, e das obrigações acessórias, definidas pela União e/ou estado do cliente ou destino da mercadoria quando for diversa.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>3.2 O cliente deve se atentar aos impostos mencionados na proposta técnica-comercial, verificando se estão em conformidade. Em caso de divergências, deve contatar o respectivo coordenador comercial da Pólux Transformadores imediatamente. </span></div>";
    $termo .=       "</div>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>4. DO PRAZO PARA FABRICAÇÃO</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>4.1 Salvo estipulação em contrário, o prazo de fabricação será de 40 dias úteis após a confirmação do pedido, exceto quando houver necessidade de confirmação de desenho, folhas de dados ou outro documento técnico que exija aprovação, em que o prazo começa a contar a partir da confirmação deste.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>4.2 Desconsidera-se o prazo acima, quando a fabricação não puder ser realizada devido a motivos de caso fortuito e/ou força maior, momento em que a Pólux Transformadores entrará imediatamente em contato com o cliente para informá-lo e estipularem prazo diverso.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>4.3 Quando a condição de pagamento for à vista antecipado, a fabricação inicia somente a partir do cumprimento integral das condições de pagamento.</span></div>";
    $termo .=       "</div>";
    $termo .=     "</div>";
    $termo .=     "<div>";
    $termo .=     "</div>";
    $termo .=     "<div class='data-container'>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>5. DO FRETE E DO RECEBIMENTO DOS PRODUTOS</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>5.1 Salvo estipulação em contrário pactuado entre as partes na proposta técnica-comercial, aplica-se a modalidade do frete em que o cliente arca e se responsabiliza pelos riscos e custos do transporte até a entrega da mercadoria ao destino final.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>5.2 No caso de entrega internacional, o cliente arca com as taxas de desembaraço alfandegário aplicáveis.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>5.3 Quando do recebimento dos produtos o cliente se compromete a realizar uma inspeção do material e havendo qualquer divergência, deve notificar imediatamente a empresa transportadora e a Pólux Transformadores antes de autorizar a descarga do material, caso não o faça entende-se que o produto chegou em perfeitas condições. </span></div>";
    $termo .=       "</div>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>6. DA GARANTIA</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>6.1 O período de garantia dos produtos Pólux Transformadores se dará pelo prazo de {$dados['Orcamento_Garantia']} meses, contados a partir da entrega do produto quando esta for de responsabilidade do fabricante ou contados a partir da retirada do produto nas dependências da Pólux Transformadores quando for de responsabilidade do cliente</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>6.2 Quando constatado algum defeito, o cliente deve fazer a comunicação por meio
    eletrônico através do endereço de e-mail especificado no orçamento e/ou proposta técnicacomercial e enviar a 1ª via da nota fiscal de compra e/ou outro documento que demonstre a data da aquisição e entrega do produto, bem como vídeo e foto do produto, momento em que será informado todo o procedimento para envio do produto com defeito para avaliação de garantia em fábrica.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>6.3 Demais cláusulas e condições sobre a garantia de fábrica e a estendida estão previstas no Termo de Garantia.</span></div>";
    $termo .=       "</div>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>7. DAS DISPOSIÇÕES GERAIS</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>7.1 A Pólux Transformadores é isenta da obrigação de atender qualquer solicitação de troca ou devolução de qualquer produto que tenha sido fabricado sob encomenda, visto que o produto é personalizado para as necessidades do cliente e este que envia as medições e características do equipamento. Nesse caso não se aplica o direito de arrependimento previsto pelo Código de Defesa do Consumidor.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>7.2 Para formalização, todas as comunicações estabelecidas deverão ser feitas por meio eletrônico através de e-mail para os endereços especificados no orçamento e/ou proposta técnica-comercial.</span></div>";
    $termo .=         "<div class='line-text'><span class='text-content'>7.3 As partes elegem o foro de Jaraguá do Sul/SC como competente para dirimir quaisquer litígios advindos do presente contrato, renunciando expressamente a qualquer outro, por mais benéfico que seja.</span></div>";
    $termo .=       "</div>";
    $termo .=     "</div>";
    $termo .=   "</div>";

    $termo .=   "<div>";
    $termo .=     "<div class='item-header text-title'>";
    $termo .=       "<span class='text-content'>CERTIFICAÇÕES</span>";
    $termo .=     "</div>";
    $termo .=     "<div class='grid-container data-container' style='grid-template-columns: 25% 25% 25% 25%;'>";
    $termo .=       "<div><img class='logo-cert' style='width: 70%;' src='{$dados['Empresa_Logo_ISO9001']}'></div>";
    $termo .=       "<div><img class='logo-cert' style='width: 70%;' src='{$dados['Empresa_Logo_ISO14001']}'></div>";
    $termo .=       "<div><img class='logo-cert' style='width: 40%;' src='{$dados['Empresa_Logo_CE']}'></div>";
    $termo .=       "<div><img class='logo-cert' style='width: 60%;' src='{$dados['Empresa_Logo_ROHS']}'></div>";
    $termo .=       "<div></div>";
    $termo .=     "</div>";
    $termo .=   "</div>";

    $termo .=   "<div>";
    $termo .=     "<div class='data-container'>";
    $termo .=       "<div class='container-text'>";
    $termo .=         "<div class='line-text'><span class='text-description'>Certos de que nossa proposta irá atendê-los,</span></div>";
    $termo .=       "</div>";
    $termo .=     "</div>";
    $termo .=     "<div class='grid-container data-container' style='grid-template-columns: 390px auto 390px;'>";
    $termo .=       "<div>";
    $termo .=         "<div class='signature'></div>";
    $termo .=         "<div class='line-text' style='text-align: center;'><span class='text-description'>{$dados['Vendedor_Nome']}</span></div>";
    $termo .=         "<div class='line-text' style='text-align: center;'><span class='text-description'>{$dados['Empresa_Nome']}</span></div>";
    $termo .=       "</div>";
    $termo .=       "<div>";
    $termo .=       "</div>";
    $termo .=       "<div>";
    $termo .=         "<div class='signature'></div>";
    $termo .=         "<div class='line-text' style='text-align: center;'><span class='text-description'>{$dados['Contato_Nome']}</span></div>";
    $termo .=         "<div class='line-text' style='text-align: center;'><span class='text-description'>{$dados['Cliente_Empresa']}</span></div>";
    $termo .=       "</div>";
    $termo .=     "</div>";
    $termo .=   "</div>";
    $termo .=   "<div style='min-height: 200px;'>";
    $termo .=   "</div>";

    $termo .= "</div>";

    return $termo;
  }

  function enderecoEntrega($dados) {
    if ($dados['T003_D148_Id_Entrega'] > 0) {
      return $dados['Entrega_Rua'] . ', ' . $dados['Entrega_Numero'] . ' - ' . $dados['Entrega_Bairro'] . ' - CEP: ' . $dados['Entrega_Cep'];
    } else {
      return $dados['D024_Endereco'] . ', ' . $dados['D024_Numero'] . ' - ' . $dados['D024_Bairro'] . ' - CEP: ' . $dados['D024_Cep'];
    }
  }

  function enderecoPagamento($dados) {
    if ($dados['T003_D148_Id_Cobranca'] > 0) {
      return $dados['Cobranca_Rua'] . ', ' . $dados['Cobranca_Numero'] . ' - ' . $dados['Cobranca_Bairro'] . ' - CEP: ' . $dados['Cobranca_Cep'];
    } else {
      return $dados['D024_Endereco'] . ', ' . $dados['D024_Numero'] . ' - ' . $dados['D024_Bairro'] . ' - CEP: ' . $dados['D024_Cep'];
    }
  }

  function prazoPagamento($T003_Id) {
    $prazo = '';

    $sqlPrazo  = " SELECT T089_Prazos_Dias ";
    $sqlPrazo .=   " FROM T089 ";
    $sqlPrazo .=  " WHERE T089_T003_Id = {$T003_Id} ";
  
    $queryPrazo = mysql_query($sqlPrazo);
    while ($dadosPrazo = mysql_fetch_assoc($queryPrazo)) {
      if (!empty($prazo)) {
        $prazo .= ' / ';
      }

      $prazo .= $dadosPrazo['T089_Prazos_Dias'];
    }

    if (!empty($prazo)) {
      $prazo .= ' DIAS';
    }

    return $prazo;
  }

  function tipoFrete($flagFrete) {
    switch ($flagFrete) {
      case '0':
        return 'Contratação por conta do Remetente';
      case '1':
        return 'Contratação por conta do Destinatário';
      case '2':
        return 'Contratação por conta de Terceiros';
      case '3':
        return 'Próprio por conta do Remetente';
      case '4':
        return 'Próprio por conta do Destinatário';
      case '9':
        return 'Sem frete';
      default:
        return '';
    }
  }

  function formatarValor($valor) {
    if ((!$valor) || (empty($valor))) {
      return '';
    } else {
      return number_format($valor, 2, ',', '.');
    }
  }

  function formatarData($data) {
    if ((!$data) || (empty($data)) || ($data == '0000-00-00')) {
      return '';
    } else {
      $dateTime = new \DateTime($data);
      return $dateTime->format('d/m/Y');
    }
  }
}