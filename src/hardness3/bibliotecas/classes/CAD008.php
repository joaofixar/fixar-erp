<?php
namespace hardness;
definirClasse('CAD008');
class CAD008_
{
    /**
     * excluir
     *
     * @param String $D106
     * @return true
     */
    public function excluir($D106_Id)
    {
        $D106_Id = mysql_real_escape_string($D106_Id);
        $sql     = "DELETE FROM D106 WHERE D106_Id='$D106_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * updateT159_Flag_Ok
     *
     * @param String $D120_Id
     * @param String $D106_Id
     * @param String $selected
     * @param String $D125_Id
     * @return bool
     */
    public function updateT159_Flag_Ok($D120_Id, $D106_Id, $selected, $D125_Id)
    {
        global $g;
        
        $T159  = mysql_query("SELECT T159_Id FROM T159 WHERE T159_D120_Id='$D120_Id' AND T159_D106_Id='$D106_Id' AND T159_D125_Id='$D125_Id'");
        $mT159 = mysql_fetch_array($T159);
        
        if (mysql_num_rows($T159) <= 0) {
            mysql_query("INSERT INTO T159 (T159_D120_Id,T159_D106_Id,T159_Flag_Ok,T159_D125_Id)
		                           VALUES ('$D120_Id','$D106_Id','S','$D125_Id')");
        } else {
            mysql_query("UPDATE T159 SET T159_Flag_Ok='$selected'
		                  WHERE T159_Id='{$mT159['T159_Id']}'");
        }
        return true;
    }
    
    /**
     * insereRegistrosT159
     *
     * @param String $D106_Id
     */     
    public function insereRegistrosT159($D106_Id)
    {
        $sql = "SELECT D106_Tipo FROM D106 WHERE D106_Id='{$D106_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        $tipo      = $row['D106_Tipo'];
        
        $sql = "SELECT D120_Id FROM D120 WHERE D120_Tipo='{$tipo}'";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T159  = mysql_query("SELECT T159_Id FROM T159 WHERE T159_D120_Id='{$row['D120_Id']}' AND T159_D106_Id='$D106_Id'");
            $mT159 = mysql_fetch_array($T159);
            
            if (mysql_num_rows($T159) <= 0) {
                mysql_query("INSERT INTO T159 (T159_D120_Id,T159_D106_Id,T159_Flag_Ok)
			                           VALUES ('{$row['D120_Id']}','$D106_Id','N')");
            }
        }
    }
    
    /**
     * insereRegistrosT160
     *
     * @param String $D106_Id
     */     
    public function insereRegistrosT160($D106_Id)
    {
        $sql = "SELECT D106_Tipo FROM D106 WHERE D106_Id='{$D106_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        $tipo      = $row['D106_Tipo'];
        
        $sql = "SELECT D121_Id FROM D121 WHERE D121_Tipo='{$tipo}'";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T160  = mysql_query("SELECT T160_Id FROM T160 WHERE T160_D121_Id='{$row['D121_Id']}' AND T160_D106_Id='$D106_Id'");
            $mT160 = mysql_fetch_array($T160);
            
            if (mysql_num_rows($T160) <= 0) {
                mysql_query("INSERT INTO T160 (T160_D121_Id,T160_D106_Id)
			                           VALUES ('{$row['D121_Id']}','$D106_Id')");
            }
        }    
    }
    
    /**
     * insereRegistrosT163
     *
     * @param String $D106_Id
     */     
    public function insereRegistrosT163($D106_Id)
    {    
        $sql = "SELECT D106_D128_Id FROM D106 WHERE D106_Id='{$D106_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        $tipo      = $row['D106_D128_Id'];
        
        $sql = "SELECT D129_Id FROM D129 WHERE D129_D128_Id='{$tipo}'";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T163  = mysql_query("SELECT T163_Id FROM T163 WHERE T163_D129_Id='{$row['D129_Id']}' AND T163_D106_Id='$D106_Id'");
            $mT163 = mysql_fetch_array($T163);
            
            if (mysql_num_rows($T163) <= 0) {
                mysql_query("INSERT INTO T163 (T163_D129_Id,T163_D106_Id)
			                           VALUES ('{$row['D129_Id']}','$D106_Id')");
            }
        }
    }
    
    /**
     * docGerarEspecificacoes
     *
     * @param String $D125_Id
     * @return String
     */     
    public function docGerarEspecificacoes($D125_Id)
    {
        $sql     = "SELECT D125_D106_Id FROM D125 left join D106 on D106_Id=D125_D106_Id left join D128 on D128_Id=D106_D128_Id WHERE D125_Id='{$D125_Id}'";
        $result  = mysql_query($sql);
        $linha   = mysql_fetch_assoc($result);
        $D106_Id = $linha['D125_D106_Id'];
        
        $sql    = "SELECT * FROM T163 left join D129 on D129_Id=T163_D129_Id WHERE T163_D106_Id='{$D106_Id}'";
        $result = mysql_query($sql);
        
        $retorno = <<<EOT
		<div class="containerSpecify">
			<div class="containerTable">
				<table>
EOT;
        $col     = 1;
        while ($linha = mysql_fetch_assoc($result)) {
            if ($col == 1) {
                $retorno .= '<tr>';
            }
            $retorno .= "<td>{$linha['D129_Descricao']}</td>";
            $retorno .= "<td class=\"tdRight\">{$linha['T163_Valor']}</td>";
            if ($col == 2) {
                $retorno .= '<tr>';
                $col = 0;
            }
            $col++;
        }
        $retorno .= <<<EOT
				</table>
			</div>
		</div>
EOT;
        
        return $retorno;
    }
}
