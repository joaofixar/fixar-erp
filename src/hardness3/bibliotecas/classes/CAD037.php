<?php
namespace hardness;
definirClasse('CAD037');
class CAD037_
{

    /**
     * getVar
     *
     * @param String $nome
     * @return String
     */ 	
    public function getVar($nome)
    {
        $sql       = "SELECT D130_Conteudo FROM D130 WHERE D130_Nome = '{$nome}'";
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        if (empty($row)) {
            return "Nome não existe: {$nome}";
        }
        return $row['D130_Conteudo'];
    }
    
    /**
     * docGerarNumAno
     *
     * @param String $acaoId
     * @return String
     */     
    public function docGerarNumAno($acaoId)
    {
        $ano = date('Y');
        return <<<EOT
			N. $acaoId/$ano
EOT;
    }

    /**
     * docGerarTableInspecao
     *
     * @param String $D124_Id
     * @return String
     */     
    public function docGerarTableInspecao($D124_Id)
    {
        $sql     = "SELECT D124_D106_Id FROM D124 left join D106 on D106_Id=D124_D106_Id left join D128 on D128_Id=D106_D128_Id WHERE D124_Id='{$D124_Id}'";
        $result  = mysql_query($sql);
        $linha   = mysql_fetch_assoc($result);
        $D106_Id = $linha['D124_D106_Id'];
        
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
    
    /**
     * docGerarCilindrosGas
     *
     * @param String $D124_Id
     * @param String $texto2
     * @param String $texto3
     * @return String
     */     
    public function docGerarCilindrosGas($D124_Id, $texto2, $texto3)
    {
        $sql = "SELECT * FROM T164 WHERE T164_D124_Id='{$D124_Id}'";
        
        $retorno = mysql_query($sql);
        
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
        
        $sql = "SELECT * FROM T165 LEFT JOIN D116 ON T165_D116_Id=D116_Id LEFT JOIN T141 ON T141_D116_Id=D116_Id LEFT JOIN D124 ON D124_T141_Id=T141_Id LEFT JOIN D131 ON T165_D131_Id=D131_Id WHERE D124_Id='{$D124_Id}'";
        
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
			</div>
		</div>
EOT;
        return $return . $retorno;   
    }
    
    /**
     * docGerarEspecificacoes
     *
     * @param String $D124_Id
     * @param String $titulo     
     * @return String
     */     
    public function docGerarEspecificacoes($D124_Id, $titulo)
    {
        $sql    = <<<EOT
			SELECT *
			FROM
				D121
			left join T160 on (T160_D121_Id=D121_Id and T160_D124_Id={$D124_Id}) 
			left join D124 on D124_Id=T160_D124_Id
			left join D106 on D106_Id=D124_D106_Id
			WHERE
				D121_D128_Id = D106_D128_Id
EOT;
        $output = '';
        $output .= '<table>';
        $output .= '<tr><td>ID</td><td>Descrição</td><td>Quantidade</td></tr>';
        
        $output .= '<tr>';
        $output .= "<td></td>";
        $output .= "<td></td>";
        $output .= "<td></td>";
        $output .= '</tr>';
        
        $resultado = mysql_query($sql);
        $output    = '';
        $output .= <<<EOT
		 <div class="containerCheckList">
				<table width="100%">
					<th colspan="2">{$titulo}</th>
				</table>
				<table width="100%">
EOT;
        
        $col = 1;
        while ($linha = mysql_fetch_assoc($resultado)) {
            if ($col == 1) {
                $output .= '<tr>';
            }
            $output .= "<td>{$linha['D121_Descricao']}</td>";
            $output .= "<td class=\"tdRight\"><b>{$linha['T160_Valor']}</b></td>";
            if ($col == 2) {
                $output .= '<tr>';
                $col = 0;
            }
            $col++;
        }
        
        $output .= <<<EOT
				</table>
			</div>
EOT;
        return $output;        
    }
    
    /**
     * docGerarTableInspecao3
     *
     * @param String $acaoId
     * @return String
     */     
    public function docGerarTableInspecao3($acaoId)
    {
        return <<<EOT
            <table class="tableInspecao" border="1">
            	<tr>
            		<td rowspan="6"  width="15%">
            			Equipamento:
            		</td>
            		<td>
            			Unidade
            		</td>
            		<td>
            			Tipo
            		</td>
            		<td>
            			Nº Série
            		</td>
            		<td>
            			Validade
            		</td>
            	</tr>
            	<tr>
            		<td class="alignRight">
            			Palamenta:
            		</td>
            		<td>
            			<font size="3">
            				<b>
            					S.O.L.A.S "A"
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            					10/2012
            				</b>
            			</font>
            		</td>
            	</tr>
            	<tr>
            		<td class="alignRight">
            			EPIRB:
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            	</tr>
            	<tr>
            		<td class="alignRight">
            			Válvula Hidrostática
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            	</tr>
            	<tr>
            		<td class="alignRight">
            			Refletor de Radar:
            		</td>
            		<td>
            			<font size="3">
            				<b>
            					SOLAS
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            	</tr>
            	<tr>
            		<td class="alignRight">
            			Primeiros Socorros:
            		</td>
            		<td>
            			<font size="3">
            				<b>
            					SOLAS
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            				</b>
            			</font>
            		</td>
            		<td>
            			<font size="3">
            				<b>
            					05/2013
            				</b>
            			</font>
            		</td>
            	</tr>
            </table>
EOT;
    }
    
    /**
     * docGerarTableInspecao4
     *
     * @param String $acaoId
     * @return String
     */     
    public function docGerarTableInspecao4($acaoId)
    { 
        return <<<EOT
            <table class="tableInspecao" border="1">
            	<tr>
            		<td rowspan="3"  width="15%">Testes:</td>
            		<td>Teste PAN</td>
            		<td>Teste EG</td>
            		<td>Teste FS</td>
            		<td>Teste Sobrecarga Davit</td>
            	</tr>
            	<tr>
            		<td>Sim/Não: <b>NÃO</b></td>
            		<td>Sim/Não: <b>NÃO</b></td>
            		<td>Sim/Não: <b>NÃO</b></td>
            		<td>Sim/Não: <b>NÃO</b></td>
            	</tr>
            	<tr>
            		<td>&nbsp;</td>
            		<td></td>
            		<td></td>
            		<td></td>
            	</tr>
            	<tr>
            		<td rowspan="2">Verificação:</td>
            		<td>
            			Data Montagem:<br />
            			<font size="3"><b>18/OUT 2011</b></font>
            		</td>
            		<td colspan="2">
            			<font size="3"><b>PRIMEIRA EMISSÃO</b></font>
            		<td>
            			Data Emissão:<br />
            			<font size="3"><b>18/OUT 2011</b></font>
            		</td>
            	</tr>
            	<tr>
            		<td class="alignRight">Certificado de <br />Conformidade</td>
            		<td colspan="3" class="alignRight">Anotações / Modificações</td>
            	</tr>
            </table>
EOT;
    }

    /**
     * docGerarRodapeEsquerda
     *
     * @param String $acaoId
     * @return String
     */     
    public function docGerarRodapeEsquerda($acaoId)
    {
        return <<<EOT
            <div class="bottomLeft">
            	<table>
            		<tr>
            			<td class="alignRight">Armador</td>
            			<td class="alignRight"><b><font size="3">Noble</font></b></td>
            		</tr>
            		<tr>
            			<td class="alignRight">Nome do Navio</td>
            			<td class="alignRight"><b><font size="3">Noble Phoenix</font></b></td>
            		</tr>
            		<tr>
            			<td class="alignRight">NR. IMO.</td>
            			<td class="alignRight"><b><font size="3">7418880</font></b></td>
            		</tr>
            		<tr>
            			<td class="alignRight">Call Sign</td>
            			<td class="alignRight"><b><font size="3">A8XM9</font></b></td>
            		</tr>
            		<tr>
            			<td class="alignRight">Porto de Registro</td>
            			<td class="alignRight"><b><font size="3">MONROVIA</font></b></td>
            		</tr>
            		<tr>
            			<td class="alignRight">Bandeira</td>
            			<td class="alignRight"><b><font size="3">LIBÉRIA</font></b></td>
            		</tr>
            	</table>
            </div>
EOT;
    }
    
    /**
     * docGerarRodapeDireita
     *
     * @param String $acaoId
     * @return String
     */     
    public function docGerarRodapeDireita($acaoId)
    {
        return <<<EOT
            <div class="bottomRight">
            	<div class="bottomCotainerRight">
            		___________________________________________________<br />
            		<label class="alignAssCenter">Assinatura</label>
            	</div>
            </div>
EOT;
    }
    
}
