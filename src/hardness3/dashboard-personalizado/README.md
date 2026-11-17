# Dashboard personalizável

O dashboard é habilitado por empresa em **Configuração > Personalização >
DASHBOARD PERSONALIZAVEL**. A configuração original não habilita o menu; a aba
**DASHBOARD** aparece somente quando a empresa possui um JSON personalizado com
`habilitado: true` e ao menos um indicador válido e ativo.

Indicadores que usam os componentes disponíveis são definidos no JSON da empresa.
Um novo tipo de componente também exige instalar seu executor e sua interface
no sistema antes de habilitá-lo na personalização.

Em bancos com mais de uma empresa, a opção **GERAL** é a configuração-base do
grupo. As abas das empresas permitem criar uma configuração específica, que
tem prioridade sobre a geral apenas naquela empresa.

## Banco espelhado

Quando o `confUsuario.php` do cliente define `$confUsuarioEspelhado`, o dashboard
usa essa conexão nas consultas dos indicadores, no histórico comercial, nas
exportações CSV/PDF e na leitura dos nomes dos arquivos de logotipo. Clientes
sem espelho continuam usando `$confUsuario`.

O espelho precisa informar `dbHost`, `dbUser`, `dbPass` e `dbDatabase`, com a
mesma `dbDatabase` da conexão principal. Configurações incompletas ou de outra
base são ignoradas. Se a conexão com o espelho falhar, o dashboard tenta a
principal. Cada tentativa de conexão tem timeout de 5 segundos; uma consulta
que falha depois de conectar não é repetida no principal. Os indicadores e as
exportações mantêm a transação somente de leitura, com limite de 15 segundos
por SQL, tanto no principal quanto no espelho. Esse limite é independente dos
5 segundos para abrir a conexão e não exige alteração no JSON personalizado.
Os dados exibidos estão sujeitos ao atraso de replicação.

O gerenciamento de acessos, as validações para abrir telas/emitir tickets do ERP
e as atualizações de títulos e vendedores continuam na conexão principal.
A configuração principal também permanece como referência para assinaturas de
sessão, arquivos e identificação do cliente nos caches.

Na instalação, inclua `connection.php` junto com `data.php`, `export.php`,
`company-logo.php` e `channel-logo.php`. Não há migração nem nova configuração
por indicador. Validação local, com conexões simuladas e sem base de cliente:
`php dashboard-personalizado/tests/conexao-espelhada.php`.

## Acessos ao dashboard

O usuário ROOT (id 63) sempre possui acesso a todos os dashboards e é quem
libera o primeiro perfil responsável. No cabeçalho do dashboard, o ROOT e os
perfis responsáveis veem o botão **Gerenciar acessos**. A janela permite
liberar todos ou alguns indicadores para cada perfil.

Além dos perfis, a aba **Usuários** permite liberar um indicador diretamente
para uma pessoa. Indicadores com filtro de `vendedor` ou `representante`
permitem selecionar o vendedor daquele usuário ou **Todos os vendedores e
representantes (gestor)**. A visão de gestor libera somente os indicadores
selecionados, sem conceder administração de acessos. Ao escolher um vendedor, o servidor força
o vendedor, usa o período padrão, bloqueia os filtros de período e vendedor e,
quando houver abas, libera apenas a aba de vendedores. CSV e PDF recebem a
mesma restrição.

Um usuário vê um indicador quando ele foi liberado para seu perfil. Perfis
responsáveis visualizam todos os indicadores da empresa e podem administrar os
demais perfis, mas apenas o ROOT pode incluir ou remover outro perfil responsável.

As permissões são salvas separadamente em
`personalizacaoConf/{empresa}DASHBOARD PERSONALIZAVEL-acessos.json`; portanto
não alteram o JSON de personalização nem os SQLs dos indicadores.

## Análise SQL exclusiva do ROOT

O cabeçalho mostra **Análise SQL** ao lado de **Gerenciar acessos** somente
quando a autorização assinada identifica ROOT (a mesma regra do ERP: id 63
ou flag root da sessão). Gerenciar acessos, ser diretor ou informar parâmetros
na URL não habilita o diagnóstico. O servidor só coleta e devolve os detalhes
para ROOT; não depende de esconder o botão.

Ao abrir indicadores, detalhes ou histórico comercial, a coleta mede os SELECTs
que o executor já realiza, incluindo etapas do carregamento progressivo. Guarda
SQL com parâmetros nomeados, valores utilizados, duração incluindo leitura,
linhas retornadas, origem no motor, código/mensagem do MySQL quando há falha,
indicador/aba, filtros, trecho do JSON e seu hash. O relatório também informa
tempo total do servidor, tempo fora do SQL, tempo percebido pelo navegador,
memória, versões de PHP/MySQL e conexão principal/espelho com número de tentativas,
sem identificar o servidor ou credenciais. A versão do MySQL vem da conexão
existente, sem SELECT adicional. Cada SQL separa preparação, execução e leitura,
e identifica a fase em que houve falha.
O total do servidor termina antes da serialização/envio da resposta; o tempo
de cada requisição no navegador inclui transporte e leitura do JSON. O bloco
**ABERTURA** acompanha também o tempo total até a montagem síncrona do DOM,
o custo dessa montagem e a soma de todas as etapas progressivas, mesmo quando
o histórico das requisições antigas já foi descartado. Não mede o tempo exato
de pintura da GPU, nem atribui automaticamente a diferença entre os tempos à rede.
A abertura aparece como **CARREGANDO** antes da resposta e pode ser copiada
nesse estado. O tempo pendente é atualizado ao abrir/copiar o painel ou ao
receber uma etapa; não há timer extra. No Painel de Cotações, inclui a carga
assíncrona da interface e das fontes, além das atualizações internas.
Falhas de configuração, SQL e apresentação ficam disponíveis mesmo quando o
indicador não abre. Erros JavaScript posteriores à abertura e rejeições de
promessas na tela também são associados ao indicador, com mensagem e pilha
limitada a nomes de arquivos/linhas. Cancelar uma carga é separado de falhar.
Respostas inválidas registram status HTTP e tipo de conteúdo sem guardar o
corpo HTML. Avisos PHP do motor são preservados no JSON ROOT sem inserir HTML
na resposta. Avisos parciais, como meta duplicada ou erro de leitura das metas,
ficam visíveis mesmo com HTTP 200. Somente contagens dos componentes são
capturadas; resultados zerados ou sem registros não comprovam erro de negócio.
Interrupções do PHP podem gerar diagnóstico se o processo
ainda conseguir responder; uma queda do servidor ou da rede pode impedir o
retorno do SQL, deixando somente o erro observado pelo navegador.

No painel, expanda uma requisição para ver consultas por duração, filtre
**Com erro**, **Com aviso** ou **Lentos (≥ 1 segundo)** e use **Copiar relatório**. O conteúdo
copiado é JSON de diagnóstico para revisar a personalização; não é um JSON
para importar no ERP. Se o navegador bloquear a cópia automática, o texto fica
selecionado para Ctrl+C. **Limpar histórico** remove os registros desta aba.

Não executa EXPLAIN, não repete consultas, não cria tabelas e não grava novo
log em disco. Não há polling nem coleta para outros usuários. A tela é montada
apenas ao abrir o painel. O histórico fica em memória na aba do navegador,
limitado a 20 aberturas, 20 requisições e 1 MB combinado, e desaparece ao recarregar. Cada requisição
limita os SQLs a 40 registros/96 KB, preserva a contagem e duração totais e
reserva o último erro e o SQL mais lento, inclusive quando ocorrem depois do
limite. Cada abertura preserva seu SQL mais lento mesmo após descartar etapas
antigas do histórico. SQLs acima de 12 KB e configuração acima de 32 KB
aparecem com marcação de truncamento. Linhas retornadas não indicam linhas
examinadas pelo MySQL. Os limites de consulta existentes continuam em vigor.

Para instalar, atualize juntos `index.html`, `dashboard.js`,
`carregamento-progressivo.js`, `carregamento-geral.php`, `painel-cotacoes.js`, `data.php`, `connection.php`,
`custom-indicator-engine.php` e os novos `analise-sql.php`, `analise-sql.js`,
`analise-sql.css`. Sem migração ou alteração no JSON do cliente. O HTML já
invalida o cache dos scripts alterados. Reabra o dashboard depois da atualização.
O identificador de diagnóstico não participa da assinatura dos lotes, mantendo
compatibilidade com detalhes e exportação dos resultados já carregados.
Para reverter, restaure os oito arquivos existentes do backup da instalação.

Validação local sem base de cliente:
`php dashboard-personalizado/tests/analise-sql.php` e
`COTACOES_CHROME=/caminho/chrome-headless-shell python3 dashboard-personalizado/tests/analise-sql-browser.py`.

## Endividamento e barras mensais selecionáveis

A personalização de Endividamento conserva o ID `inadimplencia`, usado nas
permissões existentes, e altera o título exibido. A tela contém quatro cartões
(Pago, Em aberto, Vencido e A vencer), um gráfico mensal e a tabela de títulos.
O período considera o **vencimento das parcelas**, inclusive quando o contrato
foi emitido antes dele. O padrão é o ano atual inteiro, com `inicio_ano` e
`fim_ano`, e Todas as empresas do grupo já configurado no financeiro.

Pago soma os pagamentos registrados nas parcelas desse período, incluindo
pagamentos parciais. Em aberto soma o saldo remanescente. Vencido e A vencer
distribuem esse saldo pela situação atual do vencimento. O valor de cada barra
é `valorPago + valorAberto`, agrupado pelo mês do vencimento e pela conta
financeira cadastrada, sem presumir a classificação dos contratos por texto.
As regras existentes de seleção de ENDIVIDAMENTO/risco sacado, cancelamento,
agrupamento e filtros adicionais permanecem na personalização do cliente.

