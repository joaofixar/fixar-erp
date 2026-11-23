<?php
namespace hardness;

/** Prepara mensagens; o envio é confirmado pelo usuário no WhatsApp. */
class NotaFiscalWhatsApp
{
    public function preparar($id, $formato = '')
    {
        global $g;
        $id = (int) $id;
        $empresa = (int) $g['empresaAtual'];
        $sql = mysql_query("SELECT T007.*, D024_DDD_Celular, D024_Celular,
                                   D024_DDD_Telefone_1, D024_Telefone_1,
                                   D013_Id, D013_DDD_Celular, D013_Celular,
                                   D013_DDD_Telefone_1, D013_Telefone_1, C007_Nome
                              FROM T007
                         LEFT JOIN D024 ON D024_Id = T007_D024_Id
                         LEFT JOIN T005 ON T005_Id = T007_T005_Id AND T005_D024_Id = T007_D024_Id
                         LEFT JOIN D013 ON D013_Id = T005_D013_Id AND D013_D024_Id = T007_D024_Id
                         LEFT JOIN C007 ON C007_Id = T007_C007_Id_Vendedor_Interno
                             WHERE T007_Id = '{$id}' AND T007_C004_Id = '{$empresa}'");
        $nota = $sql ? mysql_fetch_assoc($sql) : false;
        if (!$nota) {
            return array('code' => false, 'data' => 'Nota fiscal não encontrada na empresa atual.');
        }

        $destinatario = $this->destinatario($nota);
        $telefone = $destinatario['telefone'];
        $autorizada = !empty($nota['T007_Numero_Protocolo_Nfe'])
            && in_array((int) $nota['T007_Flag_ACP'], array(6, 8), true)
            && $nota['T007_Flag_Cancelada'] !== 'S'
            && $nota['T007_Flag_Inutilizado'] !== 'S'
            && $nota['T007_Flag_Denegado'] !== 'S';
        $acp = (int) $nota['T007_Flag_ACP'] === 3;
        $pdfDisponivel = $autorizada || ($acp
            && $nota['T007_Flag_Cancelada'] !== 'S'
            && $nota['T007_Flag_Inutilizado'] !== 'S'
            && $nota['T007_Flag_Denegado'] !== 'S');

        if ($formato === '') {
            return array('code' => true, 'phoneWhatsApp' => $telefone,
                'pdfDisponivel' => $pdfDisponivel, 'tipoPdf' => $acp ? 'ACP' : 'DANFE',
                'contatoId' => $destinatario['contatoId'],
                'ddd' => $destinatario['ddd'], 'celular' => $destinatario['celular'],
                'origemTelefone' => $destinatario['origem'],
                'data' => 'O envio deve ser confirmado no WhatsApp. São utilizados os dados já salvos da nota.');
        }
        if ($telefone === '') {
            return array('code' => false, 'data' => 'Atualize o celular e o DDD do contato do pedido ou do cliente antes de continuar.');
        }
        if (!in_array($formato, array('texto', 'pdf'), true)) {
            return array('code' => false, 'data' => 'Formato inválido.');
        }

        $identificacao = !empty($nota['T007_Numero_Nota_Fiscal'])
            ? $nota['T007_Numero_Nota_Fiscal'] : 'ID ' . $id;
        $titulo = ($acp ? 'ACP N. ' : 'Nota Fiscal N. ') . $identificacao;
        if ($formato === 'pdf') {
            if (!$pdfDisponivel) {
                return array('code' => false, 'data' => 'PDF disponível para ACP válida ou DANFE de NF-e/NFC-e autorizada e válida.');
            }
            $pdf = $acp ? $this->publicarACP($nota) : $this->publicarDanfe($nota);
            if ($pdf['code'] !== true) { return $pdf; }
            if ($acp) {
                $texto = 'Olá, segue link da nossa ACP N. ' . $identificacao . "\n" . $pdf['data'];
            } else {
                if (empty($pdf['xml'])) {
                    return array('code' => false, 'data' => 'Não foi possível compartilhar o XML autorizado da nota.');
                }
                $texto = 'Olá, seguem os arquivos da nossa nota fiscal N. ' . $identificacao
                    . "\n\nPDF (DANFE):\n" . $pdf['data'] . "\n\nXML:\n" . $pdf['xml'];
            }
        } else {
            $situacao = $autorizada ? 'Autorizada' : 'Em elaboração / sem autorização';
            if ($nota['T007_Flag_Cancelada'] === 'S') { $situacao = 'Cancelada'; }
            elseif ($nota['T007_Flag_Inutilizado'] === 'S') { $situacao = 'Inutilizada'; }
            elseif ($nota['T007_Flag_Denegado'] === 'S') { $situacao = 'Denegada'; }
            $texto = $titulo . "\nSituação: " . $situacao . "\n---------------------------------\n";
            $itens = mysql_query("SELECT T008_Codigo_Produto, T008_Descricao_Produto,
                                        T008_Quantidade, T008_Valor_Preco_Unitario
                                   FROM T008 WHERE T008_T007_Id = '{$id}' ORDER BY T008_Id");
            if (!$itens) { return array('code' => false, 'data' => 'Não foi possível consultar os produtos da nota.'); }
            while ($item = mysql_fetch_assoc($itens)) {
                $texto .= 'Código: ' . $item['T008_Codigo_Produto'] . "\n"
                    . 'Descrição: ' . $item['T008_Descricao_Produto'] . "\n"
                    . 'Quantidade: ' . gCorrigeNumero($item['T008_Quantidade']) . "\n"
                    . 'Preço unitário: ' . gCorrigeNumero($item['T008_Valor_Preco_Unitario']) . "\n---------------------------------\n";
            }
            $texto .= 'Total da nota: R$ ' . gCorrigeNumero($nota['T007_Valor_Total']);
        }

        // Registra a preparação, sem afirmar entrega ou leitura no WhatsApp.
        $usuario = (int) $g['usuarioAtual'];
        $cliente = (int) $nota['T007_D024_Id'];
        $contatoId = $destinatario['contatoId'];
        $remetente = mysql_real_escape_string($nota['C007_Nome']);
        $titulo = mysql_real_escape_string('Preparação WhatsApp - ' . $titulo);
        $destino = mysql_real_escape_string('Whatsapp: +' . $telefone);
        $corpo = mysql_real_escape_string($texto);
        if (!mysql_query("INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De,
                T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo,
                T139_Flag_Lido, T139_D024_Id, T139_D013_Id, T139_T007_Id, T139_C007_Id)
            VALUES ('S', 0, '{$titulo}', '{$remetente}', '{$destino}', NOW(), '{$corpo}',
                '{$corpo}', '1', '{$cliente}', '{$contatoId}', '{$id}', '{$usuario}')")) {
            return array('code' => false, 'data' => 'Não foi possível registrar a preparação da mensagem.');
        }
        $tarefas = mysql_query("SELECT T259_Id, T259_C007_Id FROM D085
            INNER JOIN T259 ON T259_Id = D085_T259_Id
            WHERE D085_Entidade = 'T007' AND D085_Id_Entidade = '{$id}'");
        if ($tarefas) {
            require_once __DIR__ . '/../funcoesCallback/assuntoTarefa.php';
            while ($tarefa = mysql_fetch_assoc($tarefas)) {
                assuntoTarefa(array('campoAtual' => '', 'todosCampos' => $tarefa), false);
            }
        }
        return array('code' => true, 'phoneWhatsApp' => $telefone, 'data' => $texto);
    }

    private function normalizarTelefone($ddd, $numero)
    {
        $numero = preg_replace('/\D/', '', (string) $numero);
        $ddd = ltrim(preg_replace('/\D/', '', (string) $ddd), '0');
        if (in_array(strlen($numero), array(12, 13), true) && substr($numero, 0, 2) === '55') {
            $numero = substr($numero, 2);
        } elseif (in_array(strlen($numero), array(8, 9), true)) {
            $numero = $ddd . $numero;
        }
        return preg_match('/^[1-9][0-9][2-9][0-9]{7,8}$/', $numero) ? '55' . $numero : '';
    }

    private function destinatario($nota)
    {
        $contatoId = (int) $nota['D013_Id'];
        $opcoes = array(
            array('D013', 'Celular', $contatoId, 'Celular do contato'),
            array('D024', 'Celular', 0, 'Celular do cliente'),
            array('D013', 'Telefone_1', $contatoId, 'Telefone 1 do contato'),
            array('D024', 'Telefone_1', 0, 'Telefone 1 do cliente')
        );
        foreach ($opcoes as $opcao) {
            list($tabela, $campo, $idContato, $origem) = $opcao;
            if ($tabela === 'D013' && !$contatoId) { continue; }
            $telefone = $this->normalizarTelefone($nota[$tabela . '_DDD_' . $campo] ?? '', $nota[$tabela . '_' . $campo] ?? '');
            if ($telefone !== '') {
                return array('telefone' => $telefone, 'ddd' => substr($telefone, 2, 2),
                    'celular' => substr($telefone, 4), 'contatoId' => $idContato, 'origem' => $origem);
            }
        }
        return array('telefone' => '', 'ddd' => '', 'celular' => '', 'contatoId' => $contatoId, 'origem' => '');
    }

    public function atualizarCelular($id, $ddd, $celular)
    {
        global $g;
        $telefone = $this->normalizarTelefone($ddd, $celular);
        if ($telefone === '') {
            return array('code' => false, 'data' => 'Informe um DDD e um número de telefone válidos.');
        }
        // Resolve novamente o destinatário pela nota e empresa, sem aceitar IDs de cadastro do navegador.
        $info = $this->preparar($id);
        if (!$info['code']) { return $info; }
        $id = (int) $id;
        $empresa = (int) $g['empresaAtual'];
        $ddd = substr($telefone, 2, 2);
        $celular = substr($telefone, 4);
        if ($info['contatoId']) {
            $contato = (int) $info['contatoId'];
            $sql = "UPDATE D013 INNER JOIN T005 ON T005_D013_Id = D013_Id
                INNER JOIN T007 ON T007_T005_Id = T005_Id AND T007_D024_Id = D013_D024_Id
                SET D013_DDD_Celular = '{$ddd}', D013_Celular = '{$celular}'
                WHERE T007_Id = '{$id}' AND T007_C004_Id = '{$empresa}' AND D013_Id = '{$contato}'";
        } else {
            $sql = "UPDATE D024 INNER JOIN T007 ON T007_D024_Id = D024_Id
                SET D024_DDD_Celular = '{$ddd}', D024_Celular = '{$celular}'
                WHERE T007_Id = '{$id}' AND T007_C004_Id = '{$empresa}'";
        }
        return mysql_query($sql)
            ? array('code' => true, 'data' => 'Celular atualizado.')
            : array('code' => false, 'data' => 'Não foi possível atualizar o celular.');
    }

    protected function publicarACP($nota)
    {
        $origem = getcwd();
        try {
            require_once __DIR__ . '/VEN003.php';
            $ven003 = new VEN003();
            $arquivo = $ven003->gerarACP($nota['T007_Id']);
        } catch (\Throwable $erro) {
            return array('code' => false, 'data' => 'Não foi possível gerar o PDF da ACP.');
        } finally {
            chdir($origem);
        }
        return $this->compartilharPdf($arquivo);
    }

    protected function publicarDanfe($nota)
    {
        global $g;
        $diretorio = rtrim($g['pathDadosAntigo'], '/') . '/nfe/xml/autorizado/';
        $protocolo = basename($nota['T007_Numero_Protocolo_Nfe']) . '.xml';
        $xml = $diretorio . substr($nota['T007_Data_Emissao'], 0, 7) . '/' . $protocolo;
        if (!is_file($xml)) { $xml = $diretorio . $protocolo; }
        if (!is_file($xml)) {
            return array('code' => false, 'data' => 'XML autorizado da nota fiscal não encontrado.');
        }
        $origem = getcwd();
        try {
            require_once __DIR__ . '/VEN003.php';
            $ven003 = new VEN003();
            $arquivo = $ven003->gerarDanfe($nota['T007_Id'], $xml);
        } catch (\Throwable $erro) {
            return array('code' => false, 'data' => 'Não foi possível gerar o DANFE da nota fiscal.');
        } finally {
            chdir($origem);
        }
        $pdf = $this->compartilharPdf($arquivo);
        if (!$pdf['code']) { return $pdf; }
        // Compartilha exatamente o XML autorizado utilizado no DANFE, sem reformatar a assinatura.
        $xmlCompartilhado = $this->compartilharArquivo($xml, 'xml');
        if (!$xmlCompartilhado['code']) { return $xmlCompartilhado; }
        $pdf['xml'] = $xmlCompartilhado['data'];
        return $pdf;
    }

    private function compartilharPdf($arquivo)
    {
        global $g;
        if (!is_file($arquivo) || file_get_contents($arquivo, false, null, 0, 5) !== '%PDF-') {
            return array('code' => false, 'data' => 'Não foi possível gerar um PDF válido do documento.');
        }
        return $this->compartilharArquivo($arquivo, 'pdf');
    }

    private function compartilharArquivo($arquivo, $extensao)
    {
        global $g;
        $pasta = rtrim($g['pathRaiz'], '/') . '/whatsapp/';
        if (!is_dir($pasta) && !mkdir($pasta, 0775, true)) {
            return array('code' => false, 'data' => 'Não foi possível criar a pasta de compartilhamento.');
        }
        $nome = bin2hex(random_bytes(24)) . '.' . $extensao;
        if (!copy($arquivo, $pasta . $nome)) {
            return array('code' => false, 'data' => 'Não foi possível compartilhar o arquivo ' . strtoupper($extensao) . ' do documento.');
        }
        require_once __DIR__ . '/LinkCompartilhamentoWhatsApp.php';
        return array('code' => true, 'data' => LinkCompartilhamentoWhatsApp::gerar($g['urlRaiz'], 'whatsapp/' . $nome));
    }
}
