<?php
namespace hardness;

/**
 * Sinaliza que outro processo já concluiu ou mantém a chave de importação.
 *
 * O controlador trata esse caso como resultado normal de idempotência, nunca
 * como falha da importação vencedora.
 */
class iset_idempotencia_conflito extends \Exception
{
    private $detalhes;

    public function __construct(array $detalhes)
    {
        $this->detalhes = $detalhes;

        $mensagem = !empty($detalhes['emProcessamento'])
            ? 'Pedido ISET já está sendo processado.'
            : 'Pedido ISET já importado anteriormente.';

        parent::__construct($mensagem);
    }

    public function detalhes()
    {
        return $this->detalhes;
    }
}

/**
 * Mantém a unicidade da importação ISET por empresa e pedido externo.
 *
 * Seus métodos são chamados somente após START TRANSACTION. Assim, a chave
 * provisória é desfeita no mesmo ROLLBACK que desfaz o pedido.
 */
class iset_idempotencia_
{
    public function adquirirNaTransacao($empresaId, $pedidoExterno)
    {
        $empresaId = (int) $empresaId;
        $pedidoExterno = $this->normalizarPedidoExterno($pedidoExterno);

        if ($empresaId <= 0 || $pedidoExterno === '') {
            throw new \Exception('Não foi possível identificar a chave de idempotência do pedido ISET.');
        }

        $pedidoSql = mysql_real_escape_string($pedidoExterno);
        $sql = <<<SQL
            INSERT INTO IST001 (
                IST001_C004_Id,
                IST001_Id_Pedido_Externo,
                IST001_Status,
                IST001_Data_Hora_Criacao
            ) VALUES (
                '{$empresaId}',
                '{$pedidoSql}',
                'P',
                NOW()
            )
SQL;

        if (mysql_query($sql)) {
            return array(
                'adquirido' => true,
                'id' => $this->localizarId($empresaId, $pedidoExterno),
            );
        }

        if ((int) mysql_errno() !== 1062) {
            throw new \Exception('Não foi possível adquirir a chave de idempotência do pedido ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }

        $existente = $this->detalhesExistentes($empresaId, $pedidoExterno, true);
        if ($this->pedidoVinculadoFoiExcluido($empresaId, $existente)) {
            $this->removerControleDoPedidoExcluido($existente['id']);

            if (!mysql_query($sql)) {
                throw new \Exception('Não foi possível recriar a chave de idempotência do pedido ISET. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
            }

            return array(
                'adquirido' => true,
                'id' => $this->localizarId($empresaId, $pedidoExterno),
            );
        }

        return array_merge(array('adquirido' => false), $existente);
    }

    public function concluirNaTransacao($controleId, $pedidoId)
    {
        $controleId = (int) $controleId;
        $pedidoId = (int) $pedidoId;

        if ($controleId <= 0 || $pedidoId <= 0) {
            throw new \Exception('Não foi possível concluir o controle de idempotência do pedido ISET.');
        }

        $sql = <<<SQL
            UPDATE IST001
               SET IST001_Status = 'I',
                   IST001_T005_Id = '{$pedidoId}',
                   IST001_Data_Hora_Conclusao = NOW()
             WHERE IST001_Id = '{$controleId}'
               AND IST001_Status = 'P'
SQL;

        if (!mysql_query($sql) || mysql_affected_rows() !== 1) {
            throw new \Exception('Não foi possível confirmar a idempotência do pedido ISET.');
        }
    }

    protected function detalhesExistentes($empresaId, $pedidoExterno, $bloquear = false)
    {
        $empresaId = (int) $empresaId;
        $pedidoSql = mysql_real_escape_string($pedidoExterno);
        $sql = <<<SQL
            SELECT
                IST001_Id,
                IST001_Status,
                IST001_T005_Id
              FROM IST001
             WHERE IST001_C004_Id = '{$empresaId}'
               AND IST001_Id_Pedido_Externo = '{$pedidoSql}'
             LIMIT 1
SQL;
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }

        $consulta = mysql_query($sql);
        $registro = $consulta ? mysql_fetch_assoc($consulta) : false;

        if (!$registro) {
            throw new \Exception('A chave de idempotência foi disputada, mas não pôde ser localizada.');
        }

        return array(
            'id' => (int) $registro['IST001_Id'],
            'status' => $registro['IST001_Status'],
            'pedidoId' => (int) $registro['IST001_T005_Id'],
            'emProcessamento' => $registro['IST001_Status'] === 'P',
        );
    }

    protected function pedidoVinculadoFoiExcluido($empresaId, array $controle)
    {
        $pedidoId = (int) $controle['pedidoId'];
        if ($controle['status'] !== 'I' || $pedidoId <= 0) {
            return false;
        }

        $empresaId = (int) $empresaId;
        $sql = <<<SQL
            SELECT T005_Id
              FROM T005
             WHERE T005_Id = '{$pedidoId}'
               AND T005_C004_Id = '{$empresaId}'
             LIMIT 1
SQL;
        $consulta = mysql_query($sql);
        if (!$consulta) {
            throw new \Exception('Não foi possível verificar o pedido vinculado ao controle de idempotência. Banco informou: ' . (mysql_error() ?: 'diagnóstico não retornado pelo banco.'));
        }

        return mysql_num_rows($consulta) === 0;
    }

    protected function removerControleDoPedidoExcluido($controleId)
    {
        $controleId = (int) $controleId;
        $sql = <<<SQL
            DELETE FROM IST001
             WHERE IST001_Id = '{$controleId}'
               AND IST001_Status = 'I'
SQL;
        if (!mysql_query($sql) || mysql_affected_rows() !== 1) {
            throw new \Exception('Não foi possível liberar a chave de idempotência do pedido excluído.');
        }
    }

    protected function localizarId($empresaId, $pedidoExterno)
    {
        $detalhes = $this->detalhesExistentes($empresaId, $pedidoExterno);
        $controleId = (int) $detalhes['id'];

        if ($controleId <= 0) {
            throw new \Exception('A chave de idempotência foi criada, mas não pôde ser identificada.');
        }

        return $controleId;
    }

    protected function normalizarPedidoExterno($pedidoExterno)
    {
        $pedidoExterno = trim((string) $pedidoExterno);

        if (strlen($pedidoExterno) > 32) {
            throw new \Exception('O identificador externo do pedido ISET excede o tamanho permitido.');
        }

        return $pedidoExterno;
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_IDEMPOTENCIA');
}

if (!class_exists(__NAMESPACE__ . '\\iset_idempotencia', false)) {
    class_alias(__NAMESPACE__ . '\\iset_idempotencia_', __NAMESPACE__ . '\\iset_idempotencia');
}
