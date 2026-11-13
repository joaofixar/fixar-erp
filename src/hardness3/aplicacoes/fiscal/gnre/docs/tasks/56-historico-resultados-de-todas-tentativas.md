# Task 56 — Histórico: resultados persistidos de todas as tentativas

## Correção

O Histórico passa a projetar a coleção completa de `GNRE005` já carregada,
usando `GNRE005_GNRE004_Id` como chave de cada cartão `GNRE004`. Para cada
fila em `FINALIZADA`, o SOAP persistido é interpretado localmente e seu
resultado individual/motivos é exibido somente na tentativa proprietária.
Recibos iguais não são usados como chave, portanto não misturam tentativas.
SOAP ausente, inválido ou sem resultado individual não produz motivo.

Complemento: `situacaoGuia=0` também é projetada no cartão proprietário como
**Resultado individual: Processada com sucesso** e **Guia autorizada/emitida
pelo Portal**. O código de lote (inclusive `402`) não é usado como autorização;
somente o resultado individual persistido produz essa informação pública.

Na aba **Processar**, a mesma confirmação individual `0` substitui os controles
redundantes por “Guia autorizada com sucesso pelo Portal.” Quando já existir
PDF oficial persistido, informa que ele está disponível para download; a ação
do cabeçalho permanece PDF (baixar ou obter), sem oferecer nova transmissão.

Não há consulta ao Portal, nem alteração de GNRE004/GNRE005, reabertura,
emissão, XML, fila, PDF, schema, migration ou dados reais.

## Cobertura

`tests/task56-history-all-attempt-results-synthetic.php` cobre três tentativas
finalizadas (duas com o mesmo recibo), confirma motivo `226` em cada cartão
correto e confirma que uma fila sem SOAP válido não recebe motivo inventado.
