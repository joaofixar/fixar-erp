<?php
namespace hardness;

definirClasse('IMP006');
class IMP006_
{
    private $apiDuimp = null;
    private $T075_Id = 0;
    private $numeroDuimp = '';
    private $versaoDuimp = 0;
    private $modeloCalculos = '';
    private $T075_D024_Id = 0;
    private $cabecalhoDuimp = array();
    private $itensListaDuimp = array();
    private $itensDuimp = array();
    private $mapaItemAdicao = array();
    private $mapaItemSequenciaAdicao = array();
    private $produtosCatalogo = array();
    private $produtosCatalogoPorChave = array();
    private $dadosProdutoPorCodigoInterno = array();
    private $aliquotasAuxiliares = array();
    private $authorizationToken = '';
    private $csrfToken = '';
    private $dataConsulta = '';
    private $snapshotProcessoId = '';
    private $tudoOk = true;
    private $msgErro = '';

    public function isTudoOk()
    {
        return $this->tudoOk;
    }

    public function getMsgErro()
    {
        return $this->msgErro;
    }

    public function getNumeroDuimp()
    {
        return $this->numeroDuimp;
    }

    public function getVersaoDuimp()
    {
        return $this->versaoDuimp;
    }

    public function getCabecalhoDuimp()
    {
        return $this->cabecalhoDuimp;
    }

    public function getItensListaDuimp()
    {
        return $this->itensListaDuimp;
    }

    public function getItensDuimp()
    {
        return $this->itensDuimp;
    }

    public function getMapaItemAdicao()
    {
        return $this->mapaItemAdicao;
    }

    public function getProdutosCatalogo()
    {
        return $this->produtosCatalogo;
    }

    public function importar($T075_Id)
    {
        $preparo = $this->prepararImportacao($T075_Id);
        if (!is_array($preparo)) {
            return $this->msgErro;
        }

        $processoId = $preparo['processoId'];
        $retorno = $this->gravarCabecalho($processoId);
        if ($retorno !== true) {
            $this->limparImportacao($processoId);
            return $this->msgErro;
        }

        $totalItens = isset($preparo['totalItens']) ? (int) $preparo['totalItens'] : 0;
        $limite = $totalItens > 0 ? $totalItens : 1;
        $retorno = $this->gravarItensLote($processoId, 0, $limite);
        if (!is_array($retorno)) {
            $this->limparImportacao($processoId);
            return $this->msgErro;
        }

        $retorno = $this->finalizarImportacao($processoId);
        if ($retorno !== true) {
            $this->limparImportacao($processoId);
            return $this->msgErro;
        }

        return true;
    }

    public function prepararImportacao($T075_Id)
    {
        $preparo = $this->prepararCabecalhoConsulta($T075_Id);
        if (!is_array($preparo)) {
            return $this->msgErro;
        }

        $processoId = $preparo['processoId'];

        $retorno = $this->carregarItensConsulta($processoId);
        if (!is_array($retorno)) {
            $this->limparImportacao($processoId);
            return $this->msgErro;
        }

        $retorno = $this->carregarCatalogoConsulta($processoId);
        if (!is_array($retorno)) {
            $this->limparImportacao($processoId);
            return $this->msgErro;
        }

        return $retorno;
    }

    public function prepararCabecalhoConsulta($T075_Id)
    {
        $this->resetarEstado();
        $this->T075_Id = (int) $T075_Id;
        $this->dataConsulta = date('Y-m-d H:i:s');

        if ($this->T075_Id <= 0) {
            return $this->deuErro('Importação DUIMP inválida: T075_Id não informado.');
        }

        require_once('bibliotecas/webservices/duimp/apiDuimp.php');

        $this->apiDuimp = new ApiDuimp();
        if (!$this->apiDuimp->isTudoOk()) {
            return $this->deuErro($this->apiDuimp->getMsgErro());
        }

        if ($this->carregarCabecalhoLegado() !== true) {
            return $this->msgErro;
        }

        if ($this->carregarNumeroDuimp() !== true) {
            return $this->msgErro;
        }

        if ($this->consultarVersaoVigente() !== true) {
            return $this->msgErro;
        }

        if ($this->consultarCabecalho() !== true) {
            return $this->msgErro;
        }

        $this->itensListaDuimp = $this->extrairListaItensCabecalho($this->cabecalhoDuimp);
        $this->snapshotProcessoId = $this->gerarProcessoId();
        if ($this->salvarSnapshot() !== true) {
            return $this->msgErro;
        }

        return array(
            'processoId' => $this->snapshotProcessoId,
            'numero' => $this->numeroDuimp,
            'versao' => $this->versaoDuimp,
            'totalItensLista' => count($this->itensListaDuimp),
        );
    }

    public function carregarItensConsulta($processoId)
    {
        if ($this->carregarSnapshot($processoId) !== true) {
            return $this->msgErro;
        }

        if ($this->inicializarApiDuimp() !== true) {
            return $this->msgErro;
        }

        if ($this->consultarItens() !== true) {
            return $this->msgErro;
        }

        if ($this->salvarSnapshot() !== true) {
            return $this->msgErro;
        }

        $totalItens = count($this->itensDuimp);
        $limiteLote = $this->obterLimiteLoteItens();
        return array(
            'totalItens' => $totalItens,
            'limiteLote' => $limiteLote,
            'totalLotes' => $limiteLote > 0 ? (int) ceil($totalItens / $limiteLote) : 0,
        );
    }

    public function carregarCatalogoConsulta($processoId)
    {
        if ($this->carregarSnapshot($processoId) !== true) {
            return $this->msgErro;
        }

        if ($this->inicializarApiDuimp() !== true) {
            return $this->msgErro;
        }

        if ($this->consultarCatalogoProdutos() !== true) {
            return $this->msgErro;
        }

        if ($this->carregarAliquotasAuxiliares() !== true) {
            return $this->msgErro;
        }

        if ($this->salvarSnapshot() !== true) {
            return $this->msgErro;
        }

        $totalItens = count($this->itensDuimp);
        $limiteLote = $this->obterLimiteLoteItens();
        return array(
            'processoId' => $processoId,
            'totalItens' => $totalItens,
            'totalProdutos' => count($this->produtosCatalogoPorChave),
            'limiteLote' => $limiteLote,
            'totalLotes' => $limiteLote > 0 ? (int) ceil($totalItens / $limiteLote) : 0,
        );
    }

    public function gravarCabecalho($processoId)
    {
        if ($this->carregarSnapshot($processoId) !== true) {
            return $this->msgErro;
        }

        mysql_query('SET AUTOCOMMIT=0');
        mysql_query('START TRANSACTION');

        error_log('[DUIMP LEGADO] gravarCabecalho | processoId=' . $processoId . ' | T075_Id=' . $this->T075_Id);
        if ($this->atualizarCabecalhoT075() !== true) {
            mysql_query('ROLLBACK');
            mysql_query('SET AUTOCOMMIT=1');
            return $this->msgErro;
        }

        mysql_query('COMMIT');
        mysql_query('SET AUTOCOMMIT=1');

        return true;
    }