O componente genérico usa `grafico.tipo: barras_empilhadas`, `campoData`,
`campoGrupo` e `campoValor`. Cores por categoria podem ser definidas em `cores`.
O clique em um segmento abre a listagem de títulos desse mês/categoria; o
clique no nome do mês reúne suas categorias. A tabela recebe o foco e identifica
a seleção no título. Não há legenda inferior: passar o cursor ou focar uma
faixa mostra nome, valor, participação e quantidade de títulos. O botão
**Categorias** de cada mês abre uma relação pesquisável dos valores, incluindo
as faixas pequenas, com acesso aos mesmos títulos. As alturas das faixas
preservam a proporção real, inclusive abaixo de um pixel. **Mostrar todos os
títulos** limpa a seleção. Cartões e gráfico mantêm os totais do período.
Só a listagem é consultada novamente,
com paginação e rodapé calculados sobre todos os títulos da seleção. CSV/PDF
recebem a mesma seleção e identificam o mês/categoria no rodapé.

O servidor valida campos, mês e intervalo, vincula os valores da seleção aos
parâmetros SQL e aplica o mesmo predicado à contagem, aos totais e às páginas.
O gráfico aceita até 120 meses, 60 categorias e 5.000 grupos mensais; exceder
esses limites produz erro, sem mostrar somas parciais. Aplicar novos filtros
ou trocar de indicador descarta a seleção e as respostas anteriores.

Para instalar, atualize `custom-indicator-engine.php`, `listagem-personalizada.php`,
`dashboard.js` e `index.html`, adicionando `barras-empilhadas.php`,
`barras-empilhadas.js` e `barras-empilhadas.css`. Importe o JSON atualizado
do cliente para habilitar o gráfico e alterar o título e o período.

Validação local, com dados fictícios e sem base de cliente:

```sh
php dashboard-personalizado/tests/endividamento.php
python3 dashboard-personalizado/tests/endividamento-sql.py
COTACOES_CHROME=/caminho/chrome-headless-shell python3 dashboard-personalizado/tests/endividamento-browser.py
```

A fixture `endividamento-mensal-planilha.json` contém somente os 48 agregados
do gráfico da planilha de referência. Os títulos da prévia visual são
simulados; não representam registros consultados no ERP.

## Controle de carteira bancos

`controleCarteiraBancos.habilitado: true` ativa a listagem de bancos com garantia
editável, o gráfico global centralizado dos próximos sete dias e, abaixo, um
gráfico de linhas com os bancos marcados. A definição está em
`tests/fixtures/controle-carteira-bancos.json`. Os nomes, cores, garantias iniciais
e vínculos de portadores pertencem ao JSON do cliente. As duas contas do Itaú
compartilham a carteira; Ouribank usa o portador BOLETO ORINVEST da planilha.

`sqlBase` fornece `id`, `portador`, `boleto`, `data` e `valor`. A configuração
entregue lê o saldo remanescente dos títulos em aberto das empresas selecionadas,
excluindo cancelados, agrupados, encontro de contas, saldos sem valor e
vencimentos anteriores à data inicial. A projeção usa a carteira atualmente
aberta; não reconstitui saldos históricos nem prevê novas emissões. Toda a
carteira além do horizonte permanece no saldo inicial. O executor faz um único
SELECT agregado, com limite de 20.000 grupos; rejeita resultados truncados.
O filtro **Empresas** abre em **Todas as empresas**, consolidando o grupo
configurado (1, 2, 4 e 5 na definição entregue). Também permite **Empresa atual**
ou uma empresa específica, pelo nome cadastrado. O executor carrega os nomes
em C004, valida a seleção e aplica `:abrangenciaEmpresa` à consulta-base.
Essa leitura de cadastro é separada do SELECT agregado dos títulos. A seleção
atualiza carteira, datas de alerta e gráficos; a garantia é descontada uma
única vez por banco no consolidado. JSONs anteriores sem esse filtro continuam
usando a empresa atual. A abrangência aparece no cabeçalho e na impressão.

Saldo livre é carteira inicial menos garantia menos vencimentos acumulados.
A primeira data com saldo menor ou igual a zero indica quando reforçar a
emissão. Bancos já sem cobertura são sinalizados desde a abertura. A projeção
aceita entre 7 e 366 dias, incluindo a data inicial. O cartão e o gráfico de
sete dias usam as primeiras sete datas: na planilha recebida, B21 aponta para
D+7 e inclui oito datas. A composição mostra todos os portadores consultados;
o resumo original da planilha omite DEVOLUCAO FORNECEDOR, ISENTO e
LEASING / FINAME / FINANCIAMENTOS. Os cartões dos bancos somam somente os
portadores vinculados aos bancos configurados.

Editar a garantia recalcula a tela sem consultar novamente os títulos.
**Salvar garantias** grava valores compartilhados pelos usuários autorizados
da empresa em `personalizacaoConf/{empresa}DASHBOARD PERSONALIZAVEL-garantias-{indicador}.json`.
As garantias não alteram o cadastro bancário ou os títulos do ERP. A gravação
usa bloqueio e revisão para impedir que um usuário sobrescreva alterações de
outro. Arquivo inválido gera erro de leitura, preservando o conteúdo existente.
Empresa e indicador vêm da autorização e da personalização. Os acessos seguem
o gerenciamento existente; o indicador novo precisa ser liberado aos perfis.

CSV exporta a abertura e todos os saldos diários dos bancos. PDF abre a janela
de impressão com os valores em edição e os gráficos da tela, identificando
simulações ainda não salvas. A seleção controla o gráfico comparativo; não
altera os totais nem os bancos do CSV.

Para instalar, atualize juntos `custom-indicator-engine.php`, `data.php`,
`export.php`, `dashboard.js` e `index.html`, adicionando
`controle-carteira-bancos.php`, `controle-carteira-bancos-model.js`,
`controle-carteira-bancos.js` e `controle-carteira-bancos.css`. Em seguida,
importe o JSON completo da entrega. Não há migração de banco. Para reverter,
restaure os arquivos e o JSON anteriores à instalação.

Validação local, sem conexão à base do cliente:

```sh
php dashboard-personalizado/tests/controle-carteira-bancos.php
php dashboard-personalizado/tests/controle-carteira-bancos.php --payload > /tmp/controle-carteira-bancos-resposta.json
node dashboard-personalizado/tests/controle-carteira-bancos.js /tmp/controle-carteira-bancos-resposta.json
python3 dashboard-personalizado/tests/controle-carteira-bancos-sql.py
COTACOES_CHROME=/caminho/chrome-headless-shell python3 dashboard-personalizado/tests/controle-carteira-bancos-browser.py
```

A fixture da planilha guarda apenas portadores, datas e valores agregados,
sem clientes ou documentos. PHP e JavaScript conferem 270 saldos diários
contra as células da planilha. O teste SQL usa títulos fictícios em SQLite;
o teste visual usa a interface real com API simulada.

## Componentes de desempenho comercial e cotações

Os componentes são ativados pelo JSON e podem coexistir com IDs independentes.
Não há ativação ou liberação automática. A personalização e os acessos de cada
cliente permanecem fora dos arquivos do motor. Configurações de exemplo usadas
pelos testes estão em `tests/fixtures/desempenho-comercial.json` e
`tests/fixtures/painel-cotacoes.json`.

Para instalar o suporte, atualize `custom-indicator-engine.php`, `data.php`,
`access.php`, `export.php`, `dashboard.js`, `dashboard.css` e `index.html`, junto
com `desempenho-comercial.php`, `desempenho-comercial.js`, `painel-cotacoes.php`,
`painel-cotacoes.js`, `painel-cotacoes-model.js`, `painel-cotacoes.html` e
`painel-cotacoes.css`. Depois importe a personalização pela configuração do ERP.
Não há migração de banco de dados.

### Desempenho comercial

Habilite `desempenhoComercial.habilitado` na definição de cada aba. `sqlBase`
fornece as notas; `desempenhoComercial.orcamentos` e `.pedidos` fornecem as
outras etapas. Cada consulta retorna `id`, `data`, `clienteId`, `cliente`,
`responsavelId`, `responsavel`, `valor` e `convertido`. As fontes passam pelo
preparador do dashboard e são agregadas separadamente por responsável,
cliente e mês, sem multiplicar valores por vínculos entre documentos.

A interface contém oito cartões e uma tabela por cliente, disponível como
resumo do período com as três etapas ou evolução mensal apenas de orçamentos
e pedidos, incluindo seus totais. As notas continuam nos cartões, no resumo
do período e nas exportações. O valor orçado soma
somente orçamentos válidos de todo o período filtrado. A conversão divide a
quantidade de orçamentos com pedido válido pela quantidade de orçamentos
válidos filtrados; cada orçamento conta uma vez. Faturamento, tickets e
projeção usam o mês da data final. Sem uma fonte de metas, a meta aparece como **Não definida**.
**Ticket médio pedido** divide o valor dos pedidos pela quantidade de pedidos
válidos; **Ticket médio orçamento** divide o valor dos orçamentos pela quantidade
de orçamentos válidos. Ambos usam somente o trecho filtrado do mês de referência,
com os mesmos filtros de empresa, equipe, vendedor e cliente das fontes.
Sem documentos, mostram traço. CSV/PDF incluem os dois tickets. Os cálculos
reutilizam as fontes existentes e dispensam mudanças no JSON de personalização.
A listagem é ordenada pelo valor orçado do período, do maior para o menor,
com desempate pelo valor de pedidos e depois por nome/ID. A mesma ordem vale
para representantes e seus clientes, UFs, cidades e clientes das regiões,
antes da paginação, no resumo, na evolução mensal e nas exportações.
A projeção por dias corridos exige que o filtro inclua o início do mês e não
termine no futuro. CSV/PDF incluem os cartões e todos os clientes filtrados.

`desempenhoComercial.valorNotas: "total"` identifica consultas que retornam
o valor total da nota, incluindo IPI e frete. Atualiza os textos da tela e
das exportações; não modifica o SQL nem recalcula valores. Sem essa opção,
os textos mantêm a indicação de valor líquido. Defina a opção em cada aba,
em concordância com seu `sqlBase`.

`desempenhoComercial.metasVendedores` permite um SELECT de metas mensais,
com aliases `vendedorId`, `inicio`, `fim`, `meta`, `atingido` e `percentual`.
O executor fornece `:metaInicio`/`:metaFim` (primeiro/último dia do mês da data
final), `:empresa` e o `:vendedor` autorizado. Essa consulta não usa o filtro
de cliente: a meta pertence ao vendedor e ao mês inteiro. A Inecel usa a tabela
personalizada **SMT120**, da tela Configuração > Comercial > Acompanhamento Meta
Vendedor. SMT080 é outro cadastro e não corresponde a essa tela. SMT120 não
possui empresa; o SELECT limita os vendedores pelo vínculo C019, usando EXISTS
para evitar repetição por empresa. Os valores cadastrados são por vendedor,
e seu atingimento original reúne pedidos das empresas 1, 4 e 5.

