<?php
namespace hardness;

/* INTEGRAÇÃO WMS
 * 
 * Documentação: https://integracao.cyberlog.net/envio_erp_wms/
 * 
 * Endpoint da API
 * http://<endereco>.ip.odhserver.com:9090/cyberweb/api PRODUÇÃO
 * http://<endereco>.ip.odhserver.com:9393/cyberweb/api HOMOLOGAÇÃO
 * 
 * IMPORTANTE! Utilizar tabela W001 para log de retorno da API do Cyberlog.
 * 
 * Autor: João Vitor
 * Telefone: (47) 99640-8337
 * GitHub: https://github.com/vittorjoao
 * Data: 26/02/2025 - (em desenvolvimento)
 */

definirClasse('WMS');
class WMS_ {
  private $url;
  private $account;
  private $password;
  private $module;
  private $depositNumber;
  private $address;
  private $key;
  private $isEnabled;
  private $errorMessage;

  // Options
  private $convertUnit;
  private $lockOrder;
  private $warehouseUsers;
  private $orderDateAsDeliveryDate;

  public function __construct() {
    global $g;

    $this->url = $g['C031']['cyberlogUrl'];
    $this->account = $g['C031']['cyberlogAccount'];
    $this->password = $g['C031']['cyberlogPassword'];
    $this->module = $g['C031']['cyberlogModule'];
    $this->depositNumber = $g['C031']['cyberlogDepositNumber'];
    $this->address = $g['C031']['cyberlogAddress'];
    $this->key = $g['C031']['cyberlogKey'];
    $this->convertUnit = $g['C031']['cyberlogConvertUnit'];
    $this->lockOrder = $g['C031']['cyberlogLockOrder'];
    $this->warehouseUsers = explode(',', trim($g['C031']['cyberlogWarehouseUsers']));
    $this->orderDateAsDeliveryDate = $g['C031']['cyberlogOrderDateAsDeliveryDate'];

    $requiredParams = [
      'cyberlogAccount',
      'cyberlogPassword',
      'cyberlogKey',
      'cyberlogUrl',
      'cyberlogDepositNumber',
      'cyberlogAddress',
      'cyberlogModule'
    ];
    
    $this->isEnabled = true;
    foreach ($requiredParams as $param) {
      if (empty($g['C031'][$param])) {
        $this->isEnabled = false;
        break;
      }
    }
  }

  public function isEnabled() {
    return $this->isEnabled;
  }

  public function isWarehouseUser() {
    global $g;

    if(!empty($this->warehouseUsers)) {
      return in_array($g['usuarioAtual'], $this->warehouseUsers);
    }

    return false;
  }

  public function getToken() {
    try {
      $headers = array(
        "Content-Type: application/x-www-form-urlencoded",
        "chave: " . $this->key
      );

      $body = http_build_query(array(
        "modulo" => $this->module,
        "conta" => $this->account,
        "senha" => $this->password,
        "numeroDeposito" => $this->depositNumber
      ));

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "{$this->url}/autenticador/login");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);

      if ($httpCode != 200) {
        $this->errorMessage = "[getToken] Erro HTTP: $httpCode - $response";
        return false;
      }

      $responseData = json_decode($response, true);

      if (!isset($responseData['token'])) {
        $this->errorMessage = "[getToken] Token não encontrado na resposta";
        return false;
      }

