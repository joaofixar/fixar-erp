<?php
namespace hardness;

/** Controla a posse e a consolidação das pendências de status de pedidos ISET. */
class iset_status_pedidos_controle_
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

        // O worker nunca pode chamar a ISET dentro da transação que atualizou T005.
        // Fora de transação o RELEASE retorna 1305; dentro dela ele conclui normalmente.
        $savepoint = 'iset_verifica_' . bin2hex(random_bytes(16));
        $sql = <<<SQL
            SAVEPOINT {$savepoint}
SQL;
        if (!mysql_query($sql)) throw new \Exception('Falha verificando transação ISET ao criar savepoint: ' . mysql_error());
        $sql = <<<SQL
            RELEASE SAVEPOINT {$savepoint}
SQL;
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
        $this->verificarTransacao();
        $sqlINT008 = <<<SQL
            SELECT INT008_Versao,
                INT008_Tentativas,
                INT008_Proxima_Tentativa,
                INT008_Motivo
            FROM INT008
            LIMIT 0
SQL;
        if (!mysql_query($sqlINT008)) throw new \Exception('Estrutura de pendências de status ISET incompleta.');

        $sql = <<<SQL
            SHOW INDEX FROM INT008 WHERE Key_name = 'UQ_INT008_Empresa_T005'