O cartão lê valor e percentual atingidos gravados pelo botão **Calcular Valor
Atingido** do ERP, sem recalcular sobre notas ou alterar o cadastro/comissões.
Com Todos, soma somente metas positivas cadastradas nesse mês; o percentual é
ponderado pelo valor das metas, não uma média simples. Vendedores sem meta não
entram nessa base. Sem cadastro mensal completo, mostra Não definida. Com meta
cadastrada e atingimento nulo, mostra **Meta do mês** e o valor cadastrado em
moeda, independentemente do cálculo no ERP. Com atingimento gravado, mantém
**Meta atingida** e o percentual registrado. Registros mensais duplicados geram
aviso no cartão, sem bloquear os demais resultados. O cartão permanece igual
ao alternar Vendedores/Representantes/Regiões; CSV/PDF incluem os mesmos valores.

`desempenhoComercial.comparativoMesAnterior: true` acrescenta aos cartões de
faturamento, valor dos pedidos, tickets médios de pedido e orçamento e valor
orçado uma seta e a variação percentual:
verde para aumento, vermelho para queda e neutro para estabilidade ou falta
de base. Compara o trecho filtrado do mês da data final com os mesmos dias do
mês anterior (01–14/10 contra 01–14/09). Dias inexistentes são limitados ao
último dia do mês anterior, inclusive em fevereiro e na virada do ano.
O cartão de orçamentos preserva o total do período: em filtros com vários meses,
mostra separadamente o valor do mês usado na comparação. Os intervalos e valores
anteriores aparecem na tela e nas exportações. Base anterior zero não gera
percentual artificial; ticket sem documentos da sua etapa não é comparável. Para bases negativas,
a variação usa o módulo do valor anterior. Três consultas agregadas adicionais
reutilizam os SQLs de orçamentos, pedidos e notas, mantendo empresa, responsável e cliente
autorizados. Sem a opção, não há comparação nem consultas adicionais.

O período deve estar no mesmo ano. O executor recusa mais de 12.000 agregados
por etapa ou 2.000 vínculos cliente/responsável, sem apresentar totais parciais.
A liberação individual aplica o vendedor no servidor, bloqueia as datas no
período padrão e permite somente a aba Vendedores nas configurações com consultas
independentes por aba. O gestor pode acessar ambas e selecionar seus filtros.

Para a visão de representantes da carteira, configure `filtros`, `sqlBase` e
`desempenhoComercial` diretamente no indicador, compartilhados pelas duas abas.
`abas` deve conter apenas `{ "id": "vendedores", "titulo": "Vendedores" }` e
`{ "id": "representantes", "titulo": "Representantes" }`, sem fontes ou filtros
próprios. Acrescente `desempenhoComercial.carteiraRepresentantes` com um SELECT
que retorne `clienteId`, `cliente`, `vendedorId`, `vendedor`, `representanteId` e
`representante`, filtrado por `:empresa`, `:vendedor` e `:cliente`.

Na INECEL, essa consulta usa `D024_C007_Vendedor_Interno` (vendedor principal) e
`D024_D021_Id` (representante atual do cliente), incluindo cadastros compartilhados
com empresa nula/zero. Clientes sem movimento também aparecem. Os valores são os
documentos já filtrados do vendedor principal, vinculados por IDs de vendedor e
cliente; o representante gravado no documento não define o agrupamento da carteira.
Clientes sem representante ou sem vendedor principal não entram nessa listagem.
Os cartões continuam com os resultados do vendedor por documento, portanto podem
ter totais diferentes dos representantes da carteira atual.

O carregamento busca a carteira uma vez (limite de 20.000 clientes, sem truncar).
A troca de abas redesenha apenas a tabela, sem requisição, preservando cartões,
comparativos e filtros. Cada representante expande seus clientes, com paginação,
em ambas as visões da tabela. A evolução mensal continua sem NF. A exportação da
aba Representantes inclui representante, vendedor principal e clientes da carteira,
com orçamentos e pedidos. Essa tabela omite NF tanto no resumo quanto na evolução
mensal: cada representante soma todos os seus clientes, e as linhas expandidas
mostram os totais individuais por cliente, respeitando o período filtrado.
Filtros salvos são compartilhados pela chave `vendedores`. Nesse modo, o usuário
individual pode abrir as duas abas, com o mesmo vendedor fixado no servidor,
inclusive em chamadas diretas e nas exportações. As configurações anteriores
mantêm seu comportamento.

Ao final da aba Vendedores, o gráfico de orçamentos e pedidos mostra os valores
mensais e os totais do período aplicado. Clicar na linha ou no nome de um cliente
troca apenas o gráfico para o mês atual até hoje e os cinco meses anteriores,
incluindo meses sem movimento. O histórico usa o ID exato do cliente e o vendedor
da linha, preservando os filtros e as restrições de empresa, equipe e vendedor
no servidor. O botão **Voltar ao período filtrado** restaura a visão geral.
O histórico é carregado sob demanda pela ação `commercialClientHistory`, com
duas consultas agregadas limitadas às fontes de orçamentos e pedidos.

Para acrescentar a terceira aba, adicione `{ "id": "regioes", "titulo": "Regiões" }`
sem fontes ou filtros próprios e configure `desempenhoComercial.carteiraRegioes`.
O SELECT deve retornar `clienteId`, `cliente`, `vendedorId`, `vendedor`, `uf`,
`cidadeId` e `cidade`, restringindo empresa, vendedor e busca de cliente.
Na INECEL, UF e cidade vêm de D018/D020 pelos vínculos do cadastro D024. A carteira
regional inclui também clientes sem representante, sem movimento e sem endereço
completo. O limite é de 20.000 clientes, com erro explícito em vez de truncamento.

Para a visão **Regiões cadastradas**, acrescente ao SELECT de carteira os aliases
`regiaoId`, `regiao` e `cidadeIbge`, e configure `territoriosRegioes` com um SELECT
que retorna `uf`, `cidadeIbge`, `regiaoId` e `regiao`. Esse cadastro municipal é
limitado às UFs da carteira autorizada, via empresa/vendedor/cliente, e a 7.000
registros (sem truncamento silencioso). A configuração da Inecel segue os vínculos
`D024_D020_Id → D020_D084_Id → D084_Id`: a região é da cidade cadastrada, não uma
divisão deduzida do nome. O pacote do cliente confirma esse vínculo, utilizado
também pela logística. As prioridades de remessa/redespacho/entrega dos pedidos
na logística não substituem o endereço cadastral da carteira de Vendedores.

A flag na aba Regiões alterna Brasil → UF → região cadastrada → clientes,
mantendo os cartões e filtros. O mapa nacional e o mapa da UF usam limites
municipais locais do IBGE, coloridos pelo vínculo D020/D084. Só regiões com
clientes na carteira selecionada têm cor e ação de clique; os territórios de
outras regiões ficam cinza. Clientes sem vínculo ficam em **Sem região cadastrada**.
Vínculos divergentes para o mesmo código municipal não recebem uma região
arbitrária no mapa. As listas continuam ordenadas por valores decrescentes.
Se uma malha estiver indisponível, a lista permanece disponível e o mapa exibe
aviso. Nenhuma malha é buscada em serviços externos durante a utilização.
Os arquivos em `mapas-municipios` são gerados por `scripts/gerar-mapas-municipios.py`
a partir da API pública de malhas do IBGE; cada arquivo registra sua fonte.

A aba Regiões preserva os cartões e usa o mapa SVG já incluído no sistema:
Brasil → UF → clientes. A cidade aparece abaixo do nome de cada cliente. Na
visão por regiões cadastradas, cada zona mostra diretamente os seus clientes,
sem exigir a abertura das cidades. A cor da UF indica a quantidade de clientes;
as tabelas mostram quantidades e totais de orçamentos/pedidos. A navegação e a
troca de abas não fazem novas consultas. Endereços sem UF válida aparecem em
“UF não informada”. CSV/PDF da aba incluem UF, cidade, vendedor, cliente e valores
do período para toda a carteira, independentemente do nível aberto no mapa.
As carteiras maiores usam meses esparsos por cliente, omitindo meses zerados na
resposta e exibindo zero na tabela. A resposta visual não duplica os dados na
listagem de exportação; CSV/PDF continuam gerando sua listagem no próprio endpoint.
As três abas mantêm o mesmo vendedor autorizado no servidor. A opção é compatível
com as configurações anteriores de duas abas.

### Painel de Cotações

Habilite `painelCotacoes.habilitado`. O componente usa consultas próprias de
T003, T005 e T007, com o preparador do dashboard e os limites da sessão.
As abas **Orçamentos**, **Pedidos**, **Faturamento** e **Projeção do mês** usam
o layout nativo, sem iframe ou menu lateral interno. Inclui filtros por
empresa, equipe, vendedor, representante e cliente; atalhos de período;
gráfico diário de quantidade/valor/toneladas; ranking; clientes com documentos
expansíveis; situação dos pedidos; CSV e impressão/PDF pelo cabeçalho.
Atualizar preserva a aba e os filtros. Os estilos e IDs são delimitados ao
componente para preservar os demais indicadores.

Na lista de documentos de um cliente, clicar na linha ou acionar o número pelo
teclado abre uma janela com o cadastro original do orçamento (VEN001), pedido
(VEN002) ou nota (VEN003). O ID interno identifica a NF, mesmo quando seu número
fiscal é diferente. A lista e os filtros são mantidos ao fechar. A janela incorpora
a tela do ERP; o indicador continua renderizado diretamente no dashboard.
As ações e personalizações dessa tela nativa permanecem as do sistema.

