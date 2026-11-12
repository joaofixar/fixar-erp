<?php
namespace hardness;

require_once __DIR__ . '/iset_sku.php';

/*
 * Camada de domínio da ISET. As telas em integradores/iset apenas encaminham
 * a requisição, guardam a prévia na sessão e renderizam a resposta.
 */
require_once __DIR__ . '/iset_cliente.php';
require_once __DIR__ . '/iset_normalizador.php';
require_once __DIR__ . '/iset_validador.php';
require_once __DIR__ . '/iset_historico.php';
require_once __DIR__ . '/iset_logs.php';
require_once __DIR__ . '/iset_configuracoes.php';
require_once __DIR__ . '/iset_aliquotas.php';
require_once __DIR__ . '/iset_idempotencia.php';

/**
 * Infraestrutura de persistência da ISET.
 *
 * Esta classe não é uma API pública: a fachada ISET coordena os seus métodos
 * com API, normalização e validação. Ela concentra o código legado de banco
 * enquanto a extração progressiva para serviços menores é mantida coesa.
 */
class iset_importador_
{
    /** Regra de negócio usada pelo fluxo automático antes de iniciar a importação. */
    public function pedidoPago(array $pedidoApi)
    {
        $order = isset($pedidoApi['order']) && is_array($pedidoApi['order']) ? $pedidoApi['order'] : array();
        $transacao = isset($order['payment']['transaction']) && is_array($order['payment']['transaction']) ? $order['payment']['transaction'] : array();
        $status = strtolower(trim(isset($transacao['status']) ? (string) $transacao['status'] : ''));
        $dataPago = isset($order['datePaid']) ? (string) $order['datePaid'] : '';
        $valorTotal = isset($pedidoApi['totalValue']) ? (float) $pedidoApi['totalValue'] : 0;
        $valorPago = isset($pedidoApi['totalValuePaid']) ? (float) $pedidoApi['totalValuePaid'] : 0;
        $valorPagoTransacao = isset($transacao['amount_paid']) ? (float) $transacao['amount_paid'] : 0;
        $valorPago = max($valorPago, $valorPagoTransacao);
        $pago = in_array($status, array('success', 'paid'), true) && $dataPago !== '' && $valorTotal > 0 && $valorPago >= $valorTotal;
        return array(
            'pago'             => $pago,
            'motivo'           => $pago ? 'Pagamento aprovado.' : 'Pagamento ainda não confirmado.',
            'statusPagamento'  => $status,
            'dataPagamento'    => $dataPago,
            'valorPago'        => $valorPago,
            'valorTotal'       => $valorTotal,
        );
    }

    /** Permite tratar reenvios de webhook sem duplicar a importação. */
    public function pedidoJaImportado(array $pedido)
    {
        global $g;
        $empresa = (int) $g['empresaAtual'];
        $id = $this->sql(isset($pedido['pedido']['id_externo']) ? $pedido['pedido']['id_externo'] : '');
        if ($empresa <= 0 || $id === '') {
            return 0;
        }

        $sql = <<<SQL
            SELECT T005_Id
              FROM T005
             WHERE T005_C004_Id = '{$empresa}'
               AND T005_Id_Pedido_Externo = '{$id}'
             ORDER BY T005_Id ASC
             LIMIT 1
SQL;
        $consulta = mysql_query($sql);
        $registro = $consulta ? mysql_fetch_assoc($consulta) : false;

        return $registro ? (int) $registro['T005_Id'] : 0;
    }

    protected function sql($valor)
    {
        return mysql_real_escape_string(trim((string) $valor));
    }

