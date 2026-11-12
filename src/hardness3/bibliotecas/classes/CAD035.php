<?php
namespace hardness;
definirClasse('CAD035');
class CAD035_
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
     * gerarDadosBanco
     *
     * @param String $T156_Id
     * @return String
     */     
    public function gerarDadosBanco($T156_Id)
    {
        
        $sql     = <<<EOT
		SELECT * FROM T156 
		left join T141 on T141_Id=T156_T141_Id 
		left join D024 on D024_Id=T141_D024_Id
		WHERE T156_Id='{$T156_Id}'
EOT;
        $retorno = mysql_query($sql);
        $result1 = mysql_fetch_assoc($retorno);
        
        /**
         * Buscando dados das contas bancárias e caixas
         */
        $sql     = "SELECT * FROM D007 left join D062 on D062_Id=D007_D062_Id WHERE D007_Id='{$result1['T156_D007_Id']}'";
        $retorno = mysql_query($sql);
        $result2 = mysql_fetch_assoc($retorno);
        
        $numeroBanco = $result2['D062_Numero_Banco'];
        $nomeBanco   = $result2['D007_Nome_Banco'];
        $numAg       = $result2['D007_Numero_Agencia'];
        $numCC       = $result2['D007_Numero_Conta'];
        $CNPJBanco   = $result2['D007_CNPJ'];
        
        return <<<EOT
    		<div class="D007Banco">
    			Depositar: {$numeroBanco} - {$nomeBanco} <br />
    			Ag: {$numAg} CC:{$numCC} <br />
    			CNPJ: {$CNPJBanco}
    		</div>
EOT;
    }
    
    /**
     * gerarDadosEmpresaCabecalho
     *
     * @param String $T156_Id
     * @return String
     */     
    public function gerarDadosEmpresaCabecalho($T156_Id)
    {
        global $g;
        
        /**
         * BUSCANDO DADOS EMPRESA ATUAL
         */
        $sql                 = <<<EOT
    		SELECT * FROM C004 
    		left join D020 on D020_Id=C004_D020_Id
    		left join D018 on D018_Id=C004_D018_Id
    		WHERE C004_Id='{$g['empresaAtual']}'
EOT;
        $retorno             = mysql_query($sql);
        $result3             = mysql_fetch_assoc($retorno);
        $InscEstEmpresaAtual = $result3['C004_Inscricao_Estadual'];
        $CEPEmpresaAtual     = $result3['C004_Cep'];
        $CidadeEmpresaAtual  = $result3['D020_Nome_Cidade'];
        $EstadoEmpresaAtual  = $result3['D018_UF'];
        $EmailEmpresaAtual   = $result3['C004_E_Mail'];
        
        return <<<EOT
            <div class="D024ClienteFornecedor">
                Inscr.Est: {$InscEstEmpresaAtual} <br />
                Cep: {$CEPEmpresaAtual} - {$CidadeEmpresaAtual} - {$EstadoEmpresaAtual}<br />
                Email: {$EmailEmpresaAtual}
            </div>
EOT;
    }
    
    /**
     * emissaoANDNum
     *
     * @param String $T156_Id
     * @return String
     */     
    public function emissaoANDNum($T156_Id)
    {
        global $g;
        
        $sql     = <<<EOT
            SELECT * FROM T156 
            left join T141 on T141_Id=T156_T141_Id 
            left join D024 on D024_Id=T141_D024_Id
            WHERE T156_Id='{$T156_Id}'
EOT;
        $retorno = mysql_query($sql);
        $result1 = mysql_fetch_assoc($retorno);
        
        $numeroRecibo = $result1['T156_Id'];
        $data         = gCorrigeData($result1['T156_Emissao']);
        
        return <<<EOT
            <table class="tableLocacaoBase">
                <tr height="30px">
                    <td width="400px">Data Emissão: <font size="3">{$data}</font>
                    <td class="alignRight"><label><b>Nº {$numeroRecibo}</b></label></td>
                </tr>
            </table>
EOT;
    }
    
    /**
     * docGerarDadosEmpresa
     *
     * @param String $T156_Id
     * @return String
     */     
    public function docGerarDadosEmpresa($T156_Id)
    {
        $sql     = <<<EOT
            SELECT * FROM T156 
            left join T141 on T141_Id=T156_T141_Id 
            left join D024 on D024_Id=T141_D024_Id
            left join D018 on D018_Id=D024_D018_Id
            left join D020 on D020_Id=D024_D020_Id
            WHERE T156_Id='{$T156_Id}'
EOT;
        $retorno = mysql_query($sql);
        $result1 = mysql_fetch_assoc($retorno);
        
        $numeroRecibo          = $result1['T156_Id'];
        $data                  = gCorrigeData($result1['T156_Emissao']);
        $NomeEmpresa           = $result1['D024_Nome_Empresa'];
        $CNPJEmpresa           = $result1['D024_Cnpj'];
        $InscrMun              = $result1['D024_Inscricao_Municipal'];
        $InscrEst              = $result1['D024_Inscricao_Estadual'];
        $EnderecoEmpresa       = $result1['D024_Endereco'];
        $CepEmpresa            = $result1['D024_Cep'];
        $CidadeEmpresa         = $result1['D020_Nome_Cidade'];
        $EstadoEmpresa         = $result1['D018_UF'];
        $PaisEmpresa           = $result1['D024_Pais'];
        $EmailEmpresa          = $result1['D024_E_Mail'];
        $NumeroEnderecoEmpresa = $result1['D024_Numero'];
        $BairroEmpresa         = $result1['D024_Bairro'];
        
        $ordemCompra = $result1['T156_Ordem_Compra'];
        $Comprador   = $result1['T156_Comprador'];
        
        return <<<EOT
            <tr>
            	<td colspan=3>
            		<label class="prefixoTable3">
            			Nome/Razão Social:
            		</label>
            		<label class="sufixoTable3">
            			{$NomeEmpresa}
            		</label>
            	</td>
            </tr>
            <tr>
            	<td>
            		<label class="prefixoTable3">
            			CNPJ:
            		</label>
            		<label class="sufixoTable3">
            			{$CNPJEmpresa}
            		</label>
            	</td>
            	<td>
            		<label class="prefixoTable3">
            			I. Estadual:
            		</label>
            		<label class="sufixoTable3">
            			{$InscrEst}
            		</label>
            	</td>
            	<td>
            		<label class="prefixoTable3">
            			I. Municipal
            		</label>
            		<label class="sufixoTable3">
            			{$InscrMun}
            		</label>
            	</td>
            </tr>
            <tr>
            	<td colspan=3>
            		<label class="prefixoTable3">
            			Endereço:
            		</label>
            		<label class="sufixoTable3">
            			{$EnderecoEmpresa}, {$NumeroEnderecoEmpresa} - {$BairroEmpresa}
            		</label>
            	</td>
            </tr>
            <tr>
            	<td>
            		<label class="prefixoTable3">
            			Município:
            		</label>
            		<label class="sufixoTable3">
            			{$CidadeEmpresa}
            		</label>
            	</td>
            	<td>
            		<label class="prefixoTable3">
            			CEP:
            		</label>
            		<label class="sufixoTable3">
            			{$CepEmpresa}
            		</label>
            	</td>
            	<td>
            		<label class="prefixoTable3">
            			UF:
            		</label>
            		<label class="sufixoTable3">
            			{$EstadoEmpresa}
            		</label>
            	</td>
            </tr>
            <tr>
            	<td>
            		<label class="prefixoTable3">
            			Nº da Ordem de Compra:
            		</label>
            		<label class="sufixoTable3">
            			{$ordemCompra}
            		</label>
            	</td>
            	<td colspan=2>
            		<label class="prefixoTable3">
            			Nome do Comprador:
            		</label>
            		<label class="sufixoTable3">
            			{$Comprador}
            		</label>
            	</td>
            </tr>
EOT;
    }
    
    /**
     * docGerarCorpoDcto
     *
     * @param String $T156_Id
     * @return String
     */     
    public function docGerarCorpoDcto($T156_Id)
    {
        global $g;
        
        $sql     = <<<EOT
    		SELECT * FROM T156 
    		left join T141 on T141_Id=T156_T141_Id 
    		left join D024 on D024_Id=T141_D024_Id
    		WHERE T156_Id='{$T156_Id}'
EOT;
        $retorno = mysql_query($sql);
        $result1 = mysql_fetch_assoc($retorno);
        
        $numeroRecibo = $result1['T156_Id'];
        
        /**
         * Buscando os ítens do recibo de locação
         */
        $sql      = "SELECT * FROM T157 WHERE T157_T156_Id='{$numeroRecibo}'";
        $retorno4 = mysql_query($sql);
        
        
        $retorno = <<<EOT
            </table>
            <table class="tableLocacaoBase">
            	<th height=25 colspan=5>
            		Descrição dos Bens
            	</th>
            	<tr>
            		<td width=10%><b>Item</b></td>
            		<td width=55%><b>Descrição</b></td>
            		<td width=5%><b>Quant</b></td>
            		<td width=15%><b>Valor Unit</b></td>
            		<td width=15%><b>Valor Total</b></td>
            	</tr>
EOT;
        $total   = '';
        while ($linha = mysql_fetch_assoc($retorno4)) {
            if ($linha['T157_Quantidade'] > 0) {
                $multi = ($linha['T157_Quantidade'] * $linha['T157_Valor_Unitario']);
                $total = $multi + $total;
                $multi = number_format($multi, 2, ',', '.');
                
                $valorUnit = number_format($linha['T157_Valor_Unitario'], 2, ',', '.');
            }
            $retorno .= <<<EOT
                <tr height=20>
                    <td>{$linha['T157_Id']}</td>
                    <td><font size="2">{$linha['T157_Descricao']}</font></td>
                    <td class="alignCenter">{$linha['T157_Quantidade']}</td>
                    <td class="alignRight">R\$ {$valorUnit}</td>
                    <td class="alignRight">R\$ {$multi}</td>
                </tr>
EOT;
        }
        if (empty($total)) {
            $total = "0,00";
        } else {
            $total = number_format($total, 2, ',', '.');
        }
        $retorno .= <<<EOT
            <tr height=25>
                <td colspan=4 class="titleTotal"><b>TOTAL DO RECIBO</b></td>
                <td class="valorTotal"><b>R\$ $total</b></td>
            </tr>
            <tr height=20>
                <td colspan=5></td>
            </tr>
            <tr height=30>
                <th colspan=5>OPERAÇÃO NÃO TRIBUTADA PELO ISS, CONFORME LEI COMPLEMENTAR 116/2003</th>
            </tr>
        </table>
EOT;
        return $retorno;
    }
    
    /**
     * docGerarRodape
     *
     * @param String $T156_Id
     * @return String
     */     
    public function docGerarRodape($T156_Id)
    {
        return <<<EOT
			<table class="tableLocacaoBase">
				<tr>
					<td colspan=3>
						Recebi(emos), da Eurosul Fornecedora de Navios Ltda. o Recibo de Locação de Bens Móveis
					</td>
				</tr>
				<tr height="70">
					<td valign="top">Data do Recebimento</td>
					<td valign="top">Assinatura do Recebedor</td>
					<td class="alignRight"><label><b>Nº {$T156_Id}</b></label></td>
				</tr>
			</table>
EOT;
    }
}
