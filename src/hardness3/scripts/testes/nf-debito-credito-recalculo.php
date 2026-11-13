<?php
/** php scripts/testes/nf-debito-credito-recalculo.php
 * Executa o atualizador real com regra fiscal e consultas simuladas.
 * Não conecta a bases, não executa procedures e não transmite NF-e.
 */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) { throw new \RuntimeException($mensagem); });
$raiz = dirname(__DIR__, 2);
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
class ResultadoRecalculo {
    public $linhas;
    public function __construct($linhas) { $this->linhas = $linhas; }
}
function mysql_fetch_array($resultado) { return array_shift($resultado->linhas); }
function mysql_real_escape_string($valor) { return addslashes((string) $valor); }
function mysql_error() { global $erroAtual; return $erroAtual; }
function mysql_query($sql) {
    global $executado, $cabecalho, $item, $falha, $erroAtual;
    $executado[] = $sql;
    $erroAtual = '';
    if ($falha !== '' && strpos($sql, $falha) !== false) {
        $erroAtual = 'Falha simulada';
        return false;
    }
    if (strpos($sql, 'SELECT * FROM T007') === 0) { return new ResultadoRecalculo([$cabecalho]); }
    if (strpos($sql, 'SELECT *') === 0) { return new ResultadoRecalculo([$item]); }
    if (strpos($sql, 'SELECT D149_') === 0) { return new ResultadoRecalculo([['D149_Percentual_Trib_Transparencia'=>0]]); }
    if (strpos($sql, 'SELECT D006_') === 0) { return new ResultadoRecalculo([['D006_Percentual_Trib_Transparencia'=>0]]); }
    conferir(preg_match('/^(UPDATE T008|call T00[78]_Gravar_Totalizacao_4)/', $sql), 'SQL inesperado');
    return true;
}
class FIS003 { public function demonstrativoNCM($id) { return ''; } }
class VEN001 {
    public function retornaAliquotas(...$parametros) {
        global $aliquotas;
        conferir($parametros[3] === 200 && $parametros[6] === 201, 'Regra fiscal não usa a nova nota/item');
        return $aliquotas;
    }
}
$fonte = file_get_contents($raiz . '/bibliotecas/classes/VEN003.php');
$inicio = strpos($fonte, 'public function atualizarAliquotasNF(');
$fim = strpos($fonte, 'public function ', $inicio + 16);
$metodo = substr($fonte, $inicio, $fim - $inicio);
// Só substitui os dois carregamentos por colaboradores isolados; o método é o real.
$metodo = preg_replace('/require_once\([^;]+;/', '', $metodo, -1, $carregamentos);
conferir($carregamentos === 2, 'Dependências do método mudaram; revisar isolamento');
eval('namespace hardness; class AtualizadorRecalculo { ' . $metodo . ' }');
preg_match_all('/\$Aliquotas\[\x27([^\x27]+)\x27\]/', $metodo, $campos);
$aliquotas = array_fill_keys($campos[1], '0');
$aliquotas = array_replace($aliquotas, ['Flag_ST'=>'N', 'Aliquota_IPI'=>'5',
    'Percentual_FCP'=>'0', 'Aliquota_ICMS'=>'12', 'Aliquota_PIS'=>'1.65', 'Aliquota_COFINS'=>'7.60',
    'Situacao_Tributaria_CBS_IBS'=>'000', 'Classificacao_Tributaria_CBS_IBS'=>'000001', 'Aliquota_CBS'=>'0.90']);
preg_match_all('/\$mT008\[\x27([^\x27]+)\x27\]/', $metodo, $campos);
$itemBase = array_replace(array_fill_keys($campos[1], '0'), ['T008_Id'=>201, 'T008_D009_Id'=>1,
    'T008_D006_Id'=>3, 'D006_Codigo_CFOP'=>'5949', 'T008_Flag_Calculo_Manual'=>'N',
    'T008_Percentual_FCP'=>'20', 'T008_Flag_ST'=>'S', 'T008_Valor_ICMS_Substituicao_Retencao'=>'62.00']);