    public function processarItensLote($processoId, $offset = 0, $limite = 5)
    {
        if ($this->carregarSnapshot($processoId) !== true) {
            return $this->msgErro;
        }

        if ($this->inicializarApiDuimp() !== true) {
            return $this->msgErro;
        }

        if (empty($this->aliquotasAuxiliares) && $this->carregarAliquotasAuxiliares() !== true) {
            return $this->msgErro;
        }

        $this->itensListaDuimp = $this->extrairListaItensCabecalho($this->cabecalhoDuimp);
        if (empty($this->itensListaDuimp)) {
            return $this->deuErro('Nenhum item da DUIMP foi encontrado para importação.');
        }

        $offset = (int) $offset;
        $limite = (int) $limite;
        if ($limite <= 0) {
            $limite = $this->obterLimiteLoteItens();
        }

        $itensLote = array_slice($this->itensListaDuimp, $offset, $limite);
        $itensNovos = array();

        foreach ($itensLote as $itemLista) {
            $numeroItem = $this->obterNumeroItemLista($itemLista);
            if ($numeroItem <= 0) {
                continue;
            }

            if (!isset($this->itensDuimp[$numeroItem])) {
                $itemCompleto = $this->apiDuimp->consultarItemDuimp($this->numeroDuimp, $this->versaoDuimp, $numeroItem);
                if ($itemCompleto === false) {
                    return $this->deuErro($this->apiDuimp->getMsgErro());
                }

                if (!is_array($itemCompleto) || empty($itemCompleto)) {
                    return $this->deuErro('A API DUIMP não retornou os dados completos do item ' . $numeroItem . '.');
                }

                $this->itensDuimp[$numeroItem] = $itemCompleto;
                $this->mapaItemAdicao[$numeroItem] = $this->obterNumeroAdicaoItem($itemCompleto, $itemLista);
            }

            if ($this->consultarCatalogoProdutoItem($this->itensDuimp[$numeroItem]) !== true) {
                return $this->msgErro;
            }

            $itensNovos[$numeroItem] = $this->itensDuimp[$numeroItem];
        }

        ksort($this->itensDuimp);
        ksort($this->mapaItemAdicao);
        $this->montarMapaSequenciaAdicao();

        mysql_query('SET AUTOCOMMIT=0');
        mysql_query('START TRANSACTION');

        foreach ($itensNovos as $numeroItem => $itemDuimp) {
            if ($this->persistirItemT055($numeroItem, $itemDuimp) !== true) {
                mysql_query('ROLLBACK');
                mysql_query('SET AUTOCOMMIT=1');
                return $this->msgErro;
            }
        }

        mysql_query('COMMIT');
        mysql_query('SET AUTOCOMMIT=1');

        if ($this->salvarSnapshot() !== true) {
            return $this->msgErro;
        }

        $processados = min($offset + count($itensLote), count($this->itensListaDuimp));
        $ultimoNumeroItem = 0;
        if (!empty($itensLote)) {
            $ultimoNumeroItem = $this->obterNumeroItemLista(end($itensLote));
        }

        return array(
            'totalItens' => count($this->itensListaDuimp),
            'processados' => $processados,
            'restantes' => max(count($this->itensListaDuimp) - $processados, 0),
            'finalizado' => ($processados >= count($this->itensListaDuimp)),
            'totalProdutos' => count($this->produtosCatalogoPorChave),
            'ultimoNumeroItem' => (int) $ultimoNumeroItem,
        );
    }

    public function gravarItensLote($processoId, $offset = 0, $limite = 20)
    {
        if ($this->carregarSnapshot($processoId) !== true) {
            return $this->msgErro;
        }

        $offset = (int) $offset;
        $limite = (int) $limite;
        if ($limite <= 0) {
            $limite = $this->obterLimiteLoteItens();
        }

        $itensLote = array_slice($this->itensDuimp, $offset, $limite, true);
        mysql_query('SET AUTOCOMMIT=0');
        mysql_query('START TRANSACTION');

        foreach ($itensLote as $numeroItem => $itemDuimp) {
            if ($this->persistirItemT055($numeroItem, $itemDuimp) !== true) {
                mysql_query('ROLLBACK');
                mysql_query('SET AUTOCOMMIT=1');
                return $this->msgErro;
            }
        }

        mysql_query('COMMIT');
        mysql_query('SET AUTOCOMMIT=1');

        $processados = min($offset + count($itensLote), count($this->itensDuimp));
        return array(
            'totalItens' => count($this->itensDuimp),
            'processados' => $processados,
            'restantes' => max(count($this->itensDuimp) - $processados, 0),
            'finalizado' => ($processados >= count($this->itensDuimp)),
        );
    }

    public function finalizarImportacao($processoId)
    {
        if ($this->carregarSnapshot($processoId) !== true) {
            return $this->msgErro;
        }

        mysql_query('SET AUTOCOMMIT=0');
        mysql_query('START TRANSACTION');

        if ($this->modeloCalculos === '2') {
            if ($this->atualizarDespesasT168Modelo2() !== true) {
                mysql_query('ROLLBACK');
                mysql_query('SET AUTOCOMMIT=1');
                return $this->msgErro;
            }
        }

        if ($this->recalcularDI() !== true) {
            mysql_query('ROLLBACK');
            mysql_query('SET AUTOCOMMIT=1');
            return $this->msgErro;
        }

        mysql_query('COMMIT');
        mysql_query('SET AUTOCOMMIT=1');
        $this->limparImportacao($processoId);
        return true;
    }

    public function limparImportacao($processoId)
    {
        $arquivo = $this->getSnapshotPath($processoId);
        if (file_exists($arquivo)) {
            @unlink($arquivo);
        }

        return true;
    }

    private function exportarSnapshot()
    {
        return array(
            'T075_Id' => $this->T075_Id,
            'numeroDuimp' => $this->numeroDuimp,
            'versaoDuimp' => $this->versaoDuimp,
            'modeloCalculos' => $this->modeloCalculos,
            'T075_D024_Id' => $this->T075_D024_Id,
            'cabecalhoDuimp' => $this->cabecalhoDuimp,
            'itensListaDuimp' => $this->itensListaDuimp,
            'itensDuimp' => $this->itensDuimp,
            'mapaItemAdicao' => $this->mapaItemAdicao,
            'mapaItemSequenciaAdicao' => $this->mapaItemSequenciaAdicao,
            'produtosCatalogo' => $this->produtosCatalogo,
            'produtosCatalogoPorChave' => $this->produtosCatalogoPorChave,
            'aliquotasAuxiliares' => $this->aliquotasAuxiliares,
            'authorizationToken' => $this->obterAuthorizationTokenSnapshot(),
            'csrfToken' => $this->obterCsrfTokenSnapshot(),
            'dataConsulta' => $this->dataConsulta,
            'snapshotProcessoId' => $this->snapshotProcessoId,
        );
    }

    private function importarSnapshot($snapshot)
    {
        $this->T075_Id = isset($snapshot['T075_Id']) ? (int) $snapshot['T075_Id'] : 0;
        $this->numeroDuimp = isset($snapshot['numeroDuimp']) ? (string) $snapshot['numeroDuimp'] : '';
        $this->versaoDuimp = isset($snapshot['versaoDuimp']) ? (int) $snapshot['versaoDuimp'] : 0;
        $this->modeloCalculos = isset($snapshot['modeloCalculos']) ? (string) $snapshot['modeloCalculos'] : '';
        $this->T075_D024_Id = isset($snapshot['T075_D024_Id']) ? (int) $snapshot['T075_D024_Id'] : 0;
        $this->cabecalhoDuimp = isset($snapshot['cabecalhoDuimp']) && is_array($snapshot['cabecalhoDuimp']) ? $snapshot['cabecalhoDuimp'] : array();
        $this->itensListaDuimp = isset($snapshot['itensListaDuimp']) && is_array($snapshot['itensListaDuimp']) ? $snapshot['itensListaDuimp'] : array();
        $this->itensDuimp = isset($snapshot['itensDuimp']) && is_array($snapshot['itensDuimp']) ? $snapshot['itensDuimp'] : array();
        $this->mapaItemAdicao = isset($snapshot['mapaItemAdicao']) && is_array($snapshot['mapaItemAdicao']) ? $snapshot['mapaItemAdicao'] : array();
        $this->mapaItemSequenciaAdicao = isset($snapshot['mapaItemSequenciaAdicao']) && is_array($snapshot['mapaItemSequenciaAdicao']) ? $snapshot['mapaItemSequenciaAdicao'] : array();
        $this->produtosCatalogo = isset($snapshot['produtosCatalogo']) && is_array($snapshot['produtosCatalogo']) ? $snapshot['produtosCatalogo'] : array();
        $this->produtosCatalogoPorChave = isset($snapshot['produtosCatalogoPorChave']) && is_array($snapshot['produtosCatalogoPorChave']) ? $snapshot['produtosCatalogoPorChave'] : array();
        $this->aliquotasAuxiliares = isset($snapshot['aliquotasAuxiliares']) && is_array($snapshot['aliquotasAuxiliares']) ? $snapshot['aliquotasAuxiliares'] : array();
        $this->authorizationToken = isset($snapshot['authorizationToken']) ? (string) $snapshot['authorizationToken'] : '';
        $this->csrfToken = isset($snapshot['csrfToken']) ? (string) $snapshot['csrfToken'] : '';
        $this->dataConsulta = isset($snapshot['dataConsulta']) ? (string) $snapshot['dataConsulta'] : '';
        $this->snapshotProcessoId = isset($snapshot['snapshotProcessoId']) ? (string) $snapshot['snapshotProcessoId'] : '';
    }

    private function salvarSnapshot()
    {
        $arquivo = $this->getSnapshotPath($this->snapshotProcessoId);
        $retorno = @file_put_contents($arquivo, serialize($this->exportarSnapshot()));
        if ($retorno === false) {
            return $this->deuErro('Não foi possível gravar o snapshot temporário da importação DUIMP.');
        }

        return true;
    }

