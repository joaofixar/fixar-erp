<?php
namespace hardness;

require_once __DIR__ . '/iset_sku.php';

/** Controle durável de criação e posse das pendências, comum ao cron e à tela. */
class iset_produtos_controle_
{
    public function verificarTransacao()
    {
        $sql = <<<SQL
            SELECT VERSION() AS versao, @@autocommit AS autocommit
SQL;
        $consulta = mysql_query($sql);
        if (!$consulta) throw new \Exception('Falha consultando versão e autocommit do banco: ' . mysql_error());
        $estado = mysql_fetch_assoc($consulta);
        if (!$estado || !isset($estado['autocommit'])) throw new \Exception('O banco não retornou o autocommit para a validação ISET.');
        if ((int) $estado['autocommit'] !== 1) {
            throw new \Exception('Execução ISET bloqueada. Banco=' . $estado['versao'] . '; autocommit=' . $estado['autocommit'] . '. É necessário autocommit=1 e nenhuma transação aberta.');
        }

        // MySQL não possui @@in_transaction. Autocommit=1 também pode coexistir
        // com START TRANSACTION, portanto não basta verificar apenas a variável.
        // Fora de transação, SAVEPOINT não persiste e RELEASE retorna erro 1305.
        // Dentro dela, ambos funcionam: removemos apenas nosso ponto e bloqueamos,
        // sem COMMIT/ROLLBACK nem alteração dos registros ou savepoints do chamador.
        $savepoint = 'iset_verifica_' . bin2hex(random_bytes(16));
        $sql = <<<SQL
            SAVEPOINT {$savepoint}
SQL;
        if (!mysql_query($sql)) throw new \Exception('Falha verificando transação ISET ao criar savepoint: ' . mysql_error());
        $sql = <<<SQL
            RELEASE SAVEPOINT {$savepoint}
SQL;
        // O erro esperado precisa funcionar tanto com retorno false como com
        // MYSQLI_REPORT_STRICT. Qualquer outro erro impede continuar a operação.
        try {
            $consulta = @mysql_query($sql);
            $codigo = mysql_errno();
            $erro = mysql_error();
        } catch (\mysqli_sql_exception $e) {
            $consulta = false;
            $codigo = (int) $e->getCode();
            $erro = $e->getMessage();
        }
        if ($consulta !== false) throw new \Exception('Execução ISET bloqueada: existe uma transação aberta na conexão. Ela não foi confirmada nem desfeita.');
        if ((int) $codigo !== 1305) throw new \Exception('Falha verificando transação ISET ao liberar savepoint. Banco=' . $estado['versao'] . '; erro SQL=' . $erro);
    }

    public function verificarDisponibilidade()
    {
        // A reserva precisa persistir fora de qualquer transação do chamador.
        $this->verificarTransacao();
        $sqlINT007 = <<<SQL
            SELECT INT007_Id
            FROM INT007
            WHERE INT007_C004_Id = 0 AND INT007_SKU = '@MIGRACAO_PRODUTOS_V1'
SQL;
        $consulta = mysql_query($sqlINT007);
        if (!$consulta || !mysql_fetch_assoc($consulta)) throw new \Exception('Carga histórica de proteção ISET não concluída. Execute a atualização de base e o script de carga pela IDE. Escritas remotas bloqueadas.');
        $sql = <<<SQL
            SHOW INDEX FROM INT007 WHERE Key_name = 'UQ_INT007_Empresa_SKU'
SQL;
        $consulta = mysql_query($sql);
        $colunas = array();
        if (!$consulta) throw new \Exception('Não foi possível verificar a unicidade da proteção ISET.');
        while ($indice = mysql_fetch_assoc($consulta)) {
            if ((int) $indice['Non_unique'] !== 0 || $indice['Sub_part'] !== null) throw new \Exception('Índice de proteção ISET inválido.');
            $colunas[(int) $indice['Seq_in_index']] = $indice['Column_name'];
        }
        ksort($colunas);
        if (array_values($colunas) !== array('INT007_C004_Id', 'INT007_SKU')) throw new \Exception('Chave única da proteção ISET ausente ou inválida.');
        $sqlINT006 = <<<SQL
            SELECT INT006_Versao, INT006_Tentativas, INT006_Proxima_Tentativa, INT006_Motivo
            FROM INT006
            LIMIT 0
SQL;
        if (!mysql_query($sqlINT006)) throw new \Exception('Estrutura de pendências ISET incompleta.');
    }

