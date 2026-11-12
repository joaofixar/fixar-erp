<?php
namespace hardness;
definirClasse('CAD040');
class CAD040_
{

    /**
     * insereRegistrosT159
     *
     * @param String $D106_Id
     * @param String $D125_Id
     */ 	
    public function insereRegistrosT159($D106_Id, $D125_Id)
    {
        $sql = "SELECT D106_D128_Id FROM D106 WHERE D106_Id='{$D106_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        $tipo      = $row['D106_D128_Id'];
        
        $sql = "SELECT D120_Id FROM D120 WHERE D120_D128_Id='{$tipo}'";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T159  = mysql_query("select T159_Id from T159 where T159_D120_Id='{$row['D120_Id']}' and T159_D106_Id='$D106_Id' and T159_D125_Id='$D125_Id'");
            $mT159 = mysql_fetch_array($T159);
            
            if (mysql_num_rows($T159) <= 0) {
                mysql_query("insert into T159 (T159_D120_Id,T159_D106_Id,T159_Flag_Ok, T159_D125_Id)
			                           values ('{$row['D120_Id']}','$D106_Id','N','$D125_Id')");
            }   
        }    
    }
    
    /**
     * updateT159_Flag_Ok
     *
     * @param String $D120_Id
     * @param String $D106_Id
     * @param String $selected
     * @param String $D125_Id
     * @return true
     */ 
    public function updateT159_Flag_Ok($D120_Id, $D106_Id, $selected, $D125_Id)
    {
        global $g;
        
        $T159 = mysql_query("select T159_Id from T159 where T159_D120_Id='$D120_Id' and T159_D106_Id='$D106_Id' and T159_D125_Id='$D125_Id'");
        
        $mT159 = mysql_fetch_array($T159);
        
        if (mysql_num_rows($T159) <= 0) {
            mysql_query("insert into T159 (T159_D120_Id,T159_D106_Id,T159_Flag_Ok,T159_D125_Id)
		                           values ('$D120_Id','$D106_Id','S','$D125_Id')");
        } else {
            mysql_query("update T159 set T159_Flag_Ok='$selected'
		                  where T159_Id='{$mT159['T159_Id']}'");
        }
        
        return true;      
    }
    
    /**
     * docGerarChecklist
     *
     * @param String $D125_Id
     * @param String $titulo
     * @return String
     */ 
    public function docGerarChecklist($D125_Id, $titulo)
    {
        
        $sql = <<<EOT
			SELECT *
			FROM
				D120
			left join T159 on (T159_D120_Id=D120_Id and T159_D125_Id={$D125_Id}) 
			left join D125 on D125_Id=T159_D125_Id
			left join D106 on D106_Id=D125_D106_Id
			left join D037 on D037_Id=D120_D037_Id
			WHERE
				D120_D128_Id = D106_D128_Id
EOT;
        
        $resultado = mysql_query($sql);
        $output    = '';
        $output .= <<<EOT
			<div class="containerCheckList">
				<table>
					<th>{$titulo}</th>
				</table>		 
					<table>
						<tr>
							<td width="5%" class="first">Part No</td>
							<td width="60%" class="first">Descrição / Description:</td>
							<td width="5%" class="first"></td>
							<td width="20%" class="first">Expiry date</td>
							<td width="5%" class="first">Pcs</td>
							<td width="5%" class="first"></td>
						</tr>
EOT;
        while ($row = mysql_fetch_assoc($resultado)) {
            $output .= '<tr>';
            $output .= "<td>{$row['D120_Id']}</td>";
            $output .= "<td>{$row['D120_Descricao']}</td>";
            $output .= "<td></td>";
            $dataExp = gCorrigeData($row['T159_Data']);
            $output .= "<td>{$dataExp}</td>";
            $output .= "<td>{$row['T159_Quantidade']}</td>";
            if ($row['T159_Flag_Ok'] == 'S')
                $output .= "<td>OK</td>";
            else
                $output .= "<td></td>";
            $output .= '</tr>';
        }
        $output .= <<<EOT
				</table>
			</div>
EOT;
        
        return $output;
        
    }

    /**
     * docGerarCilindrosGas
     *
     * @param String $D125_Id
     * @param String $texto2
     * @param String $texto3
     * @return String
     */     
    public function docGerarCilindrosGas($D125_Id, $texto2, $texto3)
    {
        $sql     = "SELECT * FROM T164 WHERE T164_D125_Id='{$D125_Id}'";
        $retorno = mysql_query($sql);
        //return $linha['D125_D106_Id'];
        
        $return = <<<EOT
            <table width="100%">
				<th colspan="2">{$texto2}</th>
EOT;
        
        while ($linha = mysql_fetch_assoc($retorno)) {
            $sql   = "SELECT * FROM T163 left join D129 on D129_Id=T163_D129_Id left join D128 on D128_Id=D129_D128_Id WHERE T163_D106_Id='{$linha['T164_D106_Id']}'";
            $ret   = mysql_query($sql);
            $teste = mysql_fetch_assoc($ret);
            $return .= <<<EOT
                <tr>
                	<td colspan="2"><b>{$teste['D128_Tipo']}</b></td>
                </tr>
                <tr>
                	<td align="left"><b>{$teste['D129_Descricao']}</b></td>
                	<td>{$teste['T163_Valor']}</td>
                </tr>
EOT;
            while ($row = mysql_fetch_assoc($ret)) {
                $return .= <<<EOT
                    <tr>
                    	<td align="left"><b>{$row['D129_Descricao']}</b></td>
                    	<td>{$row['T163_Valor']}</td>
                    </tr>
EOT;
            }
        }
        
        $sql    = "SELECT * FROM T165 LEFT JOIN D116 ON T165_D116_Id=D116_Id LEFT JOIN T141 ON T141_D116_Id=D116_Id LEFT JOIN D125 ON D125_T141_Id=T141_Id LEFT JOIN D131 ON T165_D131_Id=D131_Id WHERE D125_Id='{$D125_Id}'";
        $result = mysql_query($sql);
        
        $retorno = <<<EOT
            <div class="containerSpecify">
                <div class="containerTable">
                    <table width="100%">
                        <th colspan="2">{$texto3}</th>
                    </table>
				    <table width="100%">
EOT;
        $col     = 1;
        while ($linha = mysql_fetch_assoc($result)) {
            if ($col == 1) {
                $retorno .= '<tr>';
            }
            $retorno .= "<td>{$linha['D131_Descricao']}</td>";
            $retorno .= "<td class=\"tdRight\">{$linha['T165_Valor']}</td>";
            if ($col == 2) {
                $retorno .= '<tr>';
                $col = 0;
            }
            $col++;
        }
        $retorno .= <<<EOT
				</table>
EOT;
        return $return . $retorno;
    }
}