`cotacoes-documento.php`, chamado pela ação `cotacoesDocumentContext` de `data.php`,
valida a sessão do ERP, o acesso ao módulo (C030: 14/15/16), a empresa configurada
e o documento no período/escopo autorizado, incluindo a restrição de vendedor ou
Supervisor 1. Usa somente SELECT e busca o documento por ID. O navegador valida
a rota retornada e a sessão da tela incorporada. Não basta ter acesso ao painel
para ganhar acesso ao cadastro no ERP.
Documentos de outra empresa exigem selecionar essa empresa no ERP e reabrir o
dashboard: o parâmetro nativo de troca de empresa altera a sessão global e não
é enviado por esta integração. A funcionalidade não exige alteração no JSON.
Ao instalar, inclua também `cotacoes-documento.php` junto aos arquivos do painel.

- Empresas: empresas da mesma conexão liberadas ao usuário no C019, mais a
  empresa da autorização atual, intersectadas com `empresasConfiguradas`
  quando essa opção estiver presente.
- Equipe: Supervisor 1 (`C007_C007_Id`) do vendedor interno e documentos do
  próprio supervisor. Supervisor 2 é ignorado.
- Orçamentos: exclui somente status cancelado (`C`), conforme o resumo MCP.
  A marca de perdido, isoladamente, não exclui o documento. Valor e conversão consideram todo
  o período filtrado; um pedido válido vinculado converte o orçamento uma
  única vez, mesmo quando emitido depois do período, na mesma empresa.
- Pedidos: valor total, sem cancelados (`8`) e transferências configuradas.
- Notas: valor total, incluindo IPI e frete; saída (`S`), cancelada igual a
  `N` e protocolo não vazio, conforme `hardness_sales_summary`. Exclui as
  transferências configuradas, sem adicionar filtros por natureza de operação.
  Essa regra se aplica ao Painel de Cotações; o componente Desempenho comercial
  continua usando o SQL de notas definido no seu próprio JSON.
- Cliente consolidado: proposta (se `clienteProposta` estiver habilitado),
  seu grupo quando cadastrado; na ausência da proposta, grupo ou nome do
  cliente faturado. Nomes iguais após normalização são reunidos.
- Origem IA: se `origemIA` estiver habilitado, usa o vínculo T003A/D201 com
  `D201_Chave_Sistema = MCP_IA`. `clientesExcluidos` aceita IDs D024.

`painelCotacoes.empresasConfiguradas` aceita uma lista de objetos com `id`
ou `cnpj`, `rotulo`, `clientesTransferencia` (IDs D024), `toneladas` e
`servicosIndustrializacao` (códigos dos produtos). Se ID e CNPJ forem
informados, ambos precisam corresponder. A identificação pelo CNPJ é feita
somente entre as empresas autorizadas. Códigos e identificadores de clientes
ficam na personalização, sem valores específicos no motor.

`painelCotacoes.equipes` permite limitar os filtros aos grupos comerciais
configurados, sem criar uma equipe para cada supervisor encontrado. Cada objeto
informa `id` (identificador único), `nome`, `empresaId` ou `empresaCnpj` e
`supervisores` (lista de códigos C007). Mais de um código pode formar uma única
equipe. `supervisoresNomes` aceita nomes exatos do cadastro autorizado, ignorando
maiúsculas/minúsculas; nomes ambíguos exigem configurar os códigos. O vínculo dos
vendedores permanece dinâmico pelo Supervisor 1; os próprios supervisores também
integram a equipe. Se um supervisor configurado tiver outro supervisor cadastrado,
seus documentos ficam apenas na própria equipe. A empresa do documento também
precisa corresponder à empresa da equipe.

No consolidado aparecem os grupos das empresas autorizadas; ao escolher uma
empresa, aparecem somente seus grupos. Documentos sem vínculo com esses grupos
permanecem em **Todas**, com aviso de quantidade, sem criar novos botões de equipe.
Esse agrupamento não amplia permissões: uma liberação individual ou por supervisor
continua limitada no servidor, mesmo quando o grupo comercial reúne vários códigos.
Sem `equipes`, o comportamento anterior de descoberta automática é preservado.

`painelCotacoes.atribuirClienteProposta: true` atribui também a identidade do
cliente (ID, nome e grupo) ao cliente proposta, nas três etapas, linhas de
documentos, filtros e CSV. Sem essa opção, preserva o comportamento anterior.
Na Inecel, o campo `T003A_D024_Id_Cliente_Proposta` é copiado para
`T005A_D024_Id_Cliente_Proposta` na criação do pedido (VEN001) e para
`T007A_D024_Id_Cliente_Proposta` na emissão da nota (VEN012). Usa-se o campo
registrado em cada documento: preenchido com ID positivo, prevalece a proposta;
zero/nulo/ausente, prevalece o cliente da capa. Não consulta o cliente do
orçamento original para substituir um campo vazio no pedido/nota.

O JSON de Vendedores aplica a mesma regra aos seus três SELECTs, tanto no
`clienteId` quanto no JOIN de D024 e na busca por cliente. Assim, os movimentos
passam a se vincular ao cliente correto na carteira por representante e região.
Exemplo: nota emitida para FSI com proposta EPM retorna cliente EPM; sem proposta,
retorna FSI. Mantém o vendedor, a empresa e os valores do documento. Subconsultas
escalares evitam multiplicação de documentos por extensões repetidas (entre IDs
positivos repetidos, segue a menor chave, como o agrupamento anterior do painel).
Se a chave da proposta não existir em D024, identifica o código da proposta,
sem atribuir indevidamente seus valores ao cliente original. Regras de cancelamento,
transferências, conversão e histórico de projeção permanecem iguais. Os documentos
do ERP não são alterados; sua abertura continua mostrando a emissão original.

Transferências são excluídas de pedidos, faturamento, histórico e listagem
de clientes. Nos totais monetários de orçamentos elas permanecem, seguindo
a referência MCP. Valores negativos são preservados e sinalizados. O gráfico
diário omite fins de semana e feriados nacionais, seguindo o calendário nacional
do motor (incluindo Sexta-feira Santa e 20/11 a partir de 2024). Seus movimentos
são reunidos no próximo dia útil exibido, ou no último disponível, preservando
o total do período quando há dias úteis. Um intervalo sem dias úteis exibe
“Sem dias disponíveis neste período”. Cartões e listagens mantêm todos os documentos.

Uma empresa com `toneladas: true` abre o gráfico em toneladas e oferece
alternância para quantidade/valor. O volume vem dos itens T004/T006/T008 em
KG, dividido por mil, respeitando os filtros de exclusão/cancelamento do item
usados pelo MCP. Por isso, um cabeçalho cancelado com item ativo pode compor
toneladas, embora não componha o total monetário. São descontadas transferências,
os serviços cadastrados e os produtos com preço médio por KG inferior a R$ 1
nos últimos 270 dias da respectiva fonte, dentro do escopo autorizado.
Essa descoberta é recalculada, sem gravar uma lista de serviços. Serviços
continuam nos valores em reais. Na projeção, pedidos podem alternar R$/toneladas;
os cenários de faturamento permanecem em reais.

Em **Gerenciar acessos > Usuários**, escolha vendedor, **Equipe de [nome]**
ou **Todos os vendedores e representantes (gestor)**. Empresa e restrição
individual/equipe são aplicadas no servidor aos documentos, histórico e
cadastros, antes do envio ao navegador. Nesse componente, as datas e quatro
abas ficam livres dentro do escopo autorizado. O acesso por equipe é recusado
para componentes sem suporte. A visão de gestor não concede administração de
acessos. As permissões continuam no arquivo separado de acessos da empresa.

A projeção carrega até 24 meses de histórico diário somente ao abrir a aba.
O histórico de pedidos inclui todas as situações, descontando transferências,
conforme a consulta da projeção de referência; difere do cartão de pedidos
válidos. Movimentos em dias não úteis são atribuídos ao próximo dia útil.
Combina base histórica, ritmo linear, curva histórica e média móvel de dez
dias úteis. Usa o mês da data final e o próximo mês, respeitando empresa,
equipe, vendedor e representante; data inicial e busca de cliente não recortam
o histórico. Sem dados suficientes não cria cenários artificiais. Metas
não são distribuídas automaticamente por vendedor; a projeção não grava snapshots
nem altera os documentos do ERP.

O cartão **Meta do mês** permite informar, editar e remover uma meta pessoal de
pedidos. Ela é persistida em JSON separado por empresa da sessão, indicador e
usuário autorizado, ao lado dos arquivos de acesso. O contexto inclui mês,
empresa selecionada, equipe, vendedor, representante e unidade (R$/toneladas).
Mês de referência e próximo mês mantêm metas independentes. Somente o usuário
que informou a meta a recebe nas respostas do painel.

O endpoint `saveCotacoesGoal` exige POST JSON, autorização para o indicador e
origem do mesmo host. Usa exclusivamente empresa/usuário da autorização para
resolver o arquivo, com gravação atômica protegida por lock. Não executa SQL nem
escreve no cadastro de metas do ERP. Meta inválida ou falha de gravação mantém
o valor anterior. Valores monetários usam centavos e toneladas usam três casas.

A linha âmbar **Meta acumulada por dia útil** cresce até o valor mensal informado,
seguindo os dias úteis já usados pela projeção. O cartão mostra atingimento e
diferença em relação ao ritmo esperado até a data de referência. Alterar a meta
atualiza o cartão e o gráfico usando a projeção já calculada. A meta aparece no
CSV e na impressão/PDF. Sem meta, permanece **Não definida** e a linha é omitida.
O calendário preserva os feriados do modelo comercial, incluindo os locais de
Joinville. `calendario.feriadosAdicionais` aceita datas ISO e
`calendario.recesso` aceita `{ "de": "12-20", "ate": "01-09" }` ou `null`.

O alinhamento dos resumos MCP não torna o modelo de projeção equivalente ao
modelo avançado da referência: histórico mensal de 60 meses, tratamento
separado de grandes pedidos, regressão NNLS, limite pela carteira e snapshots
de acurácia não estão implementados. A projeção atual mantém os quatro
modelos descritos acima.

Limites: 366 dias por período, 20.000 documentos por etapa, 40.000 agregados
por fonte histórica, 2.000 vendedores e 30 empresas. Excedê-los retorna erro,
sem apresentar totais ou previsões parciais. Todas as fontes respeitam a
transação de leitura e o tempo máximo existentes do dashboard.

### Validação local