    private function carregarSnapshot($processoId)
    {
        $arquivo = $this->getSnapshotPath($processoId);
        if (!file_exists($arquivo)) {
            return $this->deuErro('Snapshot temporário da importação DUIMP não encontrado. Reinicie a consulta.');
        }

        $conteudo = @file_get_contents($arquivo);
        if ($conteudo === false || $conteudo === '') {
            return $this->deuErro('Não foi possível ler o snapshot temporário da importação DUIMP.');
        }

        $snapshot = @unserialize($conteudo);
        if (!is_array($snapshot)) {
            return $this->deuErro('Snapshot temporário da importação DUIMP está inválido. Reinicie a consulta.');
        }

        $this->resetarEstado();
        $this->importarSnapshot($snapshot);
        return true;
    }

    private function gerarProcessoId()
    {
        global $g;

        return md5('duimp|' . $g['usuarioAtual'] . '|' . $g['empresaAtual'] . '|' . $this->T075_Id . '|' . microtime(true));
    }

    private function getSnapshotPath($processoId)
    {
        global $g;

        $processoId = preg_replace('/[^a-zA-Z0-9]/', '', (string) $processoId);
        return '/tmp/duimp-importacao-' . $g['usuarioAtual'] . '-' . $g['empresaAtual'] . '-' . $processoId;
    }

    private function obterLimiteLoteItens()
    {
        return 10;
    }

    private function obterAuthorizationTokenSnapshot()
    {
        if ($this->apiDuimp instanceof ApiDuimp) {
            return (string) $this->apiDuimp->getAuthorizationToken();
        }

        return (string) $this->authorizationToken;
    }

    private function obterCsrfTokenSnapshot()
    {
        if ($this->apiDuimp instanceof ApiDuimp) {
            return (string) $this->apiDuimp->getCsrfToken();
        }

        return (string) $this->csrfToken;
    }

    private function inicializarApiDuimp()
    {
        if ($this->apiDuimp instanceof ApiDuimp) {
            return true;
        }

        require_once('bibliotecas/webservices/duimp/apiDuimp.php');

        $this->apiDuimp = new ApiDuimp();
        if (!$this->apiDuimp->isTudoOk()) {
            return $this->deuErro($this->apiDuimp->getMsgErro());
        }

        if ($this->authorizationToken !== '' && $this->csrfToken !== '') {
            $this->apiDuimp->setTokens($this->authorizationToken, $this->csrfToken);
        }

        return true;
    }

    private function carregarCabecalhoLegado()
    {
        $sql = "SELECT T075_Numero_DI, T075_Modelo_Calculos, T075_D024_Id FROM T075 WHERE T075_Id = '{$this->T075_Id}'";
        $query = mysql_query($sql);
        if (!$query) {
            return $this->deuErro('Erro ao consultar o cabeçalho da importação: ' . mysql_error());
        }

        $mT075 = mysql_fetch_array($query);
        if (!$mT075) {
            return $this->deuErro('Importação não encontrada para o T075 informado.');
        }

        $this->modeloCalculos = (string) $mT075['T075_Modelo_Calculos'];
        $this->T075_D024_Id = (int) $mT075['T075_D024_Id'];

        return true;
    }

    private function carregarNumeroDuimp()
    {
        $sql = "SELECT T075_Numero_DI FROM T075 WHERE T075_Id = '{$this->T075_Id}'";
        $query = mysql_query($sql);
        if (!$query) {
            return $this->deuErro('Erro ao consultar o cabeçalho da importação: ' . mysql_error());
        }

        $mT075 = mysql_fetch_array($query);
        if (!$mT075) {
            return $this->deuErro('Importação não encontrada para o T075 informado.');
        }

        $this->numeroDuimp = preg_replace('/[^A-Za-z0-9]/', '', (string) $mT075['T075_Numero_DI']);
        if (empty($this->numeroDuimp)) {
            return $this->deuErro('É necessário preencher o número da DUIMP no cabeçalho antes da importação.');
        }

        return true;
    }

    private function consultarVersaoVigente()
    {
        $retorno = $this->apiDuimp->consultarVersaoVigente($this->numeroDuimp);
        if ($retorno === false) {
            return $this->deuErro($this->apiDuimp->getMsgErro());
        }

        $versao = 0;
        if (is_array($retorno) && isset($retorno['versao']) && $retorno['versao'] !== '') {
            $versao = (int) $retorno['versao'];
        } elseif (is_array($retorno) && isset($retorno[0]) && is_array($retorno[0]) && isset($retorno[0]['versao']) && $retorno[0]['versao'] !== '') {
            $versao = (int) $retorno[0]['versao'];
        }

        $this->versaoDuimp = $versao;
        if ($this->versaoDuimp <= 0) {
            return $this->deuErro('Não foi possível obter a versão vigente da DUIMP.');
        }

        return true;
    }

    private function consultarCabecalho()
    {
        $retorno = $this->apiDuimp->consultarDuimp($this->numeroDuimp, $this->versaoDuimp);
        if ($retorno === false) {
            return $this->deuErro($this->apiDuimp->getMsgErro());
        }

        if (!is_array($retorno) || empty($retorno)) {
            return $this->deuErro('A API DUIMP não retornou dados do cabeçalho.');
        }

        if (!isset($retorno['identificacao']) || !is_array($retorno['identificacao'])) {
            return $this->deuErro('A API DUIMP retornou um cabeçalho inválido para a DUIMP informada.');
        }

        $this->cabecalhoDuimp = $retorno;

        if (isset($retorno['identificacao']['versao']) && (int) $retorno['identificacao']['versao'] > 0) {
            $versaoCabecalho = (int) $retorno['identificacao']['versao'];
            $this->versaoDuimp = $versaoCabecalho;
        }

        if ($this->versaoDuimp <= 0) {
            return $this->deuErro('Não foi possível identificar a versão da DUIMP após consultar o cabeçalho.');
        }

        return true;
    }

    private function consultarItens()
    {
        $this->itensListaDuimp = $this->extrairListaItensCabecalho($this->cabecalhoDuimp);
        if (empty($this->itensListaDuimp)) {
            $retorno = $this->apiDuimp->consultarItensDuimp($this->numeroDuimp, $this->versaoDuimp);
            if ($retorno === false) {
                return $this->deuErro($this->apiDuimp->getMsgErro());
            }
            $this->itensListaDuimp = $this->normalizarListaItens($retorno);
        }

        if (empty($this->itensListaDuimp)) {
            return $this->deuErro('Nenhum item da DUIMP foi encontrado para importação.');
        }

        foreach ($this->itensListaDuimp as $itemLista) {
            $numeroItem = $this->obterNumeroItemLista($itemLista);
            if ($numeroItem <= 0) {
                continue;
            }

            $itemCompleto = $this->apiDuimp->consultarItemDuimp($this->numeroDuimp, $this->versaoDuimp, $numeroItem);
            if ($itemCompleto === false) {
                return $this->deuErro($this->apiDuimp->getMsgErro());
            }

            if (!is_array($itemCompleto) || empty($itemCompleto)) {
                return $this->deuErro('A API DUIMP não retornou os dados completos do item ' . $numeroItem . '.');
            }

            $this->itensDuimp[$numeroItem] = $itemCompleto;
            $this->mapaItemAdicao[$numeroItem] = $this->obterNumeroAdicaoItem($itemCompleto, $itemLista);
        }

        if (empty($this->itensDuimp)) {
            return $this->deuErro('Não foi possível carregar os itens completos da DUIMP.');
        }

        $this->montarMapaSequenciaAdicao();
        ksort($this->itensDuimp);
        ksort($this->mapaItemAdicao);

        return true;
    }

    private function consultarCatalogoProdutos()
    {
        $produtosDistintos = array();

        foreach ($this->itensDuimp as $itemDuimp) {
            $produto = isset($itemDuimp['produto']) && is_array($itemDuimp['produto']) ? $itemDuimp['produto'] : array();
            $niResponsavel = isset($produto['niResponsavel']) ? trim((string) $produto['niResponsavel']) : '';
            $codigo = isset($produto['codigo']) ? trim((string) $produto['codigo']) : '';
            $versao = isset($produto['versao']) ? trim((string) $produto['versao']) : '';

            if ($niResponsavel === '' || $codigo === '' || $versao === '') {
                continue;
            }

            $chave = $this->montarChaveProduto($niResponsavel, $codigo, $versao);
            if (!isset($produtosDistintos[$chave])) {
                $produtosDistintos[$chave] = array(
                    'niResponsavel' => $niResponsavel,
                    'codigo' => $codigo,
                    'versao' => $versao,
                );
            }
        }

        foreach ($produtosDistintos as $chave => $produto) {
            $catalogo = $this->apiDuimp->consultarProduto($produto['niResponsavel'], $produto['codigo'], $produto['versao']);
            if ($catalogo === false) {
                return $this->deuErro($this->apiDuimp->getMsgErro());
            }

            $this->produtosCatalogo[$chave] = $catalogo;
            $this->produtosCatalogoPorChave[$chave] = $catalogo;
        }

        return true;
    }

