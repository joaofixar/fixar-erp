<?php

class classeSuperPesquisa
{
      public function setClasseCampo($x)
      {
          $this->classe_campo=$x;
      }
      public function getClasseCampo()
      {
      	  if (empty($this->classe_campo))
      	  {
      	  	return 'campo';
      	  }
          return $this->classe_campo;
      }
	
      public function setNomeCampo($x)
      {
          $this->nome_campo=$x;
      }
      public function setComplemento($x)
      {
          $this->complemento_campo=$x;
      }
      public function setLeftDiv($x)
      {
          $this->left_div=$x;
      }
      
      public function setTopDiv($x)
      {
         if ($x<0)
         {
         	$x=5;
         } 
      	 $this->top_div=$x;
      }
      
      public function setTamanhoCampo($x)
      {
          $this->tamanho_campo=$x;
      }
      public function setTabelaSql($x)
      {
          $this->tabela_sql=$x;
      }
      public function setCamposSql($x)
      {
          $this->campos_sql=$x;
      }

      public function setFiltroFixoSql($x)
      {
          $this->filtro_fixo_sql=$x;
      }
      public function setRelacionamentosSql($x)
      {
          $this->relacionamentos_sql=$x;
      }
      public function setAgruparSql($x)
      {
          $this->agrupar_sql=$x;
      }
      public function setOrdenarSql($x)
      {
          $this->ordenar_sql=$x;
      }
      public function setTitulosColunas($x)
      {
          $this->titulos_colunas=$x;
      }
      public function setColunasVisualizadas($x)
      {
          $this->colunas_visualizadas=$x;
      }
      public function setTituloConsulta($x)
      {
          $this->titulo_consulta=$x;
      }
      public function setTamanhoColunas($x)
      {
          $this->tamanho_colunas=$x;
      }
      public function setAlinhamentoColunas($x)
      {
          $this->alinhamento_colunas=$x;
      }
      public function setVisualizarCampoPK($x)
      {
          $this->visualizar_campo_pk=$x;
      }
      public function setTipoDadosColunas($x)
      {
          $this->tipo_dados_colunas=$x;
      }
      public function setValorPadraoId($x)
      {
          $this->valor_padrao_id=$x;
      }
      public function setValorPadraoTexto($x)
      {
          $this->valor_padrao_texto=$x;
      }
      public function setTabIndex($x)
      {
          $this->tab_index=$x;
      }
      public function setEntidadePrincipal($x)
      {
          $this->entidade_principal=$x;
      }
      public function setIdEntidadePrincipal($x)
      {
          $this->id_entidade_principal=$x;
      }
      public function setFuncaoOnBlur($x)
      {
          $this->funcao_onblur=$x;
      }
      public function setFuncaoOnChange($x)
      {
          $this->funcao_onchange=$x;
      }


