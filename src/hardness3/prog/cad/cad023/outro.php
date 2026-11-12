<?php
namespace hardness;

switch ($g['r'][3]) {

    case 'cad023ManualCSVDepartamentos':
        echo<<<EOT
        
        <style>
            .tabela{
                border:1px solid black;
                border-colapse: colapse;	
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
                margin-left:10px;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabela{				
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }	
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação
        <br/>-O documento deve conter <strong>1 coluna</strong> com o nome dos departamentos.
        <br/>-O nome da coluna deve estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
        <br/>-A extesão do documento deve ser (*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco.
        </p>
        </div>
        <div style="margin-left:10px;">
            <table cellspacing=1px bgcolor="black">		
                <tr>			
                    <td class="linhaTabela">Departamento</td>                   		  	
                </tr>
                 <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>		
                    <td class="linhaTabela">------</td>
                </tr>	
            </table>
        </div>
EOT;
    break;

case 'cad023ManualCSVSetores':
        echo<<<EOT
        
        <style>
            .tabela{
                border:1px solid black;
                border-colapse: colapse;	
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
                margin-left:10px;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabela{				
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }	
        </style>

        <div style="padding-left:10px;">
            <h2>Modelo de documento CSV</h2>
            <h3>Observações:</h3>
            <p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação
            <br/>-O documento deve conter <strong>1 coluna</strong> com o nome dos setores.
            <br/>-O nome da coluna deve estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
            <br/>-A extesão do documento deve ser (*.csv)
            <br/>-Ao final do documento não deve haver nenhuma linha em branco.
            </p>
        </div>
        <div style="margin-left:10px;">
            <table cellspacing=1px bgcolor="black">		
                <tr>			
                    <td class="linhaTabela">Setor</td>                   		  	
                </tr>
                 <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>		
                    <td class="linhaTabela">------</td>
                </tr>	
            </table>
        </div>
EOT;
    break;

    case 'cad023ManualCSVGrupo':
        echo<<<EOT
        
        <style>
            .tabela{
                border:1px solid black;
                border-colapse: colapse;	
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
                margin-left:10px;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabela{				
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }	
        </style>

        <div style="padding-left:10px;">
            <h2>Modelo de documento CSV</h2>
            <h3>Observações:</h3>
            <p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação
            <br/>-O documento deve conter <strong>1 coluna</strong> com o nome dos grupos.
            <br/>-O nome da coluna deve estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
            <br/>-A extesão do documento deve ser (*.csv)
            <br/>-Ao final do documento não deve haver nenhuma linha em branco.
            </p>
        </div>
        <div style="margin-left:10px;">
            <table cellspacing=1px bgcolor="black">		
                <tr>			
                    <td class="linhaTabela">Grupo</td>                   		
      	
                </tr>
                 <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>		
                    <td class="linhaTabela">------</td>
                </tr>	
            </table>
        </div>
EOT;
    break;

    case 'cad023ManualCSVSubGrupo':
        echo<<<EOT
        
        <style>
            .tabela{
                border:1px solid black;
                border-colapse: colapse;	
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
                margin-left:10px;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabela{				
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }	
        </style>
        <div style="padding-left:10px;">
            <h2>Modelo de documento CSV</h2>
            <h3>Observações:</h3>
            <p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação
            <br/>-O documento deve conter <strong>1 colunas</strong> com o nome dos sub-grupos.
            <br/>-O nome da coluna deve estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
            <br/>-A extesão do documento deve ser (*.csv)
            <br/>-Ao final do documento não deve haver nenhuma linha em branco.
            </p>
        </div>
        <div style="margin-left:10px;">
            <table cellspacing=1px bgcolor="black">		
                <tr>			
                    <td class="linhaTabela">Sub-Grupo</td>                   		
                     	
                </tr>
                 <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>		
                    <td class="linhaTabela">------</td>
                </tr>	
            </table>
        </div>
EOT;
    break;

    case 'cad023ManualCSVMarca':
        echo<<<EOT
        
        <style>
            .tabela{
                border:1px solid black;
                border-colapse: colapse;	
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
                margin-left:10px;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabela{				
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }	
        </style>
        <div style="padding-left:10px;">
            <h2>Modelo de documento CSV</h2>
            <h3>Observações:</h3>
            <p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação
            <br/>-O documento deve conter <strong>1 coluna</strong> com o nome das marcas.
            <br/>-O nome da coluna deve estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
            <br/>-A extesão do documento deve ser (*.csv)
            <br/>-Ao final do documento não deve haver nenhuma linha em branco.
            </p>
        </div>
        <div style="margin-left:10px;">
            <table cellspacing=1px bgcolor="black">		
                <tr>			
                    <td class="linhaTabela">Marca</td>                   		
                     	
                </tr>
                 <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>		
                    <td class="linhaTabela">------</td>
                </tr>	
            </table>
        </div>
EOT;
    break;

    case 'cad023ManualCSVLocacao':
        echo<<<EOT
        
        <style>
            .tabela{
                border:1px solid black;
                border-colapse: colapse;	
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
                margin-left:10px;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabela{				
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;          
            }
            em {
                font-size:14px;
                color:#FF0000;
            }    	
        </style>
        <div style="padding-left:10px;">
            <h2>Modelo de documento CSV</h2>
            <h3>Observações:</h3>
            <p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação
            <br/>-O documento deve conter <strong>1 coluna</strong> com o nome das locações.
            <br/>-O nome da coluna deve estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
            <br/>-A extesão do documento deve ser (*.csv)
            <br/>-Ao final do documento não deve haver nenhuma linha em branco.
            <br/><strong><em>-O CSV de locações deve ser importado para cada empresa.</em></strong>
            </p>
        </div>
        <div style="margin-left:10px;">
            <table cellspacing=1px bgcolor="black">		
                <tr>			
                    <td class="linhaTabela">Locacao</td>                		
                </tr>
                 <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>			
                    <td class="linhaTabela">------</td>
                </tr>
                <tr>		
                    <td class="linhaTabela">------</td>
                </tr>	
            </table>
        </div>
EOT;
    break;





}    