    private function consultarCatalogoProdutoItem($itemDuimp)
    {
        $produto = isset($itemDuimp['produto']) && is_array($itemDuimp['produto']) ? $itemDuimp['produto'] : array();
        $niResponsavel = isset($produto['niResponsavel']) ? trim((string) $produto['niResponsavel']) : '';
        $codigo = isset($produto['codigo']) ? trim((string) $produto['codigo']) : '';
        $versao = isset($produto['versao']) ? trim((string) $produto['versao']) : '';

        if ($niResponsavel === '' || $codigo === '' || $versao === '') {
            return true;
        }

        $chave = $this->montarChaveProduto($niResponsavel, $codigo, $versao);
        if (isset($this->produtosCatalogoPorChave[$chave])) {
            return true;
        }

        $catalogo = $this->apiDuimp->consultarProduto($niResponsavel, $codigo, $versao);
        if ($catalogo === false) {
            return $this->deuErro($this->apiDuimp->getMsgErro());
        }

        $this->produtosCatalogo[$chave] = $catalogo;
        $this->produtosCatalogoPorChave[$chave] = $catalogo;
        return true;
    }

    private function carregarAliquotasAuxiliares()
    {
        require_once('bibliotecas/classes/IMP003.php');
        $IMP003 = new IMP003();
        $retorno = $IMP003->retornaAliquotas(0, false, false, $this->T075_Id);
        if (!is_array($retorno)) {
            return $this->deuErro('Não foi possível obter as alíquotas auxiliares da D.I. para a empresa atual.');
        }

        $this->aliquotasAuxiliares = $retorno;
        return true;
    }

    private function persistirImportacao()
    {
        mysql_query('SET AUTOCOMMIT=0');
        mysql_query('START TRANSACTION');

        error_log('[DUIMP LEGADO] gravarCabecalho | processoId=' . $processoId . ' | T075_Id=' . $this->T075_Id);
        if ($this->atualizarCabecalhoT075() !== true) {
            mysql_query('ROLLBACK');
            mysql_query('SET AUTOCOMMIT=1');
            return false;
        }

        foreach ($this->itensDuimp as $numeroItem => $itemDuimp) {
            if ($this->persistirItemT055($numeroItem, $itemDuimp) !== true) {
                mysql_query('ROLLBACK');
                mysql_query('SET AUTOCOMMIT=1');
                return false;
            }
        }

        error_log('[DUIMP LEGADO] finalizarImportacao | processoId=' . $processoId . ' | modelo=' . $this->modeloCalculos);
        if ($this->modeloCalculos === '2') {
            if ($this->atualizarDespesasT168Modelo2() !== true) {
                mysql_query('ROLLBACK');
                mysql_query('SET AUTOCOMMIT=1');
                return false;
            }
        }

        if ($this->recalcularDI() !== true) {
            mysql_query('ROLLBACK');
            mysql_query('SET AUTOCOMMIT=1');
            return false;
        }

        mysql_query('COMMIT');
        mysql_query('SET AUTOCOMMIT=1');
        return true;
    }

    private function atualizarCabecalhoT075()
    {
        $identificacao = isset($this->cabecalhoDuimp['identificacao']) && is_array($this->cabecalhoDuimp['identificacao']) ? $this->cabecalhoDuimp['identificacao'] : array();
        $situacao = isset($this->cabecalhoDuimp['situacao']) && is_array($this->cabecalhoDuimp['situacao']) ? $this->cabecalhoDuimp['situacao'] : array();

        $numero = isset($identificacao['numero']) ? trim((string) $identificacao['numero']) : $this->numeroDuimp;
        $cnpjImportador = '';
        if (isset($identificacao['importador']) && is_array($identificacao['importador']) && isset($identificacao['importador']['ni'])) {
            $cnpjImportador = $this->formatarCnpjCpf($identificacao['importador']['ni']);
        }

        $dataRegistro = $this->normalizarData(isset($identificacao['dataRegistro']) ? $identificacao['dataRegistro'] : '');
        $situacaoDuimp = isset($situacao['situacaoDuimp']) ? trim((string) $situacao['situacaoDuimp']) : '';

        $pesoLiquidoTotal = 0.0;
        $valorItensME = 0.0;
        $valorItensBRL = 0.0;
        $valorFreteTotal = 0.0;
        $valorSeguroTotal = 0.0;
        $valorIITotal = 0.0;
        $valorIPITotal = 0.0;
        $valorPISTotal = 0.0;
        $valorCOFINSTotal = 0.0;
        $valorSiscomexTotal = 0.0;

        foreach ($this->itensDuimp as $itemDuimp) {
            $pesoLiquidoTotal += $this->toFloat($this->obterValor($itemDuimp, array('mercadoria', 'pesoLiquido')));
            $valorItensME += $this->toFloat($this->obterValor($itemDuimp, array('condicaoVenda', 'valorMoedaNegociada')));
            $valorItensBRL += $this->toFloat($this->obterValor($itemDuimp, array('condicaoVenda', 'valorBRL')));
            $valorIITotal += $this->obterValorTributo($itemDuimp, 'II', 'valor');
            $valorIPITotal += $this->obterValorTributo($itemDuimp, 'IPI', 'valor');
            $valorPISTotal += $this->obterValorTributo($itemDuimp, 'PIS', 'valor');
            $valorCOFINSTotal += $this->obterValorTributo($itemDuimp, 'COFINS', 'valor');
        }

        $valorFreteCabecalho = $this->toFloat($this->obterValor($this->cabecalhoDuimp, array('carga', 'frete', 'valorMoedaNegociada')));
        $valorSeguroCabecalho = $this->toFloat($this->obterValor($this->cabecalhoDuimp, array('carga', 'seguro', 'valorMoedaNegociada')));
        if ($valorFreteCabecalho > 0) {
            $valorFreteTotal = $valorFreteCabecalho;
        }
        if ($valorSeguroCabecalho > 0) {
            $valorSeguroTotal = $valorSeguroCabecalho;
        }

        $valorIICabecalho = $this->obterValorTributoCabecalho('II');
        $valorIPICabecalho = $this->obterValorTributoCabecalho('IPI');
        $valorPISCabecalho = $this->obterValorTributoCabecalho('PIS');
        $valorCOFINSCabecalho = $this->obterValorTributoCabecalho('COFINS');
        $valorSiscomexCabecalho = $this->obterValorTributoCabecalho('TAXA_UTILIZACAO');
        if ($valorIICabecalho > 0) {
            $valorIITotal = $valorIICabecalho;
        }
        if ($valorIPICabecalho > 0 || $this->tributoCabecalhoExiste('IPI')) {
            $valorIPITotal = $valorIPICabecalho;
        }
        if ($valorPISCabecalho > 0) {
            $valorPISTotal = $valorPISCabecalho;
        }
        if ($valorCOFINSCabecalho > 0) {
            $valorCOFINSTotal = $valorCOFINSCabecalho;
        }
        if ($valorSiscomexCabecalho > 0 || $this->tributoCabecalhoExiste('TAXA_UTILIZACAO')) {
            $valorSiscomexTotal = $valorSiscomexCabecalho;
        }

        $sql = "UPDATE T075 SET "
             . "T075_Numero_DI = '" . $this->escape($numero) . "', "
             . "T075_CNPJ_Importador = '" . $this->escape($cnpjImportador) . "', "
             . "T075_Data_Emissao_DI = '" . $this->escape($dataRegistro) . "', "
             . "T075_Valor_Frete = '" . $this->decimal($valorFreteTotal, 4) . "', "
             . "T075_Valor_Seguro = '" . $this->decimal($valorSeguroTotal, 4) . "', "
             . "T075_Valor_SISCOMEX = '" . $this->decimal($valorSiscomexTotal, 4) . "', "
             . "T075_Valor_Imposto_Importacao = '" . $this->decimal($valorIITotal, 4) . "', "
             . "T075_Valor_IPI = '" . $this->decimal($valorIPITotal, 4) . "', "
             . "T075_Valor_PIS = '" . $this->decimal($valorPISTotal, 4) . "', "
             . "T075_Valor_COFINS = '" . $this->decimal($valorCOFINSTotal, 4) . "', "
             . "T075_Peso_Liquido = '" . $this->decimal($pesoLiquidoTotal, 5) . "', "
             . "T075_Duimp_Versao = '" . (int) $this->versaoDuimp . "', "
             . "T075_Duimp_Situacao = '" . $this->escape($situacaoDuimp) . "', "
             . "T075_Duimp_Data_Consulta = '" . $this->escape($this->dataConsulta) . "' "
             . "WHERE T075_Id = '{$this->T075_Id}'";

        mysql_query($sql);
        if (mysql_error()) {
            return $this->deuErro(mysql_error());
        }

        return true;
    }

