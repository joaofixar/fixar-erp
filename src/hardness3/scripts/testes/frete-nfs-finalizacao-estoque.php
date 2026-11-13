<?php
/** php scripts/testes/frete-nfs-finalizacao-estoque.php — fluxo real, sem acesso a banco. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) { throw new \RuntimeException($mensagem); });
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
class ResultadoEstoque {
    public $linhas;
    public function __construct($linhas) { $this->linhas = $linhas; }
}
class CAD002 {}
function mysql_fetch_array($resultado) { return array_shift($resultado->linhas); }
function mysql_num_rows($resultado) { return count($resultado->linhas); }
function mysql_query($sql, $gatilhos = true) {
    global $consultas, $nota, $conferenciaPendente, $locacaoPendente;
    $consultas[] = $sql;
    // Simula cliente sem os campos opcionais da implantação do Frete NFS.
    if (preg_match('/T013A|T014A|Frete_NFS/i', $sql)) {
        throw new \RuntimeException('O estoque não deve depender dos campos de Frete NFS.');
    }
    if (strpos($sql, 'select T013_Flag_Finalidade_NFe,') === 0) { return new ResultadoEstoque(array($nota)); }
    if (strpos($sql, 'SELECT T014_Id FROM T014 WHERE') === 0) {
        return new ResultadoEstoque($conferenciaPendente ? array(array('T014_Id' => 1)) : array());
    }
    if (strpos($sql, 'SELECT T014_Id FROM T014 LEFT JOIN T066') === 0) {
        return new ResultadoEstoque($locacaoPendente ? array(array('T014_Id' => 1)) : array());
    }
    throw new \RuntimeException('Consulta inesperada no teste de estoque.');
}
$fonte = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/EST004.php');
$inicio = strpos($fonte, 'public function finalizarNotaEstoqueValidar(');
$fim = strpos($fonte, 'public function validaStatusOCVinculada(', $inicio);
$metodo = substr($fonte, $inicio, $fim - $inicio);
$metodo = str_replace('require_once("bibliotecas/classes/CAD002.php");', '', $metodo, $carregamentos);
conferir($carregamentos === 1, 'Dependência do método mudou; revisar isolamento.');
eval('namespace hardness; class ValidadorEstoque {
    public function conferenciaMateriais($id) { global $conferenciaMateriais; return $conferenciaMateriais; }
    public function validaStatusOCVinculada($id) { global $statusOC; return $statusOC; }
    ' . $metodo . '}');
$g = array('empresaAtual' => 1, 'C031' => array('conferenciaCegaCompras' => 'N'));
$nota = array('T013_Flag_Finalidade_NFe' => '1', 'T013_Numero_Conhecimento' => 'NAO TEM',
    'T013_D024_Id' => 1, 'T013_Numero_Nota_Fiscal' => '114993', 'T013_D022_Id' => 0,
    'T013_C004_Id' => 1, 'T013_Valor_Total' => '22942.10', 'T013_Valor_Frete' => '0.00');
$conferenciaPendente = false;
$locacaoPendente = false;
$conferenciaMateriais = array('code' => true, 'data' => '');
$statusOC = true;
$validador = new ValidadorEstoque();
foreach (array('0', '1', '2', '3', '4') as $frete) {
    $nota['T013_Flag_Frete'] = $frete;
    $consultas = array();
    conferir($validador->finalizarNotaEstoqueValidar(22826) === true, 'Estoque bloqueado por Frete NFS ou campos opcionais.');
    conferir(count($consultas) === 3, 'Não percorreu as verificações normais do estoque.');
}
$consultas = array();
conferir(is_string($validador->finalizarNotaEstoqueValidar(0)) && !$consultas, 'Nota sem ID deixou de ser validada.');
$g['C031']['conferenciaCegaCompras'] = 'S';
$conferenciaPendente = true;
conferir(strpos($validador->finalizarNotaEstoqueValidar(22826), 'conferência cega') !== false, 'Removeu a validação da conferência cega.');
$conferenciaPendente = false;
$conferenciaMateriais = array('code' => false, 'data' => 'Conferência de materiais pendente.');
conferir($validador->finalizarNotaEstoqueValidar(22826) === $conferenciaMateriais['data'], 'Removeu a validação de materiais.');
$conferenciaMateriais['code'] = true;
$locacaoPendente = true;
conferir(strpos($validador->finalizarNotaEstoqueValidar(22826), 'locações') !== false, 'Removeu a validação de locação.');
$locacaoPendente = false;
$statusOC = 'Ordem de compra pendente.';
conferir($validador->finalizarNotaEstoqueValidar(22826) === $statusOC, 'Removeu a validação da ordem de compra.');
echo "OK: {$verificacoes} verificações da finalização do estoque, sem dependência de Frete NFS ou acesso a banco.\n";
