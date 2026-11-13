<?php
namespace hardness;

/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Transacao
	 */
	case 'fin003CabecalhoTransacao':
		global $g;
        // Select
        $resultado = mysql_query("SELECT * FROM T216 LEFT JOIN D024 ON T216_D024_Id=D024_Id WHERE T216_Id = '{$r_T216_Id}'");
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (empty($row_)) { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }
        $row['T216_Data'] = gCorrigeData($row['T216_Data']);
        $row['T216_Valor'] = gCorrigeNumero($row['T216_Valor']);
        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc">
                        <td min-width='12%' align='center'>Data</td>
                        <td min-width='13%' align='center'>Valor</td>
                        <td min-width='25%'>Descricao</td>
                        <td min-width='25%'>Cliente</td>
                        <td min-width='25%'>Observação</td>
                    </tr>
                    <tr style="font-size:1.1em; font-weight:bold;">
                        <td style='padding:1px 1px 4px 2px' align='center'>{$row['T216_Data']}</td>
                        <td style='padding:1px 1px 4px 2px' align='center'>{$row['T216_Valor']}</td>
                        <td style='padding:1px 1px 4px 2px' align='left'>{$row['T216_Descricao']}</td>
                        <td style='padding:1px 1px 4px 2px' align='left'>{$row['D024_Nome_Empresa']}</td>
                        <td style='padding:1px 1px 4px 2px' align='left'>{$row['T216_Historico']}</td>
                    </tr>
                </table>
            </div>
EOT;

	break;
}
