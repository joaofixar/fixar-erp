<?php
namespace hardness;

definirClasse('EST013');
class EST013_
{
    /**
     * montarEmail
     *
     * @param string $ETC013OS
     */
    public function montarEmail($ETC013OS)
    {
        global $g, $confUsuario;
        
        include_once('prog/etc/etc013/grid_func-callback.php');
        
        $extra    = base64_decode(urldecode($ETC013OS));
        $sql      = <<<EOT
			SELECT 
			I002_Id, 
			I001_Modulo, 
			I002_I001_Id, 
			I001_C007_Id, 
			I002_Topico, 
			I002_Previsao_OS_Horas, 
			I002_Prioridade, 
			PROGRAMADOR.C007_Primeiro_Nome, 
			I002_C007_Id, Analista.C007_Primeiro_Nome, 
			I002_Status, 
			I002_Inicio_OS_Data, 
			I002_Inicio_OS_Hora, 
			I002_Conclusao_OS_Data, 
			I002_Conclusao_OS_Hora 
			FROM 
			I002
			{$extra}
EOT;
        $Consulta = mysql_query($sql);
        
        $array['Para']    = '';
        $array['Assunto'] = "Lista de Ordens de Serviços";
        $array['Corpo']   = '<div style="margin: 5px;"><b>Modelo Pendentes:</b><br />';
        $array['Corpo'] .= '<table>';
        $array['Corpo'] .= '<th>Tarefa</th>';
        $array['Corpo'] .= '<th>Prev. Horas</th>';
        $array['Corpo'] .= '<th>Dias</th>';
        $array['Corpo'] .= '<th>Status</th>';
        while ($row = mysql_fetch_array($Consulta)) {
            $tmpArray                                          = array();
            $tmpArray['todosCampos']['I002_Previsao_OS_Horas'] = $row['I002_Previsao_OS_Horas'];
            $tmpArray['todosCampos']['I002_Status']            = $row['I002_Status'];
            $array['Corpo'] .= '<tr>';
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px;\">{$row['I002_Topico']}</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: center;\">{$row['I002_Previsao_OS_Horas']}</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: right;\">" . validaCampoDiasOs($tmpArray) . "</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: center;\">" . validaCampoStatusOs($tmpArray) . "</td>";
            $array['Corpo'] .= '</tr>';
        }
        $array['Corpo'] .= '</table>';
        $array['Corpo'] .= '</div><br />';
        
        mysql_data_seek($Consulta, 0);
        $array['Corpo'] .= '<div style="margin: 5px;"><b>Modelo Concluídos:</b><br />';
        $array['Corpo'] .= '<table>';
        $array['Corpo'] .= '<th>Tarefa</th>';
        $array['Corpo'] .= '<th>Prev. Horas</th>';
        $array['Corpo'] .= '<th>Conc. (H)</th>';
        $array['Corpo'] .= '<th>Conc. (D)</th>';
        $array['Corpo'] .= '<th>Data Conclusão</th>';
        $array['Corpo'] .= '<th>Status</th>';
        while ($row = mysql_fetch_array($Consulta)) {
            $tmpArray                                          = array();
            $tmpArray['todosCampos']['I002_Previsao_OS_Horas'] = $row['I002_Previsao_OS_Horas'];
            $tmpArray['todosCampos']['I002_Status']            = $row['I002_Status'];
            $tmpArray['todosCampos']['I002_Conclusao_OS_Data'] = $row['I002_Conclusao_OS_Data'];
            $tmpArray['todosCampos']['I002_Conclusao_OS_Hora'] = $row['I002_Conclusao_OS_Hora'];
            $tmpArray['todosCampos']['I002_Inicio_OS_Data']    = $row['I002_Inicio_OS_Data'];
            $tmpArray['todosCampos']['I002_Inicio_OS_Hora']    = $row['I002_Inicio_OS_Hora'];
            
            $styleTD = ' style="border-top: 1px solid black; padding: 3px; text-align: center;"';
            $array['Corpo'] .= '<tr>';
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px;\">{$row['I002_Topico']}</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: center;\">{$row['I002_Previsao_OS_Horas']}</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: center;\">" . validaCampoDiasOsCH($tmpArray) . "</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: right;\">" . validaCampoDiasOsCD($tmpArray) . "</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: center;\">" . gCorrigeData($row['I002_Conclusao_OS_Data']) . "</td>";
            $array['Corpo'] .= "<td style=\"border-top: 1px solid black; padding: 3px; text-align: right;\">" . validaCampoStatusOs($tmpArray) . "</td>";
            $array['Corpo'] .= '</tr>';
        }
        $array['Corpo'] .= '</table>';
        $array['Corpo'] .= '</div><br /><br />';
        
        return $array;
    }
    
    /**
     * montarEmail
     *
     * @param string $ETC013OS
     * @param array $array
     */
    public function montarEmailCallback($ETC013OS, $array)
    {
        global $g, $confUsuario;
        
        include_once('prog/etc/etc013/grid_func-callback.php');
        
        $extra    = base64_decode(urldecode($ETC013OS));
        $sql      = <<<EOT
			SELECT 
			I002_Id, 
			I001_Modulo, 
			I002_I001_Id, 
			I001_C007_Id, 
			I002_Topico, 
			I002_Previsao_OS_Horas, 
			I002_Prioridade, 
			PROGRAMADOR.C007_Primeiro_Nome, 
			I002_C007_Id, Analista.C007_Primeiro_Nome, 
			I002_Status, 
			I002_Inicio_OS_Data, 
			I002_Inicio_OS_Hora, 
			I002_Conclusao_OS_Data, 
			I002_Conclusao_OS_Hora 
			FROM 
			I002
			{$extra}
EOT;
        $Consulta = mysql_query($sql);
        
        while ($row = mysql_fetch_array($Consulta)) {
            $para = mysql_real_escape_string($array['Para'] . ' (Em: ' . date('d/m/Y') . ')');
            $sql  = "UPDATE I002 SET I002_Email_Enviado = '{$para}' WHERE I002_Id = '{$row['I002_Id']}';";
            mysql_query($sql);
        }
    }
}
