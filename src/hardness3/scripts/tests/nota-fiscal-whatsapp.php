<?php
namespace hardness;

// Testes isolados: não acessam banco, WhatsApp ou gerador de DANFE.
require_once __DIR__ . '/../../bibliotecas/classes/NotaFiscalWhatsApp.php';

class ResultadoWhatsApp
{
    public $linhas;
    public function __construct($linhas) { $this->linhas = $linhas; }
}

function mysql_query($sql)
{
    global $consultas, $notaTeste, $falharRegistro;
    $consultas[] = $sql;
    if (strpos($sql, 'FROM T007') !== false) {
        if (strpos($sql, "T007_C004_Id = '7'") === false) {
            throw new \RuntimeException('Consulta sem restrição de empresa.');
        }
        return new ResultadoWhatsApp($notaTeste ? array($notaTeste) : array());
    }
    if (strpos($sql, 'FROM T008') !== false) {
        return new ResultadoWhatsApp(array(array('T008_Codigo_Produto' => '123',
            'T008_Descricao_Produto' => 'Produto teste', 'T008_Quantidade' => 2,
            'T008_Valor_Preco_Unitario' => 10)));
    }
    if (strpos($sql, 'INSERT INTO T139') !== false) { return !$falharRegistro; }
    if (strpos($sql, 'UPDATE D013') === 0 || strpos($sql, 'UPDATE D024') === 0) {
        conferir(strpos($sql, "T007_Id = '100'") !== false
            && strpos($sql, "T007_C004_Id = '7'") !== false, 'Atualização deve ficar restrita à nota e empresa.');
        return true;
    }
    if (strpos($sql, 'FROM D085') !== false) { return new ResultadoWhatsApp(array()); }
    throw new \RuntimeException('Consulta inesperada: ' . $sql);
}
function mysql_fetch_assoc($resultado) { return array_shift($resultado->linhas); }
function mysql_real_escape_string($texto) { return addslashes($texto); }
function gCorrigeNumero($numero) { return number_format($numero, 2, ',', '.'); }

