<?php
/** Renderiza o campo real com o framework, sem inicializar aplicação ou banco. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
function mysql_query($sql) { throw new \RuntimeException('O teste não pode consultar bancos.'); }
function mysqli_query($sql) { throw new \RuntimeException('O teste não pode consultar bancos.'); }
class Apps {
    public static function getDirAtivos() { return '/tmp/frete-nfs-render-sem-personalizacoes/'; }
    public static function getDirEditor() { return '/tmp/frete-nfs-render-sem-personalizacoes/'; }
}
$raiz = dirname(__DIR__, 2);
require_once $raiz . '/framework/sistema/automacaoFormulario.php';
$fonte = file_get_contents($raiz . '/prog/est/est004/form.php');
$g = array('empresaAtual' => 1, 'divId' => 'abaFrete', 'divIdRoot' => 'telaNotas',
    'c029Ids' => array(189 => true),
    'r_' => '/est/est004/form/est004FormCadastroNota/',
    'pathDados' => '/tmp/frete-nfs-render-sem-personalizacoes/', 'modoDesenvolvedor' => false, 'modoDesenvolvedorApp' => false);
$acaoId = 3402;
$r_acaoId = 3402;
$_SESSION['device'] = 'desktop';
preg_match("/'frete' => '(\{[^\n]+\})'/", $fonte, $posicoes);
$form_editor = array('frete' => $posicoes[1]);
$posicoesOriginais = json_decode($posicoes[1], true);
$inicioConfiguracao = strpos($fonte, '$sqlPesquisaFreteNFS = ');
if ($inicioConfiguracao === false) { $inicioConfiguracao = strpos($fonte, '$posicoesFrete = '); }
$fimConfiguracao = strpos($fonte, '$T013_Flag_Rateio_Frete_Preco = ', $inicioConfiguracao);
eval(substr($fonte, $inicioConfiguracao, $fimConfiguracao - $inicioConfiguracao));
$inicio = strpos($fonte, "'Frete_NFS_Nota_Id' => array(");
$fim = strpos($fonte, "'T013_D022_Id' => array(", $inicio);
eval('$campos = array(' . substr($fonte, $inicio, $fim - $inicio) . ');');
foreach ($campos as &$campo) {
    $campo = array_replace(array('class' => '', 'abrirJanela' => false, 'callbackAoEntrar' => false,
        'callbackAoSair' => false, 'callbackAoAlterar' => false, 'pesquisaGridCallbackJS' => ''), $campo);
}
unset($campo);
$form = new Formulario('testeFreteNFS');
$form->formArray = array('campos' => array('frete' => $campos));
$mapa = new \ReflectionProperty(Formulario::class, 'formTabId');
$mapa->setAccessible(true);
$mapa->setValue($form, array_fill_keys(array_keys($campos), 'frete'));
// O renderer legado lê opções opcionais sem isset; isola seus notices de PHP 7.4.
$nivel = error_reporting(E_ERROR | E_PARSE);
$html = '';
foreach ($campos as $id => $campo) { $html .= $form->campoCodigoHTML($id); }
error_reporting($nivel);
echo json_encode(array('html' => $html, 'posicoesOriginais' => $posicoesOriginais,
    'posicoesAtuais' => json_decode($form_editor['frete'], true)));