    private function persistirItemT055($numeroItem, $itemDuimp)
    {
        global $g;

        $numeroItem = (int) $numeroItem;
        $produtoDuimp = isset($itemDuimp['produto']) && is_array($itemDuimp['produto']) ? $itemDuimp['produto'] : array();
        $mercadoria = isset($itemDuimp['mercadoria']) && is_array($itemDuimp['mercadoria']) ? $itemDuimp['mercadoria'] : array();
        $condicaoVenda = isset($itemDuimp['condicaoVenda']) && is_array($itemDuimp['condicaoVenda']) ? $itemDuimp['condicaoVenda'] : array();

        $codigoProdutoDuimp = isset($produtoDuimp['codigo']) ? trim((string) $produtoDuimp['codigo']) : '';
        $versaoProdutoDuimp = isset($produtoDuimp['versao']) ? trim((string) $produtoDuimp['versao']) : '';
        $niResponsavel = isset($produtoDuimp['niResponsavel']) ? trim((string) $produtoDuimp['niResponsavel']) : '';
        $descricaoFornecedor = isset($mercadoria['descricao']) ? trim((string) $mercadoria['descricao']) : '';
        $ncm = isset($produtoDuimp['ncm']) ? preg_replace('/[^0-9]/', '', (string) $produtoDuimp['ncm']) : '';
        $quantidade = $this->toFloat(isset($mercadoria['quantidadeComercial']) ? $mercadoria['quantidadeComercial'] : 0);
        $pesoLiquidoTotal = $this->toFloat(isset($mercadoria['pesoLiquido']) ? $mercadoria['pesoLiquido'] : 0);
        $valorTotalME = $this->toFloat(isset($condicaoVenda['valorMoedaNegociada']) ? $condicaoVenda['valorMoedaNegociada'] : 0);
        $valorTotalBRL = $this->toFloat(isset($condicaoVenda['valorBRL']) ? $condicaoVenda['valorBRL'] : 0);
        $freteTotalBRL = $this->toFloat($this->obterValor($itemDuimp, array('condicaoVenda', 'frete', 'valorBRL')));
        $seguroTotalBRL = $this->toFloat($this->obterValor($itemDuimp, array('condicaoVenda', 'seguro', 'valorBRL')));
        $pesoLiquidoUnitario = ($quantidade > 0) ? ($pesoLiquidoTotal / $quantidade) : 0;
        $valorUnitarioME = $this->toFloat(isset($mercadoria['valorUnitarioMoedaNegociada']) ? $mercadoria['valorUnitarioMoedaNegociada'] : 0);
        if ($valorUnitarioME <= 0 && $quantidade > 0) {
            $valorUnitarioME = ($valorTotalME / $quantidade);
        }
        $numeroAdicao = isset($this->mapaItemAdicao[$numeroItem]) ? (string) $this->mapaItemAdicao[$numeroItem] : '';
        $sequenciaAdicao = isset($this->mapaItemSequenciaAdicao[$numeroItem]) ? (int) $this->mapaItemSequenciaAdicao[$numeroItem] : $numeroItem;

        $codigoInterno = '';
        $catalogo = $this->obterCatalogoItem($niResponsavel, $codigoProdutoDuimp, $versaoProdutoDuimp);
        if (is_array($catalogo)) {
            $codigoInterno = $this->obterCodigoInternoCatalogo($catalogo);
        }

        $produtoInterno = $this->obterProdutoInternoPorCodigo($codigoInterno);
        $D005_Id = $this->obterD005IdPorNcm($ncm);

        $D009_Id = 0;
        $D037_Id = 0;
        $codigoProduto = $codigoInterno;
        $descricaoProduto = $descricaoFornecedor;
        $largura = 0;
        $altura = 0;
        $comprimento = 0;

        if (!empty($produtoInterno)) {
            $D009_Id = (int) $produtoInterno['D009_Id'];
            $D037_Id = (int) $produtoInterno['D037_Id'];
            if (!empty($produtoInterno['D001_Codigo_Produto'])) {
                $codigoProduto = $produtoInterno['D001_Codigo_Produto'];
            }
            if (!empty($produtoInterno['D001_Descricao_Produto'])) {
                $descricaoProduto = $produtoInterno['D001_Descricao_Produto'];
            }
            $largura = (float) $produtoInterno['D001A_Largura'];
            $altura = (float) $produtoInterno['D001A_Altura'];
            $comprimento = (float) $produtoInterno['D001A_Comprimento'];
        }

        $tributoII = $this->obterDadosTributo($itemDuimp, 'II');
        $tributoIPI = $this->obterDadosTributo($itemDuimp, 'IPI');
        $tributoPIS = $this->obterDadosTributo($itemDuimp, 'PIS');
        $tributoCOFINS = $this->obterDadosTributo($itemDuimp, 'COFINS');

        $T055_Id = $this->buscarT055ExistentePorNumeroItem($numeroItem);

        $camposT055 = array(
            "T055_T075_Id = '{$this->T075_Id}'",
            "T055_C004_Id = '{$g['empresaAtual']}'",
            "T055_D005_Id = '{$D005_Id}'",
            "T055_D037_Id = '{$D037_Id}'",
            "T055_D009_Id = '{$D009_Id}'",
            "T055_D024_Id = '{$this->T075_D024_Id}'",
            "T055_Valor_Adicao = '" . $this->escape($numeroAdicao) . "'",
            "T055_Codigo_Produto = '" . $this->escape($codigoProduto) . "'",
            "T055_Descricao_Produto = '" . $this->escape($descricaoProduto) . "'",
            "T055_Quantidade = '" . $this->decimal($quantidade, 5) . "'",
            "T055_Valor_Custo_Unitario = '" . $this->decimal($valorUnitarioME, 7) . "'",
            "T055_Adicao_Item = '{$sequenciaAdicao}'",
            "T055_Aliquota_COFINS = '" . $this->decimal($tributoCOFINS['aliquota'], 4) . "'",
            "T055_Aliquota_Imposto_Importacao = '" . $this->decimal($tributoII['aliquota'], 4) . "'",
            "T055_Aliquota_IPI = '" . $this->decimal($tributoIPI['aliquota'], 4) . "'",
            "T055_Aliquota_PIS = '" . $this->decimal($tributoPIS['aliquota'], 4) . "'",
            "T055_Peso_Kg_Liquido_Unitario = '" . $this->decimal($pesoLiquidoUnitario, 6) . "'",
            "T055_Peso_Kg_Liquido_Unitario_Fatura = '" . $this->decimal($pesoLiquidoUnitario, 6) . "'",
            "T055_Aliquota_ICMS = '" . $this->decimal($this->obterAliquotaAuxiliar('Aliquota_ICMS_Importacao'), 4) . "'",
            "T055_Aliquota_ICMS_Presumido = '" . $this->decimal($this->obterAliquotaAuxiliar('Aliquota_ICMS_Presumido_Importacao'), 4) . "'",
            "T055_Percentual_Reducao_ICMS = '" . $this->decimal($this->obterAliquotaAuxiliar('Percentual_Reducao_ICMS_Importacao'), 4) . "'",
            "T055_Percentual_Reducao_ICMS_Presumido = '" . $this->decimal($this->obterAliquotaAuxiliar('Percentual_Reducao_ICMS_Presumido_Importacao'), 4) . "'",
            "T055_Aliquota_ICMS_PIS_COFINS = '" . $this->decimal($this->obterAliquotaAuxiliar('Aliquota_ICMS_PIS_COFINS_Importacao'), 4) . "'",
            "T055_Aliquota_ICMS_Credito_Presumido = '" . $this->decimal($this->obterAliquotaAuxiliar('Aliquota_ICMS_Credito_Presumido_Importacao'), 4) . "'",
            "T055_Flag_Calculo_Manual = 'I'",
            "T055_Flag_Frete = 'S'",
            "T055_Valor_Total_Frete = '" . $this->decimal($freteTotalBRL, 6) . "'",
            "T055_Valor_Frete = '" . $this->decimal(($quantidade > 0 ? $freteTotalBRL / $quantidade : 0), 6) . "'",
            "T055_Valor_Total_Seguro = '" . $this->decimal($seguroTotalBRL, 6) . "'",
            "T055_Valor_Seguro = '" . $this->decimal(($quantidade > 0 ? $seguroTotalBRL / $quantidade : 0), 6) . "'",
            "T055_Valor_Base_Imposto_Importacao = '" . $this->decimal($tributoII['base'], 6) . "'",
            "T055_Valor_Imposto_Importacao = '" . $this->decimal($tributoII['valor'], 6) . "'",
            "T055_Valor_Base_IPI = '" . $this->decimal($tributoIPI['base'], 6) . "'",
            "T055_Valor_IPI = '" . $this->decimal($tributoIPI['valor'], 6) . "'",
            "T055_Valor_Base_PIS = '" . $this->decimal($tributoPIS['base'], 6) . "'",
            "T055_Valor_PIS = '" . $this->decimal($tributoPIS['valor'], 6) . "'",
            "T055_Valor_Base_COFINS = '" . $this->decimal($tributoCOFINS['base'], 6) . "'",
            "T055_Valor_COFINS = '" . $this->decimal($tributoCOFINS['valor'], 6) . "'",
            "T055_Valor_ICMS = '0.000000'",
            "T055_Valor_ICMS_Presumido = '0.000000'",
            "T055_Valor_ICMS_Pagar = '0.000000'",
            "T055_Valor_Extra = '0.000000'",
            "T055_Cotacao_CC = '0.0000'"
        );

        if ($T055_Id > 0) {
            $sql = "UPDATE T055 SET " . implode(', ', $camposT055) . " WHERE T055_Id = '{$T055_Id}'";
            mysql_query($sql);
            if (mysql_error()) {
                return $this->deuErro(mysql_error());
            }
        } else {
            $camposInsert = array();
            $valoresInsert = array();
            foreach ($camposT055 as $campoValor) {
                $partes = explode('=', $campoValor, 2);
                $camposInsert[] = trim($partes[0]);
                $valoresInsert[] = trim($partes[1]);
            }

            $sql = "INSERT INTO T055 (" . implode(', ', $camposInsert) . ") VALUES (" . implode(', ', $valoresInsert) . ")";
            mysql_query($sql);
            if (mysql_error()) {
                return $this->deuErro(mysql_error());
            }
            $T055_Id = $g['mysqlLastId'];
        }

        if ($this->persistirItemT055A($T055_Id, array(
            'descricaoFornecedor' => $descricaoFornecedor,
            'largura' => $largura,
            'altura' => $altura,
            'comprimento' => $comprimento,
            'numeroItem' => $numeroItem,
            'codigoProdutoDuimp' => $codigoProdutoDuimp,
            'versaoProdutoDuimp' => $versaoProdutoDuimp,
            'codigoInterno' => $codigoInterno,
        )) !== true) {
            return false;
        }

        return true;
    }

