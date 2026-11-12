<?php

$cClausula  = " select ";
$cClausula .= " D024_Id, ";
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " D024_Nome_Fantasia, ";
$cClausula .= " D024_Endereco, ";
$cClausula .= " D024_Numero, ";
$cClausula .= " D020_Nome_Cidade, ";
$cClausula .= " D018_UF, ";
$cClausula .= " D024_Bairro, ";
$cClausula .= " D024_Cep, ";
$cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
$cClausula .= " if(D024_Cnpj='',D024_Cpf,D024_Cnpj) D024_Cnpj, ";
$cClausula .= " D024_Flag_Revenda_Consumidor, ";
$cClausula .= " D024_Flag_Tipo_Empresa, ";
$cClausula .= " D024_Nome_Contato, ";
$cClausula .= " D024_Observacoes, ";
$cClausula .= " D024_Inscricao_Estadual, ";
$cClausula .= " D024_Praca_Pagamento,  ";
$cClausula .= " D024_Aviso, ";
$cClausula .= " D024_Flag_Classe, ";
$cClausula .= " D024_Flag_Liberado, ";
$cClausula .= " D030_Nome_Pais, ";
$cClausula .= " D024_Observacao_Compra,  ";
$cClausula .= " D024_Data_Irregular, ";
$cClausula .= " D024_DDD_Telefone_1,  ";
$cClausula .= " D024_Telefone_1,  ";
$cClausula .= " D024_Telefone_2,  ";
$cClausula .= " D024_Fax, ";
$cClausula .= " D024_Dias_Carencia_Pagamento, ";
$cClausula .= " D024_Dia_Semana_Pagamento, ";
$cClausula .= " D024_Dia_Mes_Pagamento, ";
$cClausula .= " D024_Observacoes, ";
$cClausula .= " C007_Primeiro_Nome ";
$cClausula .= " from D024 ";
$cClausula .= " left join D020 on D024_D020_Id=D020_Id ";
$cClausula .= " left join D018 on D020_D018_Id=D018_Id ";
$cClausula .= " left join D030 on D024_D030_Id=D030_Id ";
$cClausula .= " left join C007 on C007_Id=D024_C007_Vendedor_Interno ";
$cClausula .= " where D024_Id='$D024_Id' ";
$cSql_D024 = mysql_query($cClausula);

$mD024=mysql_fetch_array($cSql_D024);

echo "<table border=0  cellspacing=0 bgcolor='#AFC2CD' cellpadding=0 width=800 height=100%>";

 if (mysql_num_rows($cSql_D024)<=0)
 {

   	echo "<tr bgcolor=silver>";
      	echo "<td align=center bgcolor=#F2F2EE colspan=2>";
             echo "<font class=fonte><big>Selecione um cliente</big></font>";
 		echo "</td>";
	echo "</tr>";

 }
 else
 {
	
	echo "<tr>";
		echo "<td valign=top height=100>";

			echo "<table border=0 cellspacing=1 cellpadding=2 width=100% cellpadding=0 bgcolor=silver >";

    			echo "<tr>";
   			      	echo "<td align=left bgcolor=#F2F2EE colspan=2>";
  				  		 echo "<font class=fonte>Fantasia<br><b>$mD024[D024_Nome_Fantasia]</b></font>";
					echo "</td>";
   			      	echo "<td align=left bgcolor=#F2F2EE colspan=2>";
  				  		 echo "<font class=fonte>CNPJ / CPF<br><b>$mD024[D024_Cnpj]</b></font>";
					echo "</td>";
   			      	echo "<td align=left bgcolor=#F2F2EE colspan=2>";
                        echo "<font class=fonte>I.E.<br>";
                        if (empty($mD024[D024_Inscricao_Estadual]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[D024_Inscricao_Estadual]</b></font>";
                        }
					echo "</td>";
				echo "</tr>";

    			echo "<tr>";
   			      	echo "<td align=left bgcolor=#F2F2EE colspan=2>";
                        echo "<font class=fonte>Endere�o<br>";
                        if (empty($mD024[D024_Endereco]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[D024_Endereco]</b></font>";
                        }
					echo "</td>";
   			      	echo "<td align=left bgcolor=#F2F2EE >";
                        echo "<font class=fonte>N�mero<br>";
                        if (empty($mD024[D024_Numero]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[D024_Numero]</b></font>";
                        }
					echo "</td>";
   			      	echo "<td align=left bgcolor=#F2F2EE >";
                        echo "<font class=fonte>Bairro<br>";
                        if (empty($mD024[D024_Bairro]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[D024_Bairro]</b></font>";
                        }
					echo "</td>";
					echo "<td bgcolor=#F2F2EE align=left>";
					   	  echo "<font class=fonte>Cidade<br><b>$mD024[D020_Nome_Cidade]</b> </font>";
					echo "</td>";
					echo "<td bgcolor=#F2F2EE align=left>";
					   	  echo "<font class=fonte>UF<br><b>$mD024[D018_UF]</b> </font>";
					echo "</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td align=left bgcolor=#F2F2EE >";
                        echo "<font class=fonte>DDD<br>";
                        if (empty($mD024[D024_DDD_Telefone_1]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[D024_DDD_Telefone_1]</b></font>";
                        }
					echo "</td>";
					echo "<td align=left bgcolor=#F2F2EE >";
                        echo "<font class=fonte>Telefone<br>";
                        if (empty($mD024[D024_Telefone_1]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[D024_Telefone_1]</b></font>";
                        }
					echo "</td>";
					echo "<td bgcolor=#F2F2EE align=left>";
							echo "<font class=fonte>FAX<br><b>$mD024[D024_Fax]</b></font>";
						echo "</a>";
					echo "</td>";
					echo "<td bgcolor=#F2F2EE >";
							echo "<font class=fonte>Contato<br><b>$mD024[D024_Nome_Contato]</b></font>";
						echo "</a>";
					echo "</td>";
					echo "<td bgcolor=#F2F2EE colspan=2>";
                        echo "<font class=fonte>Vendedor<br>";
                        if (empty($mD024[C007_Primeiro_Nome]))
                        {
                            echo "<font style='color: var(--BGVermelho)'><b>FALTA</b></font></font>";
                        }
                        else
                        {
                            echo "<b>$mD024[C007_Primeiro_Nome]</b></font>";
                        }
						echo "</a>";
					echo "</td>";
				echo "</tr>";

/*				echo "<tr>";
					echo "<td colspan=15 bgcolor=#F2F2EE height=35>";
							echo "<font class=fonte>Observa��o<br><b>$mD024[D024_Observacoes]</b></font>";
						echo "</a>";
					echo "</td>";
				echo "</tr>"; */

			echo "</table>";
		echo "</td>";
	echo "</tr>";
}

echo "</table>";
?>