      public function iniciarFuncoesUnicas()
      {

          echo "
          <script language='JavaScript'>
          
          var zCount=2000;

            function pesquisa_chave_estrangeira_sc(Id_Entidade,Sequencial,div)
            {
                //document.getElementById(div+'Lupa').style.display = 'none';
                document.getElementById('DivLoadingSC').style.display = 'inline';
                document.getElementById('fme_executar_funcoes').src='pesquisa_chave_estrangeira.php3?Entidade=D024&Codigo_Consulta=005&Id_Entidade=' + Id_Entidade + '&Sequencial=' + Sequencial + '&DivPesquisa=' + div;
            }


            function atalho_consulta_sc(evento,link,conteudo_campo,campo,tipo_consulta)
            {
               var codtecla = (window.Event) ? evento.which : evento.keyCode;

               if (tipo_consulta==1) {
                  if ((codtecla==113) || (codtecla==9)) {
                     carregar_opcao_sc(link,conteudo_campo,campo);
                  }
               } else {

                  if ((codtecla==113) || (codtecla==13)) {
                  	 //document.getElementById(campo).value='0';
                     carregar_opcao_sc(link,conteudo_campo,campo);
                     return false;
                  }
               }
            }

            function carregar_opcao_sc(link,conteudo_campo,campo) {
               if (conteudo_campo!='') {
                  conteudo_campo=str_replace_sc('&', '%26', conteudo_campo);
                  document.getElementById('DivLoadingSC').style.display = 'inline';
                  document.getElementById('DivLoadingSC').style.zIndex = zCount++;
                  link = link + '&Conteudo_Campo=' + conteudo_campo;
                  document.getElementById(campo+'Frame').src=link;
               }
            }
            function str_replace_sc(busca, repla, orig) {
                str = new String(orig);";
                ?>
                rExp= "/"+busca+"/g";
           <?php
                echo "rExp= eval(rExp);
                newS= String(repla);
                str = new String(str.replace(rExp, newS));
                return str;
            }

            function AbreFecharDivConsulta_sc(Campo,Visualizar) {
            	if (Visualizar=='S') {
                   document.getElementById('DivLoadingSC').style.display = 'inline';
                   document.getElementById('DivLoadingSC').style.zIndex = zCount++;
                   //document.getElementById(Campo+'DivLupa').style.display = 'none';
            	   document.getElementById(Campo+'Div').style.display='inline';
                   document.getElementById(Campo+'Div').style.zIndex = zCount++;
                   document.getElementById('DivLoadingSC').style.zIndex = zCount++;
            	} else {
            	   document.getElementById(Campo+'Div').style.display='none';
                   document.getElementById('DivLoadingSC').style.display = 'none';
                   //document.getElementById(Campo+'DivLupa').style.display = 'inline';
            	}

            }

            function selecionar_opcao_sc(conteudo,campo,tipo_campo,campo_consulta,valor_antigo) {
                var conteudo_option=conteudo.split(',,');
                document.getElementById(campo).value=conteudo_option[0];
                if (tipo_campo=='ComboBox') {
                    document.getElementById(campo_consulta).value=conteudo_option[1];
                }
                if (tipo_campo=='ComboBox') {
                    document.getElementById(campo_consulta).focus();
                } else {
                    document.getElementById(campo).focus();
                }

                AbreFecharDivConsulta_sc(campo,'N');
           		Executar_Funcoes_sc(conteudo_option[0],campo);
            }

            function Executar_Funcoes_sc(Valor,Campo)
            {
              document.getElementById('DivLoadingSC').style.display = 'inline';
              document.getElementById('fme_executar_funcoes').src='executar_funcoes.php?Campo='+Campo+'&Valor='+Valor+'&Entidade_Principal=".$this->entidade_principal."&Id_Entidade_Principal=".$this->id_entidade_principal."';
            }

            </script>

            <STYLE>
            #DivLoadingSC
            {
            	width:150px;
            	height:25px;
            	position: absolute;
            	top:50%;
            	left:48%;
            	margin-top:-50px;
            	margin-left:-50px;
            	z-index:auto;
            	background-color: yellow;
            	border: 1px solid #BDCBD8;
            	clear:both;
            	vertical-align:middle;
            	color: red;
            	padding:12px;
                font-family:arial, tahoma, verdana, helvetica, sans-serif;
            	font-size:14px;
            	text-align:center;
            }
            </STYLE>";
            echo "<iframe id='fme_executar_funcoes' name='fme_executar_funcoes' style='display:none;'></iframe>";
            echo "<div id='DivLoadingSC' name='DivLoadingSC' style='display:none;'><img src='../imagens/gif/loading.gif' alt='' /><br />Pesquisando...</div>";


      }