    public function marcarPendente($empresaId, $d009Id, $alteracao = true)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id;
        if ($empresaId <= 0 || $d009Id <= 0) return false;
        $versao = $alteracao ? 'INT006_Versao + 1' : 'INT006_Versao';
        // Alterações de preço/estoque nunca removem a posse do worker nem seu motivo de erro.
        $sqlINT006 = <<<SQL
            INSERT INTO INT006 (
                INT006_C004_Id, INT006_D009_Id, INT006_Status,
                INT006_Flag_Processando, INT006_Data_Hora_Pendente,
                INT006_Versao, INT006_Tentativas, INT006_Proxima_Tentativa, INT006_Motivo
            ) VALUES ('{$empresaId}', '{$d009Id}', 'P', 'N', NOW(), 1, 0, NOW(), 'AGUARDANDO')
            ON DUPLICATE KEY UPDATE INT006_Versao = {$versao}
SQL;
        return mysql_query($sqlINT006) !== false;
    }

    public function adquirir($empresaId, $d009Id, $automatico)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id;
        $token = bin2hex(random_bytes(16));
        $prazo = $automatico ? 'AND INT006_Proxima_Tentativa <= NOW()' : '';
        $sqlINT006 = <<<SQL
            UPDATE INT006
            SET INT006_Flag_Processando = 'S',
                INT006_Token_Processamento = '{$token}',
                INT006_Data_Hora_Processando = NOW(),
                INT006_Tentativas = INT006_Tentativas + 1
            WHERE INT006_C004_Id = '{$empresaId}' AND INT006_D009_Id = '{$d009Id}'
                AND INT006_Status IN ('P', 'E')
                AND (INT006_Flag_Processando <> 'S' OR INT006_Data_Hora_Processando < DATE_SUB(NOW(), INTERVAL 10 MINUTE))
                {$prazo}
SQL;
        if (!mysql_query($sqlINT006)) throw new \Exception('Erro ao adquirir pendência ISET D009 #' . $d009Id . ': ' . mysql_error());
        if (mysql_affected_rows() !== 1) return null;
        $sqlINT006 = <<<SQL
            SELECT * FROM INT006
            WHERE INT006_C004_Id = '{$empresaId}' AND INT006_D009_Id = '{$d009Id}'
                AND INT006_Token_Processamento = '{$token}'
SQL;
        $consulta = mysql_query($sqlINT006);
        if (!$consulta || !($INT006 = mysql_fetch_assoc($consulta))) throw new \Exception('Não foi possível reler a pendência adquirida.');
        return $INT006;
    }

    public function possui(array $INT006)
    {
        $token = mysql_real_escape_string($INT006['INT006_Token_Processamento']);
        $sqlINT006 = <<<SQL
            SELECT INT006_Id FROM INT006
            WHERE INT006_Id = '{$INT006['INT006_Id']}'
                AND INT006_C004_Id = '{$INT006['INT006_C004_Id']}'
                AND INT006_Flag_Processando = 'S' AND INT006_Token_Processamento = '{$token}'
SQL;
        $consulta = mysql_query($sqlINT006);
        return $consulta && mysql_num_rows($consulta) === 1;
    }

    public function finalizar(array $INT006, array $resultado)
    {
        $token = mysql_real_escape_string($INT006['INT006_Token_Processamento']);
        $mensagem = json_decode(json_encode($resultado['mensagem'], JSON_INVALID_UTF8_SUBSTITUTE), true);
        $mensagem = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '�', $mensagem);
        $mensagem = substr($mensagem, 0, 1000);
        while ($mensagem !== '' && !preg_match('//u', $mensagem)) $mensagem = substr($mensagem, 0, -1);
        $mensagem = mysql_real_escape_string($mensagem);
        $motivo = mysql_real_escape_string($resultado['motivo']);
        if ($resultado['resultado'] !== 'falhos') {
            $sqlINT006 = <<<SQL
                DELETE FROM INT006
                WHERE INT006_Id = '{$INT006['INT006_Id']}' AND INT006_C004_Id = '{$INT006['INT006_C004_Id']}'
                    AND INT006_Token_Processamento = '{$token}' AND INT006_Flag_Processando = 'S'
                    AND INT006_Versao = '{$INT006['INT006_Versao']}'
SQL;
            if (!mysql_query($sqlINT006)) throw new \Exception('Erro ao encerrar pendência ISET: ' . mysql_error());
            if (mysql_affected_rows() === 1) return;
        }
        $minutos = (int) $INT006['INT006_Tentativas'] === 1 ? 10 : ((int) $INT006['INT006_Tentativas'] === 2 ? 30 : 60);
        $status = $resultado['resultado'] === 'falhos' ? 'E' : 'P';
        // Se chegou uma alteração durante a chamada, preserva a nova versão para outro ciclo.
        $sqlINT006 = <<<SQL
            UPDATE INT006
            SET INT006_Status = IF(INT006_Versao = '{$INT006['INT006_Versao']}', '{$status}', 'P'),
                INT006_Motivo = IF(INT006_Versao = '{$INT006['INT006_Versao']}', '{$motivo}', 'AGUARDANDO'),
                INT006_Proxima_Tentativa = IF(INT006_Versao = '{$INT006['INT006_Versao']}', DATE_ADD(NOW(), INTERVAL {$minutos} MINUTE), NOW()),
                INT006_Data_Hora_Processado = NOW(), INT006_Mensagem_Erro = IF(INT006_Versao = '{$INT006['INT006_Versao']}', '{$mensagem}', NULL),
                INT006_Flag_Processando = 'N', INT006_Token_Processamento = NULL, INT006_Data_Hora_Processando = NULL
            WHERE INT006_Id = '{$INT006['INT006_Id']}' AND INT006_C004_Id = '{$INT006['INT006_C004_Id']}'
                AND INT006_Token_Processamento = '{$token}' AND INT006_Flag_Processando = 'S'