$cabecalhoBase = ['T007_Numero_Protocolo_Nfe'=>'', 'T007_Numero_Recibo_Nfe'=>'', 'T007_Chave_Acesso_Nfe'=>''];
foreach (['S', 'N'] as $cadastroObrigatorio) {
    foreach (['1', '2'] as $preenchimentoRTC) {
        $g = ['C020'=>['C020_NFe_Obrigatorio_Cadastro_Produto'=>$cadastroObrigatorio],
              'C004'=>['C004_Flag_Tipo_Preenchimento_Reforma_Tributaria'=>$preenchimentoRTC]];
        $executado=[]; $erroAtual=''; $falha=''; $item=$itemBase; $cabecalho=$cabecalhoBase;
        conferir((new AtualizadorRecalculo())->atualizarAliquotasNF(200, true) === true, 'Recálculo falhou');
        $sql = implode("\n", $executado);
        foreach (['T008_Flag_ST'=>'N', 'T008_Percentual_FCP'=>'0', 'T008_Aliquota_IPI'=>'5',
            'T008_Aliquota_ICMS'=>'12', 'T008_Aliquota_PIS'=>'1.65', 'T008_Aliquota_COFINS'=>'7.60',
            'T008A_Situacao_Tributaria_CBS_IBS'=>'000', 'T008A_Classificacao_Tributaria_CBS_IBS'=>'000001',
            'T008A_Aliquota_CBS'=>'0.90'] as $campo=>$valor) {
            conferir(preg_match('/' . $campo . "\\s*=\\s*'" . preg_quote($valor, '/') . "'/", $sql), 'Parâmetro herdado: ' . $campo);
        }
        conferir(strpos($sql, "call T008_Gravar_Totalizacao_4('201')") > strpos($sql, 'UPDATE T008A'), 'Item totalizado antes de atualizar regras');
        conferir(end($executado) === "call T007_Gravar_Totalizacao_4('200')", 'Cabeçalho não totalizado após itens');
        conferir(strpos($sql, 'Valor_IBS_Estornado') === false && strpos($sql, 'Valor_CBS_Transferido') === false, 'Recálculo apagou especiais');
    }
}
// Sem a opção nova, manter o comportamento anterior para NF normal/cadastro livre.
$g['C020']['C020_NFe_Obrigatorio_Cadastro_Produto']='N';
$g['C004']['C004_Flag_Tipo_Preenchimento_Reforma_Tributaria']='2';
$executado=[];
conferir((new AtualizadorRecalculo())->atualizarAliquotasNF(200) === true, 'Fluxo anterior falhou');
$sql=implode("\n",$executado);
conferir(strpos($sql, 'T008_Percentual_FCP') === false, 'Alterou cadastro livre fora do gerador');
conferir(strpos($sql, 'T008A_Situacao_Tributaria_CBS_IBS') === false, 'Alterou preenchimento RTC fora do gerador');
foreach (['UPDATE T008A', "call T008_Gravar_Totalizacao_4", "call T007_Gravar_Totalizacao_4"] as $falha) {
    $executado=[];
    conferir(strpos((new AtualizadorRecalculo())->atualizarAliquotasNF(200, true), 'Falha simulada') !== false, 'Erro de recálculo ignorado');
    conferir(strpos(end($executado), $falha) !== false, 'Continuou após erro de gravação/cálculo');
}
$falha='';
foreach (['T007_Numero_Protocolo_Nfe', 'T007_Numero_Recibo_Nfe', 'T007_Chave_Acesso_Nfe'] as $protecao) {
    $executado=[]; $cabecalho=$cabecalhoBase; $cabecalho[$protecao]='123';
    conferir(is_string((new AtualizadorRecalculo())->atualizarAliquotasNF(200, true)), 'Alterou NF protegida');
    conferir(count($executado)===1, 'Gravou após detectar NF protegida');
}
// Contrato das procedures: liberar apenas finalidades 5/6 no ramo automático.
$sql=file_get_contents($raiz . '/scripts/sql/T008_Gravar_Totalizacao_4.sql');
conferir(substr_count($sql, "T007_Flag_Finalidade_NFe IN ('5','6')")===3, 'Gates de cálculo/retidos/IRPJ-CSLL incompletos');
conferir(strpos($sql, "(xD006_Flag_Devolucao = 'N' or xC031_Atualizar_Aliquotas_Devolucao = 'S') and ifnull") !== false, 'Alterou gate do cálculo manual');
$rtc=file_get_contents($raiz . '/scripts/sql/T008_Gravar_Totalizacao_4_Reforma.sql');
conferir(strpos($rtc, "(T007_Flag_Finalidade_NFe IN ('5','6') or xD006_Flag_Devolucao") !== false, 'RTC ainda pula débito/crédito com CFOP de devolução');
echo "OK: {$verificacoes} verificações de recálculo; sem banco ou transmissão.\n";
