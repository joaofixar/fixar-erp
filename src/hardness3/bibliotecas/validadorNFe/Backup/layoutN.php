          N. ICMS Normal e ST
          #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação       
          164 N01   ICMS    Informações do ICMS da Operação própria e ST CG          1-1                        Informar apenas um dos grupos de tributação do ICMS (ICMS00, ICMS10, ...) (v2.0)
      
          ********************************Grupo Tributação do ICMS= 00********************************
          #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
          165 N02   ICMS00  Grupo Tributação do ICMS= 00                     N       1-1                        Tributada integralmente
          166 N11   orig    Origem da mercadoria                             N       1-1         1              0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;
                                                                                                                1 - Estrangeira - Importação direta, exceto a indicada no código 6;
                                                                                                                2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;
                                                                                                                3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;
                                                                                                                4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;
                                                                                                                5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;
                                                                                                                6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;
                                                                                                                7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural.
                                                                                                                8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;
          167 N12 CST       Tributação do ICMS = 00                          N       1-1         2              00=Tributada integralmente.
          168 N13 modBC     Modalidade de determinação da BC do ICMS         N       1-1         1              0=Margem Valor Agregado (%);1=Pauta (Valor);2=Preço Tabelado Máx. (valor);3=Valor da operação
          169 N15 vBC       Valor da BC do ICMS                              N       1-1         13v2
          170 N16 pICMS     Alíquota do imposto                              N       1-1        3v2-4
          171 N17 vICMS     Valor do ICMS                                    N       1-1         13v2         
          
                              
          */
          
          // 166, 173, 186 - Valida se o campo está vazio e a informação contida neste está correta de acordo com a observação.
          /*  0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;
              1 - Estrangeira - Importação direta, exceto a indicada no código 6;
              2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;
              3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;
              4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;
              5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;
              6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;
              7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural.
              8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%; 
          */
              // Valida os campos para casos específicos de cada tipo de ICMS
              // ICMS = 00
              /*if($mT00_3_5_7['codigoST'] == '00'){ 

                  if(empty($mT00_3_5_7['situacaoTributaria']) === FALSE){
                      $mT00_3_5_7['situacaoTributaria'] = trim($mT00_3_5_7['situacaoTributaria']);
                      $mT00_3_5_7['situacaoTributaria'] = substr($mT00_3_5_7['situacaoTributaria'],0,1);
                      if(($mT00_3_5_7['situacaoTributaria'] < 0) || ($mT00_3_5_7['situacaoTributaria'] > 8)){
                         $erros .= "-ORIGEM DA MERCADORIA DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['situacaoTributaria']." deve ser preenchida com: \n\t0-Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;\n\t1-Estrangeira - Importação direta, exceto a indicada no código 6;\n\t2-Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;\n\t3-Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;\n\t4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;\n\t5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;\n\t6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;\n\t7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural;\n\t8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;!\n";
                      } 
                  }else{
                      $erros .= "-ORIGEM DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") está vazia!\n";
                  }

                  // 167, 174, 187 - Tributação do ICMS 
                  // ------------------- Campo setado manualmente no nf3.class - HARDNESS ----------------------------//

                  // 168, 175, 188 - Valida se o campo, Modalidade de determinação da BC do ICMS, está preenchido e se sua informação está correta de acordo com a observação
                  //modalidadeICMS
                  if($mT00_3_5_7['modalidadeICMS']>0){
                      if(($mT00_3_5_7['modalidadeICMS'] < 0) || ($mT00_3_5_7['modalidadeICMS'] > 3)){
                          $erros .= "-MODALIDADE BASE ICMS DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['modalidadeICMS']." deve ser:\n\t0-Margem Valor Agregado (%);\n\t1-Pauta (Valor);\n\t2-Preço Tabelado Máx. (valor);\n\t3-Valor da operação.\n";
                      }    
                  }else{
                      $erros .= "-MODALIDADE BASE ICMS DO PRODUTO: ". $mT00_3_5_7['codigoProduto']." está vazio!\n";
                  }

              }

          // ICMS = 10
          /*
          ********************************Grupo Tributação do ICMS=10********************************
          #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
          172 N02   ICMS00  Grupo Tributação do ICMS=10                     N       1-1                        Tributada integralmente
          173 N11   orig    Origem da mercadoria                             N       1-1         1              0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;
                                                                                                                1 - Estrangeira - Importação direta, exceto a indicada no código 6;
                                                                                                                2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;
                                                                                                                3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;
                                                                                                                4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;
                                                                                                                5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;
                                                                                                                6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;
                                                                                                                7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural.
                                                                                                                8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;
          174 N12 CST       Tributação do ICMS = 00                          N       1-1         2              00=Tributada integralmente.
          175 N13 modBC     Modalidade de determinação da BC do ICMS         N       1-1         1              0=Margem Valor Agregado (%);1=Pauta (Valor);2=Preço Tabelado Máx. (valor);3=Valor da operação
          176 N15 vBC       Valor da BC do ICMS                              N       1-1         13v2           
          177 N16 pICMS     Alíquota do imposto                              N       1-1        3v2-4           
          178 N17 vICMS     Valor do ICMS                                    N       1-1         13v2           
          179 N18 modBCST   Modalidade de determinação da BC do ICMS ST      N       1-1         1              0=Preço tabelado ou máximo sugerido;
                                                                                                                1=Lista Negativa (valor);
                                                                                                                2=Lista Positiva (valor);
                                                                                                                3=Lista Neutra (valor);
                                                                                                                4=Margem Valor Agregado (%);
                                                                                                                5=Pauta (valor);
          180 N19 pMVAST    % da margem de valor Adicionado do ICMS ST       N       0-1        3v2-4
          182 N21 vBCST     Valor da BC do ICMS ST                           N       1-1        13v2
          183 N22 pICMSST   Alíquota do imposto do ICMS ST                   N       1-1        3v2-4                               
          184 N23 vICMSST   Valor do ICMS ST                                 N       1-1        13v2            Valor do ICMS ST retido    
          */
          /*if($mT00_3_5_7['codigoST'] == '10'){
              //#   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
              //173 N11   orig    Origem da mercadoria                             N       1-1         1              0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;1 - Estrangeira - Importação direta, exceto a indicada no código 6;2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural.8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;                           
              if(empty($mT00_3_5_7['situacaoTributaria']) === FALSE){
                  $mT00_3_5_7['situacaoTributaria'] = substr($mT00_3_5_7['situacaoTributaria'],0,1);
                  if(($mT00_3_5_7['situacaoTributaria'] < 0) || ($mT00_3_5_7['situacaoTributaria'] > 8)){
                      $erros .= "-ORIGEM DA MERCADORIA DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['situacaoTributaria']." deve ser preenchida com: \n\t0-Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;\n\t1-Estrangeira - Importação direta, exceto a indicada no código 6;\n\t2-Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;\n\t3-Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;\n\t4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;\n\t5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;\n\t6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;\n\t7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural;\n\t8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;!\n";
                      }   
              }else{
                  $erros .= "-ORIGEM MERCADORIA DO PRODUTO:". $mT00_3_5_7['codigoProduto']." está vazia!\n";
              }

              // 179 - Valida se Modalidade está preenchida e se seu valor está correto, de acordo com a observação no manual
              if(is_null($mT00_3_5_7['modalidadeBCICMS'])){
                  $erros .= "-MODALIDADE DA BASE DO ICMS ST DO PRODUTO (".$mT00_3_5_7['modalidadeBCICMS ']." DA BASE DO ICMS ST DO PRODUTO:". $mT00_3_5_7['codigoProduto']." deve conter somente 1 dígito!\n";
              }else if(($mT00_3_5_7['modalidadeBCICMS'] < 0) || (($mT00_3_5_7['modalidadeBCICMS'] > 5))){
                  $erros .= "-MODALIDADE DA BASE DO ICMS ST DO PRODUTO (" .$mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['modalidadeBCICMS']." deve ser: \n\t0-Preço tabelado ou máximo sugerido;\n\t1-Lista Negativa (valor);\n\t2-Lista Positiva (valor);\n\t3-Lista Neutra (valor);\n\t4-Margem Valor Agregado (%);\n\t5-Pauta (valor);\n";
              }

              // 182 - Valida se Valor da BC do ICMS ST está preenchido corretamente
              //-------------------------Na T008 o campo é: T008_Valor_Base_ICMS_Substituicao já nas outras tabelas----------
              //-------------------------são: _Valor_ICMS_Substituicao_Destino, Valor_Base_ICMS_Substituicao_Destino---------
              //-------------------------Qual campo utilizar, qual a diferença?--------------------------------------------//
          
              // 183 - Alíquota do imposto do ICMS ST
              //--------------------Calculado no próprio xml -------------------------------------------------------------//
              
              // 184 - Valida se Valor do ICMS ST retido está preenchido e seu tamanho valorICMSSTRetido
              
          }

          /*********************************Grupo Tributação do ICMS=20********************************
          #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
          172 N02   ICMS00  Grupo Tributação do ICMS=10                     N       1-1                        Tributada integralmente
          173 N11   orig    Origem da mercadoria                             N       1-1         1              0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;
                                                                                                                1 - Estrangeira - Importação direta, exceto a indicada no código 6;
                                                                                                                2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;
                                                                                                                3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;
                                                                                                                4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;
                                                                                                                5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;
                                                                                                                6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;
                                                                                                                7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural.
                                                                                                                8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;
          174 N12 CST       Tributação do ICMS = 00                          N       1-1         2              00=Tributada integralmente.
          189 N14 pRedBC    Percentual da Redução de BC                      N       1-1       3v2-4
        192.2 N27a vICMSDeson Valor do ICMS desonerado                          N      1-1        13v2            Informar apenas nos motivos de desoneração documentados abaixo.
          */

          /*if($mT00_3_5_7['codigoST'] == '20'){
              //#   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
              //186 N11   orig    Origem da mercadoria                             N       1-1         1              0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;1 - Estrangeira - Importação direta, exceto a indicada no código 6;2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural.8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;                           
              if(empty($mT00_3_5_7['situacaoTributaria']) === FALSE){
                  $mT00_3_5_7['situacaoTributaria'] = substr($mT00_3_5_7['situacaoTributaria'],0,1);
                  if(($mT00_3_5_7['situacaoTributaria'] < 0) || ($mT00_3_5_7['situacaoTributaria'] > 8)){
                      $erros .= "-ORIGEM DA MERCADORIA DO PRODUTO (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['situacaoTributaria']." deve ser preenchida com: \n\t0-Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8;\n\t1-Estrangeira - Importação direta, exceto a indicada no código 6;\n\t2-Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;\n\t3-Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%;\n\t4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;\n\t5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;\n\t6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX e gás natural;\n\t7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante lista CAMEX e gás natural;\n\t8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%;!\n";
                      }  
              }else{
                  $erros .= "-ORIGEM DA MERCADORIA DO PRODUTO:". $mT00_3_5_7['codigoProduto']." está vazia!\n";
              }
              
              // 192.3 - Valida se campo,Motivo da desoneração do ICMS, deve ser preenchido e se seu valor está corretomotivoDesoneracaoICMS
              if(!empty($mT00_3_5_7['valorICMSDesonerado'])){
                  if(($mT00_3_5_7['valorICMSDesonerado'] != 3) || ($mT00_3_5_7['valorICMSDesonerado'] !=  12)){
                      $erros .= "-MOTIVO DESONERAÇÃO DO ICMS (". $mT00_3_5_7['codigoProduto'].") ".$mT00_3_5_7['valorICMSDesonerado']. " deve ser: \n3-Uso na agropecuária;\n9-Outros;\n12-Órgão de fomento e desenvolvimento agropecuário !\n";                            
                  }
              }
          }
          

          // ICMS = 30
          if($mT00_3_5_7['codigoST'] == '30'){}

          // ICMS = 40
          if($mT00_3_5_7['codigoST'] == '40'){}

          // ICMS = 51
          if($mT00_3_5_7['codigoST'] == '51'){}

          // ICMS = 60
          if($mT00_3_5_7['codigoST'] == '60'){}

          // ICMS = 70
          if($mT00_3_5_7['codigoST'] == '70'){}

          // ICMS = 90
          if($mT00_3_5_7['codigoST'] == '90'){}
      $erros .= "\n";
  }
}

