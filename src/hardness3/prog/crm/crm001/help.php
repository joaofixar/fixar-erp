<?php
namespace hardness;
/**
* Ações
*/

	switch ($g['r'][3]) {

		case 'help013RelatorioInventario':

            //Explicando a funcionalidade do relatório

            echo "A tabela é construída da seguinte forma:
                Clientes atentidos: Mostra quantas atividades o usuário realizou via telefone no dia, sem a adição de orçamentos, pedidos, notas fiscais, devoluções e compras.

                Orçamentos:A coluna Total R$ é realizada pela data e pelo CFOP de VENDA filtrados. O número de orçamentos é realizado a soma do dia todo, assim somando 
                o número de clientes e itens em cada orçamento.

                Pedidos: A coluna Total R$ é realizada pela data, pelo CFOP de VENDA e por pedidos agrupados se existir. O número de pedidos é a soma do dia todo, assim somando o número de 
                clientes e itens em cada pedido.

                Notas Fiscais: É formada pelos filtros de data e CFOP de Venda, calculando o IPV também. O número de notas, clientes e itens são realizados a partir de uma soma como nas outras colunas.

                Devolução: É formada pelas notas de entrada que contém o CFOP de devolução, totalizando a somatória de clientes e valor total. 

                Compras: São coletas informações de notas fiscais recebidas de compras (Estoque -> S.R.M -> Notas Fiscais Recebidas) 
                de acordo com a data de emissão.";

        break;
    }
?>