    protected function data($valor)
    {
        $data = substr((string) $valor, 0, 10);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) ? $data : date('Y-m-d');
    }

    /**
     * Identifica o portador do pedido primeiro pelo mapeamento de e-commerce
     * já disponível no D027. O padrão da ISET evita que uma forma ainda não
     * cadastrada seja atribuída, por exemplo, ao portador de boleto.
     */
    protected function localizarPortadorPagamentoIset(array $pagamento)
    {
        global $g;

        $formas = array_unique(array_filter(array(
            isset($pagamento['metodo']) ? trim((string) $pagamento['metodo']) : '',
            isset($pagamento['titulo']) ? trim((string) $pagamento['titulo']) : '',
            isset($pagamento['tipo_transacao']) ? trim((string) $pagamento['tipo_transacao']) : '',
        )));

        foreach ($formas as $forma) {
            $forma = $this->sql($forma);
            if ($forma === '') {
                continue;
            }

            $sqlD027 = <<<SQL
                SELECT
                    D027_Id,
                    D027_Parcelas_Ecommerce
                FROM D027
                WHERE D027_Flag_Ativo = 'S'
                    AND D027_Descricao_Ecommerce LIKE '%{$forma}%'
                ORDER BY D027_Id ASC
                LIMIT 2
SQL;
            $consultaD027 = mysql_query($sqlD027);
            if (!$consultaD027) {
                throw new \Exception('Não foi possível localizar o portador de pagamento da ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
            }
            if (mysql_num_rows($consultaD027) > 1) {
                throw new \Exception('Há mais de um portador ativo mapeado para a forma de pagamento ISET “' . $forma . '”.');
            }

            $D027 = mysql_fetch_assoc($consultaD027);
            if ($D027) {
                return $D027;
            }
        }

        $D027_Id = isset($g['C031']['IsetD027IdPagamento']) ? (int) $g['C031']['IsetD027IdPagamento'] : 0;
        if ($D027_Id <= 0) {
            return array();
        }

        $sqlD027 = <<<SQL
            SELECT
                D027_Id,
                D027_Parcelas_Ecommerce
            FROM D027
            WHERE D027_Id = '{$D027_Id}'
                AND D027_Flag_Ativo = 'S'
            LIMIT 1
SQL;
        $consultaD027 = mysql_query($sqlD027);
        if (!$consultaD027) {
            throw new \Exception('Não foi possível validar o portador padrão de pagamento da ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }

        return mysql_fetch_assoc($consultaD027) ?: array();
    }

    /**
     * A geração oficial de parcelas depende das condições previamente
     * gravadas no T005. A tela de pedido faz essa gravação antes de chamar
     * VEN001::atualizarParcelas(); a importação ISET precisa repetir a mesma
     * preparação porque cria o cabeçalho diretamente.
     */
    protected function aplicarCondicoesPagamentoIset($t005, $D027_Id, $parcelas)
    {
        global $g;

        $t005 = (int) $t005;
        $D027_Id = (int) $D027_Id;
        $parcelas = (int) $parcelas;
        $sqlD024 = <<<SQL
            SELECT
                D024.D024_Pgto_Primeiro_Vcto_Tipo,
                D024.D024_Prazo_Parcela,
                D024.D024_Pgto_Dias_Vcto_Tipo,
                D024.D024_Dias_Vencimento
            FROM T005
                LEFT JOIN D024 ON D024_Id = T005_D024_Id
            WHERE T005_Id = '{$t005}'
            LIMIT 1
SQL;
        $consultaD024 = mysql_query($sqlD024);
        if (!$consultaD024) {
            throw new \Exception('Não foi possível consultar as condições de pagamento do pedido ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }
        $D024 = mysql_fetch_assoc($consultaD024);
        if (!$D024) {
            throw new \Exception('Pedido ISET não localizado para preparar as condições de pagamento.');
        }

        $primeiroVencimentoTipo = !empty($D024['D024_Pgto_Primeiro_Vcto_Tipo'])
            ? strtoupper(trim($D024['D024_Pgto_Primeiro_Vcto_Tipo']))
            : strtoupper(trim(isset($g['C031']['primeiroVencimentoTipo']) ? $g['C031']['primeiroVencimentoTipo'] : ''));
        $primeiroVencimentoValor = (int) $D024['D024_Prazo_Parcela'] > 0
            ? (int) $D024['D024_Prazo_Parcela']
            : (int) (isset($g['C031']['primeiroVencimentoNum']) ? $g['C031']['primeiroVencimentoNum'] : 0);
        $diasVencimentoTipo = !empty($D024['D024_Pgto_Dias_Vcto_Tipo'])
            ? strtoupper(trim($D024['D024_Pgto_Dias_Vcto_Tipo']))
            : strtoupper(trim(isset($g['C031']['tipoIntervalodias']) ? $g['C031']['tipoIntervalodias'] : ''));
        $diasVencimentoValor = (int) $D024['D024_Dias_Vencimento'] > 0
            ? (int) $D024['D024_Dias_Vencimento']
            : (int) (isset($g['C031']['IntervalorDias']) ? $g['C031']['IntervalorDias'] : 0);

        if ($primeiroVencimentoTipo !== 'DIAS' || $primeiroVencimentoValor <= 0) {
            throw new \Exception('Configure o primeiro vencimento em dias para importar os pagamentos ISET.');
        }
        if (!in_array($diasVencimentoTipo, array('INTERVALO', 'DIA FIXO'), true)) {
            throw new \Exception('Configure o tipo de dias de vencimento para importar os pagamentos ISET.');
        }
        if ($diasVencimentoValor <= 0) {
            throw new \Exception('Configure os dias de vencimento para importar os pagamentos ISET.');
        }

        $sqlT005 = <<<SQL
            UPDATE T005 SET
                T005_Pgto_Parcelas = '{$parcelas}',
                T005_Pgto_Primeiro_Vcto_Tipo = '{$primeiroVencimentoTipo}',
                T005_Pgto_Primeiro_Vcto_Valor = '{$primeiroVencimentoValor}',
                T005_Pgto_Dias_Vcto_Tipo = '{$diasVencimentoTipo}',
                T005_Pgto_Dias_Vcto_Valor = '{$diasVencimentoValor}',
                T005_D027_Id = '{$D027_Id}'
            WHERE T005_Id = '{$t005}'
SQL;
        if (!mysql_query($sqlT005)) {
            throw new \Exception('Não foi possível atualizar as condições de pagamento do pedido ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }
    }

    /**
     * Aplica a forma de pagamento ISET e usa a rotina oficial de parcelas do
     * pedido. Dessa forma, prazo, juros, frete e demais regras já adotadas
     * pelo ERP são os mesmos da ação manual “Gerar parcelas”.
     */
    protected function incluirPagamentoIset($t005, array $pedido)
    {
        $t005 = (int) $t005;
        $pagamento = isset($pedido['pagamento']) && is_array($pedido['pagamento']) ? $pedido['pagamento'] : array();
        $D027 = $this->localizarPortadorPagamentoIset($pagamento);
        if (empty($D027['D027_Id'])) {
            throw new \Exception('Forma de pagamento ISET sem portador configurado. Cadastre o método em D027_Descricao_Ecommerce ou informe o portador padrão nas configurações da ISET.');
        }

        $parcelas = isset($pagamento['parcelas']) ? (int) $pagamento['parcelas'] : 0;
        if (!empty($D027['D027_Parcelas_Ecommerce'])) {
            $parcelas = (int) $D027['D027_Parcelas_Ecommerce'];
        }
        $parcelas = max(1, $parcelas);

        $D027_Id = (int) $D027['D027_Id'];
        $this->aplicarCondicoesPagamentoIset($t005, $D027_Id, $parcelas);

        require_once 'bibliotecas/classes/VEN001.php';
        $VEN001 = new \hardness\VEN001();
        $dadosTabela = array(
            'tabela' => 'T005',
            'tabelaId' => $t005,
            'campos' => 'T005_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                T005_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                T005_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                T005_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                T005_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                T005_D027_Id as D027_Id,
                T005_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
                T005_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
                T005_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
                T005_Pgto_Parcelas as Total_Parcelas,
                T005_Valor_Total_IPI as valorTotalIPI,
                T005_Valor_Total as valorTotal,
                T005_Valor_Frete as valorTotalFrete,
                T005_Valor_ICMS_Substituicao_Retencao as valorTotalST',
            'where' => "T005_Id='{$t005}'",
            'tabelaPgto' => 'T090',
            'camposPgto' => 'T090_Prazos_Dias as prazoDias,T090_D027_Id as D027Pagamento,T090_Numero_Parcela as numeroParcela',
            'wherePgto' => "T090_T005_Id='{$t005}'",
        );
        $retorno = $VEN001->atualizarParcelas($dadosTabela);
        if ($retorno !== true) {
            throw new \Exception('Não foi possível gerar as parcelas do pagamento ISET. ' . $retorno);
        }

        $sqlT090 = <<<SQL
            SELECT
                COUNT(*) AS Quantidade,
                MIN(T090_Data_Vencimento) AS Primeiro_Vencimento
            FROM T090
            WHERE T090_T005_Id = '{$t005}'
SQL;
        $consultaT090 = mysql_query($sqlT090);
        if (!$consultaT090) {
            throw new \Exception('Não foi possível conferir as parcelas do pagamento ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }
        $T090 = mysql_fetch_assoc($consultaT090);
        if (!$T090 || (int) $T090['Quantidade'] !== $parcelas) {
            throw new \Exception('A quantidade de parcelas geradas para o pagamento ISET não corresponde à forma de pagamento informada.');
        }

        return array('parcelas' => $parcelas, 'portadorId' => $D027_Id, 'dataPrimeiroVencimento' => $T090['Primeiro_Vencimento']);
    }

    protected function localizarProduto($codigo) { global $g; $codigo=$this->sql((new ISET_SKU())->paraHardness($codigo)); if ($codigo === '') return array(); $empresa=(int)$g['empresaAtual']; $q=mysql_query("SELECT D001.D001_Id,D001.D001_Codigo_Produto,D001.D001_Descricao_Produto,D049.D049_Id,D009.D009_Id FROM D001 INNER JOIN D049 ON D049.D049_D001_Id=D001.D001_Id INNER JOIN D009 ON D009.D009_D049_Id=D049.D049_Id WHERE D001.D001_Codigo_Produto='{$codigo}' AND D009.D009_C004_Id='{$empresa}'"); if (!$q) return array('erro'=>mysql_error()); $linhas=array(); while ($r=mysql_fetch_assoc($q)) $linhas[]=$r; return count($linhas)===1 ? $linhas[0] : array('quantidade'=>count($linhas)); }
    protected function localizarProdutoTeste() { global $g; $empresa=(int)$g['empresaAtual']; $q=mysql_query("SELECT D001.D001_Id,D001.D001_Codigo_Produto,D001.D001_Descricao_Produto,D049.D049_Id,D009.D009_Id FROM D001 INNER JOIN D049 ON D049.D049_D001_Id=D001.D001_Id INNER JOIN D009 ON D009.D009_D049_Id=D049.D049_Id WHERE D009.D009_C004_Id='{$empresa}' AND D001.D001_Flag_Pre_Cadastro<>'S' AND LOWER(D001.D001_Descricao_Produto) LIKE '%teste%hardness%' ORDER BY D001.D001_Id ASC, D009.D009_Id ASC LIMIT 1"); return $q ? (mysql_fetch_assoc($q) ?: array()) : array(); }
    protected function produtoTemServicos($d001Id) { $q=mysql_query("SELECT D135_Id FROM D135 WHERE D135_D001_Id='".(int)$d001Id."' LIMIT 1"); return $q && mysql_num_rows($q)>0; }

    protected function ufCidade($endereco) { $uf=$this->sql($endereco['uf']); $cidade=$this->sql($endereco['cidade']); $q=mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF='{$uf}' LIMIT 1"); $estado=$q?mysql_fetch_assoc($q):false; if (!$estado) throw new \Exception('UF de entrega não localizada no Hardness.'); $q=mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='".(int)$estado['D018_Id']."' AND D020_Nome_Cidade='{$cidade}' LIMIT 1"); $cidade=$q?mysql_fetch_assoc($q):false; if (!$cidade) throw new \Exception('Cidade de entrega não localizada no Hardness.'); return array((int)$estado['D018_Id'],(int)$cidade['D020_Id']); }
    protected function localizarOuCriarCliente(array $pedido,&$clienteCriado=false) { global $g; $clienteCriado=false; $empresa=(int)$g['empresaAtual']; $doc=preg_replace('/\D/','',$pedido['cliente']['documento']); $campo=$pedido['cliente']['tipo']==='J'?'D024_Cnpj':'D024_Cpf'; $q=mysql_query("SELECT D024_Id FROM D024 WHERE D024_C004_Id='{$empresa}' AND REPLACE(REPLACE(REPLACE(TRIM({$campo}), '/', ''), '-', ''), '.', '')='".$this->sql($doc)."'"); if (!$q||mysql_num_rows($q)>1) throw new \Exception('Não foi possível localizar o cliente de forma única pelo CPF/CNPJ.'); if($r=mysql_fetch_assoc($q))return(int)$r['D024_Id']; list($estado,$cidade)=$this->ufCidade($pedido['endereco']); $nome=$this->sql($pedido['cliente']['nome']); if($nome==='')throw new \Exception('Nome do cliente não informado pela ISET.'); $telefone=preg_replace('/\D/','',$pedido['cliente']['telefone']);$ddd=substr($telefone,0,2);$fone=substr($telefone,2);$cpf=$pedido['cliente']['tipo']==='F'?$doc:'';$cnpj=$pedido['cliente']['tipo']==='J'?$doc:''; $sql="INSERT INTO D024 (D024_C004_Id,D024_D018_Id,D024_D020_Id,D024_D030_Id,D024_Cpf,D024_Cnpj,D024_Flag_Pessoa_Fisica_Juridica,D024_Nome_Empresa,D024_Nome_Fantasia,D024_Endereco,D024_Numero,D024_Complemento,D024_Cep,D024_Bairro,D024_DDD_Telefone_1,D024_Telefone_1,D024_E_Mail,D024_Flag_Cliente,D024_Flag_Fornecedor,D024_Flag_Pre_Cadastro,D024_Flag_Ativo,D024_Flag_Liberado,D024_C007_Vendedor_Interno,D024_Codigo_Regime_Tributario) VALUES ('{$empresa}','{$estado}','{$cidade}','271','".$this->sql($cpf)."','".$this->sql($cnpj)."','".$this->sql($pedido['cliente']['tipo'])."','{$nome}','{$nome}','".$this->sql($pedido['endereco']['rua'])."','".$this->sql($pedido['endereco']['numero'])."','".$this->sql($pedido['endereco']['complemento'])."','".$this->sql($pedido['endereco']['cep'])."','".$this->sql($pedido['endereco']['bairro'])."','{$ddd}','{$fone}','".$this->sql($pedido['cliente']['email'])."','S','N','N','S','N','".(int)$g['usuarioAtual']."','1')"; if(!mysql_query($sql))throw new \Exception('Não foi possível criar o cliente.'); $q=mysql_query("SELECT D024_Id FROM D024 WHERE D024_C004_Id='{$empresa}' AND REPLACE(REPLACE(REPLACE(TRIM({$campo}), '/', ''), '-', ''), '.', '')='".$this->sql($doc)."'");if(!$q||mysql_num_rows($q)!==1)throw new \Exception('Cliente criado, mas não foi possível identificá-lo de forma única pelo CPF/CNPJ.');$r=mysql_fetch_assoc($q);$clienteCriado=true;return(int)$r['D024_Id']; }
    protected function localizarOuCriarContato($clienteId,array $pedido) { $email=$this->sql($pedido['cliente']['email']);$filtro=$email!==''?" AND D013_E_Mail='{$email}'":'';$q=mysql_query("SELECT D013_Id FROM D013 WHERE D013_D024_Id='{$clienteId}'{$filtro} ORDER BY D013_Id ASC LIMIT 1");if($q&&($r=mysql_fetch_assoc($q)))return(int)$r['D013_Id'];$tel=preg_replace('/\D/','',$pedido['cliente']['telefone']);if(!mysql_query("INSERT INTO D013 (D013_D024_Id,D013_Nome_Contato,D013_E_Mail,D013_DDD_Telefone_1,D013_Telefone_1,D013_Flag_Ativo) VALUES ('{$clienteId}','".$this->sql($pedido['cliente']['nome'])."','{$email}','".substr($tel,0,2)."','".substr($tel,2)."','S')"))throw new \Exception('Não foi possível criar o contato.');$q=mysql_query("SELECT D013_Id FROM D013 WHERE D013_D024_Id='{$clienteId}'{$filtro} ORDER BY D013_Id DESC LIMIT 1");if(!$q||!($r=mysql_fetch_assoc($q)))throw new \Exception('Contato criado, mas não foi possível identificá-lo.');return(int)$r['D013_Id']; }
    protected function localizarOuCriarEndereco($clienteId,array $pedido) { $e=$pedido['endereco'];$cep=preg_replace('/\D/','',$e['cep']);$numero=$this->sql($e['numero']);$rua=$this->sql($e['rua']);if($cep===''&&trim($e['rua'])==='')return 0;$where="D148_D024_Id='{$clienteId}' AND REPLACE(REPLACE(D148_Cep, '-', ''), '.', '')='".$this->sql($cep)."' AND D148_Numero='{$numero}' AND D148_Rua='{$rua}'";$q=mysql_query("SELECT D148_Id FROM D148 WHERE {$where} ORDER BY D148_Id ASC LIMIT 1");if($q&&($r=mysql_fetch_assoc($q)))return(int)$r['D148_Id'];$q=mysql_query("SELECT D024_Id FROM D024 WHERE D024_Id='{$clienteId}' AND REPLACE(REPLACE(D024_Cep, '-', ''), '.', '')='".$this->sql($cep)."' AND D024_Numero='{$numero}' AND D024_Endereco='{$rua}' LIMIT 1");if($q&&mysql_num_rows($q)===1)return 0;list($estado,$cidade)=$this->ufCidade($e);$sql="INSERT INTO D148 (D148_D024_Id,D148_D018_Id,D148_D020_Id,D148_D030_Id,D148_Nome,D148_Cep,D148_Rua,D148_Numero,D148_Complemento,D148_Bairro,D148_Email,D148_Flag_Tipo) VALUES ('{$clienteId}','{$estado}','{$cidade}','271','".$this->sql($e['nome']?:$pedido['cliente']['nome'])."','".$this->sql($e['cep'])."','{$rua}','{$numero}','".$this->sql($e['complemento'])."','".$this->sql($e['bairro'])."','".$this->sql($pedido['cliente']['email'])."','E')";if(!mysql_query($sql))throw new \Exception('Não foi possível criar o endereço de entrega.');$q=mysql_query("SELECT D148_Id FROM D148 WHERE {$where} ORDER BY D148_Id DESC LIMIT 1");if(!$q||!($r=mysql_fetch_assoc($q)))throw new \Exception('Endereço criado, mas não foi possível identificá-lo.');return(int)$r['D148_Id']; }

    /**
     * Inclui um item sem reutilizar VEN002::incluirProduto().
     *
     * VEN002 é apropriado para a venda iniciada no ERP, pois calcula o preço
     * comercial a partir de tabela/lista. Um pedido ISET já chega fechado; por
     * isso, preservamos os valores comerciais recebidos e consultamos no ERP
     * apenas CFOP, alíquotas, classificação fiscal e parâmetros do produto.
     */
    protected function incluirItemIset($t005, array $produto, array $item, $d022, $data)
    {
        global $g;

        require_once 'bibliotecas/classes/VEN001.php';
        $ven001 = new \hardness\VEN001();
        $empresa = (int) $g['empresaAtual'];
        $t005 = (int) $t005;
        $d009 = (int) $produto['D009_Id'];

        $consulta = mysql_query("SELECT D001.D001_Id, D001.D001_Codigo_Produto, D001.D001_Descricao_Produto,
            D001.D001_Cubagem_Unitaria, D001.D001_Flag_Pre_Cadastro, D049.D049_Id,
            D049.D049_D082_Id, D009.D009_Quantidade_Estoque_Liquido, D009.D009_Valor_Custo_Unitario,
            D009.D009_Preco_Pauta_ST
            FROM D001 INNER JOIN D049 ON D049.D049_D001_Id = D001.D001_Id
            INNER JOIN D009 ON D009.D009_D049_Id = D049.D049_Id
            WHERE D009.D009_Id = '{$d009}' LIMIT 1");
        $dadosProduto = $consulta ? mysql_fetch_assoc($consulta) : false;
        if (!$dadosProduto) {
            throw new \Exception('Produto do Hardness não localizado para inclusão do item.');
        }
        if ($dadosProduto['D001_Flag_Pre_Cadastro'] === 'S' && $g['C031']['permitePedidoProdutoPreCadastro'] === 'N') {
            throw new \Exception('O produto está em pré-cadastro e não pode ser incluído no pedido.');
        }

        $cabecalho = mysql_query("SELECT T005_D024_Id, T005_Flag_ACP, T005_Flag_Lista_Preco,
            T005_C004_Id_Estoque, T005_Casas_Decimais_Preco, D148_D018_Id
            FROM T005 LEFT JOIN D148 ON D148_Id = T005_D148_Id_Entrega
            WHERE T005_Id = '{$t005}' LIMIT 1");
        $pedidoErp = $cabecalho ? mysql_fetch_assoc($cabecalho) : false;
        if (!$pedidoErp) {
            throw new \Exception('Cabeçalho do pedido não localizado para inclusão do item.');
        }

        $contextoCfop = array('ufEnderecoEntrega' => $pedidoErp['D148_D018_Id']);
        $cfop = $ven001->retornaCFOPContexto(
            (int) $pedidoErp['T005_D024_Id'], $d009, $empresa, '3', false, false, false,
            $pedidoErp['T005_C004_Id_Estoque'], false, $contextoCfop
        );
        $aliquotas = $ven001->retornaAliquotas($d009, false, $t005, false, false, false, false, $cfop);
        if (!is_array($aliquotas)) {
            throw new \Exception('Não foi possível obter os parâmetros fiscais do produto.');
        }

        $ultimo = mysql_query("SELECT COALESCE(MAX(T006_Item), 0) AS ultimo FROM T006 WHERE T006_T005_Id = '{$t005}'");
        $linha = $ultimo ? mysql_fetch_assoc($ultimo) : false;
        $numeroItem = $linha ? ((int) $linha['ultimo'] + 1) : 1;
        $quantidade = (float) $item['quantidade'];
        if ($quantidade <= 0) {
            throw new \Exception('Quantidade inválida para o produto ' . $item['codigo_referencia'] . '.');
        }
        $valorTotalIset = (float) $item['valor_total'];
        $valorOriginal = (float) $item['valor_unitario'];
        $valorUnitario = $valorTotalIset > 0 ? $valorTotalIset / $quantidade : $valorOriginal;
        if ($valorUnitario < 0) {
            throw new \Exception('Valor inválido para o produto ' . $item['codigo_referencia'] . '.');
        }
        if ($valorOriginal <= 0) {
            $valorOriginal = $valorUnitario;
        }
        $descontoUnitario = max(0, $valorOriginal - $valorUnitario);
        $percentualDesconto = $valorOriginal > 0 ? ($descontoUnitario / $valorOriginal) * 100 : 0;
        $descricao = $this->sql($produto['D001_Descricao_Produto']);
        $codigo = $this->sql($produto['D001_Codigo_Produto']);
        $d005 = isset($aliquotas['D005_Id']) && $aliquotas['D005_Id'] !== '' ? (int) $aliquotas['D005_Id'] : 0;
        $classificacao = $this->sql(isset($aliquotas['Classificacao_Fiscal']) ? $aliquotas['Classificacao_Fiscal'] : '');
        $numero = function ($valor) { return (float) $valor; };

        $sql = "INSERT INTO T006 (
            T006_C004_Id,T006_Item,T006_Codigo_Produto,T006_Descricao_Produto,T006_T005_Id,
            T006_D024_Id,T006_D009_Id,T006_D082_Id,T006_D005_Id,T006_D006_Id,
            T006_Quantidade_Estoque,T006_Quantidade,T006_Valor_Custo_Unitario,
            T006_Valor_Preco_Unitario,T006_Valor_Preco_Sem_Desconto_Unitario,
            T006_Valor_Preco_Original,T006_Valor_Preco_Original_Bruto,
            T006_Percentual_Desconto,T006_Valor_Desconto_Unitario,
            T006_Percentual_Desconto_Bruto,T006_Valor_Desconto_Unitario_Bruto,
            T006_Valor_Preco_Bruto,T006_Classificacao_Fiscal,T006_Flag_Destino_Produto,
            T006_D022_Id,T006_Data_Entrega,T006_Peso_Unitario,T006_Peso_Bruto,
            T006_Cubagem_Unitaria,T006_Preco_Pauta_ST,
            T006_Aliquota_ICMS,T006_Aliquota_ICMS_Credito,T006_Aliquota_IPI,
            T006_Aliquota_PIS,T006_Aliquota_COFINS,T006_Aliquota_IRPJ,T006_Aliquota_CSLL,
            T006_Percentual_Reducao_ICMS,T006_Percentual_Reducao_ICMS_ST,T006_ST_VA,
            T006_Flag_ST,T006_Flag_Situacao_Tributaria,T006_Situacao_Tributaria_PIS,
            T006_Situacao_Tributaria_COFINS,T006_Situacao_Tributaria_IPI,
            T006_Flag_Isento_PIS,T006_Flag_Isento_COFINS,T006_Flag_Isento_IRPJ,T006_Flag_Isento_CSLL,
            T006_ICMS_Inter_Estadual,T006_ST_ICMS_Interno,T006_ICMS_Inter_Estadual_MVA,
            T006_ST_ICMS_Interno_MVA,T006_UF_ICMS_Substituicao_Devido,
            T006_Percentual_Base_Operacao_Propria,T006_Percentual_Partilha_ICMS_Destino,
            T006_Percentual_Partilha_ICMS_Origem
        ) VALUES (
            '{$empresa}','{$numeroItem}','{$codigo}','{$descricao}','{$t005}',
            '" . (int) $pedidoErp['T005_D024_Id'] . "','{$d009}','" . (int) $dadosProduto['D049_D082_Id'] . "','{$d005}','" . (int) $cfop . "',
            '" . $numero($dadosProduto['D009_Quantidade_Estoque_Liquido']) . "','{$quantidade}','" . $numero($dadosProduto['D009_Valor_Custo_Unitario']) . "',
            '{$valorUnitario}','{$valorOriginal}','{$valorOriginal}','{$valorOriginal}',
            '{$percentualDesconto}','{$descontoUnitario}','{$percentualDesconto}','{$descontoUnitario}',
            '{$valorUnitario}','{$classificacao}','3','" . (int) $d022 . "','" . $this->sql($data) . "','" . $numero(isset($aliquotas['D001_Peso_Unitario_Kg']) ? $aliquotas['D001_Peso_Unitario_Kg'] : 0) . "','" . $numero(isset($aliquotas['D001_Peso_Unitario_Bruto']) ? $aliquotas['D001_Peso_Unitario_Bruto'] : 0) . "','" . $numero($dadosProduto['D001_Cubagem_Unitaria']) . "','" . $numero($dadosProduto['D009_Preco_Pauta_ST']) . "',
            '" . $numero(isset($aliquotas['Aliquota_ICMS']) ? $aliquotas['Aliquota_ICMS'] : 0) . "','" . $numero(isset($aliquotas['Aliquota_ICMS_Credito']) ? $aliquotas['Aliquota_ICMS_Credito'] : 0) . "','" . $numero(isset($aliquotas['Aliquota_IPI']) ? $aliquotas['Aliquota_IPI'] : 0) . "',
            '" . $numero(isset($aliquotas['Aliquota_PIS']) ? $aliquotas['Aliquota_PIS'] : 0) . "','" . $numero(isset($aliquotas['Aliquota_COFINS']) ? $aliquotas['Aliquota_COFINS'] : 0) . "','" . $numero(isset($aliquotas['Aliquota_IRPJ']) ? $aliquotas['Aliquota_IRPJ'] : 0) . "','" . $numero(isset($aliquotas['Aliquota_CSLL']) ? $aliquotas['Aliquota_CSLL'] : 0) . "',
            '" . $numero(isset($aliquotas['Percentual_Reducao_ICMS']) ? $aliquotas['Percentual_Reducao_ICMS'] : 0) . "','" . $numero(isset($aliquotas['Percentual_Reducao_ICMS_ST']) ? $aliquotas['Percentual_Reducao_ICMS_ST'] : 0) . "','" . $numero(isset($aliquotas['ST_VA']) ? $aliquotas['ST_VA'] : 0) . "',
            '" . $this->sql(isset($aliquotas['Flag_ST']) ? $aliquotas['Flag_ST'] : '') . "','" . $this->sql(isset($aliquotas['Flag_Situacao_Tributaria']) ? $aliquotas['Flag_Situacao_Tributaria'] : '') . "','" . $this->sql(isset($aliquotas['Situacao_Tributaria_PIS']) ? $aliquotas['Situacao_Tributaria_PIS'] : '') . "',
            '" . $this->sql(isset($aliquotas['Situacao_Tributaria_COFINS']) ? $aliquotas['Situacao_Tributaria_COFINS'] : '') . "','" . $this->sql(isset($aliquotas['Situacao_Tributaria_IPI']) ? $aliquotas['Situacao_Tributaria_IPI'] : '') . "',
            '" . $this->sql(isset($aliquotas['Flag_Isento_PIS']) ? $aliquotas['Flag_Isento_PIS'] : '') . "','" . $this->sql(isset($aliquotas['Flag_Isento_COFINS']) ? $aliquotas['Flag_Isento_COFINS'] : '') . "','" . $this->sql(isset($aliquotas['Flag_Isento_IRPJ']) ? $aliquotas['Flag_Isento_IRPJ'] : '') . "','" . $this->sql(isset($aliquotas['Flag_Isento_CSLL']) ? $aliquotas['Flag_Isento_CSLL'] : '') . "',
            '" . $numero(isset($aliquotas['ICMS_Inter_Estadual']) ? $aliquotas['ICMS_Inter_Estadual'] : 0) . "','" . $numero(isset($aliquotas['ST_ICMS_Interno']) ? $aliquotas['ST_ICMS_Interno'] : 0) . "','" . $numero(isset($aliquotas['ST_ICMS_Inter_Estadual_MVA']) ? $aliquotas['ST_ICMS_Inter_Estadual_MVA'] : 0) . "','" . $numero(isset($aliquotas['ST_ICMS_Interno_MVA']) ? $aliquotas['ST_ICMS_Interno_MVA'] : 0) . "','" . $this->sql(isset($aliquotas['UF_ICMS_Substituicao_Devido']) ? $aliquotas['UF_ICMS_Substituicao_Devido'] : '') . "',
            '" . $numero(isset($aliquotas['Percentual_Base_Operacao_Propria']) ? $aliquotas['Percentual_Base_Operacao_Propria'] : 0) . "','" . $numero(isset($aliquotas['Percentual_Partilha_ICMS_Destino']) ? $aliquotas['Percentual_Partilha_ICMS_Destino'] : 0) . "','" . $numero(isset($aliquotas['Percentual_Partilha_ICMS_Origem']) ? $aliquotas['Percentual_Partilha_ICMS_Origem'] : 0) . "'
        )";
        if (!mysql_query($sql)) {
            error_log('ISET: falha ao gravar T006. Pedido=' . $t005 . '; D009=' . $d009 . '; erro=' . mysql_error());
            throw new \Exception('Não foi possível gravar o item no pedido.');
        }
        $novo = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id = '{$t005}' AND T006_Item = '{$numeroItem}' LIMIT 1");
        $registro = $novo ? mysql_fetch_assoc($novo) : false;
        $t006 = $registro ? (int) $registro['T006_Id'] : 0;
        if ($t006 <= 0 || !mysql_query("INSERT INTO T006A (T006A_T006_Id,T006A_Flag_Transacao_Externa,T006A_Data_Hora_Insercao) VALUES ('{$t006}','S',NOW())")) {
            error_log('ISET: falha ao gravar T006A. Pedido=' . $t005 . '; T006=' . $t006 . '; erro=' . mysql_error());
            throw new \Exception('Não foi possível preparar o item do pedido.');
        }

        // Totalizações oficiais: usam os valores comerciais acima e não buscam
        // preço de tabela. Não possuem COMMIT próprio.
        if (!mysql_query("CALL T006_Gravar_Totalizacao_4('{$t006}')") || !mysql_query("CALL T005_Gravar_Totalizacao_4('{$t005}')")) {
            error_log('ISET: falha na totalização. Pedido=' . $t005 . '; T006=' . $t006 . '; erro=' . mysql_error());
            throw new \Exception('Não foi possível totalizar o pedido.');
        }
        return $t006;
    }

    public function importarPedido(array $pedido, array $opcoes = array())
    {
        global $g;

        $inicio = microtime(true);
        $id = (string) $pedido['pedido']['id_externo'];
        $recebidos = count($pedido['itens']);
        $importados = 0;
        $clienteCriado = false;
        $pagamentoImportado = array();
        $etapa = INT_ETAPA_VALIDACAO;
        $iniciada = false;
        $pedidoConfirmado = false;
        $controleIdempotenciaId = 0;
        $idempotencia = isset($opcoes['idempotencia']) && $opcoes['idempotencia'] instanceof ISET_IDEMPOTENCIA
            ? $opcoes['idempotencia'] : null;
        $operacao = isset($opcoes['operacao']) && $opcoes['operacao'] !== '' ? (string) $opcoes['operacao'] : INT_OPERACAO_IMPORTAR_PEDIDO;
        $base = array_merge(array(
            'pedidoIset'      => $id,
            'statusIset'      => isset($pedido['pedido']['status_id']) ? $pedido['pedido']['status_id'] : null,
            'itensRecebidos'  => $recebidos,
        ), isset($opcoes['log']) && is_array($opcoes['log']) ? $opcoes['log'] : array());
        $erro = function ($mensagem, array $detalhes = array()) use (
            &$base,
            &$etapa,
            &$importados,
            &$clienteCriado,
            &$pagamentoImportado,
            &$controleIdempotenciaId,
            &$pedidoConfirmado,
            $inicio,
            $id,
            $operacao,
            &$g
        ) {
            $log = array_merge($base, $detalhes, array(
                'itensImportados' => $importados,
                'clienteCriado' => $clienteCriado,
                'pagamento' => $pagamentoImportado,
                'usuarioErpId' => (int) $g['usuarioAtual'],
                'versaoIntegracao' => '1.0',
                'idempotencia' => array(
                    'adquirida' => $controleIdempotenciaId > 0,
                    'controleId' => $controleIdempotenciaId > 0 ? $controleIdempotenciaId : null,
                ),
                'transacao' => array(
                    'pedidoConfirmado' => $pedidoConfirmado,
                ),
                'tempoTotalMs' => (int) round((microtime(true) - $inicio) * 1000),
            ));
            integracoesRegistrarLog(array(
                'integracao' => 'ISET',
                'operacao' => $operacao,
                'status' => INT_STATUS_ERRO,
                'etapa' => $etapa,
                'idExterno' => $id,
                'mensagem' => $mensagem,
                'log' => $log,
            ));
        };
        $validacao = (new ISET_VALIDADOR())->validar($pedido);
        if (isset($validacao['frete']) && is_array($validacao['frete'])) {
            $base['freteIset'] = $validacao['frete'];
        }
        if ($validacao['bloqueado']) {
            $mensagem = 'A importação foi bloqueada pela validação.';
            $detalhes = array();
            if ($validacao['itens_nao_localizados']) {
                $indice = (int) key($validacao['itens_nao_localizados']);
                $sku = isset($pedido['itens'][$indice]['codigo_referencia']) ? $pedido['itens'][$indice]['codigo_referencia'] : '';
                $mensagem = 'Produto ' . $sku . ' não localizado.';
                $detalhes = array('sku' => $sku, 'produtoEncontrado' => false);
            } elseif (isset($validacao['frete']['erro']) && $validacao['frete']['erro'] !== '') {
                $mensagem = $validacao['frete']['erro'];
                $detalhes = array('freteIset' => $validacao['frete']);
            }
            $erro($mensagem, $detalhes);
            throw new \Exception($mensagem);
        }

        if (!mysql_query('START TRANSACTION')) {
            $etapa = 'INICIO_TRANSACAO';
            $erro('Não foi possível iniciar a transação de importação.');
            throw new \Exception('Não foi possível iniciar a transação de importação.');
        }
        $iniciada = true;
        try {
            $externo = $this->sql($id);

            if ($idempotencia) {
                $aquisicao = $idempotencia->adquirirNaTransacao((int) $g['empresaAtual'], $id);
                if (empty($aquisicao['adquirido'])) {
                    throw new iset_idempotencia_conflito($aquisicao);
                }
                $controleIdempotenciaId = (int) $aquisicao['id'];
            }

            $duplicado = mysql_query("SELECT T005_Id FROM T005 WHERE T005_C004_Id='" . (int) $g['empresaAtual'] . "' AND T005_Id_Pedido_Externo='{$externo}' FOR UPDATE");
            if (!$duplicado) {
                throw new \Exception('Não foi possível validar a duplicidade do pedido.');
            }
            if (mysql_num_rows($duplicado) > 0) {
                throw new \Exception('Pedido já importado anteriormente.');
            }

            $etapa = INT_ETAPA_CRIACAO_CLIENTE;
            $cliente = $this->localizarOuCriarCliente($pedido, $clienteCriado);
            $etapa=INT_ETAPA_CRIACAO_CONTATO;$contato=$this->localizarOuCriarContato($cliente,$pedido);$etapa=INT_ETAPA_CRIACAO_ENDERECO;$endereco=$this->localizarOuCriarEndereco($cliente,$pedido);$etapa=INT_ETAPA_CRIACAO_PEDIDO;
            $observacaoTeste='';if(isset($g['C031']['IsetModoTesteImportacao'])&&$g['C031']['IsetModoTesteImportacao']==='S'){$teste=$this->localizarProdutoTeste();foreach($pedido['itens'] as $testeItem){$original=$this->localizarProduto($testeItem['codigo_referencia']);if(empty($original['D009_Id'])&&!empty($teste['D009_Id']))$observacaoTeste.='Modo teste ISET: produto original '.$testeItem['codigo_referencia'].' ('.$testeItem['nome'].') substituído pelo produto teste '.$teste['D001_Codigo_Produto'].' ('.$teste['D001_Descricao_Produto'].').'."\n";}}
            $frete = (float) $validacao['frete']['custo'];
            $desconto = (float) $pedido['totais']['desconto'];
            $percentualDesconto = isset($pedido['totais']['percentual_desconto']) ? (float) $pedido['totais']['percentual_desconto'] : 0;
            $valorProdutos = (float) $pedido['totais']['produtos'];
            $valorProdutosLiquido = $valorProdutos - $desconto;
            $valorTotal = (float) $pedido['totais']['total'];
            $empresa = (int) $g['empresaAtual'];
            $usuario = (int) $g['usuarioAtual'];
            $d022 = isset($validacao['frete']['D022_Id']) ? (int) $validacao['frete']['D022_Id'] : 0;
            $d022T005 = $d022 > 0 ? $d022 : 'NULL';
            $d036 = !empty($validacao['frete']['D036_Id']) ? (int) $validacao['frete']['D036_Id'] : 'NULL';
            $flagFrete = $validacao['frete']['flagFrete'];
            $data = $this->data($pedido['pedido']['data_compra']);
            $hora = date('H:i:s');
            $observacaoFrete = '';
            if (!empty($validacao['frete']['transportadoraPadrao'])) {
                $freteTitulo = $validacao['frete']['titulo'];
                $freteMetodo = $validacao['frete']['metodo'];
                $freteTitulo = $freteTitulo !== '' ? $freteTitulo : 'não informado';
                $freteMetodo = $freteMetodo !== '' ? $freteMetodo : 'não informado';
                $observacaoFrete = 'ISET: utilizada a transportadora padrão D022 ' . $d022 . ', pois não foi encontrada transportadora correspondente ao título "' . $freteTitulo . '" e ao método "' . $freteMetodo . '".';
            }
            $observacaoComercial = $this->sql(trim($pedido['pedido']['observacao'] . "\n" . $observacaoTeste . $observacaoFrete));
            $observacaoPagamento = $this->sql($pedido['pagamento']['titulo'] ? $pedido['pagamento']['titulo'] : $pedido['pagamento']['metodo']);

            // O percentual é gravado antes dos itens porque a procedure oficial
            // T006_Gravar_Totalizacao_4 o distribui nos produtos e, em seguida,
            // T005_Gravar_Totalizacao_4 forma o desconto do cabeçalho.
            $sqlT005 = <<<SQL
                INSERT INTO T005 (
                    T005_C004_Id,
                    T005_D024_Id,
                    T005_D013_Id,
                    T005_D148_Id_Entrega,
                    T005_D148_Id_Cobranca,
                    T005_C007_Id_Vendedor_Interno,
                    T005_C007_Id_Embalagem,
                    T005_Data_Emissao,
                    T005_Hora_Emissao,
                    T005_Data_Entrega,
                    T005_Id_Pedido_Externo,
                    T005_Pedido_Ecommerce,
                    T005_Flag_ACP,
                    T005_Flag_Lista_Preco,
                    T005_Flag_Destino_Produto,
                    T005_Flag_Status,
                    T005_Flag_Frete,
                    T005_D022_Id,
                    T005_D036_Id,
                    T005_Valor_Frete,
                    T005_Percentual_Desconto,
                    T005_Valor_Desconto,
                    T005_Valor_Total_Produtos,
                    T005_Valor_Total_Produtos_Sem_Desconto,
                    T005_Valor_Total,
                    T005_Observacao_Comercial,
                    T005_Observacao_Pagamento,
                    T005_Flag_Plataforma_Ecommerce,
                    T005_Flag_Totalizar_Peso
                ) VALUES (
                    '{$empresa}',
                    '{$cliente}',
                    '{$contato}',
                    '{$endereco}',
                    '{$endereco}',
                    '{$usuario}',
                    '{$usuario}',
                    '{$data}',
                    '{$hora}',
                    '{$data}',
                    '{$externo}',
                    '{$externo}',
                    '6',
                    '1',
                    '3',
                    '0',
                    '{$flagFrete}',
                    {$d022T005},
                    {$d036},
                    '{$frete}',
                    '{$percentualDesconto}',
                    '{$desconto}',
                    '{$valorProdutosLiquido}',
                    '{$valorProdutos}',
                    '{$valorTotal}',
                    '{$observacaoComercial}',
                    '{$observacaoPagamento}',
                    '7',
                    'S'
                )
SQL;
            if (!mysql_query($sqlT005)) {
                throw new \Exception('Não foi possível criar o cabeçalho do pedido.');
            }

            $sqlT005Inserido = <<<SQL
                SELECT T005_Id
                FROM T005
                WHERE T005_C004_Id = '{$empresa}'
                    AND T005_Id_Pedido_Externo = '{$externo}'
                ORDER BY T005_Id DESC
                LIMIT 1
SQL;
            $consultaT005 = mysql_query($sqlT005Inserido);
            $T005 = $consultaT005 ? mysql_fetch_assoc($consultaT005) : false;
            $t005 = $T005 ? (int) $T005['T005_Id'] : 0;
            if ($t005 <= 0) {
                throw new \Exception('O cabeçalho do pedido foi gravado, mas não pôde ser confirmado.');
            }

            $sqlT005A = <<<SQL
                INSERT INTO T005A (
                    T005A_T005_Id,
                    T005A_Flag_Ratear_Frete_Custo
                ) VALUES (
                    '{$t005}',
                    'N'
                )
SQL;
            if (!mysql_query($sqlT005A)) {
                throw new \Exception('Não foi possível preparar o pedido para inclusão de itens.');
            }
            $etapa = INT_ETAPA_CRIACAO_ITENS;
            foreach ($pedido['itens'] as $item) {
                $produto = $this->localizarProduto($item['codigo_referencia']);
                if (empty($produto['D009_Id'])) {
                    $produto = isset($g['C031']['IsetModoTesteImportacao']) && $g['C031']['IsetModoTesteImportacao'] === 'S'
                        ? $this->localizarProdutoTeste() : array();
                    if (empty($produto['D009_Id'])) {
                        throw new \Exception('Produto de teste não localizado.');
                    }
                }
                $this->incluirItemIset($t005, $produto, $item, $d022, $data);
                $importados++;
            }
            $etapa=INT_ETAPA_CONFERENCIA_ITENS;$q=mysql_query("SELECT COUNT(*) AS quantidade FROM T006 WHERE T006_T005_Id='{$t005}'");$r=$q?mysql_fetch_assoc($q):false;if(!$r||(int)$r['quantidade']!==$recebidos)throw new \Exception('A quantidade de itens gravados não corresponde à quantidade recebida da ISET.');

            $etapa = INT_ETAPA_CRIACAO_PAGAMENTO;
            $pagamentoImportado = $this->incluirPagamentoIset($t005, $pedido);

            if ($idempotencia) {
                $idempotencia->concluirNaTransacao($controleIdempotenciaId, $t005);
            }

            $etapa=INT_ETAPA_COMMIT;if(!mysql_query('COMMIT'))throw new \Exception('Não foi possível concluir a importação do pedido.');$iniciada=false;$pedidoConfirmado=true;
            $logProcessamento = array_merge($base, array('itensImportados'=>$importados, 'clienteCriado'=>$clienteCriado, 'pagamento'=>$pagamentoImportado, 'usuarioErpId'=>(int)$g['usuarioAtual'], 'versaoIntegracao'=>'1.0', 'tempoImportacaoMs'=>(int)round((microtime(true)-$inicio)*1000)));
            $processamento = (new ISET_ALIQUOTAS())->iniciar((int) $g['empresaAtual'], $t005, $id, $operacao, $logProcessamento);
            return array('pedidoId' => $t005, 'pagamento' => $pagamentoImportado, 'processamento' => $processamento);
        } catch (\Throwable $e) {
            if ($iniciada) {
                mysql_query('ROLLBACK');
                $iniciada = false;
            }

            if ($e instanceof iset_idempotencia_conflito) {
                throw $e;
            }

            $detalhes = array(
                'erroDetalhado' => integracoesMascararTextoSensivel($e->getMessage()),
                'excecao' => integracoesDiagnosticoExcecao($e),
            );

            if ($pedidoConfirmado) {
                $detalhes['pedidoErpId'] = (int) $t005;
                $erro('Pedido gravado, mas a finalização da importação falhou: ' . $e->getMessage(), $detalhes);
                throw new \Exception('O pedido ' . $t005 . ' foi gravado, mas a finalização falhou. ' . $e->getMessage());
            }

            if ($etapa === INT_ETAPA_CRIACAO_ITENS && isset($item['codigo_referencia'])) {
                $detalhes['sku'] = (string) $item['codigo_referencia'];
            }

            $erro($e->getMessage(), $detalhes);
            throw $e;
        }
    }

}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_IMPORTADOR');
}

if (!class_exists(__NAMESPACE__ . '\\iset_importador', false)) {
    class_alias(__NAMESPACE__ . '\\iset_importador_', __NAMESPACE__ . '\\iset_importador');
}
