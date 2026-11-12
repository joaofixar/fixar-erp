<?php
namespace hardness;
definirClasse('FIN006');
class FIN006_
{

    /**
     * gravarMeta
     *
     * @param Form $form
     * @param String $T137_Id
     * @return String/bool
     */ 
    public function gravarMeta($form, $T137_Id)
    {
        global $g;
        
        if ($T137_Id <= 0) {
            return "T137_Id nao foi passado!";
        }
        
        $T137_Valor_Meta = str_replace('.', '', $form->campoValorEnviado('T137_Valor_Meta'));
        $T137_Valor_Meta = str_replace(',', '.', $T137_Valor_Meta);
        
        $T137_Formula_Comissao = mysql_real_escape_string($form->campoValorEnviado('T137_Formula_Comissao'));
        
        if (!empty($T137_Formula_Comissao)) {
            mysql_query("SELECT ($T137_Formula_Comissao) FROM T137 limit 1");
            $cErro = mysql_error();
            if (!empty($cErro))
                return "<div class='msgError'><span class='msgErrorTitle'>Existe um erro na fórmula</span>Erro Técnico:<br />" . str_replace(array(
                    '"',
                    "'"
                ), "", $cErro) . "</div>";
        }
        
        $T137_Formula_Comissao = trim($T137_Formula_Comissao);
        //$T137_Formula_Comissao = str_replace(array("\n","\r"),"",$T137_Formula_Comissao);
        
        $xT137_Formula_Comissao = $T137_Formula_Comissao;
        if (empty($xT137_Formula_Comissao)) {
            //-- adiciona aspas para gravar a formula em branco
            $xT137_Formula_Comissao = "'" . $xT137_Formula_Comissao . "'";
        }
        
        return mysql_query("UPDATE T137 SET T137_Valor_Meta = '$T137_Valor_Meta',
		                             T137_Formula_Comissao = '$T137_Formula_Comissao'
		                       		 WHERE T137_Id='$T137_Id'");
    }
    
    /**
     * totalizarComissoes
     *
     * @param Form $form
     * @return String/bool
     */ 
    public function totalizarComissoes($form)
    {
        global $g;
        $Ano = $form->campoValorEnviado('Ano');
        $Mes = $form->campoValorEnviado('Mes');
        
        $Ano_Mes      = $Ano . '-' . str_pad($Mes, 2, "0", STR_PAD_LEFT);
        $UltimoDiaMes = date("t", mktime(0, 0, 0, $Mes, 1, $Ano));
        
        mysql_query("UPDATE T007
		          LEFT JOIN D024 on D024_Id=T007_D024_Id
		          LEFT JOIN D006 on D006_Id=T007_D006_Id
		                SET T007_C007_Id_Devolucao=D024_C007_Vendedor_Interno
		              WHERE D006_Flag_Venda_Compra_Outros='DV'
		                AND ifnull(T007_C007_Id_Devolucao,0)<=0");
        
        mysql_query("UPDATE T013
		          LEFT JOIN D024 on D024_Id=T013_D024_Id
		          LEFT JOIN D006 on D006_Id=T013_D006_Id
		                SET T013_C007_Id_Devolucao=D024_C007_Vendedor_Interno
		              WHERE D006_Flag_Venda_Compra_Outros='DV'
		                AND ifnull(T013_C007_Id_Devolucao,0)<=0");
        
        $C007 = mysql_query("SELECT C007_Id,  
								  C007_C007_Id  
		                     FROM T007
		                LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
		                LEFT JOIN D006 ON D006_Id=T007_D006_Id
		                    WHERE T007_Data_Emissao LIKE '$Ano_Mes%'
		                      AND T007_C004_Id='{$g['empresaAtual']}'
		                      AND D006_Flag_Venda_Compra_Outros='V'
		                      AND T007_Flag_Cancelada!='S'
		                      AND C007_Id IS NOT NULL
		                      AND C007_Flag_Vendedor!=''
		                 GROUP BY T007_C007_Id_Vendedor_Interno");
        
        while ($mC007 = mysql_fetch_array($C007)) {
            
            $T004 = mysql_query("SELECT sum(T004_Valor_Custo_Unitario*T004_Quantidade) as Custo,
									  sum(T004_Valor_Preco_Unitario*T004_Quantidade) as Preco
				    			 FROM T004
							LEFT JOIN T003 on T004_T003_Id=T003_Id
							LEFT JOIN D006 on D006_Id=T003_D006_Id							  
							    WHERE T003_C007_Id_Vendedor_Interno = '{$mC007['C007_Id']}'
								  AND T003_Data_Emissao LIKE '$Ano_Mes%'
								  AND T003_C004_Id='{$g['empresaAtual']}'
								  AND D006_Flag_Venda_Compra_Outros='V'
						     GROUP BY T003_C007_Id_Vendedor_Interno");
            
            if (mysql_error()) {
                return mysql_error();
            }
            
            $mT004 = mysql_fetch_array($T004);
            
            $T008 = mysql_query("SELECT sum(T008_Valor_Custo_Unitario*T008_Quantidade) as Custo,
									  sum(T008_Valor_Preco_Unitario*T008_Quantidade) as Preco
				    			 FROM T008
							LEFT JOIN T007 on T008_T007_Id=T007_Id
							LEFT JOIN D006 on D006_Id=T007_D006_Id							  
							    WHERE T007_C007_Id_Vendedor_Interno = '{$mC007['C007_Id']}'
								  AND T007_Data_Emissao LIKE '$Ano_Mes%'
								  AND T007_C004_Id='{$g['empresaAtual']}'
								  AND D006_Flag_Venda_Compra_Outros='V'
								  AND T007_Flag_Cancelada!='S'
						     GROUP BY T007_C007_Id_Vendedor_Interno");
            
            if (mysql_error()) {
                return mysql_error();
            }
            
            $mT008 = mysql_fetch_array($T008);
            
            //-- PEGO AS DEVOLUCOES
            $T008D = mysql_query("SELECT
									  sum(T008_Valor_Preco_Unitario*T008_Quantidade) as Devolucoes
								 FROM T008
						    LEFT JOIN T007 on T008_T007_Id=T007_Id
							LEFT JOIN D006 on D006_Id=T007_D006_Id							  
							    WHERE T007_C007_Id_Devolucao = '{$mC007['C007_Id']}'
								  AND T007_Data_Emissao LIKE '$Ano_Mes%'
								  AND T007_C004_Id='{$g['empresaAtual']}'
								  AND D006_Flag_Venda_Compra_Outros='DV'
								  AND D006_Flag_Devolucao='S'							  
								  AND T007_Flag_Cancelada!='S'
						     GROUP BY T007_C007_Id_Devolucao");
            
            if (mysql_error()) {
                return mysql_error();
            }
            
            $mT008D = mysql_fetch_array($T008D);
            
            //-- PEGO AS DEVOLUCOES
            $T014D = mysql_query("SELECT
									  sum(T014_Valor_Custo_Unitario*T014_Quantidade) as Devolucoes
								 FROM T014
						    LEFT JOIN T013 on T014_T013_Id=T013_Id
							LEFT JOIN D006 on D006_Id=T013_D006_Id							  
							    WHERE T013_C007_Id_Devolucao = '{$mC007['C007_Id']}'
								  AND T013_Data_Entrada LIKE '$Ano_Mes%'
								  AND T013_C004_Id='{$g['empresaAtual']}'
								  AND D006_Flag_Venda_Compra_Outros='DV'
								  AND D006_Flag_Devolucao='S'							  
								  AND T013_Flag_Cancelada!='S'
						     GROUP BY T013_C007_Id_Devolucao");
            
            if (mysql_error()) {
                return mysql_error();
            }
            
            $mT014D = mysql_fetch_array($T014D);
            
            $T137 = mysql_query("SELECT T137_Id,
									    T137_Formula_Comissao 
			                			FROM T137 
			                			WHERE T137_C004_Id='{$g['empresaAtual']}' 
			                			AND T137_Mes='$Mes' 
			                			AND T137_Ano='$Ano' 
			                			AND T137_C007_Id_Vendedor='{$mC007['C007_Id']}' ");
            
            if (mysql_error()) {
                return mysql_error();
            }
            
            $mT008D['Devolucoes'] += $mT014D['Devolucoes'];
            
            unset($Percentual_Aproveitamento);
            if ($mT004['Preco'] > 0) {
                $Percentual_Aproveitamento = ($mT008['Preco'] / $mT004['Preco']) * 100;
            }
            
            if (mysql_num_rows($T137) == 0) {
                
                //-- PEGA A ULTIMA FORMULA
                $T137u = mysql_query("SELECT T137_Formula_Comissao,
				                             T137_Valor_Meta
		 		                			 FROM T137 
				                			 WHERE T137_C004_Id='{$g['empresaAtual']}' 
				                			 AND T137_C007_Id_Vendedor='{$mC007['C007_Id']}' 
				                			 order by T137_Ano desc, T137_Mes desc
				                			 limit 1");
                
                if (mysql_error()) {
                    return mysql_error();
                }
                
                $mT137u = mysql_fetch_array($T137u);
                
                $cClausula = "INSERT INTO T137 (
										T137_C004_Id,
										T137_Mes,
										T137_Ano,
										T137_Data_Inicial,
										T137_Data_Final,
										T137_C007_Id_Vendedor,
										T137_C007_Id_Supervisor,
										T137_Valor_CMV,
										T137_Valor_Vendas,
										T137_Valor_Devolucoes,
										T137_IPV,
										T137_Percentual_Aproveitamento,
										T137_Percentual_Fechamento,
										T137_Valor_Meta,
										T137_Formula_Comissao
										) values (
										'{$g['empresaAtual']}',
										'$Mes',
										'$Ano',
										'$Ano-$Mes-01',
										'$Ano-$Mes-$UltimoDiaMes',
										'{$mC007['C007_Id']}',
										'{$mC007['C007_C007_Id']}',
										'{$mT008['Custo']}',
										'{$mT008['Preco']}',
										'{$mT008D['Devolucoes']}',
										if(T137_Valor_CMV>0,T137_Valor_Vendas/T137_Valor_CMV,0),
										'$Percentual_Aproveitamento',
										if(T137_Valor_Meta>0,(T137_Valor_Vendas*100)/T137_Valor_Meta,0),
										'$mT137u[T137_Valor_Meta]',
										'$mT137u[T137_Formula_Comissao]'
										)";
            } else {
                $mT137 = mysql_fetch_array($T137);
                
                $xT137_Formula_Comissao = $mT137['T137_Formula_Comissao'];
                if (empty($xT137_Formula_Comissao)) {
                    //-- adiciona aspas para gravar a formula em branco
                    $xT137_Formula_Comissao = "'" . $xT137_Formula_Comissao . "'";
                }
                
                
                $cClausula = "UPDATE T137 SET
								T137_Valor_CMV                 = '{$mT008['Custo']}',
								T137_Valor_Vendas              = '{$mT008['Preco']}',
								T137_Valor_Devolucoes          = '{$mT008D['Devolucoes']}',
								T137_IPV                       = if(T137_Valor_CMV>0,T137_Valor_Vendas/T137_Valor_CMV,0),
							    T137_Percentual_Aproveitamento = '$Percentual_Aproveitamento',						
							    T137_Percentual_Fechamento     = if(T137_Valor_Meta>0,(T137_Valor_Vendas*100)/T137_Valor_Meta,0),						
							    T137_Valor_Comissao            = $xT137_Formula_Comissao
								WHERE T137_C004_Id             = '{$g['empresaAtual']}' 
								AND T137_Mes                   = '$Mes' 
								AND T137_Ano                   = '$Ano' 
								AND T137_C007_Id_Vendedor='{$mC007['C007_Id']}'";
                
            }
            mysql_query($cClausula);
            
        }
        
        $T137 = mysql_query("SELECT sum(T137_Valor_CMV) as Custo,
									sum(T137_Valor_Vendas) as Venda, 
									sum(T137_Valor_Devolucoes) as Devolucoes,
									T137_C007_Id_Supervisor
									FROM T137 
						       LEFT JOIN C007 on C007_Id=T137_C007_Id_Supervisor			
									WHERE T137_C004_Id='{$g['empresaAtual']}'
									AND T137_Mes='$Mes' 
									AND T137_Ano='$Ano' 
									AND T137_C007_Id_Supervisor>0
									GROUP BY T137_C007_Id_Supervisor");
        
        while ($mT137 = mysql_fetch_array($T137)) {
            $T137_Consulta = mysql_query("SELECT T137_Id,
			                                     T137_Formula_Comissao
			                			FROM T137 
			                			WHERE T137_C004_Id='{$g['empresaAtual']}' 
			                			AND T137_Mes='$Mes' 
			                			AND T137_Ano='$Ano' 
			                			AND T137_C007_Id_Vendedor='{$mT137['T137_C007_Id_Supervisor']}' ");
            
            if (mysql_error()) {
                return mysql_error();
            }
            
            if (mysql_num_rows($T137_Consulta) == 0) {
                
                $cClausula = "INSERT INTO T137 (
										T137_C004_Id,
										T137_Mes,
										T137_Ano,
										T137_Data_Inicial,
										T137_Data_Final,
										T137_C007_Id_Vendedor,
										T137_C007_Id_Supervisor,
										T137_Valor_CMV_Equipe,
										T137_Valor_Vendas_Equipe,
										T137_Valor_Devolucoes_Equipe,
										T137_IPV_Equipe,
										T137_Percentual_Fechamento
										) values (
										'{$g['empresaAtual']}',
										'$Mes',
										'$Ano',
										'$Ano-$Mes-01',
										'$Ano-$Mes-$UltimoDiaMes',
										'{$mT137['T137_C007_Id_Supervisor']}',
										'{$mT137['T137_C007_Id_Supervisor']}',
										'{$mT137['Custo']}',
										'{$mT137['Venda']}',
										'{$mT137['Devolucoes']}',
										if(T137_Valor_CMV_Equipe>0,T137_Valor_Vendas_Equipe/T137_Valor_CMV_Equipe,0),
									    if(T137_Valor_Vendas_Equipe>0,(T137_Valor_Vendas_Equipe*100)/T137_Valor_Meta,0)						
										)";
            } else {
                $mT137_Consulta         = mysql_fetch_array($T137_Consulta);
                $xT137_Formula_Comissao = $mT137_Consulta['T137_Formula_Comissao'];
                if (empty($xT137_Formula_Comissao)) {
                    //-- adiciona aspas para gravar a formula em branco
                    $xT137_Formula_Comissao = "'" . $xT137_Formula_Comissao . "'";
                }
                
                $cClausula = "UPDATE T137 SET
									T137_Valor_CMV_Equipe        = '{$mT137['Custo']}',
									T137_Valor_Vendas_Equipe     = '{$mT137['Venda']}',
									T137_Valor_Devolucoes_Equipe = '{$mT137['Devolucoes']}',
									T137_IPV_Equipe              = if(T137_Valor_CMV_Equipe>0,T137_Valor_Vendas_Equipe/T137_Valor_CMV_Equipe,0),							
							        T137_Percentual_Fechamento   = if(T137_Valor_Vendas_Equipe>0,(T137_Valor_Vendas_Equipe*100)/T137_Valor_Meta,0),
							        T137_Valor_Comissao          = $xT137_Formula_Comissao						
									WHERE T137_C004_Id='{$g['empresaAtual']}' 
									AND T137_Mes='$Mes' 
									AND T137_Ano='$Ano' 
									AND T137_C007_Id_Vendedor='{$mT137['T137_C007_Id_Supervisor']}'";
            }
            mysql_query($cClausula);
        }
        return true;
    }   
}
