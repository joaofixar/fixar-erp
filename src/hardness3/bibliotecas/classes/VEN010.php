<?php
namespace hardness;

definirClasse('VEN010');
class VEN010_
{
    /**
     * excluir - Excluir um Contrato de Locação
     *
     * @param int $T141_Id
     * return true/string(erro)
     */
    public function excluir($T141_Id)
    {
        $T141_Id = mysql_real_escape_string($T141_Id);
        $sql     = "delete from T141 where T141_Id='$T141_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * gerarContasReceber - Gerar contas a receber de um contrato
     *
     * @param int $T141_Id
     * return true/string(erro)
     */
    public function gerarContasReceber($T141_Id)
    {
        
        $sql = mysql_query("select * from T141 where T141_Id='$T141_Id'");
        if (mysql_num_rows($sql) < 1)
            return 'contrato não localizado';
        
        $contrato = mysql_fetch_array($sql);
        
        // verifica se já foram geradas as contas desse contrato
        $sqlAux = mysql_query("select * from T002 where T002_T141_Id='$T141_Id'");
        if (mysql_num_rows($sqlAux) != 0)
            return 'Já foram geradas as contas a receber deste contrato';
        
        $dataInicio = $contrato['T141_Data_Inicio'];
        $dataFim    = $contrato['T141_Data_Validade'];
        
        $diaInicio = $contrato['T141_Dia_Vencimento'];
        $diaHoje   = date("d");
        
        $datasRecebimentos = array();
        // se o dia de inicio for maior que o de hoje, começa neste mês, senão só no próximo
        if ($diaInicio >= $diaHoje) {
            $aux = explode("-", $dataInicio);
            array_push($datasRecebimentos, date("Y-m-d", mktime(0, 0, 0, intval($aux[1]), intval($diaInicio), intval($aux[0]))));
        } else {
            
            $aux = explode("-", $dataInicio);
            array_push($datasRecebimentos, date("Y-m-d", mktime(0, 0, 0, intval($aux[1]) + 1, intval($diaInicio), intval($aux[0]))));
        }
        
        $continua = true;
        $timeFim  = strtotime($dataFim);
        // enquanto a ultima data inserida for menor do que a data final, insere mais uma
        while ($continua) {
            $qtdRec     = sizeof($datasRecebimentos);
            $ultimaData = strtotime($datasRecebimentos[$qtdRec - 1]);
            
            if ($ultimaData <= $timeFim) {
                $aux = explode("-", $datasRecebimentos[$qtdRec - 1]);
                array_push($datasRecebimentos, date("Y-m-d", mktime(0, 0, 0, intval($aux[1]) + 1, intval($diaInicio), intval($aux[0]))));
            } else {
                array_pop($datasRecebimentos);
                $continua = false;
            }
        }
        
        if (sizeof($datasRecebimentos) < 1)
            return 'Não há contas a gerar, verifique as datas';
        
        $count = 1;
        
        foreach ($datasRecebimentos as $dataVencimento) {
            $Numero_Duplicata = $contrato['T141_Id'] . chr($count + 64);
            mysql_query("insert into T002 
	                            (T002_D024_Id,
 								 T002_D021_Id,
								 T002_C004_Id,
								 T002_D027_Id,
								 T002_D014_Id,
								 T002_T141_Id,
								 T002_Numero_Duplicata,
								 T002_Numero_Documento,
								 T002_Data_Emissao,
								 T002_Data_Vencimento,
								 T002_Data_Recebimento,
								 T002_Valor_Duplicata,
								 T002_Valor_Juros,
								 T002_Valor_Desconto,
								 T002_Valor_Total,
								 T002_Data_Lancamento,
								 T002_Hora_Lancamento,
								 T002_Numero_Parcela,
								 T002_Observacao_2)
						   values				 
						       ('$contrato[T141_D024_Id]',
								'',
								'$contrato[T141_C004_Id]',
								'',
								'',
								'$contrato[T141_Id]',
								'C$Numero_Duplicata',
								'C$contrato[T141_Id]',
								curdate(),
								'$dataVencimento',
								'0000-00-00',
								'$contrato[T141_Valor_Mensal]',
								'0',
								'0',
								'$contrato[T141_Valor_Mensal]',
								curdate(),
								'" . strftime('%T') . "',
								'$count',
								'*** CONTRATOS DE LOCACAO ***')");
            
            $cErro = mysql_error();
            if (!empty($cErro))
                return "<div class='msgError'>Erro no lançamento de contas a Receber!<br />Erro: $cErro<br />Clausula: $cClausula</div>";
            
            $count++;
            
        }							
        return true;
    }
}