Os testes usam dados fictícios, SQL em SQLite e substitutos locais de mysqli;
não carregam credenciais nem consultam bases de clientes.
O teste SQL também extrai os filtros dos repositórios reais de resumo e itens
do MCP, sem instanciar PDO ou abrir conexão, e compara seus resultados com o
painel nas mesmas massas fictícias. Cobre transferências por empresa,
cancelamentos, valores negativos, proposta/grupo, KG e serviços.

```sh
php dashboard-personalizado/tests/desempenho-comercial.php --fixture | node dashboard-personalizado/tests/desempenho-comercial.js
python3 dashboard-personalizado/tests/desempenho-comercial-sql.py
php dashboard-personalizado/tests/painel-cotacoes.php
python3 dashboard-personalizado/tests/painel-cotacoes-sql.py
node dashboard-personalizado/tests/painel-cotacoes.js
COTACOES_CHROME=/caminho/chrome-headless-shell python3 dashboard-personalizado/tests/painel-cotacoes-browser.py
```

Para validar uma personalização completa com os dois componentes e a regra de
valor total das notas, execute também:

```sh
php dashboard-personalizado/tests/desempenho-comercial.php --config=/caminho/painel-completo.json
python3 dashboard-personalizado/tests/desempenho-comercial-paridade.py /caminho/painel-completo.json
```

O teste de paridade compara as seis consultas do JSON fornecido com os filtros
reais do MCP, usando empresas, documentos, vendedores e representantes fictícios.
Inclui transferências específicas por empresa e identificação da filial pelo
CNPJ com e sem máscara. Não consulta a base do cliente.

O teste de navegador usa Chrome/Chromium e servidor HTTP local, sem bibliotecas
Python externas. Cobre abas, escopos, projeções, exportações, responsividade e
troca entre o componente e um indicador padrão. `COTACOES_SCREENSHOTS` permite
escolher a pasta das capturas; por padrão usa a pasta temporária do sistema.

## Inclusão de agenda nos títulos a receber

O detalhamento modal da listagem pode oferecer **Incluir** na coluna Agenda.
A ação abre o cadastro original do ERP em uma janela dentro do dashboard,
com o cliente e o título selecionados. O formulário nativo faz a gravação e
executa as validações, auditoria e personalizações já existentes. Nessa janela,
o cabeçalho é somente para leitura, os atalhos de cadastro ficam ocultos e a
navegação para outras telas é bloqueada. As aberturas de tarefa/orçamento e
suas perguntas de inclusão são suprimidas, preservando os avisos de validação.
Essas restrições são locais ao iframe; o ERP fora do dashboard mantém seu
comportamento. Os vínculos e registros automáticos feitos pelo servidor ao
salvar D085 continuam seguindo as regras nativas.

Habilite no objeto `listagem.detalhamento`:

```json
"agenda": {
  "habilitarInclusao": true,
  "campoMensagem": "agenda"
}
```

O detalhamento precisa de `modal: true` e seu `sqlBase` deve retornar, além das
colunas exibidas, os campos abaixo. Os IDs não precisam estar em `colunas`:

```sql
T002_Id AS tituloId,
T002_D024_Id AS clienteId,
T002_C004_Id AS empresaTituloId,
agendaTitulo.D085_Id AS agendaId
```

`agendaTitulo` representa o `LEFT JOIN` com a última ocorrência de D085 pelo
`D085_T002_Id`. Use `MAX(D085_Id)` para identificar a última inclusão; datas
podem ser alteradas por personalizações. Mantenha os filtros e os vínculos
originais da consulta. Indicadores sem essa configuração mantêm a agenda
somente para leitura.

`data.php?action=agendaContext` é uma consulta de autorização, sem escrita.
Ela aplica o acesso ao indicador, suas restrições individuais e os mesmos
filtros/grupo usados no detalhamento; em seguida confere os vínculos reais de
T002, usuário ativo, empresa ativa e acesso ao Contas a Receber (módulo 35)
nas empresas da sessão e do título. ROOT/suporte seguem a regra do ERP.
O navegador não escolhe os IDs de cliente/empresa da URL nativa. Ter acesso
ao dashboard, sozinho, não libera a ação de cadastro.

A sessão PHP do ERP precisa estar ativa, no mesmo usuário e empresa usados
ao abrir o dashboard. Se ela mudar, reabra o dashboard pelo ERP. A janela usa
a mesma origem e o modo nativo `oneWindow`, sem trocar a empresa ou o usuário
da sessão compartilhada. Em **Todas as empresas**, o vínculo é o ID do título;
as demais regras do cadastro são as mesmas da inclusão pela tela do ERP.

Após o retorno do formulário, os títulos são consultados novamente com os
filtros aplicados ao abrir o detalhamento. A confirmação de atualização só
aparece quando a releitura retorna um `agendaId` maior após o envio da agenda.
O envio é reconhecido pelos campos agrupados de `crm001FormAgenda`, incluindo
`internal_sent`, conforme a serialização nativa de `Formulario` e `jquery.form`.
Durante o envio e a confirmação, a janela mostra o carregamento e bloqueia
novas interações; erros removem o carregamento e preservam os avisos nativos.
Após a confirmação da gravação, a janela é fechada, e a lista atualizada dos títulos volta a
ficar acessível, acompanhada da confirmação. O botão **× (Fechar)** também
refaz a consulta, inclusive quando a inclusão foi cancelada ou houve erro de
validação. Erros não são tratados como gravação bem-sucedida.

Na abertura, o carregamento usa o cartão e a barra visual do dashboard,
sobrepostos ao formulário. O iframe mantém a mesma altura antes e depois
do carregamento, sem reservar uma faixa de texto que deixe espaço no rodapé.

Arquivos de implantação: `data.php`, `agenda-action.php`, `agenda.js`,
`dashboard.js`, `dashboard.css` e `index.html`. O JSON de cada cliente é
distribuído separadamente. O ERP precisa permitir que sua própria página
seja exibida em um iframe da mesma origem.

Testes locais, sem conexão à base de cliente:

```sh
php dashboard-personalizado/tests/agenda.php
python3 dashboard-personalizado/tests/agenda-sql.py
node dashboard-personalizado/tests/agenda.js
```

Com Node 18+ e Playwright disponíveis, `node dashboard-personalizado/tests/agenda-browser.cjs`
testa a abertura, os bloqueios de navegação/tarefa/orçamento, o envio via
`ajaxSubmit` com campos agrupados, o carregamento, a validação e o retorno aos
títulos em Chromium com os arquivos reais de jQuery 1.6.2 e `jquery.form`
carregados pelo ERP. Todas as requisições são interceptadas com dados fictícios.
A integração usa `bind/unbind`, compatíveis
com o jQuery 1.6.2, que não possui `on/off`.

Os testes cobrem autorização, vínculos, escopo por empresa, SQL com dados
fictícios, escape da mensagem, sessão divergente e atualização do modal. A
validação final do formulário e das personalizações requer abrir o cadastro
no ERP do cliente e realizar uma inclusão autorizada.

## Exportação

Todo indicador disponível apresenta as ações **CSV** e **PDF** no cabeçalho.
Os arquivos usam exatamente a aba, os filtros e a seleção de UF que estavam
aplicados na última consulta concluída. O CSV leva a listagem do indicador (ou
o comparativo de representantes); o PDF inclui o resumo e essa mesma tabela.
As exportações respeitam as mesmas permissões de acesso do dashboard.

### Impressão das linhas marcadas

`listagem.impressaoMarcadas.habilitada: true` acrescenta um checkbox antes
da coluna identificada por `campoNome` e o botão **Imprimir marcadas**.
`campoChave` deve ser uma chave numérica positiva presente no SQL principal;
`campoNome` e `campoTipo` identificam os metadados do cabeçalho. A seleção
permanece ao mostrar mais linhas e é limpa ao atualizar filtros ou trocar
de indicador/aba. O checkbox do cabeçalho marca somente as linhas exibidas.

O modelo recebe `titulo`, `assinatura`, `rotulosTipo`, `colunas` (campo,
título, formato e largura proporcional) e `totais` (campos das colunas).
Seu `sqlBase` projeta a mesma chave de agrupamento e os campos das colunas,
restringindo obrigatoriamente a empresa por `:empresa`. Os filtros
`dataInicio` e `dataFim` vêm da última consulta aplicada. `observacao`
permite explicar na listagem quando a impressão usa outro critério de data,
como recebimento de parcelas em lugar da emissão da nota.

O escopo `marcadas` do exportador valida o indicador, a aba, as restrições
do usuário e os IDs contra a fonte principal antes de consultar o relatório.
A empresa vem da sessão. Até 500 IDs são vinculados como parâmetros e duas
consultas atendem a seleção inteira. O SQL do modelo não é publicado na API.
O PDF abre em nova aba, em A4, e cada registro começa em uma folha separada,
com totais e assinatura. Relatórios longos continuam em folhas adicionais;
o limite de 10.000 linhas recusa a impressão inteira, sem truncar valores.

Instalar juntos `impressao-marcadas.php`, `custom-indicator-engine.php`,
`export.php`, `dashboard.js`, `dashboard.css` e `index.html`; depois habilitar
o modelo no JSON do cliente. Sem a configuração, a listagem permanece igual.
Não há migração ou gravação de dados. Validação local:
`php dashboard-personalizado/tests/impressao-marcadas.php` e
`node dashboard-personalizado/tests/impressao-marcadas.js`.

## Estrutura mínima

