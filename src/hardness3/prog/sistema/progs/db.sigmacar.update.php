<?php
namespace hardness;
mysqli_query("USE {$bancoDestinoConversao};");
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
//$result = mysqli_query($sql);

echo "Atualizando C004<br>";
mysqli_query("update C004 set C004_Flag_Ativo='S', C004_Banco_Dados='{$bancoDestinoConversao}', C004_Banco_Configuracao='{$bancoDestinoConversao}',C004_Caminho_Sistema='{$bancoDestinoConversao}'");

echo "Atualizando T007<br>";
mysqli_query("update T007 left join D006 on D006_Codigo_CFOP=T007_Codigo_CFOP set T007_Flag_ACP=1, T007_D006_Id=D006_Id");

echo "Atualizando T008<br>";
mysqli_query("update T008 left join T007 on T007_Numero_Nota_Fiscal=T008_Numero_Nota_Fiscal set T008_T007_Id=T007_Id,T008_Valor_Total_Preco_Sem_Desconto=T008_Valor_Preco_Sem_Desconto_Unitario*T008_Quantidade,T008_Valor_Total_Preco=T008_Quantidade*T008_Valor_Preco_Unitario");

echo "Atualizando T002<br>";
mysql_query("update T002 set T002_Valor_Saldo=T002_Valor_Duplicata-T002_Valor_Recebido-T002_Valor_Desconto+T002_Valor_Juros");

echo "Atualizando T015<br>";
mysql_query("update T015 set T015_Valor_Saldo=T015_Valor_Duplicata-T015_Valor_Pago-T015_Valor_Desconto+T002_Valor_Juros");

echo "Atualizando D024<br>";
mysqli_query("update D024 
          left join D020 on D020_Nome_Cidade=D024_Cidade 
          left join D018 on D018_UF=D024_Estado 
          left join D030 on D030_Nome_Pais=D024_Pais 
           set D024_D020_Id=D020_Id,D024_D018_Id=D018_Id,D024_D030_Id=30");

echo "Cadastrando C007";
mysqli_query("insert into C007 (C007_Id,C007_Login,C007_Senha,C007_Email,C007_Nome,C007_Primeiro_Nome,C007_Especial,C007_Ativo,C007_Id_Perfil,C007_Usuario_Suporte,C");

echo "Cadastrando perfis para C004";
mysqli_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,1,63)");
mysqli_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,2,63)");
mysqli_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,3,63)");
mysqli_query("insert into C019 (C019_C021_Id,C019_C004_Id,C019_C007_Id) values (12,4,63)");

mysqli_query("truncate D009");
mysqli_query("truncate D049");
mysqli_query("truncate T066");
mysqli_query("truncate D041");


$D001=mysqli_query("select D001_Id,D001_Codigo_Grupo,D001_Lote_Economico from D001");

while ($mD001=mysql_fetch_array($D001)) 
{
	  mysqli_query("insert into D049 (D049_D001_Id,
	                                 D049_D082_Id)
                             values ('$mD001[D001_Id]',
                                     '$mD001[D001_Codigo_Grupo]')");
	  $D049_Id=mysql_insert_id();
	
	  mysqli_query("insert into D041 (D041_D049_Id,
	                                  D041_Valor_Base,
	                                  D041_Valor_Preco_Consumidor,
	                                  D041_Valor_Preco_Revenda)
                             values ('$D049_Id',
                                     '$mD001[D001_Lote_Economico]',
                                     '$mD001[D001_Lote_Economico]',
                                     '$mD001[D001_Lote_Economico]')");

      for ($i=1;$i<=4;$i++)
      {	  
	      mysqli_query("insert into D009 (D009_C004_Id,
	                                     D009_D049_Id,
	                                     D009_Data_Cadastro,
	 				                     D009_Flag_Ativo)
			                     values ('$i',
				                         '$D049_Id',
	 			                          current_date(),
	                                     'S')");
      }   
}

mysqli_query("UPDATE T001 LEFT JOIN D001 ON D001_ID=T001_D001_ID LEFT JOIN D049 ON D049_D001_ID=D001_ID LEFT JOIN D009 ON D009_D049_ID=D049_ID SET T001_D009_ID=D009_ID");

mysqli_query("update T001 set T001_Flag_Operacao='S' where T001_Flag_Operacao='V' or T001_Flag_Operacao='S'");
mysqli_query("update T001 set T001_Flag_Operacao='B' where T001_Flag_Operacao='I'");
mysqli_query("update T001 set T001_Flag_Operacao='E' where T001_Flag_Operacao!='S' and T001_Flag_Operacao!='B'");

mysqli_query("update D001 set D001_Flag_Ativo='S'");
mysqli_query("update D049 set D049_D082_Id=1 where D049_D082_Id<=0");
mysqli_query("update D024 set D024_Flag_Cliente='S'");
mysqli_query("update D024 set D024_Flag_Fornecedor='S'");
mysqli_query("update D001 left join D037 on D037_Unidade=D001_Aplicacao set D001_D037_Id=D037_Id");
mysqli_query("update D024 set D024_Flag_Revenda_Consumidor='2'");
mysqli_query("update D024 set D024_Flag_Contribuinte='1'");
mysqli_query("update D024 set D024_Flag_Lista_Preco='1'");
mysqli_query("update D024 set D024_Flag_Pessoa_Fisica_Juridica='F'");
mysqli_query("update D001 set D001_D005_Id='299'");
mysqli_query("update D116 left join D133 on D133_Id=D116_D133_Id left join D134 on D134_Id=D116_D134_Id set D116_Modelo=D133_Modelo,D116_Cor=D134_Cor;");
mysqli_query("update D001 set D001_Flag_Ativo='N' where D001_Descricao_Produto like '*%'");
mysqli_query("update D024 set D024_Flag_Pessoa_Fisica_Juridica='J' where D024_CNPJ != ''");
                                                                                        
mysqli_query("update D006 set D006_Flag_Venda_Compra_Outros='V',
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
						 	 D006_Flag_Departamento='COMERCIAL',
						 	 D006_Flag_Somar_IPI_Base_ICMS='N',
						 	 D006_D059_Id='1'
                       where D006_Codigo_Natureza like '6%'
                         and D006_Descricao like '%VENDA%';");

mysqli_query("update D006 set D006_Flag_Venda_Compra_Outros='V',
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
						 	 D006_Flag_Departamento='COMERCIAL',
						 	 D006_Flag_Somar_IPI_Base_ICMS='N',
						 	 D006_D059_Id='1'
                       where D006_Codigo_Natureza like '5%'
                         and D006_Descricao like '%VENDA%';");

/*
mysqli_query("truncate T003");
mysqli_query("truncate T004");
mysqli_query("truncate T005");
mysqli_query("truncate T006");
mysqli_query("truncate T099");
mysqli_query("truncate T145");
mysqli_query("truncate T090");
mysqli_query("truncate T089");
mysqli_query("truncate T147");
mysqli_query("truncate T138");
*/
                          
echo "\n<br />Fim!\n<br />";