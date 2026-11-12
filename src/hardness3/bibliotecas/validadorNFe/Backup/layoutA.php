<?php
namespace hardness;

require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";

definirClasse('layoutA');

class layoutA
{
	/*
        A. Dados da Nota Fiscal eletrônica
        #   ID    Campo   Descrição                                       Tipo    Ocor.       Tam.            Observação
        2   A02   versao  Versão do leiaute                                 C      1-1        1-4            Versão do leiaute (v2.0)
        3   A03    Id     Identificador da TAG a ser assinada               C      1-1         47            Informar a Chave de Acesso precedida do literal ‘NFe’,
        4   A04  pk_nItem Regra para que a numeração do item de detalhe da NF-e seja única.   1-1            Regra de validação do item de detalhe da NF-e, campo de controle do Schema XML, o contribuinte não deve se preocupar com o preenchimento deste campo.
    */

        // 2 - Versão Leiaute
        // -------------------------- É setado manualmente ------------------------//

        // 3 - Id - Identificador da TAG a ser assinada 
        // ----------------- Gerado pelo sistema -------------------//

        // 4 - pk_nItem
        // ----------------- Não consta na implementação do nf3.class ----------------//

}