<?php
namespace hardness;
/**
* Ações
*/

	switch ($g['r'][3]) {

		case 'help013RelatorioInventario':

            //Explicando a funcionalidade do relatório

            echo "Após utilizar os filtros para realizar seu inventário, será realizada a multiplicação do saldo com o custo, assim mostrando o resultado na coluna Total.
            
            Para o cálculo do peso total, é necessário haver o peso cadastrado do produto para que a multiplicação seja efetuada.

            A soma total dos valores e de peso (se tiver) será mostrada na parte inferior da página.";

        break;
    }
?>