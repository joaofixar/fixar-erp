<?php
namespace hardness;

require_once __DIR__ . '/../../hardness/class/DanfeSimplificada.php';

/** Localiza o XML autorizado da empresa atual sem gravar preferências ou arquivos. */
class DanfeSimplificadaDocumento
{
    public static function gerar($id, $empresa, $pastaDados)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        $empresa = filter_var($empresa, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$id || !$empresa) { throw new \InvalidArgumentException('Nota ou empresa inválida.'); }
        $sql = mysql_query("SELECT T007_Chave_Acesso_Nfe, T007_Numero_Protocolo_Nfe, T007_Data_Emissao, T007_Flag_Cancelada
            FROM T007 WHERE T007_Id = '{$id}' AND T007_C004_Id = '{$empresa}' AND T007_Flag_ACP = '6' LIMIT 1");
        $nota = $sql ? mysql_fetch_assoc($sql) : false;
        if (!$nota) { throw new \InvalidArgumentException('NF-e não encontrada na empresa atual.'); }
        $protocolo = (string) $nota['T007_Numero_Protocolo_Nfe'];
        if (!preg_match('/^[0-9]{1,20}$/D', $protocolo)) {
            throw new \InvalidArgumentException('A impressão simplificada fica disponível após a autorização da NF-e.');
        }
        $base = rtrim($pastaDados, '/\\') . '/nfe/xml/autorizado/';
        $arquivos = array();
        if (preg_match('/^(\d{4}-\d{2})-\d{2}/D', $nota['T007_Data_Emissao'], $data)) {
            $arquivos[] = $base . $data[1] . '/' . $protocolo . '.xml';
        }
        $arquivos[] = $base . $protocolo . '.xml';
        foreach ($arquivos as $arquivo) {
            if (!is_file($arquivo) || !is_readable($arquivo)) { continue; }
            $xml = file_get_contents($arquivo);
            if ($xml === false) { continue; }
            $danfe = new DanfeSimplificada($xml, ($nota['T007_Flag_Cancelada'] ?? '') === 'S');
            $dados = $danfe->dados();
            if ($dados['chave'] !== $nota['T007_Chave_Acesso_Nfe'] || $dados['protocolo'] !== $protocolo) {
                throw new \InvalidArgumentException('O XML autorizado não corresponde à NF-e selecionada.');
            }
            return array('conteudo' => $danfe->gerarPDF(), 'nome' => 'danfe_simplificada_' . $protocolo . '.pdf');
        }
        throw new \InvalidArgumentException('XML autorizado da NF-e não encontrado.');
    }
}