    private function persistirItemT055A($T055_Id, $dados)
    {
        $sqlBusca = "SELECT T055A_Id FROM T055A WHERE T055A_T055_Id = '{$T055_Id}'";
        $query = mysql_query($sqlBusca);
        if (!$query) {
            return $this->deuErro(mysql_error());
        }

        $mT055A = mysql_fetch_array($query);
        $campos = array(
            "T055A_Descricao_Produto_Fornecedor = '" . $this->escape($dados['descricaoFornecedor']) . "'",
            "T055A_Largura = '" . $this->decimal($dados['largura'], 6) . "'",
            "T055A_Altura = '" . $this->decimal($dados['altura'], 6) . "'",
            "T055A_Comprimento = '" . $this->decimal($dados['comprimento'], 6) . "'",
            "T055A_Duimp_Numero_Item = '" . (int) $dados['numeroItem'] . "'",
            "T055A_Duimp_Codigo_Produto = '" . $this->escape($dados['codigoProdutoDuimp']) . "'",
            "T055A_Duimp_Versao_Produto = '" . $this->escape($dados['versaoProdutoDuimp']) . "'",
            "T055A_Duimp_Codigo_Interno = '" . $this->escape($dados['codigoInterno']) . "'"
        );

        if ($mT055A && (int) $mT055A['T055A_Id'] > 0) {
            $sql = "UPDATE T055A SET " . implode(', ', $campos) . " WHERE T055A_T055_Id = '{$T055_Id}'";
        } else {
            $camposInsert = array('T055A_T055_Id');
            $valoresInsert = array("'{$T055_Id}'");
            foreach ($campos as $campoValor) {
                $partes = explode('=', $campoValor, 2);
                $camposInsert[] = trim($partes[0]);
                $valoresInsert[] = trim($partes[1]);
            }
            $sql = "INSERT INTO T055A (" . implode(', ', $camposInsert) . ") VALUES (" . implode(', ', $valoresInsert) . ")";
        }

        mysql_query($sql);
        if (mysql_error()) {
            return $this->deuErro(mysql_error());
        }

        return true;
    }

    private function atualizarDespesasT168Modelo2()
    {
        $valorFreteTotalReais = 0.0;
        $valorSeguroTotalReais = 0.0;
        $valorFreteTotalME = $this->toFloat($this->obterValor($this->cabecalhoDuimp, array('carga', 'frete', 'valorMoedaNegociada')));
        $valorSeguroTotalME = $this->toFloat($this->obterValor($this->cabecalhoDuimp, array('carga', 'seguro', 'valorMoedaNegociada')));
        $valorSiscomexTotal = $this->obterValorTributoCabecalho('TAXA_UTILIZACAO');

        foreach ($this->itensDuimp as $itemDuimp) {
            $valorFreteTotalReais += $this->toFloat($this->obterValor($itemDuimp, array('condicaoVenda', 'frete', 'valorBRL')));
            $valorSeguroTotalReais += $this->toFloat($this->obterValor($itemDuimp, array('condicaoVenda', 'seguro', 'valorBRL')));
        }

        $cotacaoFrete = ($valorFreteTotalME > 0 && $valorFreteTotalReais > 0) ? ($valorFreteTotalReais / $valorFreteTotalME) : 0;
        $cotacaoSeguro = ($valorSeguroTotalME > 0 && $valorSeguroTotalReais > 0) ? ($valorSeguroTotalReais / $valorSeguroTotalME) : 0;

        $retorno = $this->garantirDespesasMinimasT168Modelo2();
        if ($retorno !== true) {
            return $retorno;
        }

        $T168 = mysql_query("SELECT T168_Id, T168_Tipo_Despesa, T168_Flag_ME FROM T168 WHERE T168_T075_Id = '{$this->T075_Id}' AND T168_Tipo_Despesa != '1'");
        if (!$T168) {
            return $this->deuErro(mysql_error());
        }

        while ($mT168 = mysql_fetch_array($T168)) {
            $updates = array();
            if ($mT168['T168_Tipo_Despesa'] == '3') {
                $updates[] = "T168_Valor = '" . $this->decimal($valorSeguroTotalReais, 6) . "'";
                $updates[] = "T168_Valor_Confirmado = '" . $this->decimal($valorSeguroTotalReais, 6) . "'";
                $updates[] = "T168_Cotacao_ME = '0.0000'";
                $updates[] = "T168_Flag_ME = 'N'";
            }
            if ($mT168['T168_Tipo_Despesa'] == '4') {
                $updates[] = "T168_Valor = '" . $this->decimal($valorSiscomexTotal, 6) . "'";
                $updates[] = "T168_Valor_Confirmado = '" . $this->decimal($valorSiscomexTotal, 6) . "'";
                $updates[] = "T168_Cotacao_ME = '0.0000'";
                $updates[] = "T168_Flag_ME = 'N'";
            }
            if ($mT168['T168_Tipo_Despesa'] == '6') {
                $updates[] = "T168_Valor = '" . $this->decimal($valorFreteTotalReais, 6) . "'";
                $updates[] = "T168_Valor_Confirmado = '" . $this->decimal($valorFreteTotalReais, 6) . "'";
                $updates[] = "T168_Cotacao_ME = '0.0000'";
                $updates[] = "T168_Flag_ME = 'N'";
            }

            if (!empty($updates)) {
                mysql_query("UPDATE T168 SET " . implode(', ', $updates) . " WHERE T168_Id = '{$mT168['T168_Id']}'");
                if (mysql_error()) {
                    return $this->deuErro(mysql_error());
                }
            }
        }

        return true;
    }