```json
{
  "habilitado": true,
  "titulo": "Dashboard de vendas",
  "indicadores": [
    {
      "id": "vendas_pr",
      "habilitado": true,
      "ordem": 1,
      "titulo": "Vendas no Paraná",
      "subtitulo": "NF-e de venda destinadas ao PR",
      "icone": "chart",
      "cor": "blue",
      "filtros": [
        { "id": "dataInicio", "titulo": "Emissão inicial", "tipo": "data", "padrao": "inicio_mes", "obrigatorio": true },
        { "id": "dataFim", "titulo": "Emissão final", "tipo": "data", "padrao": "hoje", "obrigatorio": true }
      ],
      "sqlBase": [
        "SELECT T007_Id AS id, T007_Numero_Nota_Fiscal AS documento,",
        "T007_Data_Emissao AS data, D024_Nome_Empresa AS descricao,",
        "T007_Valor_Total AS valor",
        "FROM T007",
        "INNER JOIN D006 ON D006_Id = T007_D006_Id",
        "INNER JOIN D024 ON D024_Id = T007_D024_Id",
        "INNER JOIN D018 ON D018_Id = D024_D018_Id",
        "WHERE T007_C004_Id = :empresa",
        "AND T007_Data_Emissao BETWEEN :dataInicio AND :dataFim",
        "AND T007_Flag_Entrada_Saida = 'S'",
        "AND IFNULL(T007_Numero_Protocolo_Nfe, '') != ''",
        "AND (T007_Flag_Cancelada != 'S' OR T007_Flag_Cancelada IS NULL)",
        "AND IFNULL(D006_Flag_Devolucao, 'N') != 'S'",
        "AND D006_Flag_Venda_Compra_Outros = 'V'",
        "AND D018_UF = 'PR'"
      ],
      "card": {
        "titulo": "Total vendido",
        "campo": "valor",
        "calculo": "soma",
        "formato": "moeda"
      },
      "grafico": {
        "habilitado": true,
        "titulo": "Vendas mensais no Paraná",
        "campoData": "data",
        "campoValor": "valor",
        "calculo": "soma"
      },
      "listagem": {
        "habilitada": true,
        "titulo": "NF-e consideradas",
        "limite": 200,
        "colunas": [
          { "campo": "documento", "titulo": "NF-e" },
          { "campo": "data", "titulo": "Emissão", "formato": "data" },
          { "campo": "descricao", "titulo": "Cliente" },
          { "campo": "valor", "titulo": "Valor", "formato": "moeda" }
        ]
      }
    }
  ]
}
```

## Apresentação compacta

`"apresentacao": { "compacta": true }` no indicador mostra os resumos somente
com título e valor e remove instruções e subtítulos das tabelas, mantendo
as conferências por clique ou teclado. Sem essa opção, preserva a apresentação
existente. Para abrir um título como ficha, use
`"apresentacao": "ficha"` em `listagem.detalhamento`; aplica-se a um registro,
mantendo a tabela quando houver vários. Validação local:
`node dashboard-personalizado/tests/apresentacao-compacta.js`.

## Filtros

Tipos aceitos: `data`, `texto`, `numero` e `selecao`. O id do filtro vira um
parâmetro SQL com o mesmo nome, por exemplo `dataInicio` vira `:dataInicio`.
Para datas, os padrões `hoje`, `inicio_mes`, `fim_mes` e `inicio_ano` são reconhecidos.
`fim_mes` permite abrir o planejamento com o mês completo, incluindo dias futuros.

O executor permite a tabela de classificação de materiais `SMT029`;
as demais tabelas customizadas continuam sujeitas à validação existente.

Filtros com `"persistir": false` não usam nem salvam preferências. Ao abrir o
indicador, começam no `padrao`; depois de uma consulta, exibem o valor aplicado.
Use `"recarregarAoAlterar": true` para consultar novamente ao mudar o filtro,
sem precisar clicar em **Aplicar**. Seleções obrigatórias mostram apenas as
opções configuradas, sem a opção vazia **Todos**.

## Dias úteis na projeção de caixa

Gráficos `barras_comparativas` deslocam cada título para o próximo dia útil
antes de aplicar `campoDiasUteisAdicionais`. A barra e seu detalhamento usam
a mesma data projetada, preservando o vencimento original e o valor do título.