      return $responseData['token'];
    } catch (\Exception $e) {
      $this->errorMessage = "[getToken] Erro ao obter token: " . $e->getMessage();
      return false;
    }
  }

  public function getErrorMessage() {
    return $this->errorMessage;
  }

  /**
   * @param response = resposta da requisição
   * @return boolean true | false
   */
  private function proccessResponse($response) {
    try {
      $responseData = json_decode($response, true);
      
      if (!isset($responseData['CyberWeb'])) {
        $this->errorMessage = '[proccessResponse] Resposta inválida da API';
        throw new \Exception($this->errorMessage);
      }

      $statusMessage = $responseData['CyberWeb'];
      
      if (strpos($statusMessage, '!') === 0) {
        $this->errorMessage = "[proccessResponse] Erro na requisição: $statusMessage";
        throw new \Exception($this->errorMessage);
      }

      return true;
    } catch (\Exception $e) {
      $this->errorMessage = "[proccessResponse] Erro ao processar resposta: " . $e->getMessage();
      throw $e;
    }
  }

  /**
   * Converte a quantidade baseada na unidade do produto
   * @param quantidade = quantidade do produto
   * @param unidade = unidade do produto
   * @return float quantidade convertida
   */
  private function convertQuantity($quantidade, $unidade = 'PC') {
    switch ($unidade) {
      case 'CT':
        return $quantidade * 100;
      case 'MIL':
        return $quantidade * 1000;
      default:
        return $quantidade;
    }
  }

  /**
   * @param token = token de acesso, função getToken()
   * @param tipo = cadastro | processo
   * @param servico = integrarPessoa | integrarProduto | integrarPedido | integrarRecebimento | ...
   * @param nomeServico = pessoa | produto | pedido | recebimento | ...
   * @param parametros = json
   * @return boolean true | false
   */
  public function post($token, $tipo, $servico, $nomeServico, $parametros) {
    if(empty($token)) {
      $this->errorMessage = "[post] Token não informado";
      throw new \Exception($this->errorMessage);
    }

    $headers = array(
      "Content-Type: application/x-www-form-urlencoded",
      "chave: " . $this->key,
      "token: " . $token
    );

    $body = $nomeServico . '=' . urlencode(json_encode($parametros));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$this->url}/{$tipo}/{$servico}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $responseData = json_decode($response, true);

    if($httpCode != 200) {
      $parts = explode(' - ', $responseData['CyberWeb']);
      $mensagens = [];

      foreach ($parts as $part) {
        if (preg_match('/^!\d+/', trim($part))) continue;
        $mensagens[] = trim($part);
      }

      $this->errorMessage = implode('<br>', $mensagens);
      throw new \Exception($this->errorMessage);
    }

    if (!$this->proccessResponse($response)) {
      throw new \Exception($this->errorMessage);
    }

    return true;
  }

  /**
   * @param operacao = INSERT | UPDATE | DELETE
   * @param T013_Id = ID da nota de recebimento
   * @param avaria = true | false
   * @param confCega = true | false
   * @param tipo = 'G' garantia | 'D' devolução | 'C' nota cancelada | 'O' ordem de produção | 'N' normal | 'A' abastecimento matriz
   * @return boolean true | false
   */
  public function integrarRecebimento($operacao, $T013_Id, $avaria = false, $confCega = true, $tipo = 'N') {
    if(empty($T013_Id)) {
      $this->errorMessage = "[integrarRecebimento] ID da nota não informado";
      return false;
    }
    
    try {
      $cT013 = mysql_query("
        SELECT
          T013_Id AS erpId,
          T013_Numero_Nota_Fiscal AS documento,
          T013_D024_Id AS erpIdPessoa,
          T013_C004_Id AS empresa
        FROM T013
        WHERE T013_Id = '{$T013_Id}' AND (T013_Tipo = '1' OR T013_Tipo = '2')
      ");

      if(!$cT013) {
        $this->errorMessage = "[integrarRecebimento] Erro ao consultar nota $T013_Id:<br> " . mysql_error();
        return false;
      }
      
      $dT013 = mysql_fetch_assoc($cT013);

      if(!$dT013) {
        $this->errorMessage = "[integrarRecebimento] Nota não encontrada, ou tipo de documento não confere com NF-e: $T013_Id";
        return false;
      }

      $integrarPessoa = $this->integrarPessoa('INSERT', $dT013['erpIdPessoa'], 'F');

      if(!$integrarPessoa) {
        $this->errorMessage = "[integrarRecebimento] Erro ao integrar pessoa:<br> " . $this->getErrorMessage();
        return false;
      }

      $parametros = array(
        'operacao' => $operacao,
        'erpId' => $dT013['erpId'],
        'empresa' => empty($dT013['empresa']) ? 1 : $dT013['empresa'],
        'data' => date('M d, Y H:i:s A'),
        'documento' => $dT013['documento'],
        'codFornecedor' => $dT013['erpIdPessoa'],
        'avaria' => $avaria,
        'confCega' => $confCega,
        'tipo' => $tipo,
        'doca' => 0,
        'prioridade' => 0,
        'devolucao' => false,
        'informarlote' => false,
        'impressora' => '',
      );

      $itensRecebimento = array();

      $cT014 = mysql_query("
        SELECT
          D001_Codigo_Produto AS codigoReduzido,
          T014_Quantidade AS quantidade,
          D001_Id AS erpId,
          D037_Unidade AS unidade
        FROM T014
          LEFT JOIN D009 ON D009_Id=T014_D009_Id
          LEFT JOIN D049 ON D049_Id=D009_D049_Id
          LEFT JOIN D001 ON D001_Id=D049_D001_Id
          LEFT JOIN D037 ON D037_Id=D001_D037_Id
        WHERE T014_T013_Id = '{$T013_Id}'
      ");

      if(!$cT014) {
        $this->errorMessage = "[integrarRecebimento] Erro ao consultar itens da nota $T013_Id:<br> " . mysql_error();
        return false;
      }

      while($dT014 = mysql_fetch_assoc($cT014)) {
        $quantidade = $this->convertUnit == 'S' ? $this->convertQuantity($dT014['quantidade'], $dT014['unidade']) : $dT014['quantidade'];
        
        $itensRecebimento[] = array(
          'codigoReduzido' => $dT014['codigoReduzido'],
          'erpId' => $dT014['erpId'],
          'quantidade' => $quantidade,
        );
      }

      $parametros['itensRecebimento'] = $itensRecebimento;

      return $this->post($this->getToken(), 'processo', 'integrarRecebimento', 'recebimento', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[integrarRecebimento] Erro na requisição de recebimento:<br> " . $e->getMessage();
      return false;
    }
  }

  /**
   * @param T013_Id = ID da nota de recebimento
   * @return boolean true | false
   */
  public function cancelarRecebimento($T013_Id) {
    if(empty($T013_Id)) {
      $this->errorMessage = "[cancelarRecebimento] ID da nota não informado";
      return false;
    }

    try {
      $cT013 = mysql_query("SELECT T013_Id AS erpId, T013_C004_Id AS empresa FROM T013 WHERE T013_Id = '{$T013_Id}'");

      if(!$cT013) {
        $this->errorMessage = "[cancelarRecebimento] Erro ao consultar nota $T013_Id:<br> " . mysql_error();
        return false;
      }
      
      $dT013 = mysql_fetch_assoc($cT013);

      if(!$dT013) {
        $this->errorMessage = "[cancelarRecebimento] Nota não encontrada: $T013_Id";
        return false;
      }

      $parametros = array(
        'operacao' => 'UPDATE',
        'erpId' => $dT013['erpId'],
        'empresa' => $dT013['empresa'],
        'tipo' => 'C',
      );
      
      return $this->post($this->getToken(), 'processo', 'integrarRecebimento', 'recebimento', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[cancelarRecebimento] Erro na requisição de cancelamento de recebimento:<br> " . $e->getMessage();
      return false;
    }
  }

    /**
   * @param operacao = INSERT | UPDATE | DELETE
   * @param T007_Id = ID da nota de recebimento origem emitida
   * @param avaria = true | false
   * @param confCega = true | false
   * @param tipo = 'G' garantia | 'D' devolução | 'C' nota cancelada | 'O' ordem de produção | 'N' normal | 'A' abastecimento matriz
   * @return boolean true | false
   */
  public function integrarRecebimentoOrigemEmitida($operacao, $T007_Id, $avaria = false, $confCega = true, $tipo = 'D') {
    if(empty($T007_Id)) {
      $this->errorMessage = "[integrarRecebimentoOrigemEmitida] ID da nota de recebimento origem emitida não informado";
      return false;
    }
    
    try {
      $cT007 = mysql_query("
        SELECT
          T007_Id AS erpId,
          T007_Numero_Nota_Fiscal AS documento,
          T007_D024_Id AS erpIdPessoa,
          T007_C004_Id AS empresa
        FROM T007
        WHERE T007_Id = '{$T007_Id}' AND T007_Flag_ACP = '6'
      ");

      if(!$cT007) {
        $this->errorMessage = "[integrarRecebimentoOrigemEmitida] Erro ao consultar nota $T007_Id:<br> " . mysql_error();
        return false;
      }

      $dT007 = mysql_fetch_assoc($cT007);
      
      if(!$dT007) {
        $this->errorMessage = "[integrarRecebimentoOrigemEmitida] Nota não encontrada: $T007_Id";
        return false;
      }

      $integrarPessoa = $this->integrarPessoa('INSERT', $dT007['erpIdPessoa'], 'C');

      if(!$integrarPessoa) {
        $this->errorMessage = "[integrarRecebimentoOrigemEmitida] Erro ao integrar pessoa:<br> " . $this->getErrorMessage();
        return false;
      }
      
      $parametros = array(
        'operacao' => $operacao,
        'erpId' => $dT007['erpId'],
        'empresa' => empty($dT007['empresa']) ? 1 : $dT007['empresa'],
        'data' => date('M d, Y H:i:s A'),
        'documento' => $dT007['documento'],
        'codFornecedor' => $dT007['erpIdPessoa'],
        'avaria' => $avaria,
        'confCega' => $confCega,
        'tipo' => $tipo,
        'doca' => 0,
        'prioridade' => 0,
        'devolucao' => false,
        'informarlote' => false,
        'impressora' => '',
      );

      $itensRecebimento = array();

      $cT008 = mysql_query("
        SELECT
          D001_Codigo_Produto AS codigoReduzido,
          T008_Quantidade AS quantidade,
          D001_Id AS erpId,
          D037_Unidade AS unidade
        FROM T008
          LEFT JOIN D009 ON D009_Id=T008_D009_Id
          LEFT JOIN D049 ON D049_Id=D009_D049_Id
          LEFT JOIN D001 ON D001_Id=D049_D001_Id
          LEFT JOIN D037 ON D037_Id=D001_D037_Id
        WHERE T008_T007_Id = '{$T007_Id}'
      ");

      if(!$cT008) {
        $this->errorMessage = "[integrarRecebimentoOrigemEmitida] Erro ao consultar itens da nota $T007_Id:<br> " . mysql_error();
        return false;
      }

      while($dT008 = mysql_fetch_assoc($cT008)) {
        $quantidade = $this->convertUnit == 'S' ? $this->convertQuantity($dT008['quantidade'], $dT008['unidade']) : $dT008['quantidade'];
        
        $itensRecebimento[] = array(
          'codigoReduzido' => $dT008['codigoReduzido'],
          'erpId' => $dT008['erpId'],
          'quantidade' => $quantidade,
        );
      }

      $parametros['itensRecebimento'] = $itensRecebimento;

      return $this->post($this->getToken(), 'processo', 'integrarRecebimento', 'recebimento', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[integrarRecebimentoOrigemEmitida] Erro na requisição de recebimento de origem emitida:<br> " . $e->getMessage();
      return false;
    }
  }

  /**
   * @param T007_Id = ID da nota de recebimento origem emitida
   * @return boolean true | false
   */
  public function cancelarRecebimentoOrigemEmitida($T007_Id) {
    if(empty($T007_Id)) {
      $this->errorMessage = "[cancelarRecebimentoOrigemEmitida] ID da nota não informado";
      return false;
    }

    try {
      $cT007 = mysql_query("SELECT T007_Id AS erpId, T007_C004_Id AS empresa FROM T007 WHERE T007_Id = '{$T007_Id}'");

      if(!$cT007) {
        $this->errorMessage = "[cancelarRecebimentoOrigemEmitida] Erro ao consultar nota $T007_Id:<br> " . mysql_error();
        return false;
      }
      
      $dT007 = mysql_fetch_assoc($cT007);

      if(!$dT007) {
        $this->errorMessage = "[cancelarRecebimentoOrigemEmitida] Nota não encontrada: $T007_Id";
        return false;
      }

      $parametros = array(
        'operacao' => 'UPDATE',
        'erpId' => $dT007['erpId'],
        'empresa' => $dT007['empresa'],
        'tipo' => 'C',
      );

      return $this->post($this->getToken(), 'processo', 'integrarRecebimento', 'recebimento', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[cancelarRecebimentoOrigemEmitida] Erro na requisição de cancelamento de recebimento de origem emitida:<br> " . $e->getMessage();
      return false;
    }
  }

  /**
   * @param operacao = INSERT | UPDATE | DELETE
   * @param D024_Id = ID do cliente/fornecedor
   * @param tipo = 'C' cliente | 'F' fornecedor | 'A' cliente/fornecedor
   * @return boolean true | false
   */
  public function integrarPessoa($operacao, $D024_Id, $tipo = null) {
    if(empty($D024_Id)) {
      $this->errorMessage = "[integrarPessoa] ID da pessoa não informado";
      return false;
    }

    if(empty($tipo)) {
      $cT024 = mysql_query("SELECT D024_Flag_Cliente AS flagCliente, D024_Flag_Fornecedor AS flagFornecedor FROM D024 WHERE D024_Id = '{$D024_Id}'");
      $dT024 = mysql_fetch_assoc($cT024);

      if(!$dT024) {
        $this->errorMessage = "[integrarPessoa] Pessoa não encontrada: $D024_Id";
        return false;
      }

      $tipo = ($dT024['flagCliente'] == 'S' && $dT024['flagFornecedor'] == 'S') ? 'A' : ($dT024['flagCliente'] == 'S' ? 'C' : 'F');
    }

    if(!in_array($tipo, ['C', 'F', 'A'])) {
      $this->errorMessage = "[integrarPessoa] Tipo de pessoa inválido";
      return false;
    }
    
    try {
      $cT024 = mysql_query("
        SELECT
          D024_Id AS erpId,
          D024_Nome_Empresa AS nome,
          D024_Cnpj AS cnpj,
          D024_Cep AS cep,
          D018_UF AS uf,
          D020_Nome_Cidade AS cidade,
          D024_Endereco AS endereco,
          D024_Numero AS numero,
          CONCAT(D024_DDD_Telefone_1, D024_Telefone_1) AS telefone,
          D024_E_Mail AS email,
          D024_Bairro AS bairro
        FROM D024
          LEFT JOIN D018 ON D018_Id=D024_D018_Id
          LEFT JOIN D020 ON D020_Id=D024_D020_Id
        WHERE D024_Id = '{$D024_Id}'
      ");

      if(!$cT024) {
        $this->errorMessage = "[integrarPessoa] Erro ao consultar pessoa $D024_Id:<br> " . mysql_error();
        return false;
      }

      $dT024 = mysql_fetch_assoc($cT024);

      if(!$dT024) {
        $this->errorMessage = "[integrarPessoa] Pessoa não encontrada: $D024_Id";
        return false;
      }

      $endereco = empty($dT024['numero']) ? $dT024['endereco'] : $dT024['endereco'] . ', ' . $dT024['numero'];

      $parametros = array(
        'operacao' => $operacao,
        'erpId' => $dT024['erpId'],
        'nome' => substr($dT024['nome'], 0, 100),
        'pessoa' => $tipo,
        'telefone' => $dT024['telefone'],
        'cep' => $dT024['cep'],
        'cnpj' => $dT024['cnpj'],
        'email' => substr($dT024['email'], 0, 100),
        'uf' => $dT024['uf'],
        'nomeCidade' => $dT024['cidade'],
        'nomeBairro' => $dT024['bairro'],
        'endereco' => substr($endereco, 0, 100),
      );

      return $this->post($this->getToken(), 'cadastro', 'integrarPessoa', 'pessoa', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[integrarPessoa] Erro na requisição de pessoa:<br> " . $e->getMessage();      
      return false;
    }
  }

  /**
   * @param operacao = INSERT | UPDATE | DELETE
   * @param D001_Id = ID do produto
   * @param empresa  = ID da empresa
   * @return boolean true | false
   */
  public function integrarProduto($operacao, $D001_Id) {
    global $g;
    require_once("bibliotecas/classes/FX002.php");
    $FX002 = new FX002();
    
    if(empty($D001_Id)) {
      $this->errorMessage = "[integrarProduto] ID do produto não informado";
      return false;
    }

    $retornoDunCustomizado = $FX002->fatorDunCustomizado();
    $fatorDun = !empty($retornoDunCustomizado) ? $retornoDunCustomizado : 'D001A_Qtde_Embalagem_Master';

    try {
      $cD001 = mysql_query("
        SELECT
          D001_Id AS erpId,
          D001_Codigo_Produto AS codigoReduzido,
          CONCAT('9', D001_Codigo_Produto) AS codigoBarras,
          CONCAT('99', D001_Codigo_Produto) AS codigoDisplay,
          CONCAT('999', D001_Codigo_Produto) AS codigoDun,
          D001_Descricao_Produto AS nome,
          CAST(IFNULL(D001A_Altura, 0) * 1000 AS UNSIGNED) AS altura,
          CAST(IFNULL(D001A_Largura, 0) * 1000 AS UNSIGNED) AS largura,
          CAST(IFNULL(D001A_Comprimento, 0) * 1000 AS UNSIGNED) AS profundidade,
          D037_Unidade AS unidade,
          D001_Peso_Unitario_Kg AS pesoUnitario,
          {$fatorDun} AS fatorDun,
          D001_Qte_Embalagem AS fatorDisplay,
          D001_Peso_Unitario_Kg AS pesoDisplay,
          D082_Marca AS marca,
          D009_C004_Id AS empresa
        FROM D001
          LEFT JOIN D037 ON D037_Id=D001_D037_Id
          LEFT JOIN D001A ON D001A_D001_Id=D001_Id
          LEFT JOIN D049 ON D049_D001_Id=D001_Id
          LEFT JOIN D082 ON D049_D082_Id=D082_Id
          LEFT JOIN D009 on D009_D049_Id=D049_Id
        WHERE D001_Id = '{$D001_Id}' AND D009_C004_Id = '{$g['empresaAtual']}' GROUP BY D009_Id
      ");

      if(!$cD001) {
        $this->errorMessage = "[integrarProduto] Erro ao consultar produto $D001_Id:<br> " . mysql_error();
        return false;
      }

      $dD001 = mysql_fetch_assoc($cD001);

      if(!$dD001) {
        $this->errorMessage = "[integrarProduto] Produto não encontrado: $D001_Id";
        return false;
      }

      // IMPORTANTE: Caso a opção gravarCodProdAoCadastrar não esteja ativa, o código reduzido tera de ser informado manualmente durante cadastro do produto
      if($g['C031']['gravarCodProdAoCadastrar'] == 'S') {
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $dD001['codigoReduzido'] = $CAD002->obterNovoCodigoProduto($D001_Id);
      }

      $parametros = array(
        'erpId' => $dD001['erpId'],
        'operacao' => $operacao,
        'empresa' => $dD001['empresa'],
        'codigoReduzido' => $dD001['codigoReduzido'],
        'codigoBarras' => $dD001['codigoBarras'],
        'nome' => $dD001['nome'],
        'unome' => $dD001['nome'],
        'unidade' => $this->convertUnit == 'S' ? 'PC' : $dD001['unidade'],
        'pesoUnitario' => $dD001['pesoUnitario'],
        'codigoDun' => $dD001['codigoDun'],
        'fatorDun' => $this->convertUnit == 'S' ? $this->convertQuantity($dD001['fatorDun'], $dD001['unidade']) : $dD001['fatorDun'],
        'unidadeDun' => 'CX',
        'codigoDisplay' => $dD001['codigoDisplay'],
        'fatorDisplay' => $this->convertUnit == 'S' ? $this->convertQuantity($dD001['fatorDisplay'], $dD001['unidade']) : $dD001['fatorDisplay'],
        'pesoDisplay' => $dD001['pesoDisplay'],
        'unidadeDisplay' => 'DP',
        'altura' => $dD001['altura'],
        'largura' => $dD001['largura'],
        'profundidade' => $dD001['profundidade'],
        'marca' => $dD001['marca'],
        'item' => true,
        'separacaoMinimo' => 'P',
        'semPickingFixo' => false,
        'empilhamento' => 0,
      );
      
      return $this->post($this->getToken(), 'cadastro', 'integrarProduto', 'produto', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[integrarProduto] Erro na requisição de produto:<br> " . $e->getMessage();      
      return false;
    }
  }

  /**
   * @param operacao = INSERT | UPDATE | DELETE
   * @param T005_Id = ID do pedido
   * @return boolean true | false
   */
  public function integrarPedido($operacao, $T005_Id) {
    if(empty($T005_Id)) {
      $this->errorMessage = "[integrarPedido] ID do pedido não informado";
      return false;
    }
    
    // IMPORTANTE: O campo de observação deve ser sempre o campo de observação voltado ao almoxarifado/expedição
    try {
      $cT005 = mysql_query("
        SELECT
          T005_Id AS erpId,
          T005_C004_Id AS empresa,
          T005_Valor_Total AS valor,
          T005_Data_Emissao AS data,
          D024_Id AS clienteErpId,
          D024_Nome_Empresa AS nomeCliente,
          T005_Data_Entrega AS dataEntrega,
          T005_Flag_Frete AS flagFrete,
          D022_Id AS transportadoraErpId,
          D022_Nome_Empresa AS nomeTransportadora,
          D022A_Prioridade AS prioridade,
          T005_Observacao_1 AS observacao
        FROM T005
          LEFT JOIN D024 ON D024_Id=T005_D024_Id
          LEFT JOIN D022 ON D022_Id=T005_D022_Id
          LEFT JOIN D022A ON D022A_D022_Id=T005_D022_Id
        WHERE T005_Id = '{$T005_Id}'
      ");

      if(!$cT005) {
        $this->errorMessage = "[integrarPedido] Erro ao consultar pedido $T005_Id:<br> " . mysql_error();
        return false;
      }

      $dT005 = mysql_fetch_assoc($cT005);

      if(!$dT005) {
        $this->errorMessage = "[integrarPedido] Pedido não encontrado: $T005_Id";
        return false;
      }

      $integrarPessoa = $this->integrarPessoa('INSERT', $dT005['clienteErpId'], 'C');

      if(!$integrarPessoa) {
        $this->errorMessage = "[integrarPedido] Erro ao integrar pessoa:<br> " . $this->getErrorMessage();
        return false;
      }

      if($dT005['flagFrete'] != '9') {
        $integrarTransportadora = $this->integrarTransportadora('INSERT', $dT005['transportadoraErpId']);

        if(!$integrarTransportadora) {
          $this->errorMessage = "[integrarPedido] Erro ao integrar transportadora:<br> " . $this->getErrorMessage();
          return false;
        }
      }

      $data = $this->orderDateAsDeliveryDate == 'S' ? $dT005['dataEntrega'] : $dT005['data'];
      
      $parametros = array(
        'operacao' => $operacao,
        'erpId' => $dT005['erpId'],
        'empresa' => empty($dT005['empresa']) ? 1 : $dT005['empresa'],
        'data' => date('M d, Y H:i:s A', strtotime($data)),
        'documento' => $dT005['erpId'],
        'valor' => $dT005['valor'],
        'avaria' => false,
        'noCliente' => $dT005['clienteErpId'],
        'clienteErpId' => $dT005['clienteErpId'],
        'nomeCliente' => $dT005['nomeCliente'],
        'dataEntrega' => date('M d, Y H:i:s A', strtotime($dT005['dataEntrega'])),
        'transportadoraErpId' => $dT005['transportadoraErpId'],
        'nomeTransportadora' => $dT005['nomeTransportadora'],
        'noRota' => $dT005['transportadoraErpId'],
        'descRota' => $dT005['nomeTransportadora'],
        'prioridade' => $dT005['prioridade'],
        'observacao' => $dT005['observacao'],
      );

      $itensPedido = array();

      $cT006 = mysql_query("
        SELECT
          T006_Id AS erpId,
          D001_Codigo_Produto AS codigoReduzido,
          T006_Quantidade AS quantidade,
          T006_Observacao AS observacao,
          D037_Unidade AS unidade
        FROM T006
          LEFT JOIN D009 ON D009_Id=T006_D009_Id
          LEFT JOIN D049 ON D049_Id=D009_D049_Id
          LEFT JOIN D001 ON D001_Id=D049_D001_Id
          LEFT JOIN D037 ON D037_Id=T006_D037_Id
        WHERE T006_T005_Id = '{$T005_Id}'
      ");

      if(!$cT006) {
        $this->errorMessage = "[integrarPedido] Erro ao consultar itens do pedido $T005_Id:<br> " . mysql_error();
        return false;
      }

      while($dT006 = mysql_fetch_assoc($cT006)) {
        $quantidade = $this->convertUnit == 'S' ? $this->convertQuantity($dT006['quantidade'], $dT006['unidade']) : $dT006['quantidade'];

        $itensPedido[] = array(
          'operacao' => $operacao,
          'erpId' => $dT006['erpId'],
          'codigoReduzido' => $dT006['codigoReduzido'],
          'quantidade' => $quantidade,
          'observacao' => $dT006['observacao'],
        );
      }

      $parametros['itensPedido'] = $itensPedido;

      return $this->post($this->getToken(), 'processo', 'integrarPedido', 'pedido', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[integrarPedido] Erro na requisição de pedido:<br> " . $e->getMessage();
      return false;
    }
  }

  /**
   * @param T005_Id = ID do pedido
   * @return boolean true | false
   */
  public function cancelarPedido($T005_Id) {
    if(empty($T005_Id)) {
      $this->errorMessage = "[cancelarPedido] ID do pedido não informado";
      return false;
    }

    try {
      $cT005 = mysql_query("SELECT T005_Id AS erpId, T005_C004_Id AS empresa FROM T005 WHERE T005_Id = '{$T005_Id}'");

      if(!$cT005) {
        $this->errorMessage = "[cancelarPedido] Erro ao consultar pedido $T005_Id:<br> " . mysql_error();
        return false;
      }
      
      $dT005 = mysql_fetch_assoc($cT005);

      if(!$dT005) {
        $this->errorMessage = "[cancelarPedido] Pedido não encontrado: $T005_Id";
        return false;
      }

      $parametros = array(
        'operacao' => 'DELETE',
        'erpId' => $dT005['erpId'],
      );

      return $this->post($this->getToken(), 'processo', 'integrarPedido', 'pedido', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[cancelarPedido] Erro na requisição de cancelamento de pedido:<br> " . $e->getMessage();
      return false;
    }
  }

  /**
   * @param operacao = INSERT | UPDATE | DELETE
   * @param D022_Id = ID da transportadora
   * @return boolean true | false
   */
  public function integrarTransportadora($operacao, $D022_Id) {
    if(empty($D022_Id)) {
      $this->errorMessage = "[integrarTransportadora] ID da transportadora não informado";
      return false;
    }
    
    try {
      $cT022 = mysql_query("
        SELECT
          D022_Id AS erpId,
          D022_Nome_Empresa AS nome
        FROM D022
        WHERE D022_Id = '{$D022_Id}'
      ");

      if(!$cT022) {
        $this->errorMessage = "[integrarTransportadora] Erro ao consultar transportadora $D022_Id:<br> " . mysql_error();
        return false;
      }

      $dT022 = mysql_fetch_assoc($cT022);

      if(!$dT022) {
        $this->errorMessage = "[integrarTransportadora] Transportadora não encontrada: $D022_Id";
        return false;
      }

      $parametros = array(
        'operacao' => $operacao,
        'erpId' => $dT022['erpId'],
        'nome' => $dT022['nome'],
      );

      return $this->post($this->getToken(), 'cadastro', 'integrarTransportadora', 'transportadora', $parametros);
    } catch (\Exception $e) {
      $this->errorMessage = "[integrarTransportadora] Erro na requisição de transportadora:<br> " . $e->getMessage();
      return false;
    }
  }

  /**
   * @param nomeRelatorio = Nome da etiqueta/relatório
   * @param copias = Quantidade de cópias
   * @param filtrosRelatorio = Array de filtros (nome/valor) para gerar a etiqueta
   * @return string|false Retorna o PDF da etiqueta em base64 ou false em caso de erro
   */
  public function obterEtiqueta($nomeRelatorio, $copias = 1, $filtrosRelatorio = array()) {
    global $g;

    try {      
      $headers = array(
        "Content-Type: application/x-www-form-urlencoded",
        "chave: " . $this->key,
        "token: " . $this->getToken()
      );

      $parametros = array(
        'nomeRelatorio' => $nomeRelatorio,
        'copias' => $copias,
        'filtrosRelatorio' => $filtrosRelatorio
      );

      $body = 'etiqueta=' . urlencode(json_encode($parametros));

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "{$this->url}/processo/obterEtiquetas");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);

      if ($httpCode != 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['retorno'])) {
          $this->errorMessage = "[obterEtiqueta] " . $responseData['retorno'];
        } else {
          $this->errorMessage = "[obterEtiqueta] Erro HTTP: $httpCode";
        }
        return false;
      }

      if (preg_match('/^%PDF/', urldecode($response))) {
        $dir = $g['pathRaiz'] . $g['pathDados'];

        if (!is_dir($dir)) {
          mkdir($dir, 0777, true);
        }
 
        $files = glob($dir . "/*.pdf");
        $maxAge = 30 * 60 * 1000;
        $now = time();
 
        foreach ($files as $file) {
          if (is_file($file) && ($now - filemtime($file)) > $maxAge) {
            unlink($file);
          }
        }
 
        $fileName = "etiqueta_" . time() . ".pdf";
        $filePath = $dir . "pdf/" . $fileName;
 
        file_put_contents($filePath, $response);

        return str_replace('www/', '', $filePath);
      }

      $responseData = json_decode($response, true);

      if (isset($responseData['retorno'])) {
        $this->errorMessage = "[obterEtiqueta] " . $responseData['retorno'];
        return false;
      }

      $this->errorMessage = "[obterEtiqueta] Resposta inválida da API";
      return false;
    } catch (\Exception $e) {
      $this->errorMessage = "[obterEtiqueta] Erro ao obter etiqueta: " . $e->getMessage();
      return false;
    }
  }
}
