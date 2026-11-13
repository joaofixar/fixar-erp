<?php
declare(strict_types=1);

/**
 * Borda de leitura de NF para a GNRE.
 *
 * Não depende de fluxos ou classes de NF-e. A consulta usa somente fatos já
 * persistidos em T007, T008 e D006.
 */
final class GnreNfEligibilityReader
{
    public static function filters(array $source): array
    {
        $date = static function ($value): string {
            $value = trim((string) $value);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                && checkdate((int) substr($value, 5, 2), (int) substr($value, 8, 2), (int) substr($value, 0, 4))
                ? $value : '';
        };
        $text = static function ($value, int $limit): string {
            return mb_substr(trim((string) $value), 0, $limit, 'UTF-8');
        };
        $uf = strtoupper($text($source['recipient_uf'] ?? '', 2));
        return [
            'issued_from'=>$date($source['issued_from'] ?? ''),
            'issued_to'=>$date($source['issued_to'] ?? ''),
            'note_number'=>$text($source['note_number'] ?? '', 30),
            'recipient_uf'=>preg_match('/^[A-Z]{2}$/', $uf) ? $uf : '',
            'recipient_name'=>$text($source['recipient_name'] ?? '', 100),
        ];
    }

    /** Pure projection used by the synthetic contract test. */
    public static function eligible(array $fact): bool
    {
        $authorized = trim((string) ($fact['protocol'] ?? '')) !== '';
        $blocked = (string) ($fact['cancelled'] ?? 'N') === 'S'
            || (string) ($fact['invalidated'] ?? 'N') === 'S'
            || trim((string) ($fact['invalidation_protocol'] ?? '')) !== ''
            || (string) ($fact['denied'] ?? 'N') === 'S';
        return $authorized && !$blocked && (!empty($fact['st']) || !empty($fact['difal']) || !empty($fact['fcp']));
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public static function load(mysqli $db, int $companyId, string $issuedFrom, string $issuedTo, array $filters = []): array
    {
        if ($companyId <= 0 || $issuedFrom === '' || $issuedTo === '') return [];
        $noteNumber=(string) ($filters['note_number'] ?? '');
        $recipientUf=(string) ($filters['recipient_uf'] ?? '');
        $recipientName=(string) ($filters['recipient_name'] ?? '');

        $sql = "SELECT
                    n.T007_Id,
                    n.T007_Numero_Nota_Fiscal,
                    n.T007_Serie,
                    n.T007_Data_Emissao,
                    n.T007_Cliente_Razao,
                    n.T007_Cliente_CNPJ_CPF,
                    n.T007_Cliente_Inscricao_Estadual,
                    n.T007_Cliente_Codigo_Cidade_IBGE,
                    n.T007_Cliente_Estado,
                    n.T007_Origem_Modelo,
                    n.T007_Chave_Acesso_Nfe,
                    n.T007_Valor_ICMS_Substituicao_Retencao,
                    n.T007_Valor_FCP_ST,
                    n.T007_Valor_Total_Partilha_ICMS_Destino,
                    n.T007_Valor_Total_FCP_Partilha,
                    MAX(CASE WHEN IFNULL(cfop.D006_Flag_Gera_GNRE_ST, 'N') = 'S' THEN 1 ELSE 0 END) AS indicator_st,
                    MAX(CASE WHEN IFNULL(cfop.D006_Flag_Gera_GNRE_DIFAL, 'N') = 'S' THEN 1 ELSE 0 END) AS indicator_difal,
                    MAX(CASE WHEN IFNULL(cfop.D006_Flag_Gera_GNRE_FCP, 'N') = 'S' THEN 1 ELSE 0 END) AS indicator_fcp
                FROM T007 n
                INNER JOIN T008 item ON item.T008_T007_Id=n.T007_Id
                INNER JOIN D006 cfop ON cfop.D006_Id=item.T008_D006_Id
                WHERE n.T007_C004_Id=?
                    AND n.T007_Flag_ACP='6'
                    AND n.T007_Data_Emissao BETWEEN ? AND ?
                    AND IFNULL(n.T007_Numero_Protocolo_Nfe, '') <> ''
                    AND IFNULL(n.T007_Flag_Cancelada, 'N') <> 'S'
                    AND IFNULL(n.T007_Flag_Inutilizado, 'N') <> 'S'
                    AND IFNULL(n.T007_Protocolo_Inutilizacao, '') = ''
                    AND IFNULL(n.T007_Flag_Denegado, 'N') <> 'S'
                    AND (? = '' OR n.T007_Numero_Nota_Fiscal = ?)
                    AND (? = '' OR UPPER(IFNULL(n.T007_Cliente_Estado, '')) = ?)
                    AND (? = '' OR n.T007_Cliente_Razao LIKE CONCAT('%', ?, '%'))
                    AND (
                        (EXISTS (SELECT 1 FROM T008 es INNER JOIN D006 cs ON cs.D006_Id=es.T008_D006_Id WHERE es.T008_T007_Id=n.T007_Id AND IFNULL(cs.D006_Flag_Gera_GNRE_ST,'N')='S') AND n.T007_Valor_ICMS_Substituicao_Retencao>0 AND NOT EXISTS (SELECT 1 FROM GNRE002 ds INNER JOIN GNRE001 gs ON gs.GNRE001_Id=ds.GNRE002_GNRE001_Id AND gs.GNRE001_C004_Id=n.T007_C004_Id WHERE ds.GNRE002_T007_Id=n.T007_Id AND ds.GNRE002_Tributo_Origem='ST'))
                        OR (EXISTS (SELECT 1 FROM T008 ed INNER JOIN D006 cd ON cd.D006_Id=ed.T008_D006_Id WHERE ed.T008_T007_Id=n.T007_Id AND IFNULL(cd.D006_Flag_Gera_GNRE_DIFAL,'N')='S') AND n.T007_Valor_Total_Partilha_ICMS_Destino>0 AND NOT EXISTS (SELECT 1 FROM GNRE002 dd INNER JOIN GNRE001 gd ON gd.GNRE001_Id=dd.GNRE002_GNRE001_Id AND gd.GNRE001_C004_Id=n.T007_C004_Id WHERE dd.GNRE002_T007_Id=n.T007_Id AND dd.GNRE002_Tributo_Origem='DIFAL'))
                        OR (EXISTS (SELECT 1 FROM T008 ef INNER JOIN D006 cf ON cf.D006_Id=ef.T008_D006_Id WHERE ef.T008_T007_Id=n.T007_Id AND IFNULL(cf.D006_Flag_Gera_GNRE_FCP,'N')='S') AND n.T007_Valor_Total_FCP_Partilha>0 AND NOT EXISTS (SELECT 1 FROM GNRE002 df INNER JOIN GNRE001 gf ON gf.GNRE001_Id=df.GNRE002_GNRE001_Id AND gf.GNRE001_C004_Id=n.T007_C004_Id WHERE df.GNRE002_T007_Id=n.T007_Id AND df.GNRE002_Tributo_Origem='FCP'))
                    )
                GROUP BY n.T007_Id, n.T007_Numero_Nota_Fiscal, n.T007_Serie,
                    n.T007_Data_Emissao, n.T007_Cliente_Razao, n.T007_Cliente_CNPJ_CPF,
                    n.T007_Cliente_Inscricao_Estadual, n.T007_Cliente_Codigo_Cidade_IBGE,
                    n.T007_Cliente_Estado, n.T007_Origem_Modelo,
                    n.T007_Chave_Acesso_Nfe, n.T007_Valor_ICMS_Substituicao_Retencao,
                    n.T007_Valor_FCP_ST,
                    n.T007_Valor_Total_Partilha_ICMS_Destino, n.T007_Valor_Total_FCP_Partilha
                HAVING indicator_st=1 OR indicator_difal=1 OR indicator_fcp=1
                ORDER BY n.T007_Data_Emissao DESC, n.T007_Id DESC";
        $statement = mysqli_prepare($db, $sql);
        if (!$statement) throw new RuntimeException('Não foi possível preparar a consulta de notas elegíveis.');
        mysqli_stmt_bind_param($statement, 'issssssss', $companyId, $issuedFrom, $issuedTo, $noteNumber, $noteNumber, $recipientUf, $recipientUf, $recipientName, $recipientName);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $rows = [];
        while ($result && ($row = mysqli_fetch_assoc($result))) {
            $row['T007_Id'] = (int) $row['T007_Id'];
            $row['indicator_st'] = (int) $row['indicator_st'] === 1;
            $row['indicator_difal'] = (int) $row['indicator_difal'] === 1;
            $row['indicator_fcp'] = (int) $row['indicator_fcp'] === 1;
            $rows[] = $row;
        }
        mysqli_stmt_close($statement);
        return $rows;
    }

    /** Releitura servidor-side de uma única NF, sem confiar no navegador. */
    public static function loadById(mysqli $db, int $companyId, int $noteId): ?array
    {
        if ($companyId <= 0 || $noteId <= 0) return null;
        $sql = "SELECT
                    n.T007_Id, n.T007_Numero_Nota_Fiscal, n.T007_Serie,
                    n.T007_Data_Emissao, n.T007_Cliente_Razao, n.T007_Cliente_CNPJ_CPF,
                    n.T007_Cliente_Inscricao_Estadual, n.T007_Cliente_Codigo_Cidade_IBGE,
                    n.T007_Cliente_Estado, n.T007_Origem_Modelo, n.T007_Chave_Acesso_Nfe,
                    n.T007_Valor_ICMS_Substituicao_Retencao,
                    n.T007_Valor_FCP_ST,
                    n.T007_Valor_Total_Partilha_ICMS_Destino,
                    n.T007_Valor_Total_FCP_Partilha,
                    MAX(CASE WHEN IFNULL(cfop.D006_Flag_Gera_GNRE_ST, 'N') = 'S' THEN 1 ELSE 0 END) AS indicator_st,
                    MAX(CASE WHEN IFNULL(cfop.D006_Flag_Gera_GNRE_DIFAL, 'N') = 'S' THEN 1 ELSE 0 END) AS indicator_difal,
                    MAX(CASE WHEN IFNULL(cfop.D006_Flag_Gera_GNRE_FCP, 'N') = 'S' THEN 1 ELSE 0 END) AS indicator_fcp
                FROM T007 n
                INNER JOIN T008 item ON item.T008_T007_Id=n.T007_Id
                INNER JOIN D006 cfop ON cfop.D006_Id=item.T008_D006_Id
                WHERE n.T007_Id=? AND n.T007_C004_Id=? AND n.T007_Flag_ACP='6'
                    AND IFNULL(n.T007_Numero_Protocolo_Nfe, '') <> ''
                    AND IFNULL(n.T007_Flag_Cancelada, 'N') <> 'S'
                    AND IFNULL(n.T007_Flag_Inutilizado, 'N') <> 'S'
                    AND IFNULL(n.T007_Protocolo_Inutilizacao, '') = ''
                    AND IFNULL(n.T007_Flag_Denegado, 'N') <> 'S'
                GROUP BY n.T007_Id, n.T007_Numero_Nota_Fiscal, n.T007_Serie,
                    n.T007_Data_Emissao, n.T007_Cliente_Razao, n.T007_Cliente_CNPJ_CPF,
                    n.T007_Cliente_Inscricao_Estadual, n.T007_Cliente_Codigo_Cidade_IBGE,
                    n.T007_Cliente_Estado, n.T007_Origem_Modelo, n.T007_Chave_Acesso_Nfe,
                    n.T007_Valor_ICMS_Substituicao_Retencao,
                    n.T007_Valor_FCP_ST,
                    n.T007_Valor_Total_Partilha_ICMS_Destino,
                    n.T007_Valor_Total_FCP_Partilha
                HAVING indicator_st=1 OR indicator_difal=1 OR indicator_fcp=1";
        $statement = mysqli_prepare($db, $sql);
        if (!$statement) throw new RuntimeException('Não foi possível reler a nota para inclusão.');
        mysqli_stmt_bind_param($statement, 'ii', $noteId, $companyId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);
        if (!$row) return null;
        $row['T007_Id']=(int) $row['T007_Id'];
        foreach (['indicator_st','indicator_difal','indicator_fcp'] as $key) $row[$key]=(int) $row[$key] === 1;
        return $row;
    }
}