Use `"considerarFeriados": true` e `"considerarFeriadosNacionais": true`
no gráfico para somar os feriados nacionais brasileiros aos feriados ativos
cadastrados em D206/D207. Sem a nova opção, permanece o comportamento anterior.
O cálculo inclui as datas fixas, 20/11 a partir de 2024 e a Sexta-feira da Paixão
calculada por ano, inclusive quando a projeção atravessa a virada do ano.
Pontos facultativos e feriados locais dependem do cadastro D206/D207.
Referência para 2026: [Portaria MGI nº 11.460/2025](https://portal.ifpe.edu.br/afogados/wp-content/uploads/sites/10/2025/12/P.-MGI-11.460-2025-1.pdf).

Configure o SQL da série para retornar zero dias adicionais nos pagamentos
e recebimentos comuns, dois para boletos em aberto e um quando a data-base
já for a data de crédito bancário. Exemplo: vencimento em 12/10/2026 leva
pagamentos e recebimentos comuns a 13/10; boletos em aberto a 15/10.
Uma data de crédito em 14/10 acrescida de um dia útil também resulta em 15/10.
Sem feriados, um boleto vencendo no sábado ou domingo fica disponível na quarta.

A projeção redistribui os registros retornados pelo SQL, sem alterar seus valores.
Use `"limitarAoPeriodoFiltrado": true` para manter somente datas projetadas entre
os filtros `dataInicio` e `dataFim`, inclusive. O recorte ocorre depois dos dias
úteis e da compensação, antes de somar barras e montar o detalhamento. Os totais
do gráfico também consideram apenas esse intervalo, e os agrupamentos semanais
e mensais respeitam seus limites. As duas datas são obrigatórias nesse modo.
Sem essa opção, datas projetadas podem ultrapassar o período-base.

Esse recorte não altera os critérios do SQL-base: quando o filtro seleciona
vencimentos, continuam valendo tanto a seleção original quanto o limite da
projeção. Para consultar títulos deslocados além do fim do período, amplie a
data final. Os demais componentes do indicador continuam usando suas consultas.
O gráfico exibe apenas dias com registros projetados; feriados e fins de semana
não ganham colunas vazias.

Use `"exibirSaldoProjetado": false` para mostrar somente os totais das séries,
sem o cartão de saldo. A opção não muda os valores das barras. Por padrão,
gráficos com duas séries continuam exibindo o saldo.

Validação local, sem conexão com banco:
`php dashboard-personalizado/tests/projecao-caixa.php` e
`node dashboard-personalizado/tests/projecao-caixa.js`.

### Previsões manuais e recebimentos de pedidos no Fluxo de caixa

`previsaoCaixa.habilitada: true` acrescenta duas séries identificadas como
previsão: recebimentos de pedidos e contas a pagar digitadas. Instale juntos
`previsao-caixa.php`, `previsao-caixa.js`, `custom-indicator-engine.php`,
`data.php`, `export.php`, `dashboard.js`, `dashboard.css` e `index.html`.
A configuração do indicador é personalizada diretamente no cliente e não
faz parte desta alteração do motor. A fixture `tests/fixtures/previsao-caixa.php`
serve somente aos testes locais. Não há migração de banco.

Configure a emissão inicial no filtro `emissaoPedidosInicio` e a opção
`simularPedidos` para ativar ou desligar a fonte. A data final da projeção
também limita a emissão consultada. O período padrão pertence ao JSON do cliente.
A simulação usa `T005_Data_Entrega` como faturamento
previsto e soma `T090_Prazos_Dias` de cada parcela para chegar ao vencimento;
depois aplica os dias úteis e, para boletos, dois dias úteis adicionais.
O parcelamento usa os valores e portadores de T090. O valor restante é estimado
pela proporção monetária dos itens ainda não faturados, descontando quantidades
T008 de notas válidas da mesma empresa. Os centavos são distribuídos preservando
o total proporcional das parcelas. Notas ligadas diretamente ao pedido, sem
vínculo de itens, abatem o valor faturado proporcionalmente ao total do pedido.
Somente operações configuradas para gerar contas a receber entram na simulação.
Parcelas fora do período projetado não
compõem barras nem totais.

Pedidos cancelados/negados e devoluções ficam fora. Sem entrega válida,
parcelas completas, portador ou soma compatível com o total do pedido, a tela
lista o pedido para conferência. Pedidos com financeiro antecipado sem nota
também exigem conferência e não geram previsão adicional. O limite de 5.000
parcelas recusa a consulta inteira, sem mostrar totais parciais.

O formulário permite incluir, editar e remover descrição, data e valor de
despesas adicionais. Essas previsões são individuais por usuário e empresa,
salvas em JSON ao lado das preferências/acessos, com escrita atômica e bloqueio
de concorrência. Não criam títulos financeiros. Cada empresa aceita até 200
previsões por usuário. A visão Todas lê as empresas configuradas em
`previsaoCaixa.empresas`; edição e remoção exigem abrir a empresa de origem.
Somente usuários autorizados ao indicador podem gravar suas previsões.

Todas as séries passam pelo mesmo cálculo de projeção e detalhamento. CSV/PDF
incluem os valores por dia, totais de cada série e pedidos para conferência.
Validação com dados fictícios, sem acesso ao banco de clientes:
`php dashboard-personalizado/tests/previsao-caixa.php`,
`python3 dashboard-personalizado/tests/previsao-caixa-sql.py` e
`COTACOES_CHROME=/caminho/chrome-headless-shell python3 dashboard-personalizado/tests/previsao-caixa-browser.py`.

## Conferência dos blocos do PCP

O JSON `dashboard-personalizado/indicadores-pcp-realizado.json` habilita
conferência somente no indicador `inecel_pcp_planejamento`. Instalar juntos
`custom-indicator-engine.php`, `conferencia.php`, `dashboard.js`, `dashboard.css`
e `index.html`, depois importar o JSON na personalização do ERP.

Os seis cartões, as linhas e os rodapés dos quatro blocos abrem os registros
considerados, critérios, filtros e totais calculados sobre esses registros.
O clique na listagem de OPs continua mostrando seus produtos e pedidos.
O planejado usa uma linha por OP; o realizado usa uma linha por apontamento
T257 com a soma de T257A_Peso. A máquina é a do planejamento atual da OP.
Não há consulta automática ao banco na instalação nem alteração de acessos.

Na programação semanal, cada célula de segunda a domingo abre somente as
OPs daquele dia dentro da semana selecionada. O rótulo e os totais da linha
abrem a semana; o rodapé abre o período. `conferencia.camposDia` na seção
habilita as células, e a definição de conferência autoriza os dias (0 a 6)
e o campo de data em `camposDia` e `campoDataDia`.

O filtro Plano de corte oferece todos, com plano e sem plano, pela existência
de `SMT134.SMT134_T255_Id`, preservando uma linha por OP/apontamento mesmo
com vários planos. Aplica-se aos cartões, blocos, listagem e conferências.
Na produção realizada, o filtro de cliente usa `T257A_T005_Id` e a empresa
do pedido. Um apontamento selecionado mantém seu peso total, inclusive quando
possui vários pedidos. O planejado mantém o vínculo dos itens da OP ao pedido.

O filtro **Empresas** abre em **Empresa atual**, lista as demais empresas
individualmente pelo nome cadastrado e oferece **Todas as empresas** por último,
consolidando as empresas 1, 2, 4 e 5, conforme o grupo já configurado nos
indicadores financeiros da Inecel. A seleção se aplica aos cartões, programação,
carga por máquina, produção diária, listagem, detalhes, conferências e exportação.
Setor, acabamento e material carregam opções da mesma abrangência selecionada.
`opcoesCadastro: empresasGrupo` carrega os nomes em C004, limitado aos IDs de
`empresas`, exclui a empresa atual da lista individual e valida a seleção.
As opções dinâmicas aceitam `:abrangenciaEmpresa` (`atual`/ID da empresa/`todas`),
validado antes de carregar os demais cadastros; `:empresa` continua sendo
preenchido pela sessão. O SQL mantém
os vínculos de pedidos com a empresa de origem da OP. Para ativar, atualizar o
executor e importar o JSON atualizado na personalização do ERP; a configuração
específica da Inecel está em `clientes/inecel/indicadores-fluxo-caixa.json`.

`conferencia` no indicador define as fontes, colunas e totais por identificador.
Cartões/resumos referenciam esse identificador em `conferencia`. Cada seção
usa `{ "id": "identificador", "campoGrupo": "campoDaLinha" }`.
O servidor aplica o grupo ao campo da definição autorizada, preserva empresa
e filtros, valida SQL e parâmetros e recusa mais de 5.000 linhas sem apresentar
totais parciais. A resposta de conferência não inclui suas consultas SQL.
Indicadores sem essa configuração preservam o comportamento anterior.

Validação local com dados fictícios, sem credenciais ou conexão ao cliente:
`php dashboard-personalizado/tests/conferencia-blocos.php`,
`python3 dashboard-personalizado/tests/conferencia-blocos-sql.py` e
`COTACOES_CHROME=/caminho/chrome-headless-shell python3 dashboard-personalizado/tests/conferencia-blocos-browser.py`.

## Contrato do SQL-base

- deve ser um único `SELECT`, sem ponto e vírgula ou comentários;
- deve declarar as colunas explicitamente;
- deve obrigatoriamente restringir um campo `C004_Id` com `:empresa`;
- `:empresa` é sempre preenchido pela sessão e não pode ser enviado pelo navegador;
- os demais parâmetros precisam corresponder aos filtros configurados;
- card, gráfico e listagem são derivados da mesma consulta-base;
- a listagem é limitada a no máximo 500 registros.

Comandos de escrita, múltiplas instruções, tabelas de sistema, `UNION`, funções
de espera e campos sensíveis são bloqueados pelo executor.

## Somas adicionais na listagem agrupada

Use `listagem.agrupamento.camposSoma` para somar outras colunas do `sqlBase`
em cada grupo, além do total definido em `campoValor`:

```json
"agrupamento": {
  "campo": "cfop",
  "campoValor": "valor",
  "campoContagem": "id",
  "camposSoma": ["icms", "pis", "cofins", "irpj", "csll"]
}
```

Cada soma mantém o nome do campo e retorna zero quando todos os valores são
nulos. Declare esses nomes também em `listagem.colunas` para exibi-los.
`campos` continua usando `MAX`; quando um campo aparece nas duas opções,
`camposSoma` tem prioridade. Campos repetidos, identificadores inválidos,
o campo de agrupamento e os nomes reservados `quantidade`, `valor` e
`percentual` são ignorados em `camposSoma`.

Listagens agrupadas também aceitam `ordenacao` com `campo` e `direcao`. Para
exibir a tabela imediatamente após o card e a composição, use
`"posicao": "apos_resumo"`. Um rodapé calculado no navegador pode ser
configurado com `rodapeTotais`:

```json
"rodapeTotais": {
  "habilitado": true,
  "titulo": "Total geral",
  "campos": ["aluminio", "solar", "valor"]
}
```

Os textos do contador podem ser personalizados por
`rotuloQuantidadeSingular` e `rotuloQuantidadePlural`.

## Gráfico abaixo de um bloco

Uma seção pode exibir um gráfico de barras abaixo da sua tabela, reutilizando
as linhas de outra seção do mesmo indicador. Identifique a seção de origem
com `"id": "estoque_acabamento"` e acrescente à seção que receberá o gráfico:

```json
"grafico": {
  "habilitado": true,
  "titulo": "Estoque por acabamento",
  "fonteSecao": "estoque_acabamento",
  "campoRotulo": "acabamento",
  "campoValor": "estoqueKg",
  "formato": "numero",
  "unidade": "kg"
}
```

O gráfico usa os registros já consultados e os filtros da seção de origem,
sem executar SQL adicional. Ordena os valores do maior para o menor e mostra
saldos negativos em vermelho à esquerda do zero. Seções sem essa configuração
continuam exibindo apenas suas tabelas.

## Imagens na listagem

As colunas da listagem aceitam `"formato": "imagem"`. O campo correspondente
no `sqlBase` deve retornar uma URL de imagem HTTP(S) ou um caminho relativo do
próprio sistema. URLs ausentes, inválidas ou que não carregarem exibem `Sem
foto` no lugar da miniatura.

```json
{
  "campo": "imagem",
  "titulo": "Foto",
  "formato": "imagem",
  "campoAlt": "descricao"
}
```

`campoAlt` é opcional e define o campo usado como texto alternativo da imagem;
sem ele, o dashboard utiliza `descricao` quando disponível.

Para um indicador somente de listagem, defina `"habilitada": false` no objeto
`card` e também desabilite `grafico` e `composicao`.

## Calendário com curva de calor

Use `calendarios` para somar ou contar registros por dia a partir do mesmo
`sqlBase` do indicador. Sem `faixas`, as cinco cores são distribuídas de forma
proporcional aos valores retornados; para referências fixas, informe as faixas.

```json
{
  "calendarios": [
    {
      "id": "vendas_diarias",
      "titulo": "Valor de venda em calendário",
      "subtitulo": "Últimos 30 dias",
      "periodo": "ultimos_30_dias",
      "campoData": "data",
      "campoValor": "valor",
      "calculo": "soma",
      "formato": "moeda",
      "faixas": [
        { "ate": 29999.99, "cor": "red", "rotulo": "<30k" },
        { "ate": 39999.99, "cor": "yellow", "rotulo": "30-39k" },
        { "ate": 59999.99, "cor": "orange", "rotulo": "40-59k" },
        { "ate": 79999.99, "cor": "blue", "rotulo": "60-79k" },
        { "cor": "green", "rotulo": "80k+" }
      ]
    }
  ]
}
```

O calendário aceita `posicao: "apos_resumo"` para aparecer após os cartões,
antes das seções. Sem essa opção, mantém a posição padrão; ao trocar de
indicador, a posição é restaurada conforme a nova configuração.

Colunas da listagem aceitam `textoNulo: "Não informado"`. Esse texto é escapado
como HTML e aparece somente para valores ausentes; zero continua numérico.
Sem a opção, a formatação anterior permanece.

O validador permite leitura de `SMT130`, usada nos itens dos planos de corte
vinculados a `SMT134`. A restrição de empresa e os bloqueios de escrita e de
tabelas fora da lista permitida continuam obrigatórios.

As cores permitidas são `red`, `yellow`, `orange`, `blue` e `green`. A última
faixa pode omitir `ate`, recebendo todos os valores acima da faixa anterior.
Com `periodo: "ultimos_30_dias"`, o calendário usa seus próprios 30 dias,
independentemente dos filtros `dataInicio` e `dataFim` do restante do indicador.

O indicador Vendedores pode incluir o filtro de seleção `equipe` e
`desempenhoComercial.equipes: true`. O JSON da Inecel usa as mesmas definições
de equipe do Painel de Cotações nas opções e nos predicados de suas sete fontes
(documentos, metas, carteiras e territórios). As opções são consultadas por
empresa; Supervisor 1 define o time. Supervisores configurados ficam em sua
própria equipe, mesmo quando vinculados a outro supervisor. Equipe e vendedor
são filtros cumulativos, inclusive no comparativo do mês anterior.
O filtro aparece para root/gestores com visão completa. Para acesso individual,
o servidor remove o filtro e qualquer `f_equipe` recebido, fixando o vendedor
a autorização e usando equipe vazia. Não concede acesso gerencial ao vendedor.

Na aba Representantes, os cabeçalhos Orçamentos e Pedidos são botões acessíveis
que ordenam do maior para o menor antes da paginação. Com os representantes
recolhidos, ordenam a lista principal. Havendo representante expandido, ordenam
somente seus clientes, preservando a ordem dos representantes, a página e a
rolagem; a expansão é identificada pelo ID do representante. O orçamento é o padrão. Na evolução mensal,
clicar na coluna de um mês ordena aquele mês; clicar no total ordena o período.
A coluna ativa informa `aria-sort=descending`. A ordenação não altera dados,
cartões ou somatórios e não faz nova consulta. Exportações mantêm a ordenação
padrão do período definida no servidor.

O carregamento de períodos ampliados é ativado por
`desempenhoComercial.carregamentoProgressivo: true` em Vendedores e
`painelCotacoes.carregamentoProgressivo: true` em Painel de Cotações.
Permite até dois anos de calendário entre as datas, inclusive quando atravessam
anos. No acesso individual de Vendedores, as datas ficam editáveis e o vendedor
continua fixado pelo servidor. Indicadores sem essa opção mantêm seus limites.

`carregamento-progressivo.php` divide as fontes financeiras por mês e pagina
cada intervalo em 2.000 linhas, com ordenação determinística. Cada chamada HTTP
executa um lote novo e responde 202 com o token/progresso; a próxima chamada
reutiliza as consultas anteriores. Timeout de banco subdivide o intervalo,
descartando previamente as páginas daquele intervalo para não duplicar totais.
O limite de execução de cada SQL permanece ativo. Falhas definitivas retornam
erro sem apresentar somatórios parciais. Carteiras, territórios e cadastros
mantêm seus limites próprios de segurança; não são fontes financeiras.

O cache temporário fica fora da pasta pública, com diretórios 0700 e arquivos
0600, token aleatório e assinatura de usuário/empresa/base, configuração,
filtros e vínculos de acesso. Empresas e Supervisor 1 são revalidados a cada
continuação/exportação. Cargas expiram em uma hora e a próxima consulta limpa
cargas anteriores do mesmo usuário/indicador e cargas expiradas. CSV/PDF de
Vendedores usam o token da consulta concluída, preservando os totais exibidos.
Não requer tabelas, migrações, cron ou alterações na personalização de outros
indicadores. Atualizar PHP/JS/HTML/CSS e o JSON juntos.

O frontend compartilhado `carregamento-progressivo.js` solicita os lotes
sequencialmente usando a apresentação de carregamento original dos indicadores.
Não acrescenta contadores, botões ou uma nova animação de carregamento.
Cartões e listas são renderizados somente depois de todas as fontes terminarem. Na evolução mensal de Vendedores,
cada mês inclui o ano quando o filtro atravessa anos.

Validação local, sem conexão à base do cliente:
`php tests/carregamento-progressivo.php`,
`node tests/carregamento-progressivo.js` e
`COTACOES_CHROME=/caminho/chrome-headless-shell python3 tests/carregamento-progressivo-browser.py`.

Em Regiões cadastradas, o nome de cada cliente abre uma linha abaixo com
orçamentos e pedidos mensais. Os dados vêm das mesmas fontes já consolidadas,
pelo cliente e vendedor principal; não há SQL adicional nem recálculo de cartões.
Clientes vazios usam meses esparsos no payload. A expansão preenche os meses
sem movimento com zero e mostra traço fora do filtro, sem notas fiscais.
O mês inclui o ano, permitindo distinguir períodos que atravessam anos.

Na liberação por usuário, root pode marcar **Usuário gestor**. A propriedade
`usuarios[id].gestor` é salva no arquivo de acessos, independente do JSON dos
indicadores. Ela habilita Gerenciar acessos sem dar visão de todos os indicadores
nem dispensar restrições por vendedor/equipe. O usuário administra somente os
indicadores liberados diretamente para ele; acessos herdados do perfil continuam
seguindo a regra de visualização existente. Root e Perfil responsável mantêm a
administração global anterior. Somente root modifica a nova flag.

Para a gestão limitada, `access` devolve apenas os indicadores desse escopo e
as respectivas regras. `saveAccess` verifica o escopo e mescla alterações com
os acessos atuais, preservando os outros indicadores, restrições e gestores.
O gestor não modifica a própria autorização nem concede uma visão mais ampla
que a sua. Uma restrição individual só pode ser delegada com a mesma restrição.
A API também protege chamadas manuais e tentativas de mudar a flag por omissão.

Para William, desmarcar o Perfil responsável usado anteriormente e marcar
Usuário gestor em seu usuário, mantendo os dois indicadores selecionados.
Não requer mudar o JSON de indicadores nem migrar dados. Validação local:
`php tests/access-user-manager.php` e
`COTACOES_CHROME=/caminho/chrome-headless-shell python3 tests/access-user-manager-browser.py`.

## Desempenho geral: detalhes e opções de filtros

`performance.php` é parte do motor. Atualize-o junto com
`custom-indicator-engine.php`, `data.php`, `export.php`, `dashboard.js` e
`index.html`. Não há alteração visual, migração ou edição dos JSONs.

O modal solicita somente seu detalhamento, mantendo a consulta, parâmetros,
limites e validações do fluxo completo. Edições que precisam atualizar a tela
continuam usando o fluxo completo. A exportação geral também permanece completa;
a exportação do detalhe pode usar o caminho específico. Indicadores com motores
próprios, cargas progressivas, multiempresa e clientes inativos preservam seus
fluxos existentes. A otimização é aplicada onde o detalhamento SQL compartilhado
é compatível, sem exigir identificação de um indicador específico.

Opções dinâmicas de filtros obtidas por SELECT simples em cadastros Dxxx e SMT029
podem ser reutilizadas por 30 segundos. A chave inclui o cliente, autorização,
empresa/usuário, acessos, configuração efetiva, SQL e parâmetros. Autorizações,
movimentos, resultados dos indicadores, consultas com subconsultas/funções de
tempo e cargas progressivas não entram nesse cache. Os arquivos ficam fora da
pasta pública, com diretório 0700 e arquivos 0600. Falhas de cache usam a consulta
normal; erros e opções inválidas não são armazenados. Se uma seleção não existir
nas opções em cache, o motor consulta novamente antes de rejeitar o filtro.

Para desativar separadamente no ambiente PHP, use
`HARDNESS_DASHBOARD_FILTER_CACHE_TTL=0` e/ou
`HARDNESS_DASHBOARD_DETAIL_FAST_PATH=0`. O TTL aceita de 0 a 60 segundos; sem
configuração, é 30. Novos cadastros podem levar esse intervalo para aparecerem
na lista, sem cache dos totais. A aparência e os atalhos do dashboard são os mesmos.

Validação sem base de cliente: `php dashboard-personalizado/tests/performance.php`
e `node dashboard-personalizado/tests/performance.js`. Os testes comparam os
resultados e SQL do detalhe nos dois caminhos, contagem de consultas, restrições,
isolamento/expiração do cache, cadastro novo e atualização completa após edição.

Os detalhes de calendário e barras comparativas também aceitam
`somenteDetalhamento=1`. O calendário executa somente o SQL de detalhe, com
o mesmo período próprio, parâmetros, ordenação e limite do caminho completo.
As barras consultam suas fontes e feriados pelo mesmo cálculo compartilhado,
sem recalcular cartões, composição, calendários ou listagem. O período das
fontes não é reduzido ao dia clicado: a projeção mantém dias úteis, feriados,
previsões e movimentos bancários. Os dois caminhos preservam as restrições
e usam o fluxo completo nos contextos incompatíveis já descritos acima.
`HARDNESS_DASHBOARD_DETAIL_FAST_PATH=0` também desativa essa ampliação.

Validação adicional sem banco:
`php dashboard-personalizado/tests/performance-detalhes.php` e
`node dashboard-personalizado/tests/performance-detalhes.js`.
Nas fixtures, calendário passa de seis consultas para uma e barras de sete
para três, com os mesmos detalhes, SQL e parâmetros. Isso mede chamadas,
não o tempo de execução em uma base de cliente.

## Execução progressiva geral por fonte

O executor compartilhado aceita `"carregamentoProgressivo": true` no objeto
do indicador. É opcional e não é ativado nos JSONs de clientes por esta entrega.
Instale também `carregamento-geral.php`, junto com
`carregamento-progressivo.php`, `carregamento-progressivo.js`,
`custom-indicator-engine.php`, `performance.php`, `export.php`, `dashboard.js`
e `index.html`. Não requer tabelas, migrações ou tarefas agendadas.
Para desativar o modo geral no ambiente PHP, use
`HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL=0`.

Cada chamada consulta uma fonte nova do plano efetivamente percorrido pelo
executor (opções dinâmicas, cartões/resumos, composição, gráficos, calendários,
listagem e blocos). Consultas concluídas são reutilizadas nas continuações da
mesma carga. A API retorna 202 até concluir o indicador e a tela apresenta os
resultados somente ao terminar. A revalidação de vínculos de acesso permanece
uma consulta adicional em cada chamada. Uma nova atualização inicia outra
carga; o token concluído permite exportar as mesmas fontes, sem novas consultas
de dados. Detalhes consultados depois são consultas novas, com as validações
normais; o caminho direto de detalhes continua disponível fora da carga ativa.

Esse modo preserva o SQL, parâmetros, ordenação e limites originais, inclusive
quando o SQL menciona as tabelas comerciais. Não aplica a identificação de
fontes financeiras nem a divisão mensal dos motores comerciais. Não fatia
automaticamente somas, médias, contagens distintas, filas ou subconsultas.
O timeout de cada SQL é de quinze segundos. Portanto, este recurso
reduz o trabalho repetido em continuações e evita acumular todas as consultas
na mesma requisição, mas não acelera sozinho uma fonte que já excede o timeout.
Dividir essa fonte em linhas/períodos exige um contrato específico de paginação
e recomposição e fica fora deste modo.

Os arquivos ficam fora da pasta pública, protegidos por diretórios 0700 e
arquivos 0600. Há trava exclusiva por token; uma chamada concorrente é recusada
antes de executar fontes. A assinatura inclui cliente, usuário, empresa,
configuração efetiva, vínculos, filtros, aba, UF, Pareto e contexto dos detalhes.
Cargas expiram em uma hora e também são invalidadas na mudança do dia. Fontes
corrompidas ou ausentes retornam erro; nenhuma soma parcial é apresentada.
O limite é de 100 fontes, 20.000 linhas e 8 MiB por fonte, e 64 MiB por carga.
Excessos falham explicitamente, sem truncar o resultado. Os limites de linhas
menores de cada componente continuam valendo. O modo geral não aumenta o
limite de memória do PHP nem impõe o horizonte comercial de dois anos.

O resultado concluído congela as fontes consultadas para exportação, mas as
consultas de etapas diferentes não compartilham uma transação MySQL. Movimentos
alterados durante a carga podem ser observados em instantes distintos. Habilite
somente depois de avaliar esse comportamento no indicador; quando for exigida
uma leitura transacional única entre todas as fontes, mantenha o modo geral
desativado. Motores comerciais e clientes inativos mantêm seus fluxos próprios.
Previsões manuais de caixa e saldo bancário também ficam fora deste modo, pois
possuem estado adicional além das fontes SQL. Indicadores com edição de títulos
ou inclusão de agendas no detalhamento mantêm o fluxo completo, preservando a
atualização após edição e evitando exportar uma carga anterior à alteração.

Validação local sem banco:
`php dashboard-personalizado/tests/carregamento-geral.php` e
`node dashboard-personalizado/tests/carregamento-geral.js`.
As fixtures comparam todas as saídas, consultas, parâmetros e tipos entre
execução completa e progressiva, exportação sem repetir fontes, isolamento,
revalidação de vínculos, concorrência, corrupção, expiração e limites.
