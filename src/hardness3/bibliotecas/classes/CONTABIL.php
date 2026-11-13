<?php

namespace hardness {
    set_time_limit(0);
    definirClasse('CONTABIL');

    class CONTABIL_
    {        
        private string $campoTabelaREGEX =  '/\w\d{3}\w{1}?\w+/';

        /**
         * processaLancamentos
         *         
         * @param string $mes
         * @param string $ano
         * @return String/bool
         */
        public function processaLancamentos($mes, $ano)
        {            
            global $g;
            // busca as escriturações
            $D194 = mysql_query('select 
                                            D194_Id,
                                            D194_Descricao,
                                            D194_Tabela,
                                            D194_Campo_Data,
                                            D194_Extra 
                                        from 
                                        D194');            
            
            while ($mD194 = mysql_fetch_array($D194)) {
                //busca as correlações
                $D195 = mysql_query("select 
                                            D195_Id, 
                                            D195_Descricao,
                                            D195_Campo, 
                                            D195_Conta_Debito, 
                                            D195_Conta_Credito, 
                                            D195_Descricao_Contabil 
                                        from D195
                                        where D195_D194_Id = '{$mD194['D194_Id']}'");
                $correlacoes = [];
                while ($mD195 = mysql_fetch_array($D195)) {
                    $correlacoes[] = $mD195;
                }

                // busca os campos que serão utilizados na query
                $campos = array_column($correlacoes, 'D195_Campo');
                $campos[] = $mD194['D194_Campo_Data'];                 
                foreach(array_column($correlacoes, 'D195_Descricao_Contabil') as $descricao) {
                    if(preg_match_all ($this->campoTabelaREGEX, $descricao, $matchs)) {
                        $campos = array_merge($campos,$matchs[0]);
                    }
                }       

                $campos = array_unique($campos);

                $query = $this->montarQuery(
                    $campos,
                    $mD194['D194_Tabela'],
                    $mD194['D194_Extra'],
                    "(month({$mD194['D194_Campo_Data']}) = '$mes' and year({$mD194['D194_Campo_Data']}) = '$ano') 
                    and {$mD194['D194_Tabela']}_C004_Id = {$g['empresaAtual']}"
                );                
                                                                           
                $tabela = mysql_query($query);

                while ($mTabela = mysql_fetch_array($tabela)) {
                     $this->processaCorrelacoes($mTabela, $correlacoes, $mD194);                                       
                }                                                                
            }

            $this->atualizarTotalContaAnalitica($mes,$ano);
            $this->atualizarTotalContaSintetica($mes,$ano);
            $this->atualizarSaldo($mes,$ano);
            return true;
        }

        public function montarQuery($campos, $tabela, $extra, $filtro)
        {
            $query = "select :tabela_Id, :campos from :tabela :condicao";

            $campos = array_reduce(
                $campos,
                function ($resultado, $campo) {
                    $resultado .= $campo . ',';
                    return $resultado;
                }
            );

            $campos = substr($campos, 0, -1);

            $query = str_replace(
                array(':campos', ':tabela', ':condicao'),
                array($campos, $tabela, $extra),
                $query
            );

            if (stripos($query, 'where') === false) {
                return "$query where $filtro";
            }

            return  "$query and $filtro";
        }

        public function processaCorrelacoes($linha, $correlacoes, $escrituracao)
        {       
            global $g;
            
            foreach ($correlacoes as $correlacao) {
                $valor = (float)$linha[$correlacao['D195_Campo']];

                //faz lancamento apenas se tiver valor
                if($valor > 0) {                    

                    mysql_query("SET AUTOCOMMIT=0");
                    mysql_query("START TRANSACTION");
                                      
                    // preenche os campos da descrição com os valores da consulta
                    $descricaoContabil =  preg_replace_callback($this->campoTabelaREGEX,function($matches) use ($linha){
                        return $linha[$matches[0]];
                    }, $correlacao['D195_Descricao_Contabil']);
                    

                    $this->registrarLancamento(
                        array(
                            'idContaDebito' => $correlacao['D195_Conta_Debito'],
                            'idContaCredito' => $correlacao['D195_Conta_Credito'],
                            'data' => $linha[$escrituracao['D194_Campo_Data']],
                            'valor' => $valor,
                            'tabela' => $escrituracao['D194_Tabela'],
                            'tabelaId' => $linha["{$escrituracao['D194_Tabela']}_Id"],
                            'descricao' => $descricaoContabil,
                            'entidade' => $correlacao['D195_Id'],
                            'empresaAtual' => $g['empresaAtual']
                        )
                    );                       

                    mysql_query("COMMIT");           
                    mysql_query("SET AUTOCOMMIT=1");                     
                }
            }
        }

        public function atualizarTotalContaAnalitica($mes,$ano) {
            global $g;

            $D193 = mysql_query("select D193_Id, T301_Id from D193 left join T301 on T301_D193_Id = D193_Id and T301_Mes = '$mes' and T301_Ano = '$ano' and T301_C004_Id = '{$g['empresaAtual']}'
            where D193_Flag_Conta_Movimento = 'S'");
        
            while ($mD193 = mysql_fetch_array($D193)) {
                if ($mD193['T301_Id']) {
                    mysql_query("
                        update T301 
                        set T301_Total=(select sum(T302_Valor) from T302 where T302_D193_Id='{$mD193['D193_Id']}' and T302_C004_Id = '{$g['empresaAtual']}' and T302_Data like '$ano-$mes%') 
                        where T301_Id='{$mD193['T301_Id']}'");
                } else {
                    mysql_query("
                        insert into T301 (T301_Mes, T301_Ano, T301_Total,T301_D193_Id, T301_C004_Id)
                        values (                             
                            '{$mes}',
                            '{$ano}',
                            (select sum(T302_Valor) from T302 where T302_D193_Id='{$mD193['D193_Id']}' and T302_C004_Id = '{$g['empresaAtual']}' and T302_Data like '{$ano}-{$mes}%'),
                            '{$mD193['D193_Id']}',
                            '{$g['empresaAtual']}'
                        )");
                }
            }
        }

        public function atualizarTotalContaSintetica($mes,$ano) {
            global $g;

            // Busca todas as contas ordenadas pelo codido de forma decrescente, para vir totalizando as contas do menor nível para maior nível
            $D193 = mysql_query("select D193_Codigo, D193_Id, T301_Id, D193_Flag_Conta_Movimento, T301_Total
                                   from D193 
                              left join T301 on T301_D193_Id=D193_Id and T301_Mes='{$mes}' and T301_Ano='$ano'  and T301_C004_Id = '{$g['empresaAtual']}'
                               order by D193_Codigo desc");

            $totalAnalitico = 0;
            $totalSintetico = 0;
            $totalSinteticoGeral = 0;
            $contaPrefixo   = '';
            $tipoContaAnterior = '';
        
            while ($mD193 = mysql_fetch_assoc($D193)) {

                // Sempre zera o totalSinteticoGeral quando mudar de prefixo, exemplo 1 para 2, etc...
                // Prefixo é o primeiro digito do código da conta
                if ($contaPrefixo != substr($mD193['D193_Codigo'],0,1)){
                    // Quando muda o prefixo da conta, o prefixo passa a ter o valor do prefixo atual
                    $contaPrefixo = substr($mD193['D193_Codigo'],0,1);
                    // Zera o total sintegico geral sempre que muda para novo prefixo
                    $totalSinteticoGeral = 0;
                }

                if ($mD193['D193_Flag_Conta_Movimento'] == 'S') {
                    // Caso seja conta analitica apenas somar no total analitico e marcar como a conta anterior sendo uma conta analitica
                    $totalAnalitico += $mD193['T301_Total'];
                    // Guarda a conta anterior com o tipo da conta atual
                    $tipoContaAnterior = $mD193['D193_Flag_Conta_Movimento'];
                    continue;
                } else {
                    // totalSinteticoGeral guarda a soma de todas contas analiticas do prefixo
                    $totalSinteticoGeral += $totalAnalitico;

                    if ($tipoContaAnterior == $mD193['D193_Flag_Conta_Movimento']) {
                        // totalSintetico é a variável com o total que será gravado na conta sintética
                        // Quando o tipo da conta anterior for igual a conta atual, ou seja, anterior era uma conta sintetica e a atual também,
                        // significa que é uma conta de maior nível 
                        $totalSintetico = $totalSinteticoGeral;
                    } else {
                        // Quando o tipo da conta anterior for diferente a conta atual, ou seja, anterior era uma conta analitica e a atual sintetica,
                        // significa que é uma conta de sintetica de primeiro nível
                        $totalSintetico = $totalAnalitico;
                    }
                    // Guarda a conta anterior com o tipo da conta atual
                    $tipoContaAnterior = $mD193['D193_Flag_Conta_Movimento'];
                    // Zera o total analitico sempre que muda para uma conta sintetica
                    $totalAnalitico = 0;
                }


                if ($mD193['T301_Id']>0) {
                                                         
                    if($mD193['T301_Total'] != $totalSintetico) {                                 
                        // Altera o total exista para a conta, mês/ano e empresa.
                        mysql_query("update T301 
                                        set T301_Total='{$totalSintetico}'
                                    where T301_Id='{$mD193['T301_Id']}'");
                    }
                } else {
                    // Insere novo valor total para a conta, mêe/ano e empresa caso não exista
                    mysql_query("insert into T301 (T301_D193_Id, T301_Total, T301_Mes, T301_Ano, T301_C004_Id) 
                                 values (
                                     '{$mD193['D193_Id']}', 
                                     '{$totalSintetico}',
                                     '{$mes}',
                                     '{$ano}',
                                     '{$g['empresaAtual']}'                     
                                 )");
                }
            }
        }

        public function atualizarSaldo($mes, $ano) {
            global $g;

            $D193 = mysql_query("select D193_Id, T301_Id, T301_Total, T301_Saldo
                                   from D193 
                              left join T301 on T301_D193_Id=D193_Id and T301_Mes='{$mes}' and T301_Ano='$ano'  and T301_C004_Id = '{$g['empresaAtual']}'");
            
            
            while($mD193 = mysql_fetch_assoc($D193)) {
                $SALDO = mysql_query("select SUM(T301_Total) as Total from T301 where T301_D193_Id = '{$mD193['D193_Id']}' and concat(T301_Ano, T301_Mes) < '{$ano}{$mes}' and T301_C004_Id = '{$g['empresaAtual']}'");                                                
                $mSALDO = mysql_fetch_assoc($SALDO);

                $saldoAnterior = (float)$mSALDO['Total'];
                $saldoAtual =  $saldoAnterior + (float)$mD193['T301_Total'];
                                
                if(empty($mD193['T301_Saldo']) || ((float)$mD193['T301_Saldo'] !== $saldoAtual))   {                    
                    mysql_query("update T301
                                    set T301_Saldo = '{$saldoAtual}'
                                where T301_Id ='{$mD193['T301_Id']}'");                                
                }                    
            }
        }

        public function registrarLancamento($array)
        {                        
            mysql_query("insert into T302 (
                    T302_D193_Id,                    
                    T302_Data,
                    T302_Valor,
                    T302_Descricao,
                    T302_Credito_Debito,
                    T302_Tabela,
                    T302_Tabela_Id,
                    T302_Entidade,
                    T302_C004_Id) 
                    values (
                        '{$array['idContaCredito']}',                        
                        '{$array['data']}',
                        '{$array['valor']}',
                        '{$array['descricao']}',
                        'C',
                        '{$array['tabela']}',
                        '{$array['tabelaId']}',
                        '{$array['entidade']}',
                        '{$array['empresaAtual']}'
                    )");
       
            mysql_query("insert into T302 (
                T302_D193_Id,                    
                T302_Data,
                T302_Valor,
                T302_Descricao,
                T302_Credito_Debito,
                T302_Tabela,
                T302_Tabela_Id,
                T302_Entidade,
                T302_C004_Id) 
                values (
                    '{$array['idContaDebito']}',                        
                    '{$array['data']}',
                    '{$array['valor']}',
                    '{$array['descricao']}',
                    'D',
                    '{$array['tabela']}',
                    '{$array['tabelaId']}',
                    '{$array['entidade']}',
                    '{$array['empresaAtual']}'
                )");  
                    

            return true;
        }
    }
}