SQL;
        if (!mysql_query($sqlINT006) || mysql_affected_rows() !== 1) throw new \Exception('Não foi possível finalizar a pendência ISET com o token adquirido.');
    }

    public function controles($empresaId, $d009Id, $sku)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id;
        $sku = mysql_real_escape_string($sku);
        $sqlINT007 = <<<SQL
            SELECT * FROM INT007
            WHERE INT007_C004_Id = '{$empresaId}'
                AND (INT007_D009_Id = '{$d009Id}' OR INT007_SKU = '{$sku}')
SQL;
        $consulta = mysql_query($sqlINT007);
        if (!$consulta) throw new \Exception('Não foi possível consultar a proteção de criação ISET.');
        $INT007s = array();
        while ($INT007 = mysql_fetch_assoc($consulta)) $INT007s[] = $INT007;
        return $INT007s;
    }

    public function reservarCriacao($empresaId, $d009Id, $sku, $tentativa)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id;
        $sku = mysql_real_escape_string($sku); $tentativa = mysql_real_escape_string($tentativa);
        $sqlINT007 = <<<SQL
            INSERT INTO INT007 (
                INT007_C004_Id, INT007_D009_Id, INT007_SKU, INT007_Estado,
                INT007_Tentativa, INT007_Data_Hora_Tentativa
            ) VALUES ('{$empresaId}', '{$d009Id}', '{$sku}', 'TENTATIVA_REGISTRADA', '{$tentativa}', NOW())
SQL;
        // O INSERT é atômico e durável em autocommit. Não remover essa linha após falha HTTP.
        if (!mysql_query($sqlINT007)) throw new \Exception('Criação bloqueada: reserva já existente ou falha ao persistir INT007.');
    }

    public function temHistorico($empresaId, $d009Id)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id;
        // Também cobre evidências gravadas após a migração, inclusive consultas manuais.
        $sql = <<<SQL
            SELECT INT003_D009_Id AS D009_Id FROM INT003
            WHERE INT003_C004_Id = '{$empresaId}' AND INT003_D009_Id = '{$d009Id}'
            UNION ALL
            SELECT INT005_D009_Id FROM INT005
            WHERE INT005_C004_Id = '{$empresaId}' AND INT005_D009_Id = '{$d009Id}'
            UNION ALL
            SELECT INT004_D009_Id FROM INT004
            WHERE INT004_C004_Id = '{$empresaId}' AND INT004_D009_Id = '{$d009Id}' AND INT004_Ultimo_Sucesso_Em IS NOT NULL
            UNION ALL
            SELECT INT001_Id_Principal FROM INT001
            WHERE INT001_C004_Id = '{$empresaId}' AND INT001_Integracao = 'ISET' AND INT001_Tabela_Principal = 'D009'
                AND INT001_Id_Principal = '{$d009Id}'
                AND (INT001_Etapa = 'CRIACAO_PRODUTO' OR INT001_Etapa = 'INICIO_ENVIO'
                    OR (INT001_Status = 'S' AND INT001_Id_Externo REGEXP '^[1-9][0-9]*$' AND INT001_Operacao IN ('SINCRONIZAR_PRODUTO', 'CONSULTAR_PRODUTO')))
            LIMIT 1
