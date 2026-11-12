<?php

// Classe processamento de falhas de cadadastro

class Processamentos
{
	//Declaracao de Atributos, como privados pois so podem ser acessados por métodos da classe
	private  $Empresa_Atual;
	private  $Id_Estoque;

	//Classe Construtora
	public function __construct($Empresa,$D048_Id=1)
	{
		set_time_limit(0);
		$this->Empresa_Atual = $Empresa;
		$this->Id_Estoque = $D048_Id;
	}

	public function exec_mysql($cClausula)
	{
		$xRetorno=mysql_query($cClausula);
		$cErro=mysql_error();
		if (!empty($cErro))
		{
			echo "Ocorreu um erro ao excecutar a clausula SQL";
			echo "<script>alert('Ocorreu um erro ao excecutar a clausula SQL');</script>";
			echo $cErro;
			mysql_query("ROLLBACK");
			exit;
		}
		return $xRetorno;
	}

	public function processarPendencias ()
	{
		mysql_query("START TRANSACTION");

		//-- TIRA DE FALHAS OS ITENS QUE A NOTA FISCAL FOI CANCELADA
		$cClausula  = " UPDATE T072 ";
		$cClausula .= " left join T007 on T007_Id=T072_T007_Id ";
		$cClausula .= " set T072_Flag_Atualizado='S' ";
		$cClausula .= " where T007_Flag_Cancelada='S' ";
		$this->exec_mysql($cClausula);


		//-- PEGA TODOS OS ITENS QUE ESTAO NA PENDENCIA
		$cClausula =  " SELECT T072_Id, ";
		$cClausula .= " T072_T007_Id, ";
		$cClausula .= " T072_T008_Id, ";
		$cClausula .= " T072_D024_Id, ";
		$cClausula .= " T072_D024_Id_Cliente, ";
		$cClausula .= " T072_D009_Id, ";
		$cClausula .= " T072_D009_Id_Original,";
		$cClausula .= " T072_D001_Id,";
		$cClausula .= " T072_D006_Id,";
		$cClausula .= " T072_T066_Id,";
		$cClausula .= " T072_C004_Id,";
		$cClausula .= " T072_Codigo_Produto,";
		$cClausula .= " T072_Data_Lancamento,";
		$cClausula .= " T072_Descricao_Operacao, ";
		$cClausula .= " T072_Quantidade,";
		$cClausula .= " T072_Quantidade_Saldo,";
		$cClausula .= " T072_Flag_Operacao,";
		$cClausula .= " T072_Valor_Preco_Unitario,";
		$cClausula .= " T072_Observacao,";
		$cClausula .= " T072_Valor_Custo_Unitario,";
		$cClausula .= " T072_Flag_Complemento,";
		$cClausula .= " T072_Demonstrativo_Custo, ";
		$cClausula .= " T072_Flag_MMF,";
		$cClausula .= " T072_Flag_Atualiza_Custo, ";
		$cClausula .= " T072_Flag_Atualizado,";
		$cClausula .= " D049_D024_Id";
		$cClausula .= " from T072";
		$cClausula .= " left join D009 on T072_D009_Id=D009_Id";
		$cClausula .= " left join D049 on D009_D049_Id=D049_Id";
		$cClausula .= " where if(T072_Flag_Operacao='X',D009_Quantidade_Estoque>=T072_Quantidade,true)";
		$cClausula .= " and T072_C004_Id='".$this->getEmpresaAtual()."'";
		$cClausula .= " and D009_Id  is not null";
		$cClausula .= " and T072_Flag_Atualizado!='S'";
		
		$cSql_T072 = $this->exec_mysql($cClausula);
		
		while ($mT072=mysql_fetch_array($cSql_T072)) 
		{
			if ($mT072[T072_Flag_Operacao]=='X')
			{
				$cSql_D009=sql_query("select D009_Id
										from D009
			 							where D009_Quantidade_Estoque>='$mT072[T072_Quantidade]'
										and D009_Id='$mT072[T072_D009_Id]'");
				if (sql_num_rows($cSql_D009)<=0)
				{
					continue;
				}
			}
			
			//-- ENCONTRA O ID DO T066
			$cClausula  = " select T066_Id ";
			$cClausula .= " from T066 ";
			$cClausula .= " where T066_D048_Id='".$this->getIdEstoque()."' ";
			$cClausula .= " and T066_D009_Id='$mT072[T072_D009_Id]' ";
			$cSql_T066 = $this->exec_mysql($cClausula);
			if (mysql_num_rows($cSql_T066)==0)
			{
				echo "Erro!, não foi encontrado o T066_Id.<br>";
				echo "Codigo: $mT072[T072_Codigo_Produto] <br>";
				echo "D009_Id: $mT072[T072_D009_Id] <br>";
				echo "Estoque: ".$this->getIdEstoque()."<br>";
				echo $cClausula."<br><br>";
				continue;
			}
			$mT066=mysql_fetch_array($cSql_T066);
			
			//-- ALTERA O FLAG DAS PENDENCIAS
			$this->exec_mysql("UPDATE T072 SET T072_Flag_Atualizado='S' WHERE T072_Id='$mT072[T072_Id]'");
			if ($mT072[T072_D009_Id_Original]!=$mT072[T072_D009_Id]) 
			{
				$this->exec_mysql("update T008 set T008_D009_Id_Pendencia='$mT072[T072_D009_Id]' where T008_Id='$mT072[T072_T008_Id]'");
			}
			
			//-- FAZ A INCLUSAO NO HISTORICO
			$cClausula  = " INSERT INTO T001 (";
			$cClausula .= "             T001_T072_Id, ";
			$cClausula .= "             T001_T007_Id, ";
			$cClausula .= "             T001_T008_Id, ";
			$cClausula .= "             T001_D024_Id, ";
			$cClausula .= "             T001_D024_Id_Cliente, ";
			$cClausula .= "             T001_D009_Id, ";
			$cClausula .= "             T001_D001_Id, ";
			$cClausula .= "             T001_D006_Id, ";
			$cClausula .= "             T001_T066_Id, ";
			$cClausula .= "             T001_C004_Id, ";
			$cClausula .= "             T001_Codigo_Produto, ";
			$cClausula .= "             T001_Data_Lancamento, ";
			$cClausula .= "             T001_Descricao_Operacao, ";
			$cClausula .= "             T001_Quantidade, ";
			$cClausula .= "             T001_Flag_Operacao, ";
			$cClausula .= "             T001_Valor_Preco_Unitario, ";
			$cClausula .= "             T001_Observacao, ";
			$cClausula .= "             T001_Valor_Custo_Unitario, ";
			$cClausula .= "             T001_Flag_Complemento, ";
			$cClausula .= "             T001_Demonstrativo_Custo, ";
			$cClausula .= "             T001_Flag_MMF, ";
			$cClausula .= "             T001_Flag_Atualiza_Custo, ";
			$cClausula .= "             T001_Quantidade_Saldo ";
			$cClausula .= " ) VALUES ( ";
			$cClausula .= "              '$mT072[T072_Id]', ";
			$cClausula .= "              '$mT072[T072_T007_Id]', ";
			$cClausula .= "              '$mT072[T072_T008_Id]', ";
			$cClausula .= "              '$mT072[D049_D024_Id]', ";
			$cClausula .= "              '$mT072[T072_D024_Id_Cliente]', ";
			$cClausula .= "              '$mT072[T072_D009_Id]', ";
			$cClausula .= "              '$mT072[T072_D001_Id]', ";
			$cClausula .= "              '$mT072[T072_D006_Id]', ";
			$cClausula .= "              '$mT066[T066_Id]', ";
			$cClausula .= "              '$mT072[T072_C004_Id]', ";
			$cClausula .= "              '$mT072[T072_Codigo_Produto]', ";
			$cClausula .= "              now(), ";
			$cClausula .= "              '$mT072[T072_Descricao_Operacao]', ";
			$cClausula .= "              '$mT072[T072_Quantidade]', ";
			$cClausula .= "              '$mT072[T072_Flag_Operacao]', ";
			$cClausula .= "              '$mT072[T072_Valor_Preco_Unitario]', ";
			$cClausula .= "              '$mT072[T072_Observacao]', ";
			$cClausula .= "              '$mT072[T072_Valor_Custo_Unitario]', ";
			$cClausula .= "              '$mT072[T072_Flag_Complemento]', ";
			$cClausula .= "              '$mT072[T072_Demonstrativo_Custo]', ";
			$cClausula .= "              '$mT072[T072_Flag_MMF]', ";
			$cClausula .= "              '$mT072[T072_Flag_Atualiza_Custo]', ";
			$cClausula .= "              '0' ";
			$cClausula .= " ) ";
			
			$this->exec_mysql($cClausula);
			
		}

		mysql_query("COMMIT");

	}


	public function getEmpresaAtual()
	{
		return $this->Empresa_Atual;
	}
	public function getIdEstoque()
	{
		return $this->Id_Estoque;
	}

}

?>