      public function criarCampo()
      {
    	    $cLink_Cab  = "../class/query_super_pesquisa.php";
    	    $cLink_Cab .= "?Nome_Campo=".$this->nome_campo;
	        $cLink_Cab .= "&Tabela=".$this->tabela_sql;
	        $cLink_Cab .= "&Campos=".$this->campos_sql;
	        $cLink_Cab .= "&Filtro_Fixo=".str_replace("+","mais",base64_encode($this->filtro_fixo_sql));
	        $cLink_Cab .= "&Relacionamentos=".base64_encode($this->relacionamentos_sql);
	        $cLink_Cab .= "&Agrupar=".$this->agrupar_sql;
	        $cLink_Cab .= "&Ordenar=".$this->ordenar_sql;
  	        $cLink_Cab .= "&Colunas_Visualizadas=".$this->colunas_visualizadas;
	        $cLink_Cab .= "&Titulos_Colunas=".$this->titulos_colunas;
	        $cLink_Cab .= "&Tamanho_Colunas=".$this->tamanho_colunas;
	        $cLink_Cab .= "&Alinhamento_Colunas=".$this->alinhamento_colunas;
	        $cLink_Cab .= "&Visualizar_Campo_PK=".$this->visualizar_campo_pk;
	        $cLink_Cab .= "&Tipo_Dados_Colunas=".$this->tipo_dados_colunas;
    	//	echo "<DIV style='WIDTH: 100%; POSITION: relative; HEIGHT: 20px'>";
        //    echo "<span style='Z-INDEX: auto; LEFT: 0px; POSITION: absolute; TOP: 0px'>";

            echo "<table border=0 cellpadding=0 cellspacing=0>";
			echo "<tr>";
			echo "<td>";

	        echo "<input type='text' ";
	        echo "name='Consulta_".$this->nome_campo."' ";
            echo "id='Consulta_".$this->nome_campo."' ";
            if (!empty($this->tamanho_campo) and strpos($this->tamanho_campo,"%")===FALSE)
            {
	        	echo "size='".$this->tamanho_campo."' ";
            }	        	
	        //echo "maxlength='".$this->tamanho_campo."' ";
	        
	        if (strlen($this->valor_padrao_texto)>0)
	        {
	           echo " value ='".$this->valor_padrao_texto."' ";
	        }
	        
	        echo " class='".$this->getClasseCampo()."' ";

	        if (strlen($this->complemento_campo)>0)
	        {
	           echo " ".$this->complemento_campo." ";
	        }
	        
	        //-- DEIXA TUDO EM MAIUSCULO
	        echo "style='text-transform: uppercase;font-weight:normal;";
            if (!empty($this->tamanho_campo) and strpos($this->tamanho_campo,"%")!==FALSE)
            {
	        	echo "size='".$this->tamanho_campo."%' ";
            }	        	
	        
	        echo "' ";
	        
	        echo "tabindex='".$this->tab_index."' ";
	        
	        //-- NAO DEIXA GRAVADO HISTORICO DO QUE FOI DIGITADO
	        echo "autocomplete='off' ";

	        if (!empty($this->funcao_onblur))
	        {
	           echo " onBlur=".$this->funcao_onblur." ";
	        }
	        
	        if (!empty($this->funcao_onchange))
	        {
	           echo " onChange=\"".$this->funcao_onchange."\" ";
	        }

	        //-- ACIONA A FUNCAO QUE IRA BUSCAR O ID
	        echo "onKeyDown=\"return atalho_consulta_sc(event,'$cLink_Cab',document.getElementById('Consulta_".$this->nome_campo."').value,'".$this->nome_campo."',2);\" ";
	        echo ">";
			echo "</td>";

			echo "<td width=20>";
                 //echo "<div id='".$this->nome_campo."DivLupa' style='position:absolute; display:inline;top:2;' class='cssDivConsultaRapida'>";
					echo "<table align=center border=0 width=15 cellpadding=2 cellspacing=0>";
				         echo "<tr align=center valign=top>";
							echo "<td>";
							     echo "<img src='../imagens/gif/lupa.gif' ";
								 echo "title='Também use o atalho: Pressione F2 ou Enter' ";
								 echo "style='cursor:pointer;' ";
							 	 echo "onClick=\"carregar_opcao_sc('$cLink_Cab',document.getElementById('Consulta_".$this->nome_campo."').value,'".$this->nome_campo."');\" ";
								 echo "tabindex=-1";
								 echo ">";
                           echo "</td>";
                       echo "</tr>";
                     echo "</table>";
                // echo "</div>";
            echo "</td>";

			//-- CAMPO PARA CHAVE PRIMARIA
		    if ($this->visualizar_campo_pk=='S') echo "<td>";
			     echo "<input ";
        		 if ($this->visualizar_campo_pk=='S')
			     {
                     echo "type='text' ";
                 }
                 else
                 {
                     echo "type='hidden' ";
                 }
			     echo "name='".$this->nome_campo."' ";
			     echo "id='".$this->nome_campo."' ";
     	         if (strlen($this->valor_padrao_id)>0)
    	         {
    	            echo " value ='".$this->valor_padrao_id."' ";
    	         }
			     echo " class='".$this->getClasseCampo()."' ";
			     echo "size='5' ";
	             echo "tabindex='".($this->tab_index+1)."' ";
			     echo "onChange=\"pesquisa_chave_estrangeira_sc(this.value,'0','".$this->nome_campo."Div');\" ";
			     echo ">";
		    if ($this->visualizar_campo_pk=='S') echo "</td>";

            echo "<td>";
            echo "</tr>";
            echo "</table>";
			//echo "</span>";
			//echo "</div>";

            echo "<div ";
            echo "id='".$this->nome_campo."Div' ";
            echo "name='".$this->nome_campo."Div' ";
            echo "tabindex=-1 ";
            echo "style='position:absolute; display:none; ";
            if ($this->posicao_x>0)
            {
               echo "".$this->posicao_x;
            }
            echo "top:".$this->top_div."; ";
            echo "left:".$this->left_div."; ";
            echo "border: 0px solid; ' ";
            echo ">";
            

                echo "<table bgcolor=#47678D cellpadding='2' cellspacing='0' border='0' style='border:2px solid #000096'>";
				     echo "<tr bgcolor='#000096'>";
					      echo "<td>";
						       echo "<font class='fonte' style='color:white;font-size:14px'>";
							        echo "<b>".$this->titulo_consulta."</b>";
                               echo "</font>";
                          echo "</td>";
					      echo "<td align=right style='padding:0px 0px 0p 0px'>";
						      echo "<input type='button' name='BotaoFechar' id='BotaoFechar' value='X' style='width:22px;height:22px;font-family:arial, tahoma, verdana, helvetica, sans-serif;font-size:12px;padding:0;' onclick=\"javascript: AbreFecharDivConsulta_sc('".$this->nome_campo."','N');\">";
					      echo "</td>";
				     echo "</tr>";
				     echo "<tr bgcolor=#E0DFE3>";
					      echo "<td align=center colspan=2>";
						      echo "<iframe name='".$this->nome_campo."Frame' id='".$this->nome_campo."Frame' src='' width='675' height='365' frameborder='0' ></iframe>";
					      echo "</td>";
			         echo "</tr>";
			    echo "</table>";

            echo  "</div>";

  }



}


?>