    private function garantirDespesasMinimasT168Modelo2()
    {
        $tiposExistentes = array();
        $maiorOrdem = 0;

        $T168 = mysql_query("SELECT T168_Tipo_Despesa, T168_Ordem FROM T168 WHERE T168_T075_Id = '{$this->T075_Id}'");
        if (!$T168) {
            return $this->deuErro(mysql_error());
        }

        while ($mT168 = mysql_fetch_array($T168)) {
            $tipoDespesa = (string) $mT168['T168_Tipo_Despesa'];
            if ($tipoDespesa !== '') {
                $tiposExistentes[$tipoDespesa] = true;
            }
            $ordemAtual = (int) $mT168['T168_Ordem'];
            if ($ordemAtual > $maiorOrdem) {
                $maiorOrdem = $ordemAtual;
            }
        }

        $despesasMinimas = array(
            '3' => array(
                'descricao' => 'Seguro DUIMP',
                'flagMe' => 'N',
                'tipo' => '3',
            ),
            '4' => array(
                'descricao' => 'Siscomex DUIMP',
                'flagMe' => 'N',
                'tipo' => '2',
            ),
            '6' => array(
                'descricao' => 'Frete DUIMP',
                'flagMe' => 'N',
                'tipo' => '3',
            ),
        );

        foreach ($despesasMinimas as $tipoDespesa => $config) {
            if (isset($tiposExistentes[$tipoDespesa])) {
                continue;
            }

            $maiorOrdem++;
            mysql_query(
                "INSERT INTO T168 (
                    T168_T075_Id,
                    T168_D190_Id,
                    T168_Aliquota,
                    T168_Flag_Tipo,
                    T168_Descricao,
                    T168_Valor,
                    T168_Formula,
                    T168_Ordem,
                    T168_Flag_Soma_Base_IPI,
                    T168_Flag_Soma_Base_ICMS,
                    T168_Flag_Soma_Base_PIS,
                    T168_Flag_Soma_Base_COFINS,
                    T168_Flag_Soma_Base_II,
                    T168_Flag_Soma_Custo,
                    T168_Flag_Soma_Preco_NFe,
                    T168_Flag_Tipo_Rateio,
                    T168_Flag_Base_Rateio,
                    T168_Flag_ME,
                    T168_Tipo_Despesa,
                    T168_Tipo,
                    T168_Data
                ) VALUES (
                    '{$this->T075_Id}',
                    '0',
                    '0',
                    'V',
                    '" . $this->escape($config['descricao']) . "',
                    '0',
                    '',
                    '{$maiorOrdem}',
                    'N',
                    'N',
                    'N',
                    'N',
                    'N',
                    'S',
                    'S',
                    'C',
                    '1',
                    '{$config['flagMe']}',
                    '{$tipoDespesa}',
                    '{$config['tipo']}',
                    NOW()
                )"
            );

            if (mysql_error()) {
                return $this->deuErro(mysql_error());
            }
        }

