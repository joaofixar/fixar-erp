<?php
namespace hardness;
$queryUse = mysql_query("USE {$bancoDestinoConversao};");
if (!$queryUse) { die('Invalid query: ' . mysql_error()); }
echo "Executando script extra de conversão...\n<br /><br />";

/* Criar script daqui para baixo */

// Exemplo: Executa uma SQL no banco origem (SQL Server)
//
//$result = sybase_query("SELECT * FROM cp_ContasPagar;", $dbproc);
//while ($row = sybase_fetch_row($result)) {
	//
//}

// Exemplo: Executa uma SQL no banco da coversão (MySQL - e212)
//$sql = "INSERT INTO c004 (...) VALUES (...);";
//$result = mysql_query($sql);


echo "Atualizando C004<br>";
mysql_query("update C004 set C004_Flag_Ativo='S', C004_Banco_Dados='{$bancoDestinoConversao}', C004_Banco_Configuracao='{$bancoDestinoConversao}',C004_Caminho_Sistema='{$bancoDestinoConversao}'");

echo "Atualizando T008<br>";
mysql_query("update T008 
          left join T007 on T007_Numero_Nota_Fiscal=T008_Numero_Nota_Fiscal 
          left join D001 on D001_Codigo_Produto=T008_Codigo_Produto
          left join D049 on D049_D001_Id=D001_Id
          left join D009 on D009_D049_Id=D049_Id
                set T008_T007_Id=T007_Id,
                    T008_D009_Id=D009_Id,
                    T008_Valor_Total_Preco_Sem_Desconto=T008_Valor_Preco_Sem_Desconto_Unitario*T008_Quantidade,
                    T008_Valor_Total_Preco=T008_Quantidade*T008_Valor_Preco_Unitario");

echo "Atualizando T014<br>";
mysql_query("update T014 
          left join T013 on T013_Numero_Nota_Fiscal=T014_Numero_Nota_Fiscal 
          left join D001 on D001_Codigo_Produto=T014_Codigo_Produto
          left join D049 on D049_D001_Id=D001_Id
          left join D009 on D009_D049_Id=D049_Id
                set T014_T013_Id=T013_Id,
                    T014_D009_Id=D009_Id,
                    T014_Valor_Total_Custo=T014_Quantidade*T014_Valor_Custo_Unitario");

echo "Deletando T007/T013<br>";
mysql_query("delete from T007 where T007_Flag_Entrada_Saida='1' and T007_Codigo_CFOP not like '%3102%'");
mysql_query("delete from T013 where T013_Flag_OK_Fiscal='2' or T013_Codigo_CFOP like '%3102%'");

mysql_query("delete from T008 where (select T007_Id from T007 where T007_Id=T008_T007_Id) is null");
mysql_query("delete from T014 where (select T013_Id from T013 where T013_Id=T014_T013_Id) is null");

echo "Atualizando T007<br>";
mysql_query("update T007
          left join D006 on D006_Codigo_CFOP=T007_Codigo_CFOP  
                set T007_D006_Id=D006_Id");

mysql_query("update T007 
         right join T127 on T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal
                set T007_Flag_ACP=6,
                    T007_Chave_Acesso_Nfe=T127_Chave_Acesso_NFe,
                    T007_Numero_Recibo_Nfe=T127_Numero_Recibo_Nfe");

echo "Atualizando T013<br>";
mysql_query("update T013
          left join D006 on D006_Codigo_CFOP=T013_Codigo_CFOP  
                set T013_D006_Id=D006_Id,
                    T013_Tipo=4,
                    T013_Flag_Travar_Nota='S'");
                    
echo "Atualizando T002<br>";
mysql_query("delete from T002 where T002_Flag_Conta!='1'");
mysql_query("update T002 set T002_Numero_Duplicata=concat(T002_Numero_Documento,'-',T002_Numero_Parcela),
                             T002_Valor_Saldo=T002_Valor_Duplicata-T002_Valor_Recebido");

echo "Atualizando T015<br>";
mysql_query("delete from T015 where T015_Flag_Conta!='2'");
mysql_query("update T015 set T015_Numero_Duplicata=concat(T015_Numero_Documento,'-',T015_Numero_Parcela),
                             T015_Valor_Saldo=T015_Valor_Duplicata-T015_Valor_Pago");

echo "Atualizando T019<br>";
mysql_query("update T019 left join D007 on D007_Nome_Contato=T019_Conta set T019_D007_Id=D007_Id");
mysql_query("UPDATE T019 SET T019_Valor_Lancamento=T019_Valor_Lancamento*IF(T019_Flag_Deb_Crd=1,-1,1)");
mysql_query("update D007 set D007_Nome_Contato=''");

echo "Atualizando D007<br>";
mysql_query("UPDATE D007 SET D007_Flag_Ativo='N'");
mysql_query("UPDATE D007 SET D007_Flag_Ativo='S' WHERE D007_Id=1");
mysql_query("UPDATE D007 SET D007_Flag_Ativo='S' WHERE D007_Id=2");
mysql_query("UPDATE D007 SET D007_Flag_Ativo='S' WHERE D007_Id=3");
mysql_query("UPDATE D007 SET D007_Flag_Ativo='S' WHERE D007_Id=12");

                             
echo "Atualizando D024<br>";
mysql_query("update D024 
                    left join X001 on X001_Id=D024_D020_Id 
                set D024_Cidade = X001_Descricao where X001_Descricao !=''");

mysql_query("update D024 set D024_Nome_Fantasia=D024_Nome_Empresa where D024_Nome_Fantasia='';");

mysql_query("update D024 
                set D024_Flag_Revenda_Consumidor=if(D024_Flag_Pessoa_Fisica_Juridica=2,'1','2'),
                    D024_Flag_Lista_Preco=if(D024_Flag_Pessoa_Fisica_Juridica=2,'1','2'),
                    D024_Flag_Pessoa_Fisica_Juridica='J',
                    D024_Flag_Cliente=if(D024_D046_Id=1,'S','N'),
                    D024_Flag_Fornecedor=if(D024_D046_Id=2,'S','N')");

mysql_query("update D024 
          left join D020 on D020_Nome_Cidade=D024_Cidade 
          left join D018 on D018_Id=D020_D018_Id 
          left join D030 on D030_Nome_Pais=D024_Pais 
           set D024_D020_Id=D020_Id,D024_D018_Id=D018_Id,D024_D030_Id=30 where D020_Id is not null");


mysql_query("insert into D118 (D118_Id,D118_Nome,D118_Estado,D118_Cidade,D118_D030_Id,D118_C004_Id) (select D024_Id,D024_Nome_Empresa,D024_Estado,D024_Cidade,D024_D030_Id,'1' from D024 WHERE D024_D046_Id=10)");
           
echo "Cadastrando perfis para C004<br>";
mysql_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,1,63)");
mysql_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,2,63)");
mysql_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,3,63)");
mysql_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,4,63)");

echo "Atualizando D005<br>";
mysql_query("truncate D005");
mysql_query("insert into D005 (D005_Classificacao_Fiscal) (select D001_Aplicacao from D001 group by D001_Aplicacao)");

echo "Atualizando D001<br>";
mysql_query("UPDATE D001 LEFT JOIN D005 ON D005_Classificacao_Fiscal=D001_Aplicacao SET D001_D005_Id=D005_Id");

mysql_query("update D005 left join D001 on D001_D005_Id=D005_Id left join X003 on X003_D001_Id=D001_Id left join X002 on X002_Id=X003_X002_Id set D005_Aliquota_IPI=X002_IPI");

mysql_query("update D001 set D001_D003_Id=1 where D001_Descricao_Produto like '%VISCOSIMETRO%'");
mysql_query("update D001 set D001_D003_Id=2 where D001_Descricao_Produto like '%FILTRO%'");
mysql_query("update D001 set D001_D003_Id=5 where D001_Descricao_Produto like '%OVEN%'");

mysql_query("
update D001 set D001_C008_Id=2;
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%FANN%';
update D001 set D001_C008_Id=1 whupdate D001 set D001_C008_Id=2;
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%FANN%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%AGITADOR%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BALAO%VOLUMETRICO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BARRA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BASTAO%VIDRO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BECKER%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BROOKFIELD%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BURETA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%CADINHO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%CAPSULA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%ELETRODO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%ERLENMEYER%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%ESPATULA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%LUBRICANT%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%LUBRIFICACAO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%MCMASTER%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%METTLER%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%OFITE%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%MERCK%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%PIPETA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%PROVETA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%UNISAFE%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%PN476%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%SERVICOS%MANUTENCAO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%BURETA%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%TD500%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%VIDRO%RELOGIO%';
update D001 set D001_C008_Id=1 where D001_Descricao_Produto like '%WETTING%';");

mysql_query("insert into D001 (D001_C008_Id,D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D001_Flag_Pre_Cadastro) (select '2',D108_Codigo,D108_Descricao_Pt,D108_Descricao_En,'S' from D108 where D108_D107_Id=1)");

mysql_query("insert into D001 (D001_C008_Id,D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D001_Flag_Pre_Cadastro) (select '1',D108_Codigo,D108_Descricao_Pt,D108_Descricao_En,'S' from D108 where D108_D107_Id=2)");

$D001=mysql_query("select D001_Id,D001_Codigo_Produto from D001 left join D049 on D049_D001_Id=D001_Id where D049_Id is null");

while ($mD001=mysql_fetch_array($D001)) 
{
	  mysql_query("insert into D049 (D049_D001_Id,
	                                 D049_D082_Id)
                             values ('$mD001[D001_Id]',
                                     '$mD001[D001_Codigo_Produto]')");
	  $D049_Id=$g['mysqlLastId'];
	
      for ($i=1;$i<=4;$i++)
      {	  
	      mysql_query("insert into D009 (D009_C004_Id,
	                                     D009_D049_Id,
	                                     D009_Data_Cadastro,
	 				                     D009_Flag_Ativo)
			                     values ('$i',
				                         '$D049_Id',
	 			                          current_date(),
	                                     'S')");
      }   
}

mysql_query("update D049 left join D001 on D049_D001_Id=D001_Id set D049_D082_Id=2, D049_Complemento='' where D001_C008_Id=1");                

mysql_query("update D049 
                set D049_D082_Id=1,
                    D049_Flag_Tipo='CP',
                    D049_Flag_Nacional_Importado='I',
                    D049_Modelo=''");

mysql_query("delete from D024 WHERE D024_Nome_Empresa='RESERVADO';");                    

mysql_query("update T055 left join D001 on D001_Codigo_Produto=T055_Codigo_Produto set T055_Descricao_Produto=D001_Descricao_Produto");

mysql_query("update D006 set D006_Flag_Venda_Compra_Outros='O'");

mysql_query("update D006 set D006_Flag_Venda_Compra_Outros='V',
                             D006_Flag_Gera_Contas='R',
                             D006_Flag_Estoque='D',
                             D006_Flag_Estado='F',
                             D006_Flag_Devolucao='N',
                             D006_Flag_Inativo='N',
                             D006_Flag_MMF='S',
                             D006_Flag_Isento_ICMS='N',
							 D006_Flag_Isento_IPI='N',
							 D006_Flag_Retorno='N',
							 D006_Flag_Deb_Crd_ICMS='S',
						 	 D006_Flag_Deb_Crd_IPI='S',
						 	 D006_Flag_Entrada_Saida='S',
						 	 D006_Flag_Atualiza_Custo='N',
						 	 D006_Flag_Zona_Franca='N',
						 	 D006_Flag_Comercial_Exportadora='N',
						 	 D006_Flag_Substituicao_Tributaria='N',
						 	 D006_Flag_Isento_PIS='N',
						 	 D006_Flag_Forma_Calculo_PIS='V',
						 	 D006_Situacao_Tributaria_PIS='01',
						 	 D006_Flag_Isento_COFINS='N',
						 	 D006_Flag_Forma_Calculo_COFINS='V',
						 	 D006_Situacao_Tributaria_COFINS='01',
						 	 D006_Flag_Somar_IPI_Base_ICMS='',
						 	 D006_Flag_Credita_IPI='S',
						 	 D006_Flag_Somar_Despesas_Base_IPI='N',
						 	 D006_D059_Id='1'
                       where D006_Codigo_Natureza like '6%'
                         and D006_Descricao like '%VENDA%';");

mysql_query("update D006 set D006_Flag_Venda_Compra_Outros='V',
                             D006_Flag_Gera_Contas='R',
                             D006_Flag_Estoque='D',
                             D006_Flag_Estado='D',
                             D006_Flag_Devolucao='N',
                             D006_Flag_Inativo='N',
                             D006_Flag_MMF='S',
                             D006_Flag_Isento_ICMS='N',
							 D006_Flag_Isento_IPI='N',
							 D006_Flag_Retorno='N',
							 D006_Flag_Deb_Crd_ICMS='S',
						 	 D006_Flag_Deb_Crd_IPI='S',
						 	 D006_Flag_Entrada_Saida='S',
						 	 D006_Flag_Atualiza_Custo='N',
						 	 D006_Flag_Zona_Franca='N',
						 	 D006_Flag_Comercial_Exportadora='N',
						 	 D006_Flag_Substituicao_Tributaria='N',
						 	 D006_Flag_Isento_PIS='N',
						 	 D006_Flag_Forma_Calculo_PIS='V',
						 	 D006_Situacao_Tributaria_PIS='01',
						 	 D006_Flag_Isento_COFINS='N',
						 	 D006_Flag_Forma_Calculo_COFINS='V',
						 	 D006_Situacao_Tributaria_COFINS='01',
						 	 D006_Flag_Somar_IPI_Base_ICMS='',
						 	 D006_Flag_Credita_IPI='S',
						 	 D006_Flag_Somar_Despesas_Base_IPI='N',
						 	 D006_D059_Id='1'
                       where D006_Codigo_Natureza like '5%'
                         and D006_Descricao like '%VENDA%';");
                          

mysql_query("update D006 set D006_Flag_Venda_Compra_Outros='C',
                             D006_Flag_Gera_Contas='P',
                             D006_Flag_Estoque='C',
                             D006_Flag_Estado='F',
                             D006_Flag_Devolucao='N',
                             D006_Flag_Inativo='N',
                             D006_Flag_MMF='N',
                             D006_Flag_Isento_ICMS='N',
							 D006_Flag_Isento_IPI='N',
							 D006_Flag_Retorno='N',
							 D006_Flag_Deb_Crd_ICMS='S',
						 	 D006_Flag_Deb_Crd_IPI='S',
						 	 D006_Flag_Entrada_Saida='E',
						 	 D006_Flag_Atualiza_Custo='S',
						 	 D006_Flag_Zona_Franca='N',
						 	 D006_Flag_Comercial_Exportadora='N',
						 	 D006_Flag_Substituicao_Tributaria='N',
						 	 D006_Flag_Isento_PIS='N',
						 	 D006_Flag_Forma_Calculo_PIS='V',
						 	 D006_Situacao_Tributaria_PIS='01',
						 	 D006_Flag_Isento_COFINS='N',
						 	 D006_Flag_Forma_Calculo_COFINS='V',
						 	 D006_Situacao_Tributaria_COFINS='01',
						 	 D006_Flag_Somar_IPI_Base_ICMS='',
						 	 D006_Flag_Credita_IPI='S',
						 	 D006_Flag_Somar_Despesas_Base_IPI='N',
						 	 D006_D059_Id='1'
                       where D006_Descricao like '%COMPRA%' OR  D006_Descricao like '%IMPORTACAO%';");
                       
mysql_query("update D001 set D001_Descricao_Ingles=D001_Descricao_Produto, D001_Descricao_Produto ='' where D001_C008_Id=1 and D001_Flag_Pre_Cadastro='S'");                       

mysql_query("update D049 left join D001 on D001_Id=D049_D001_Id set D049_D082_Id=2 where D001_C008_Id=1 and D001_Flag_Pre_Cadastro='S'"); 



mysql_query("truncate T001");

mysql_query("insert into T001 (T001_Codigo_Produto,
                               T001_Data_Lancamento,
                               T001_Descricao_Operacao,
                               T001_Quantidade,
                               T001_Flag_Operacao,
                               T001_Valor_Preco_Unitario,
                               T001_Valor_Custo_Unitario,
                               T001_Flag_MMF,
                               T001_D006_Id,
                               T001_T008_Id,
                               T001_T007_Id,
                               T001_D024_Id,
                               T001_D024_Id_Cliente,
                               T001_D009_Id)
                       (select T008_Codigo_Produto,
                               T007_Data_Emissao,
                               concat('NF-',T007_Numero_Nota_Fiscal),
                               T008_Quantidade,
                               'S',
                               T008_Valor_Preco_Unitario,
                               T008_Valor_Preco_Unitario,
                               'S',
                               T007_D006_Id,
                               T008_Id,
                               T008_T007_Id,
                               T007_D024_Id,
                               T007_D024_Id,
                               T008_D009_Id
                          from T008 
                     left join T007 on T007_Id=T008_T007_Id)");                

mysql_query("insert into T001 (T001_Codigo_Produto,
                               T001_Data_Lancamento,
                               T001_Descricao_Operacao,
                               T001_Quantidade,
                               T001_Flag_Operacao,
                               T001_Valor_Preco_Unitario,
                               T001_Valor_Custo_Unitario,
                               T001_Flag_MMF,
                               T001_D006_Id,
                               T001_T014_Id,
                               T001_T013_Id,
                               T001_D024_Id,
                               T001_D024_Id_Cliente,
                               T001_D009_Id)
                       (select T014_Codigo_Produto,
                               T013_Data_Emissao,
                               concat('NF-',T013_Numero_Nota_Fiscal),
                               T014_Quantidade,
                               'E',
                               T014_Valor_Custo_Unitario,
                               T014_Valor_Custo_Unitario,
                               'N',
                               T013_D006_Id,
                               T014_Id,
                               T014_T013_Id,
                               T013_D024_Id,
                               T013_D024_Id,
                               T014_D009_Id
                          from T014 
                     left join T013 on T013_Id=T014_T013_Id)");                
                     
mysql_query("update T007 
left join D024 on D024_Id=T007_D024_Id
            set T007_Cliente_Razao=D024_Nome_Empresa,
                T007_Cliente_Endereco=D024_Endereco,
                T007_Cliente_Endereco_Numero=D024_Numero,
                T007_Cliente_Bairro=D024_Bairro,
                T007_Cliente_Cep=D024_CEP,
                T007_Cliente_Cidade=D024_Cidade,
                T007_Cliente_Estado=D024_Estado,
                T007_Cliente_CNPJ_CPF=D024_CNPJ");

mysql_query("update D024 set D024_C007_Vendedor_Interno=63");

mysql_query("update D001 set D001_D037_Id=1");

mysql_query("update D009 set D009_Valor_Custo_Unitario=1");

mysql_query("update D005 set D005_D088_Id=14, D005_Flag_ST='N'");
                
mysql_query("truncate D085");
mysql_query("insert into D085 (D085_D024_Id,D085_C007_Id,D085_Data,D085_Mensagem,D085_Entidade,D085_Id_Entidade) (select T007_D024_Id,63,T007_Data_Emissao,concat('Nota fiscal emitidada n. ',T007_Numero_Nota_Fiscal,' R$ ',T007_Valor_Total),'T007',T007_Id from T007)");
mysql_query("insert into D085 (D085_D024_Id,D085_C007_Id,D085_Data,D085_Mensagem,D085_Entidade,D085_Id_Entidade) (select T013_D024_Id,63,T013_Data_Emissao,concat('Nota Fiscal recebida n.',T013_Numero_Nota_Fiscal,' R$ ',T013_Valor_Total),'T013',T013_Id from T013)");
                 

echo "\n<br />Fim!\n<br />";