SQL;
        $consulta = mysql_query($sql);
        if (!$consulta) throw new \Exception('Não foi possível verificar o histórico anterior à criação.');
        return mysql_num_rows($consulta) > 0;
    }

    public function agendarAposConsulta(array $INT006)
    {
        $token = mysql_real_escape_string($INT006['INT006_Token_Processamento']);
        $sqlINT006 = <<<SQL
            UPDATE INT006 SET INT006_Status = 'P', INT006_Motivo = 'AGUARDANDO',
                INT006_Proxima_Tentativa = NOW(), INT006_Data_Hora_Processado = NOW(),
                INT006_Flag_Processando = 'N', INT006_Token_Processamento = NULL, INT006_Data_Hora_Processando = NULL
            WHERE INT006_Id = '{$INT006['INT006_Id']}' AND INT006_C004_Id = '{$INT006['INT006_C004_Id']}'
                AND INT006_Token_Processamento = '{$token}' AND INT006_Flag_Processando = 'S'
SQL;
        if (!mysql_query($sqlINT006) || mysql_affected_rows() !== 1) throw new \Exception('Não foi possível reagendar a pendência consultada.');
    }

    public function marcarIncerto($empresaId, $sku, $tentativa)
    {
        $empresaId = (int) $empresaId; $sku = mysql_real_escape_string($sku); $tentativa = mysql_real_escape_string($tentativa);
        $sqlINT007 = <<<SQL
            UPDATE INT007 SET INT007_Estado = 'RESULTADO_INCERTO'
            WHERE INT007_C004_Id = '{$empresaId}' AND INT007_SKU = '{$sku}'
                AND INT007_Tentativa = '{$tentativa}' AND INT007_Estado = 'TENTATIVA_REGISTRADA'
SQL;
        if (!mysql_query($sqlINT007)) throw new \Exception('Erro ao registrar resultado incerto; reserva de criação preservada.');
    }

    public function confirmarExistencia($empresaId, $d009Id, $sku, $idIset)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id; $idIset = (int) $idIset;
        foreach ($this->controles($empresaId, $d009Id, $sku) as $INT007) {
            if ((int) $INT007['INT007_D009_Id'] !== $d009Id || ((int) $INT007['INT007_Id_Produto_ISET'] > 0 && (int) $INT007['INT007_Id_Produto_ISET'] !== $idIset)) {
                throw new \Exception('Conflito com o controle persistente de produto ISET.');
            }
        }
        $skuOriginal = $sku;
        $sku = mysql_real_escape_string($sku);
        $sqlINT007 = <<<SQL
            INSERT INTO INT007 (INT007_C004_Id, INT007_D009_Id, INT007_SKU, INT007_Estado, INT007_Id_Produto_ISET, INT007_Data_Hora_Confirmacao)
            VALUES ('{$empresaId}', '{$d009Id}', '{$sku}', 'EXISTENCIA_CONFIRMADA', '{$idIset}', NOW())
            ON DUPLICATE KEY UPDATE
                INT007_Estado = IF(INT007_D009_Id = VALUES(INT007_D009_Id) AND (INT007_Id_Produto_ISET IS NULL OR INT007_Id_Produto_ISET = VALUES(INT007_Id_Produto_ISET)), 'EXISTENCIA_CONFIRMADA', INT007_Estado),
                INT007_Data_Hora_Confirmacao = IF(INT007_D009_Id = VALUES(INT007_D009_Id) AND (INT007_Id_Produto_ISET IS NULL OR INT007_Id_Produto_ISET = VALUES(INT007_Id_Produto_ISET)), NOW(), INT007_Data_Hora_Confirmacao),
                INT007_Id_Produto_ISET = IF(INT007_D009_Id = VALUES(INT007_D009_Id) AND (INT007_Id_Produto_ISET IS NULL OR INT007_Id_Produto_ISET = VALUES(INT007_Id_Produto_ISET)), VALUES(INT007_Id_Produto_ISET), INT007_Id_Produto_ISET)
SQL;
        if (!mysql_query($sqlINT007)) throw new \Exception('Não foi possível persistir a existência do produto ISET.');
        foreach ($this->controles($empresaId, $d009Id, $skuOriginal) as $INT007) {
            if ((int) $INT007['INT007_D009_Id'] !== $d009Id || ((int) $INT007['INT007_Id_Produto_ISET'] > 0 && (int) $INT007['INT007_Id_Produto_ISET'] !== $idIset)) throw new \Exception('Conflito de produto ISET durante a confirmação.');
        }
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) definirClasse('ISET_PRODUTOS_CONTROLE');
if (!class_exists(__NAMESPACE__ . '\\iset_produtos_controle', false)) class_alias(__NAMESPACE__ . '\\iset_produtos_controle_', __NAMESPACE__ . '\\iset_produtos_controle');
