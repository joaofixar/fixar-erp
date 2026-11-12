<?php
namespace hardness;
	
	function validaCampoComprar($array){
		global $g,$r_acaoId;
		$retorno = "<input onchange=\"est010CheckboxComprar(this, '{$array['todosCampos']['T006_Id']}', '{$g['divId']}')\" type='checkbox' id='T006_Flag_Comprar' title='Cotar'";
		if ($array['todosCampos']['T006_Flag_Comprar']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	}
	
	function campoPrecoUltimaCompra($array) {
		$D009=mysql_query("select T001_Valor_Custo_Unitario Preco_Fob,
                                 T001_Data_Lancamento Data_Preco_Fob,
                                 T001_Descricao_Operacao Nota_Preco_Fob
                            from T001
                           where T001_D009_Id='{$array['todosCampos']['D009_Id']}'
                             and T001_Flag_Atualiza_Custo='S'
                             and (T001_D024_Id!=0 or T001_D024_Id is not null)
                             order by T001_Data_Lancamento desc
                            limit 1");
       $mD009=mysql_fetch_array($D009);
       
       return gCorrigeNumero($mD009['Preco_Fob']);
	}
	function campoCustoFornecedor($array,$Forn){
		global $g;
		$D069=mysql_query("select D069_Preco_Unitario,
                                 D024_Nome_Fantasia,
                                 D069_Percentual_Desconto
                            from D083
                       left join D069 on D069_Codigo_Produto=D083_Codigo_Produto_Fornecedor
                       left join D070 on D070_Id=D069_D070_Id
                       left join D024 on D024_Id=D070_D024_Id
                           where D083_D049_Id='{$array['todosCampos']['D049_Id']}'
                             and D070_D024_Id=D083_D024_Id
                             and D070_Flag_Ativo='S'
                        order by D069_Preco_Unitario*(1-(D069_Percentual_Desconto/100))");
    	$mD069=mysql_fetch_array($D069);
    	if ($array['todosCampos']['D049_Flag_Tipo']=='MD')
       {
           $D049=mysql_query("select D049_Id
                                from D049
                           left join D009 on D009_D049_Id=D049_Id and D009_C004_Id='{$g['empresaAtual']}'
                               where D049_D001_Id='{$array['todosCampos']['D001_Id']}'
                                 and D049_Flag_Tipo='CP'");
           $mD049=mysql_fetch_array($D049);
           $D0691=mysql_query("select D069_Preco_Unitario,
                                     D024_Nome_Fantasia,
                                     D069_Percentual_Desconto
                                from D083
                           left join D069 on D069_Codigo_Produto=D083_Codigo_Produto_Fornecedor
                           left join D070 on D070_Id=D069_D070_Id
                           left join D024 on D024_Id=D070_D024_Id
                               where D083_D049_Id='{$mD049['D049_Id']}'
                                 and D070_D024_Id=D083_D024_Id
                            order by D069_Preco_Unitario*(1-(D069_Percentual_Desconto/100))");
            $mD06911=mysql_fetch_array($D0691);
            $mD06912=mysql_fetch_array($D0691);
            $mD06913=mysql_fetch_array($D0691);
            
            $Fornecedor1=$mD06911['D024_Nome_Fantasia'];
            $Fornecedor2=$mD06912['D024_Nome_Fantasia'];
            $Fornecedor3=$mD06913['D024_Nome_Fantasia'];
       }
    	if ($array['todosCampos']['D049_Flag_Tipo']!='MD')
       {
          $Fornecedor1=$mD069['D024_Nome_Fantasia'];
       }

       if ($array['todosCampos']['D049_Flag_Tipo']=='MD')
       {
              $Forn1 = substr($Fornecedor1,0,10).'<br>'.number_format($mD06911['D069_Preco_Unitario']*(1-($mD06911['D069_Percentual_Desconto']/100)),2,',','.');
           $mD069=mysql_fetch_array($D069);
           if ($array['todosCampos']['D049_Flag_Tipo']!='MD')
           {
              $Fornecedor2=$mD069['D024_Nome_Fantasia'];
           }
              $Forn2 = substr($Fornecedor2,0,10).'<br>'.number_format($mD06912['D069_Preco_Unitario']*(1-($mD06912['D069_Percentual_Desconto']/100)),2,',','.');
           $mD069=mysql_fetch_array($D069);
           if ($array['todosCampos']['D049_Flag_Tipo']!='MD')
           {
              $Fornecedor3=$mD069['D024_Nome_Fantasia'];
           }
		      $Forn3 = substr($Fornecedor3,0,10).'<br>'.number_format($mD06913['D069_Preco_Unitario']*(1-($mD06913['D069_Percentual_Desconto']/100)),2,',','.');
       }
       else
       {
              $Forn1 = substr($Fornecedor1,0,10).'<br>'.number_format($mD069['D069_Preco_Unitario']*(1-($mD069['D069_Percentual_Desconto']/100)),2,',','.');
           $mD069=mysql_fetch_array($D069);
           if ($array['todosCampos']['D049_Flag_Tipo']!='MD')
           {
              $Fornecedor2=$mD069['D024_Nome_Fantasia'];
           }
		      $Forn2 = substr($Fornecedor2,0,10).'<br>'.number_format($mD069['D069_Preco_Unitario']*(1-($mD069['D069_Percentual_Desconto']/100)),2,',','.');

           $mD069=mysql_fetch_array($D069);
           if ($array['todosCampos']['D049_Flag_Tipo']!='MD')
           {
              $Fornecedor3=$mD069['D024_Nome_Fantasia'];
           }
              $Forn3 = substr($Fornecedor3,0,10).'<br>'.number_format($mD069['D069_Preco_Unitario']*(1-($mD069['D069_Percentual_Desconto']/100)),2,',','.');
        }
		if($Forn=="Forn1"){
			return $Forn1;
		}elseif($Forn=="Forn2"){
			return $Forn2;
		}elseif($Forn=="Forn3"){
			return $Forn3;
		}
	}

?>