        return true;
    }

    private function recalcularDI()
    {
        require_once('bibliotecas/classes/IMP003.php');
        $IMP003 = new IMP003();
        $retorno = $IMP003->Calcular($this->T075_Id);
        if ($retorno !== true) {
            return $this->deuErro((string) $retorno);
        }

        return true;
    }

    private function buscarT055ExistentePorNumeroItem($numeroItem)
    {
        $sql = "SELECT T055_Id FROM T055 LEFT JOIN T055A ON T055A_T055_Id = T055_Id WHERE T055_T075_Id = '{$this->T075_Id}' AND T055A_Duimp_Numero_Item = '" . (int) $numeroItem . "' LIMIT 1";
        $query = mysql_query($sql);
        if (!$query) {
            $this->deuErro(mysql_error());
            return 0;
        }

        $mT055 = mysql_fetch_array($query);
        return $mT055 ? (int) $mT055['T055_Id'] : 0;
    }

    private function obterProdutoInternoPorCodigo($codigoInterno)
    {
        global $g;

        $codigoInterno = trim((string) $codigoInterno);
        if ($codigoInterno === '') {
            return array();
        }

        if (isset($this->dadosProdutoPorCodigoInterno[$codigoInterno])) {
            return $this->dadosProdutoPorCodigoInterno[$codigoInterno];
        }

        $sql = "SELECT D009_Id,
                       D037_Id,
                       D001_Codigo_Produto,
                       D001_Descricao_Produto,
                       D001_Peso_Unitario_Kg,
                       D001A_Largura,
                       D001A_Altura,
                       D001A_Comprimento
                  FROM D009
             LEFT JOIN D049 ON D049_Id = D009_D049_Id
             LEFT JOIN D001 ON D001_Id = D049_D001_Id
             LEFT JOIN D001A ON D001A_D001_Id = D001_Id
             LEFT JOIN D037 ON D037_Id = D001_D037_Id
                 WHERE D001_Codigo_Produto = '" . $this->escape($codigoInterno) . "'
                   AND D009_C004_Id = '{$g['empresaAtual']}'
                 LIMIT 2";

        $query = mysql_query($sql);
        if (!$query) {
            $this->deuErro(mysql_error());
            return array();
        }

        $total = mysql_num_rows($query);
        if ($total !== 1) {
            $this->dadosProdutoPorCodigoInterno[$codigoInterno] = array();
            return array();
        }

        $produto = mysql_fetch_array($query);
        $this->dadosProdutoPorCodigoInterno[$codigoInterno] = $produto ? $produto : array();
        return $this->dadosProdutoPorCodigoInterno[$codigoInterno];
    }

    private function obterCatalogoItem($niResponsavel, $codigo, $versao)
    {
        $chave = $this->montarChaveProduto(trim((string) $niResponsavel), trim((string) $codigo), trim((string) $versao));
        return isset($this->produtosCatalogoPorChave[$chave]) ? $this->produtosCatalogoPorChave[$chave] : array();
    }

    private function obterCodigoInternoCatalogo($catalogo)
    {
        if (isset($catalogo[0]) && is_array($catalogo[0])) {
            $catalogo = $catalogo[0];
        }

        if (isset($catalogo['codigosInterno']) && is_array($catalogo['codigosInterno'])) {
            foreach ($catalogo['codigosInterno'] as $codigoInterno) {
                if (trim((string) $codigoInterno) !== '') {
                    return trim((string) $codigoInterno);
                }
            }
        }

        return '';
    }

    private function obterD005IdPorNcm($ncm)
    {
        $ncm = preg_replace('/[^0-9]/', '', (string) $ncm);
        if ($ncm === '') {
            return 0;
        }

        $query = mysql_query("SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal = '" . $this->escape($ncm) . "' LIMIT 1");
        if (!$query) {
            $this->deuErro(mysql_error());
            return 0;
        }

        $mD005 = mysql_fetch_array($query);
        return $mD005 ? (int) $mD005['D005_Id'] : 0;
    }

    private function obterDadosTributo($itemDuimp, $tipo)
    {
        $retorno = array('base' => 0.0, 'aliquota' => 0.0, 'valor' => 0.0);
        if (!isset($itemDuimp['tributos']['tributosCalculados']) || !is_array($itemDuimp['tributos']['tributosCalculados'])) {
            return $retorno;
        }

        foreach ($itemDuimp['tributos']['tributosCalculados'] as $tributo) {
            if (!is_array($tributo) || !isset($tributo['tipo']) || strtoupper((string) $tributo['tipo']) !== strtoupper((string) $tipo)) {
                continue;
            }

            $retorno['base'] = $this->toFloat($this->obterValor($tributo, array('memoriaCalculo', 'baseCalculoBRL')));
            $retorno['aliquota'] = $this->toFloat($this->obterValor($tributo, array('memoriaCalculo', 'valorAliquota')));
            $retorno['valor'] = $this->toFloat($this->obterValor($tributo, array('valoresBRL', 'calculado')));
            return $retorno;
        }

        return $retorno;
    }

    private function obterValorTributo($itemDuimp, $tipo, $campo)
    {
        $dados = $this->obterDadosTributo($itemDuimp, $tipo);
        if ($campo === 'base') {
            return $dados['base'];
        }
        if ($campo === 'aliquota') {
            return $dados['aliquota'];
        }
        return $dados['valor'];
    }

    private function obterValorTributoCabecalho($tipo)
    {
        if (!isset($this->cabecalhoDuimp['tributos']['tributosCalculados']) || !is_array($this->cabecalhoDuimp['tributos']['tributosCalculados'])) {
            return 0.0;
        }

        foreach ($this->cabecalhoDuimp['tributos']['tributosCalculados'] as $tributo) {
            if (!is_array($tributo) || !isset($tributo['tipo']) || strtoupper((string) $tributo['tipo']) !== strtoupper((string) $tipo)) {
                continue;
            }

            if (strtoupper((string) $tipo) === 'TAXA_UTILIZACAO') {
                $valor = $this->toFloat($this->obterValor($tributo, array('valoresBRL', 'aRecolher')));
                if ($valor > 0) {
                    return $valor;
                }

                $valor = $this->toFloat($this->obterValor($tributo, array('valoresBRL', 'recolhido')));
                if ($valor > 0) {
                    return $valor;
                }
            }

            return $this->toFloat($this->obterValor($tributo, array('valoresBRL', 'calculado')));
        }

        return 0.0;
    }

    private function tributoCabecalhoExiste($tipo)
    {
        if (!isset($this->cabecalhoDuimp['tributos']['tributosCalculados']) || !is_array($this->cabecalhoDuimp['tributos']['tributosCalculados'])) {
            return false;
        }

        foreach ($this->cabecalhoDuimp['tributos']['tributosCalculados'] as $tributo) {
            if (is_array($tributo) && isset($tributo['tipo']) && strtoupper((string) $tributo['tipo']) === strtoupper((string) $tipo)) {
                return true;
            }
        }

        return false;
    }

    private function obterAliquotaAuxiliar($campo)
    {
        return isset($this->aliquotasAuxiliares[$campo]) ? $this->toFloat($this->aliquotasAuxiliares[$campo]) : 0;
    }

    private function montarMapaSequenciaAdicao()
    {
        $this->mapaItemSequenciaAdicao = array();
        $contadorPorAdicao = array();

        foreach ($this->itensDuimp as $numeroItem => $itemDuimp) {
            $numeroAdicao = isset($this->mapaItemAdicao[$numeroItem]) ? (string) $this->mapaItemAdicao[$numeroItem] : '';
            if (!isset($contadorPorAdicao[$numeroAdicao])) {
                $contadorPorAdicao[$numeroAdicao] = 0;
            }
            $contadorPorAdicao[$numeroAdicao]++;
            $this->mapaItemSequenciaAdicao[$numeroItem] = $contadorPorAdicao[$numeroAdicao];
        }
    }

    private function extrairListaItensCabecalho($cabecalho)
    {
        if (!is_array($cabecalho)) {
            return array();
        }

        if (isset($cabecalho['itens']) && is_array($cabecalho['itens'])) {
            return $this->normalizarListaItens($cabecalho['itens']);
        }

        if (isset($cabecalho['itensDuimp']) && is_array($cabecalho['itensDuimp'])) {
            return $this->normalizarListaItens($cabecalho['itensDuimp']);
        }

        return array();
    }

    private function normalizarListaItens($lista)
    {
        if (!is_array($lista)) {
            return array();
        }

        if (isset($lista['itens']) && is_array($lista['itens'])) {
            return $this->normalizarListaItens($lista['itens']);
        }

        if (isset($lista['result']) && is_array($lista['result'])) {
            return $this->normalizarListaItens($lista['result']);
        }

        if (isset($lista['identificacao']) && is_array($lista['identificacao'])) {
            return array($lista);
        }

        $itens = array();
        foreach ($lista as $item) {
            if (is_array($item)) {
                $itens[] = $item;
            }
        }

        return $itens;
    }

    private function obterNumeroItemLista($itemLista)
    {
        $campos = array('numeroItem', 'numero', 'item', 'ordem', 'indice');
        foreach ($campos as $campo) {
            if (isset($itemLista[$campo]) && (int) $itemLista[$campo] > 0) {
                return (int) $itemLista[$campo];
            }
        }

        if (isset($itemLista['identificacao']) && is_array($itemLista['identificacao'])) {
            foreach ($campos as $campo) {
                if (isset($itemLista['identificacao'][$campo]) && (int) $itemLista['identificacao'][$campo] > 0) {
                    return (int) $itemLista['identificacao'][$campo];
                }
            }
        }

        if (isset($itemLista['link']) && is_string($itemLista['link'])) {
            if (preg_match('/\/itens\/(\d+)(?:\?.*)?$/', $itemLista['link'], $m)) {
                return (int) $m[1];
            }
        }

        return 0;
    }

    private function obterNumeroAdicaoItem($itemCompleto, $itemLista)
    {
        $fontes = array($itemCompleto, $itemLista);
        $campos = array('numeroAdicao', 'adicao', 'numeroSequencialAdicao');

        foreach ($fontes as $fonte) {
            if (!is_array($fonte)) {
                continue;
            }

            foreach ($campos as $campo) {
                if (isset($fonte[$campo]) && trim((string) $fonte[$campo]) !== '') {
                    return (string) $fonte[$campo];
                }
            }

            if (isset($fonte['adicao']) && is_array($fonte['adicao'])) {
                foreach ($campos as $campo) {
                    if (isset($fonte['adicao'][$campo]) && trim((string) $fonte['adicao'][$campo]) !== '') {
                        return (string) $fonte['adicao'][$campo];
                    }
                }
            }
        }

        if (isset($this->cabecalhoDuimp['adicoes']) && is_array($this->cabecalhoDuimp['adicoes'])) {
            $numeroItem = $this->obterNumeroItemLista($itemLista);
            foreach ($this->cabecalhoDuimp['adicoes'] as $adicao) {
                if (!is_array($adicao) || !isset($adicao['numero']) || !isset($adicao['itens']) || !is_array($adicao['itens'])) {
                    continue;
                }
                foreach ($adicao['itens'] as $itemAdicao) {
                    if ((int) $itemAdicao === (int) $numeroItem) {
                        return (string) $adicao['numero'];
                    }
                }
            }
        }

        return '';
    }

    private function montarChaveProduto($niResponsavel, $codigo, $versao)
    {
        return $niResponsavel . '|' . $codigo . '|' . $versao;
    }

    private function obterValor($array, $caminho)
    {
        $atual = $array;
        foreach ($caminho as $indice) {
            if (!is_array($atual) || !array_key_exists($indice, $atual)) {
                return null;
            }
            $atual = $atual[$indice];
        }
        return $atual;
    }

    private function toFloat($valor)
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }
        if (is_numeric($valor)) {
            return (float) $valor;
        }
        $valor = str_replace('.', '', (string) $valor);
        $valor = str_replace(',', '.', $valor);
        return is_numeric($valor) ? (float) $valor : 0.0;
    }

    private function decimal($valor, $casas = 4)
    {
        return number_format((float) $valor, (int) $casas, '.', '');
    }

    private function normalizarData($valor)
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return '';
        }
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $valor, $m)) {
            return $m[1] . '-' . $m[2] . '-' . $m[3];
        }
        return $valor;
    }

    private function formatarCnpjCpf($valor)
    {
        $valor = preg_replace('/\D/', '', (string) $valor);
        if (strlen($valor) === 14) {
            return substr($valor, 0, 2) . '.' . substr($valor, 2, 3) . '.' . substr($valor, 5, 3) . '/' . substr($valor, 8, 4) . '-' . substr($valor, 12, 2);
        }
        if (strlen($valor) === 11) {
            return substr($valor, 0, 3) . '.' . substr($valor, 3, 3) . '.' . substr($valor, 6, 3) . '-' . substr($valor, 9, 2);
        }
        return $valor;
    }

    private function escape($valor)
    {
        return addslashes((string) $valor);
    }

    private function resetarEstado()
    {
        $this->apiDuimp = null;
        $this->T075_Id = 0;
        $this->numeroDuimp = '';
        $this->versaoDuimp = 0;
        $this->modeloCalculos = '';
        $this->T075_D024_Id = 0;
        $this->cabecalhoDuimp = array();
        $this->itensListaDuimp = array();
        $this->itensDuimp = array();
        $this->mapaItemAdicao = array();
        $this->mapaItemSequenciaAdicao = array();
        $this->produtosCatalogo = array();
        $this->produtosCatalogoPorChave = array();
        $this->dadosProdutoPorCodigoInterno = array();
        $this->aliquotasAuxiliares = array();
        $this->authorizationToken = '';
        $this->csrfToken = '';
        $this->dataConsulta = '';
        $this->snapshotProcessoId = '';
        $this->tudoOk = true;
        $this->msgErro = '';
    }

    private function deuErro($mensagem)
    {
        $this->tudoOk = false;
        $this->msgErro = $mensagem;
        return $mensagem;
    }
}