class WhatsAppTeste extends NotaFiscalWhatsApp
{
    public $geracoes = 0;
    public $geracoesACP = 0;
    public $xmlDisponivel = true;
    protected function publicarACP($nota)
    {
        $this->geracoesACP++;
        return array('code' => true, 'data' => 'https://erp.example/whatsapp/acp.pdf');
    }
    protected function publicarDanfe($nota)
    {
        $this->geracoes++;
        return array('code' => true, 'data' => 'https://erp.example/whatsapp/teste.pdf',
            'xml' => $this->xmlDisponivel ? 'https://erp.example/whatsapp/teste.xml' : '');
    }
}
function conferir($condicao, $mensagem)
{
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
function semRegistro()
{
    global $consultas;
    foreach ($consultas as $sql) {
        if (strpos($sql, 'INSERT INTO T139') !== false) { return false; }
    }
    return true;
}

$g = array('empresaAtual' => 7, 'usuarioAtual' => 42);
$base = array('T007_Id' => 100, 'T007_D024_Id' => 20, 'D013_Id' => 30,
    'D013_DDD_Celular' => '41', 'D013_Celular' => '99999-1234',
    'D024_DDD_Celular' => '48', 'D024_Celular' => '98888-1234',
    'T007_Flag_ACP' => 6, 'T007_Numero_Nota_Fiscal' => '1234',
    'T007_Numero_Protocolo_Nfe' => '123456789', 'T007_Valor_Total' => 20,
    'T007_Flag_Cancelada' => 'N', 'T007_Flag_Inutilizado' => 'N',
    'T007_Flag_Denegado' => 'N', 'C007_Nome' => 'Vendedor');
$notaTeste = $base;
$consultas = array();
$falharRegistro = false;
$servico = new WhatsAppTeste();
$info = $servico->preparar(100);
conferir($info['phoneWhatsApp'] === '5541999991234', 'Deve priorizar o contato e normalizar o celular.');
conferir($info['contatoId'] === 30 && $info['pdfDisponivel'], 'Contato e disponibilidade de DANFE incorretos.');
conferir(semRegistro(), 'A consulta inicial não deve registrar preparação.');

$notaTeste['D013_Celular'] = '';
$info = $servico->preparar(100);
conferir($info['phoneWhatsApp'] === '5548988881234' && !$info['contatoId'], 'Deve usar celular do cliente sem contato.');
$notaTeste['D024_Celular'] = '';
$consultas = array();
conferir(!$servico->preparar(100, 'texto')['code'] && semRegistro(), 'Celular ausente deve impedir preparação.');
$notaTeste = $base;
$notaTeste['D013_Celular'] = '123';
conferir($servico->preparar(100)['phoneWhatsApp'] === '5548988881234', 'Celular inválido do contato deve permitir fallback ao cliente.');

// Reproduz o cadastro exibido: DDD celular zerado, celular vazio e número válido em Telefone 1.
$notaTeste['D013_Id'] = 0;
$notaTeste['D024_DDD_Celular'] = '0';
$notaTeste['D024_Celular'] = '';
$notaTeste['D024_DDD_Telefone_1'] = '41';
$notaTeste['D024_Telefone_1'] = '996521307';
$info = $servico->preparar(100);
conferir($info['phoneWhatsApp'] === '5541996521307', 'Deve usar Telefone 1 quando celular está vazio.');
conferir($info['origemTelefone'] === 'Telefone 1 do cliente', 'Deve informar a origem do número.');
conferir($info['ddd'] === '41' && $info['celular'] === '996521307', 'Editor compacto deve receber o telefone encontrado.');
conferir($servico->preparar(100, 'texto')['code'], 'O número do Telefone 1 deve permitir preparar o texto.');
$notaTeste['D024_Telefone_1'] = '';
$info = $servico->preparar(100);
conferir($info['phoneWhatsApp'] === '', 'DDD zero e número vazio nunca devem virar +550.');
conferir(!$servico->preparar(100, 'texto')['code'], 'Número vazio deve bloquear preparação.');

foreach (array('41 99652-1307', '+55 (41) 99652-1307') as $numeroCompleto) {
    $notaTeste['D024_Celular'] = $numeroCompleto;
    conferir($servico->preparar(100)['phoneWhatsApp'] === '5541996521307', 'Não deve duplicar DDI/DDD em número completo.');
}
$notaTeste = $base;
conferir($servico->atualizarCelular(100, '41', '99999-4321')['code'], 'Deve salvar celular do contato.');
conferir(strpos(end($consultas), 'UPDATE D013') === 0, 'Atualização de contato deve usar D013.');
$notaTeste['D013_Id'] = 0;
$notaTeste['D013_Celular'] = '';
conferir($servico->atualizarCelular(100, '48', '98888-4321')['code'], 'Deve salvar celular do cliente sem contato.');
conferir(strpos(end($consultas), 'UPDATE D024') === 0, 'Atualização do cliente deve usar D024.');
$quantidadeConsultas = count($consultas);
conferir(!$servico->atualizarCelular(100, '0', '')['code'], 'Não deve salvar telefone inválido.');
conferir(count($consultas) === $quantidadeConsultas, 'Telefone inválido não deve alterar o banco.');

foreach (array('T007_Flag_Cancelada', 'T007_Flag_Inutilizado', 'T007_Flag_Denegado') as $flag) {
    $notaTeste = $base;
    $notaTeste[$flag] = 'S';
    $consultas = array();
    conferir(!$servico->preparar(100)['pdfDisponivel'], $flag . ' não deve oferecer PDF.');
    conferir(!$servico->preparar(100, 'pdf')['code'] && semRegistro(), $flag . ' não deve gerar PDF.');
}
conferir($servico->geracoes === 0, 'Notas inválidas não podem acionar o gerador.');
$notaTeste = $base;
$notaTeste['T007_Numero_Protocolo_Nfe'] = '';
$consultas = array();
conferir(!$servico->preparar(100, 'pdf')['code'] && semRegistro(), 'Nota sem protocolo não deve gerar PDF.');
$texto = $servico->preparar(100, 'texto');
conferir($texto['code'] && strpos($texto['data'], 'Em elaboração / sem autorização') !== false, 'Deve identificar nota sem autorização.');
conferir(strpos($texto['data'], 'Produto teste') !== false && strpos($texto['data'], '20,00') !== false, 'Texto deve incluir produtos e total.');
conferir(!semRegistro(), 'Texto preparado deve ser registrado.');

$notaTeste = $base;
$pdf = $servico->preparar(100, 'pdf');
conferir($pdf['code'] && strpos($pdf['data'], 'https://erp.example/whatsapp/teste.pdf') !== false, 'Deve preparar o link do PDF.');
conferir($servico->geracoes === 1, 'Deve gerar apenas o PDF solicitado.');
conferir(strpos($pdf['data'], "PDF (DANFE):\nhttps://erp.example/whatsapp/teste.pdf") !== false
    && strpos($pdf['data'], "XML:\nhttps://erp.example/whatsapp/teste.xml") !== false,
    'NF-e deve incluir os dois links identificados na mesma mensagem.');
$notaTeste = $base;
$notaTeste['T007_Flag_ACP'] = 3;
$notaTeste['T007_Numero_Protocolo_Nfe'] = '';
$notaTeste['T007_Numero_Nota_Fiscal'] = 'A2';
$infoACP = $servico->preparar(100);
conferir($infoACP['pdfDisponivel'] && $infoACP['tipoPdf'] === 'ACP', 'ACP válida deve disponibilizar seu próprio PDF sem protocolo.');
$pdfACP = $servico->preparar(100, 'pdf');
conferir($pdfACP['code'] && strpos($pdfACP['data'], 'ACP N. A2') !== false
    && strpos($pdfACP['data'], '/acp.pdf') !== false, 'A mensagem deve identificar a ACP e incluir seu PDF.');
conferir(strpos($pdfACP['data'], 'XML:') === false, 'ACP deve continuar compartilhando somente seu PDF.');
conferir($servico->geracoesACP === 1 && $servico->geracoes === 1, 'ACP não pode chamar o gerador de DANFE.');
foreach (array('T007_Flag_Cancelada', 'T007_Flag_Inutilizado', 'T007_Flag_Denegado') as $flag) {
    $notaTeste[$flag] = 'S';
    $consultas = array();
    conferir(!$servico->preparar(100)['pdfDisponivel'], 'ACP inválida não deve disponibilizar PDF.');
    conferir(!$servico->preparar(100, 'pdf')['code'] && semRegistro(), 'ACP inválida deve ser bloqueada sem registro.');
    $notaTeste[$flag] = 'N';
}
$notaTeste = $base;
$servico->xmlDisponivel = false;
$consultas = array();
conferir(!$servico->preparar(100, 'pdf')['code'] && semRegistro(), 'Sem XML, NF-e não pode registrar uma preparação parcial como concluída.');
$servico->xmlDisponivel = true;
$falharRegistro = true;
conferir(!$servico->preparar(100, 'texto')['code'], 'Falha no registro deve ser informada.');
$notaTeste = false;
$consultas = array();
conferir(!$servico->preparar(999, 'texto')['code'] && semRegistro(), 'Nota inexistente não deve preparar mensagem.');
echo "OK: destinatário, empresa, formatos, situação da nota e registro de preparação.\n";