SQL;
        $consulta = mysql_query($sql);
        if (!$consulta) throw new \Exception('Não foi possível verificar a unicidade da fila de status ISET.');
        $colunas = array();
        while ($indice = mysql_fetch_assoc($consulta)) {
            if ((int) $indice['Non_unique'] !== 0 || $indice['Sub_part'] !== null) throw new \Exception('Chave única da fila de status ISET inválida.');
            $colunas[(int) $indice['Seq_in_index']] = $indice['Column_name'];
        }
        ksort($colunas);
        if (array_values($colunas) !== array('INT008_C004_Id', 'INT008_T005_Id')) {
            throw new \Exception('Chave única da fila de status ISET ausente ou inválida.');
        }
    }

    public function marcarPendente($empresaId, $T005_Id)
    {
        $empresaId = (int) $empresaId;
        $T005_Id = (int) $T005_Id;
        if ($empresaId <= 0 || $T005_Id <= 0) return false;

        // A versão é incrementada inclusive quando o worker já possui a linha.
        // Assim, a finalização preserva a alteração de status mais recente.
        $sqlINT008 = <<<SQL
            INSERT INTO INT008 (
                INT008_C004_Id,
                INT008_T005_Id,
                INT008_Status,
                INT008_Flag_Processando,
                INT008_Data_Hora_Pendente,
                INT008_Versao,
                INT008_Tentativas,
                INT008_Proxima_Tentativa,
                INT008_Motivo
            ) VALUES (
                '{$empresaId}',
                '{$T005_Id}',
                'P',
                'N',
                NOW(),
                1,
                0,
                NOW(),
                'AGUARDANDO'
            ) ON DUPLICATE KEY UPDATE
                INT008_Versao = INT008_Versao + 1
SQL;
        return mysql_query($sqlINT008) !== false;
    }

    public function adquirir($empresaId, $T005_Id, $automatico)
    {
        $empresaId = (int) $empresaId;
        $T005_Id = (int) $T005_Id;
        $token = bin2hex(random_bytes(16));
        $prazo = $automatico ? 'AND INT008_Proxima_Tentativa <= NOW()' : '';
        $sqlINT008 = <<<SQL
            UPDATE INT008
            SET INT008_Flag_Processando = 'S',
                INT008_Token_Processamento = '{$token}',
                INT008_Data_Hora_Processando = NOW(),
                INT008_Tentativas = INT008_Tentativas + 1
            WHERE INT008_C004_Id = '{$empresaId}'
                AND INT008_T005_Id = '{$T005_Id}'
                AND INT008_Status IN ('P', 'E')
                AND (INT008_Flag_Processando <> 'S' OR INT008_Data_Hora_Processando < DATE_SUB(NOW(), INTERVAL 10 MINUTE))
                {$prazo}
SQL;
        if (!mysql_query($sqlINT008)) throw new \Exception('Erro ao adquirir pendência de status ISET T005 #' . $T005_Id . ': ' . mysql_error());
        if (mysql_affected_rows() !== 1) return null;

        $sqlINT008 = <<<SQL
            SELECT *
            FROM INT008
            WHERE INT008_C004_Id = '{$empresaId}'
                AND INT008_T005_Id = '{$T005_Id}'
                AND INT008_Token_Processamento = '{$token}'
SQL;
        $consulta = mysql_query($sqlINT008);
        if (!$consulta || !($INT008 = mysql_fetch_assoc($consulta))) throw new \Exception('Não foi possível reler a pendência de status ISET adquirida.');
        return $INT008;
    }

    public function possui(array $INT008)
    {
        $token = mysql_real_escape_string($INT008['INT008_Token_Processamento']);
        $sqlINT008 = <<<SQL
            SELECT INT008_Id
            FROM INT008
            WHERE INT008_Id = '{$INT008['INT008_Id']}'
                AND INT008_C004_Id = '{$INT008['INT008_C004_Id']}'
                AND INT008_Flag_Processando = 'S'
                AND INT008_Token_Processamento = '{$token}'
SQL;
        $consulta = mysql_query($sqlINT008);
        return $consulta && mysql_num_rows($consulta) === 1;
    }

    public function finalizar(array $INT008, array $resultado)
    {
        $token = mysql_real_escape_string($INT008['INT008_Token_Processamento']);
        $mensagem = json_decode(json_encode($resultado['mensagem'], JSON_INVALID_UTF8_SUBSTITUTE), true);
        $mensagem = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '�', $mensagem);
        $mensagem = substr($mensagem, 0, 1000);
        while ($mensagem !== '' && !preg_match('//u', $mensagem)) $mensagem = substr($mensagem, 0, -1);
        $mensagem = mysql_real_escape_string($mensagem);
        $motivo = mysql_real_escape_string($resultado['motivo']);

        if ($resultado['resultado'] !== 'falhos') {
            $sqlINT008 = <<<SQL
                DELETE FROM INT008
                WHERE INT008_Id = '{$INT008['INT008_Id']}'
                    AND INT008_C004_Id = '{$INT008['INT008_C004_Id']}'
                    AND INT008_Token_Processamento = '{$token}'
                    AND INT008_Flag_Processando = 'S'
                    AND INT008_Versao = '{$INT008['INT008_Versao']}'
SQL;
            if (!mysql_query($sqlINT008)) throw new \Exception('Erro ao encerrar pendência de status ISET: ' . mysql_error());
            if (mysql_affected_rows() === 1) return;
        }

        $minutos = (int) $INT008['INT008_Tentativas'] === 1 ? 10 : ((int) $INT008['INT008_Tentativas'] === 2 ? 30 : 60);
        $status = $resultado['resultado'] === 'falhos' ? 'E' : 'P';
        // Uma nova versão entra imediatamente na fila e não herda o erro do status anterior.
        $sqlINT008 = <<<SQL
            UPDATE INT008
            SET INT008_Status = IF(INT008_Versao = '{$INT008['INT008_Versao']}', '{$status}', 'P'),
                INT008_Motivo = IF(INT008_Versao = '{$INT008['INT008_Versao']}', '{$motivo}', 'AGUARDANDO'),
                INT008_Proxima_Tentativa = IF(INT008_Versao = '{$INT008['INT008_Versao']}', DATE_ADD(NOW(), INTERVAL {$minutos} MINUTE), NOW()),
                INT008_Data_Hora_Processado = NOW(),
                INT008_Mensagem_Erro = IF(INT008_Versao = '{$INT008['INT008_Versao']}', '{$mensagem}', NULL),
                INT008_Flag_Processando = 'N',
                INT008_Token_Processamento = NULL,
                INT008_Data_Hora_Processando = NULL
            WHERE INT008_Id = '{$INT008['INT008_Id']}'
                AND INT008_C004_Id = '{$INT008['INT008_C004_Id']}'
                AND INT008_Token_Processamento = '{$token}'
                AND INT008_Flag_Processando = 'S'
SQL;
        if (!mysql_query($sqlINT008) || mysql_affected_rows() !== 1) {
            throw new \Exception('Não foi possível finalizar a pendência de status ISET com o token adquirido.');
        }
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_STATUS_PEDIDOS_CONTROLE');
}

if (!class_exists(__NAMESPACE__ . '\\iset_status_pedidos_controle', false)) {
    class_alias(__NAMESPACE__ . '\\iset_status_pedidos_controle_', __NAMESPACE__ . '\\iset_status_pedidos_controle');
}
