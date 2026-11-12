<?php
namespace hardness;

/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Transacao
	 */
	case 'bi002OutroTelaRelatorios':
		global $g;

        $resultado = mysql_query("SELECT D176_Link,D176_Titulo FROM D176 LEFT JOIN D177 ON D177_D176_Id=D176_Id WHERE D177_Id = '{$r_D176_Id}'");
        $row = mysql_fetch_array($resultado);

        // Registro vazios = FALTA
        if (mysql_num_rows(($row)<=0)) {
            $row_ = '<span style="color: var(--BGVermelho);">Relatório não cadastrado</span>';
        }else{
            echo <<<EOT
                <style>
                    .tabela {
                        padding:20px;
                        border-collapse: collapse;
                        width: 100%;
                    }
                    table.tabela tr{
                        text-align: center;
                        padding-left:20px;
                        font-weight:bold;
                        font-size:60px;
                        font-family: arial, sans-serif;
                    }
                </style>
                <table class="tabela">
                    <!--<tr>
                        RELATÓRIO {$row['D176_Titulo']}
                    </tr>-->
                    <tr>
                        <iframe width='100%' height='100%' frameborder="0" src="{$row['D176_Link']}"></iframe>
                    </tr>
                </table>
EOT;
        }

	break;
}
