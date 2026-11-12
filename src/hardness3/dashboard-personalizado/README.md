# Dashboard personalizável

O dashboard é habilitado por empresa em **Configuração > Personalização >
DASHBOARD PERSONALIZAVEL**. A configuração original não habilita o menu; a aba
**DASHBOARD** aparece somente quando a empresa possui um JSON personalizado com
`habilitado: true` e ao menos um indicador válido e ativo.

Cada indicador é definido integralmente no JSON da empresa. Não é necessário
alterar `data.php` ou `dashboard.js` para criar um novo indicador.

Em bancos com mais de uma empresa, a opção **GERAL** é a configuração-base do
grupo. As abas das empresas permitem criar uma configuração específica, que
tem prioridade sobre a geral apenas naquela empresa.

## Acessos ao dashboard

O usuário ROOT (id 63) sempre possui acesso a todos os dashboards e é quem
libera o primeiro perfil responsável. No cabeçalho do dashboard, o ROOT e os
perfis responsáveis veem o botão **Gerenciar acessos**. A janela permite
liberar todos ou alguns indicadores para cada perfil.

Além dos perfis, a aba **Usuários** permite liberar um indicador diretamente
para uma pessoa. Indicadores com filtro de `vendedor` ou `representante`
permitem selecionar o vendedor daquele usuário. Nesse modo, o servidor força
o vendedor, usa o período padrão, bloqueia os filtros de período e vendedor e,
quando houver abas, libera apenas a aba de vendedores. CSV e PDF recebem a
mesma restrição.

Um usuário vê um indicador quando ele foi liberado para seu perfil. Perfis
responsáveis visualizam todos os indicadores da empresa e podem administrar os
demais perfis, mas apenas o ROOT pode incluir ou remover outro perfil responsável.

As permissões são salvas separadamente em
`personalizacaoConf/{empresa}DASHBOARD PERSONALIZAVEL-acessos.json`; portanto
não alteram o JSON de personalização nem os SQLs dos indicadores.

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

## Filtros

Tipos aceitos: `data`, `texto`, `numero` e `selecao`. O id do filtro vira um
parâmetro SQL com o mesmo nome, por exemplo `dataInicio` vira `:dataInicio`.
Para datas, os padrões `hoje`, `inicio_mes` e `inicio_ano` são reconhecidos.

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

As cores permitidas são `red`, `yellow`, `orange`, `blue` e `green`. A última
faixa pode omitir `ate`, recebendo todos os valores acima da faixa anterior.
Com `periodo: "ultimos_30_dias"`, o calendário usa seus próprios 30 dias,
independentemente dos filtros `dataInicio` e `dataFim` do restante do indicador.
