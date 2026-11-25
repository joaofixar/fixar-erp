# Estudo de viabilidade: um FX ERP escrito do zero

Estudo feito em 25/11/2026, só com leitura do repositório no commit `ab2a37a0`. Nenhum código foi alterado, nada foi executado contra o banco, nenhum contêiner subiu e `atualizacaoBase.php` não rodou. O modelo de dados vem de `banco/fotografia.sql` e do código.

Como ler as citações:

- Caminhos que começam por `prog/`, `bibliotecas/`, `configuracoes/`, `scripts/`, `sistema/`, `hardness/` e `rest_api/` ficam sob `src/hardness3/`.
- "fotografia" é `banco/fotografia.sql`.
- `arquivo:10–20` cita as linhas 10 a 20.
- O que não está provado no repositório vem marcado como **Hipótese**.

A Personalização, os segredos e os arquivos de outros clientes ficaram fora deste documento. Onde há credencial no código, o texto cita só o arquivo.

---

## 1. Resumo executivo

**Veredito.** Reescrever o ERP inteiro e trocar tudo de uma vez não vale a pena para uma equipe de duas pessoas. Vale a pena, sob condições, fazer o FX ERP novo crescer ao lado do Hardness, começando pela frente comercial (televendas, depois orçamento, depois pedido e crédito), lendo a mesma Base. A parte fiscal, a NF e o financeiro ficam no Hardness por tempo indeterminado.

**Por quê.**

- O comportamento de hoje está em cerca de 512 mil linhas de PHP de tela, 196 mil linhas de classes, 14 mil linhas de regras por tabela e 11 mil linhas de rotinas SQL (seção 4). Só a classe do orçamento tem 15.359 linhas e a da NF de saída, 19.830.
- A ordem de grandeza para refazer os módulos da lista é de 3 a 5 anos com duas pessoas (seção 9). Na troca única, a operação não recebe nada durante esse tempo.
- A Publicação está trazendo a reforma tributária: 72 funções de CBS e IBS já estão em `scripts/sql/`. Assumir a legislação fiscal é o custo permanente mais alto para duas pessoas.
- Crescendo ao lado, a primeira entrega útil para o televendas fica entre 6 e 12 meses, e cada fase pode parar sem perder o que já entrou.

**Condições para valer.**

1. Entregar antes o cronograma em curso, que termina em 13/03/2027 e exclui por escrito "reescrever o sistema do zero" (`cronograma-erp-fixar.html:179`, `:257`).
2. Registrar um ADR novo, porque esta proposta contradiz o ADR 0001 (seção 2).
3. Levantar com a operação quais telas são usadas de fato e inventariar a Personalização. O repositório não responde nenhuma das duas coisas.
4. Aprovar um protótipo de convivência antes de escrever módulo: sessão única, tela nova dentro de uma Aba do Shell e gravação na mesma Base sem pular as regras do Hardness (seção 8.3).
5. Dar a cada fase um critério de parada.

**Stack recomendada.** PHP atual com Laravel, telas em React com Inertia e Tailwind, o mesmo MariaDB 11, fila e agendador do próprio framework, WebSocket para os Alertas e a biblioteca de NF-e que o Hardness já declara. Tudo em `docker compose` sobre o Kiri. O motivo central é que as regras a reaproveitar estão em PHP e SQL de MariaDB, e duas pessoas não sustentam duas linguagens de servidor (seção 7).

**O que este estudo não consegue afirmar.** Que o sistema novo será mais rápido: não há medição de desempenho no repositório. Que o mapa funcional é o uso real: não há dado de produção nem registro de uso. E qual é o comportamento exato da Fixar hoje: ele é Hardness mais Personalização, e a Personalização ficou fora por regra do pedido e, na maior parte, fora do git.

---

## 2. O que a leitura corrigiu no pedido

1. **Esta proposta contradiz o ADR 0001.** O ADR aceito diz que "um trecho só é reescrito quando uma mudança da operação não cabe num ajuste localizado" (`docs/adr/0001-sistema-novo-nasce-do-legado.md:9`) e rejeitou "reescrever módulo a módulo antes de o legado subir", porque "estoque, nota, boleto e financeiro compartilham núcleo e tabelas; com duas pessoas a primeira tela atrasaria anos" (`:13`). O legado já subiu, o que muda o contexto. O argumento do núcleo compartilhado continua de pé e este estudo o confirma. A spec do visual novo também deixou fora de escopo "reescrever cada fluxo como página única" e "reescrever grade e formulário tela por tela" (`.scratch/visual-novo/spec.md:199–201`).
2. **Perfil e permissões não são C019/C029/C031.** Na fotografia e no código:
   - C019 liga Usuário, Empresa e Perfil (`fotografia:811–823`).
   - C021 é o Perfil (`fotografia:960–975`).
   - C029 é o catálogo de acessos (`fotografia:1118–1130`).
   - C030 liga Perfil e acesso, com `C030_Acesso_Liberado` (`fotografia:1353–1362`).
   - C031 não é permissão. É configuração chave e valor por Empresa (`fotografia:1590–1600`), carregada em `$g['C031']` (`sistema/index-framework.php:26`).
3. **N001 está morta como armazenamento, e isso se confirma.** Os Alertas vivem em arquivos serializados (seção 5.4).
4. **`sistema-legado/` não existe nesta máquina.** A leitura usou `src/hardness3` e `referencia/`.
5. **"Televendas" não é nome de tela no Hardness.** O termo só aparece como rótulo de um grupo de campos no cadastro de produto (`prog/cad/cad002/form.php:5038`). **Hipótese:** o televendas trabalha na tela C.R.M. (`prog/crm/crm001`), que reúne Cliente, orçamento, pedido, NF e agenda.
6. **O ADR 0004 fala em 195 rotinas; a lista hoje tem 252** (`scripts/atualizacaoBase-rotinas-lista.php`: 153 funções, 43 procedures, 56 triggers). O número do ADR ficou atrás.

---

## 3. Mapa funcional

### 3.1 O que dá para afirmar sobre uso

O repositório não prova quais telas a Fixar usa. A Base nasce sem linhas de negócio e sem retrato da produção (`docs/adr/0002-base-sem-personalizacao-e-sem-retrato.md`), e o Menu é o do Hardness, com Orçamentos, Pedidos e Boleto comentados (`bibliotecas/menu.php:181`, `:182`, `:218`). As evidências disponíveis são três:

- a lista de módulos do pedido deste estudo;
- o ciclo descrito para a Base: cadastro de Cliente e Fornecedor, produtos, orçamento, pedido, NF de saída, estoque, NF de entrada, expedição, contas a receber, contas a pagar, caixa e bancos, boleto, CFOP, NCM, ICMS, usuários e empresa (`.scratch/base-local/spec.md:66`);
- o percurso com a operação previsto para 22/11 a 12/12 no cronograma.

A mesma spec põe CRM e e-mail integrado fora do ciclo (`.scratch/base-local/spec.md:83`), e o pedido deste estudo os põe dentro. Essa diferença vai para as perguntas à operação.

Toda linha da tabela abaixo é, quanto ao uso, **Hipótese**. Quanto ao que existe no código, é fato citado.

### 3.2 Convenção de tela

Cada tela mora em `prog/<módulo>/<tela>/` com arquivos de nome fixo: `content.php`, `tab.php`, `grid.php`, `form.php`, `form_func-ajax.php`, `grid_func-ajax.php`, `grid_func-callback.php`, `grid_func-js.php`. O `content.php` divide a área (`prog/ven/ven005/content.php:11–26`), o `tab.php` declara as abas (`prog/ven/ven005/tab.php:12–31`) e o `grid.php` declara colunas e a SQL (`prog/ven/ven005/grid.php:23–70`). A classe de mesmo nome fica em `bibliotecas/classes/`.

### 3.3 Módulos

| Módulo | Entrada no Menu (`bibliotecas/menu.php`) | Telas em `prog/` | Classes (linhas) | Tabelas principais (linhas na fotografia) |
|---|---|---|---|---|
| Vendas e televendas | C.R.M., linha 18, acesso 104 | `crm/crm001` (14 arquivos, 933 KB), `crm/crm002`, `crm/crm003`, `crm/crm004`, `crm/crm005` | `CRM001` (219), `CRM005` (179). A lógica está nos arquivos de tela | D024 Cliente e Fornecedor (4506–4773), T003, T005, T007, R001 (13482–13562) |
| Orçamento | Orçamentos comentado, linha 181. Abre pela aba do C.R.M. (`prog/crm/crm001/tab.php:54`). Análise de Orçamento, linha 22 | `ven/ven001` (12 arquivos, 936 KB), `ven/ven005` | `VEN001` (15.359), `VEN005` (132), `gravarTotalizacaoT004.php` (1.225) | T003 (14592–14835), T004 itens (14902–15112), T004A (15125–15205), T089 parcelas (20839–20853), T250 alíquotas (26447–26461) |
| Pedido | Pedidos comentado, linha 182. Abre pelo C.R.M. (`tab.php:58`). Análise de Pedido, linha 23 | `ven/ven002` (12 arquivos, 856 KB), `ven/ven013` | `VEN002` (10.342), `VEN013` (147), `gravarTotalizacaoT006.php` (1.236) | T005 (15218–15522), T006 itens (15620–15879), T005A, T006A, T090 parcelas (20866–20888), T251 (26474–26488), T178 histórico de liberação (23467–23476) |
| Crédito | Análise de Crédito, linhas 73–74, acesso 39 | `fin/fin007` (8 arquivos, 84 KB) | `FIN007` (441), `VEN002::verificaCreditoLiberado` (`VEN002.php:6964`) | D024, T002, T005, T007, T090, T178 |
| Estoque e almoxarifado | Estoque, linha 28. Expedição, linha 29. Conferência Cega, linha 69. Divergências comentado, linha 189 | `est/est003`, `est/est002` (14 arquivos, 657 KB), `etc/etc016`, `est/est001`, `cad/cad002` (produto, 1.217 KB) | `EST002` (2.392), `EST003` (937), `EST001` (796), `CAD002` (14.074) | D001 (2944–3022), D009 (3608–3778), D049 (5460–5533), T001 movimento (14264–14377), T066 locação (19874–19902), D004 local (3234–3253), T238 separação (26134–26146) |
| Compras | Análise de Ordem de compra, linha 33. S.R.M., linha 26. Cotação, ordem e requisição comentadas, linhas 192–202 | `est/est016` (844 KB), `est/est015` (848 KB), `est/est006`, `est/est008`, `est/est014`, `srm/srm001` | `EST015` (9.925), `EST016` (6.845), `EST008` (1.978), `EST006` (1.803), `EST014` (1.747) | T218 e T219 (24521–24716, 24768–24956), T224 e T225 (25146–25383, 25444–25665), T011 e T012 (17178–17238, 17251–17296), T010, T173, T174, T189 requisição (23794–23819) |
| Financeiro | Contas a Receber, linha 70. Contas a Pagar, 71. Caixa e Bancos, 72. Boleto comentado, linha 218 | `fin/fin001` (652 KB), `fin/fin002`, `fin/fin003`, `fin/fin005` | `FIN001` (7.367), `FIN002` (3.353), `FIN003` (897), `FIN005` (1.907) | T002 a receber (14415–14554), T015 a pagar (17763–17875), T019 lançamentos (17992–18033), T020, T117 boleto (21688–21728), T035 remessa (18544–18560), D007 conta (3510–3574), D027 portador (4887–4922), D014 plano de contas (4032–4070) |
| NF | Notas Fiscais, linha 20. NF Loja, linha 21. NF Recebidas, linha 30 | `ven/ven003` (976 KB), `ven/ven012` (456 KB), `est/est004` (480 KB) | `VEN003` (19.830), `VEN012` (24.303), `EST004` (10.715), `gravarTotalizacaoT008.php` (1.789), emissor em `hardness/class/nfe4.class.php` (5.950) | T007 (16254–16602), T008 itens (16670–16909), T013 e T014 NF recebida (17309–17463, 17524–17669), T127 retorno da SEFAZ (22057–22076), T321 eventos (28865–28890), D006 natureza e CFOP (3314–3478) |
| CRM e agenda | Agenda de Contatos, linha 11. Emails, linha 19. Atividades e Agenda como abas do C.R.M. (`tab.php:46`, `:97`) | `etc/etc017`, `cad/cad013`, `etc/etc006`, `crm/crm001` | `ETC017` (96), `ETC006` (230), `CAD013` (26) | D085 contato (6827–6867), T259 tarefa (26718–26730), T260 leitura (26743–26751), T139 e-mail (22467–22519), D013 contato do Cliente (3952–3989) |
| Alertas do sino | Sem item de Menu. O Shell mostra o sino | `prog/sistema/funcoes/mensagens.php`, `removerNotificacao.php`, `scripts/notifications/` | `NOTIFICACOES` (3.345) | Arquivos serializados. N001 (12214–12238) não é usada |
| Chat | Sem item de Menu | `prog/sistema/funcoes/chat.php` (255 linhas) | Sem classe | T237 (26108–26121) |

Notas sobre a tabela:

- As abas do C.R.M. mostram o tamanho do televendas: Atividades, Orçamentos, Pedidos, Catálogo, Notas Fiscais, Clientes, Resumo, Agenda, Email, Faturar comercial, Faturar balcão, Receber, Compras/Produção, Tabela Preço e B2B (`prog/crm/crm001/tab.php:39–127`). Por Cliente: Tarefas, Orçamentos, Pedidos, Notas Fiscais, Produtos Vendidos, Agenda, E-mail, Extrato, Resumo, Tabela de preços e CNPJ Matriz (`tab.php:150–220`).
- Cliente e Fornecedor são a mesma tabela, D024, separados por `D024_Flag_Cliente` e `D024_Flag_Fornecedor` (`fotografia:4637–4638`).
- **Hipótese:** D001 é o produto, D049 é o produto por marca ou fornecedor e D009 é o item de estoque por Empresa. A base da hipótese é a estrutura: D049 aponta para D001 e D024, e D009 aponta para D001, D049 e C004 e guarda as quantidades (`fotografia:3608–3621`, `:5460–5470`).
- **Hipótese:** T218 e T219 são cotação de compra e itens, T224 e T225 são ordem de compra e itens. A base é o rótulo do Menu para `est015` e `est016` (`bibliotecas/menu.php:200–202`) e a coluna `T224_T218_Id` (`fotografia:25157`).
- Há dois fluxos de compra no código, o antigo (`est006`, `est008`, tabelas T010 a T012) e o novo (`est015`, `est016`). Só a operação diz qual está em uso.
- A fila de NF lê e apaga a tabela Q001 (`scripts/queueNF/queueNF.php:49–53`). Q001 não está na fotografia; a definição está em `src/hardness3/global.sql:26`.

---

## 4. Catálogo de regras

### 4.1 Onde as regras moram

| Lugar | Tamanho | O que guarda | Serve de especificação? |
|---|---|---|---|
| `configuracoes/<TABELA>.php` | 231 arquivos, 14.350 linhas. 128 com validação de campo, 86 com trava, 65 com gatilho | Validação de campo, valor padrão, trava de edição, gatilho PHP antes e depois de gravar, troca de Empresa | Sim, é a melhor fonte. É PHP legível, uma regra por bloco, organizado por tabela |
| `scripts/sql/` | 264 arquivos, 11.333 linhas. 252 rotinas na lista | Totalização, status, saldo de estoque, baixa de título, crédito antecipado | Sim. SQL puro, sem tela |
| `bibliotecas/classes/` | 144 arquivos, 196.421 linhas | Fluxo: gerar pedido, alçada, crédito, faturar, emitir | Em parte. A regra está lá, misturada com SQL concatenada, `$g` e mensagem em HTML |
| `prog/` | 1.306 arquivos, 512.076 linhas | Colunas de grade, SQL de listagem, filtro por alçada, botões | Pouco. Serve para saber o que a tela mostra, não como regra |
| `referencia/static/js/sistema.js` | 12.699 linhas | Abertura de Janela, grade, formulário, sino | Não. É mecânica de interface |
| `sistema/` (núcleo ofuscado) | `interfaceUsuario.php` com 265 KB e outros | Automação de formulário e banco, carga das regras | Não. Lê-se pela cópia em `referencia/sistema/` |

Como o núcleo aplica as regras por tabela: carrega `configuracoes/<TABELA>.php` (`referencia/sistema/confSistema.php:28`) e executa cada bloco com `eval` (`:267` validação, `:386` trava, `:477` gatilho). Há também arquivos por banco e por Empresa em `configuracoes/configuracoesPorBanco/` (`:37`, `:46`).

### 4.2 Graus de acoplamento usados abaixo

- **Baixo:** SQL puro ou bloco declarativo. Dá para reescrever lendo só o trecho.
- **Médio:** depende de `$g`, de chave da C031 ou de gatilho em cascata.
- **Alto:** método dentro de classe de milhares de linhas, com SQL concatenada, `$g` e mensagem de tela no meio.

### 4.3 Catálogo inicial

| # | Regra | Onde está | Acoplamento |
|---|---|---|---|
| 1 | Orçamento nasce com forma de pagamento "28 DIAS", frete "A COMBINAR" e validade "5 DIAS" | `configuracoes/T003.php:25–28` | Baixo |
| 2 | Orçamento exige Cliente com tipo e lista de preço | `configuracoes/T003.php:59–77` | Baixo |
| 3 | Campo de expedição é obrigatório quando a C031 `modeloExpedicao` vale 2 | `configuracoes/T003.php:83–87` | Médio |
| 4 | Situação do orçamento (N, A, L, P, C, F) decidida por perda e por existência de pedido | `configuracoes/T003.php:118–141`. Rótulos em `scripts/sql/T003_Status_Orcamento.sql:6–26` | Médio |
| 5 | Orçamento que já gerou pedido não cancelado fica travado | `configuracoes/T003.php:202–214` | Baixo |
| 6 | Totalização do orçamento e dos itens ao gravar | `configuracoes/T003.php:113–169`, `scripts/sql/T003_Gravar_Totalizacao_4.sql` (404 linhas), `scripts/sql/T004_Gravar_Totalizacao_4.sql` (415 linhas) | Médio |
| 7 | Comissão do representante e frete recalculados ao gravar o orçamento | `configuracoes/T003.php:143–167` | Alto |
| 8 | Alçada de desconto: vendedor e supervisor têm teto; diretor não passa por liberação; liberação anterior vale se o total não mudou | `bibliotecas/classes/VEN001.php:7358–7477` | Alto |
| 9 | Fila de análise de orçamento filtrada pela Empresa e pela alçada do Usuário | `prog/ven/ven005/grid.php:61–69` | Médio |
| 10 | Crédito do pedido: dispensa por pagamento à vista ou portador liberado; bloqueio por duplicata vencida, crédito marcado como não liberado, saldo de limite negativo e pré-cadastro; limite somado por CNPJ matriz; motivo gravado no histórico | `bibliotecas/classes/VEN002.php:6970–7106` | Alto |
| 11 | Saldo de limite = limite − (vencido + a vencer + pedidos em aberto sem NF) | `bibliotecas/classes/VEN002.php:7050–7072` | Alto |
| 12 | Crédito antecipado e crédito de troca do Cliente somados dos títulos a receber e a pagar | `scripts/sql/D024_Gravar_Totalizacao.sql:16–81` | Baixo |
| 13 | Cliente inativo volta a ativo quando recebe contato, orçamento, pedido ou NF, conforme quatro chaves da C031 | `scripts/sql/D024_Ativar_Cliente.sql:35–58` | Baixo |
| 14 | Situações do pedido, 26 códigos numéricos com rótulo | `scripts/sql/T005_Status_Pedido.sql:5–84` | Baixo |
| 15 | Quantidade empenhada em pedido, por situação do pedido e por natureza que movimenta estoque | `scripts/sql/D009_Quantidade_Pedido.sql:4–24` | Médio |
| 16 | Estoque real, líquido, de loja e compra de emergência recalculados a cada alteração do item | `scripts/sql/D009_trigger_estoque_real.sql:1–8`, `scripts/sql/T006_estoque_real_inclusao.sql:1–4` | Médio |
| 17 | Baixa de título a receber: juros, desconto, recebido, saldo e data de baixa | `scripts/sql/T002_Baixar_Titulo.sql:6–45` | Baixo |
| 18 | NF emitida cria contato de CRM, tarefa e marca de leitura | `scripts/sql/T007_trigger_after_insert.sql:6–19` | Médio |
| 19 | Contato manual ativa o Cliente; edição de contato travada sem o acesso 238 | `configuracoes/D085.php:27–30`, `:104–113` | Baixo |
| 20 | Quem vê um Alerta: destinatário, alçada, acesso do Perfil, configuração da Empresa, vínculo com a Empresa e não lido | `prog/sistema/funcoes/mensagens.php:252–293` | Médio |
| 21 | Alertas só são gerados em dia útil das 7h às 18h e sábado das 7h às 13h | `scripts/notifications/notificacoes.php:16–26` | Baixo |
| 22 | Validação de CNPJ, CPF, duplicidade e chave estrangeira no cadastro | `bibliotecas/restricoes.php:74–244`, `configuracoes/D024.php:61–126` | Baixo |
| 23 | Menu mostra só o item cujo acesso está no Perfil | `sistema/index-framework.php:172`, `bibliotecas/menu.php` | Médio |
| 24 | Troca de Empresa de um orçamento ou pedido leva os itens junto | `configuracoes/T003.php:218–264`, `configuracoes/T005.php:235–253` | Médio |

Três observações que valem para o catálogo inteiro:

- Estas são regras do Hardness. O glossário diz que a Personalização "cobre orçamento, pedido, boleto, histórico de produto e a totalização que os acompanha" (`GLOSSARY.md:51–52`). Logo, nas linhas 6 a 11 o comportamento da Fixar hoje pode ser outro. O cronograma confirma que "a totalização é o ponto central" do ajuste à Fixar (`cronograma-erp-fixar.html:234`).
- Parâmetros de regra estão espalhados em 634 chaves distintas da C031 lidas pelo código, mais cerca de 100 marcas por Empresa na C020 (`fotografia:838–945`). Uma regra só vira especificação junto com o valor dessas chaves na produção.
- A rotina da linha 13 fixa a Empresa em 1, com o cálculo por documento comentado (`scripts/sql/D024_Ativar_Cliente.sql:10–33`). Regra reaproveitada precisa de revisão, não de cópia.

---

## 5. Modelo de dados

### 5.1 Números da fotografia

- 775 tabelas. Por prefixo: T 350, D 216, C 44, P 43, MCP 19, DIM 13, F 12, GNRE 11, INT 8, WHK 7, S 7, PCP 7 e outros.
- 698 em InnoDB e 77 em MyISAM. Em MyISAM estão, entre outras, T004, itens do orçamento (`fotografia:14902–15112`), C007, Usuário (`:396–509`), e T139, e-mail (`:22467–22519`).
- Conjuntos de caracteres misturados: latin1 em 400 tabelas, utf8mb3 em 340, utf8mb4 em 31, cp1250 em 4. A conexão pede `utf8` (`bibliotecas/conexaoBanco.php:48–49`).
- 77 chaves estrangeiras declaradas, em 40 tabelas. O resto dos vínculos existe só por nome de coluna, no padrão `X_<TABELA>_Id`.
- 1.343 colunas `_Flag_`, 262 colunas `enum('S','N')` e 204 colunas com data padrão `0000-00-00`.
- Tabelas largas: T007 com 306 colunas, T005 com 272, D024 com 232, T006 com 222, T003 com 211.
- Há uma `T006_CRASH` com 219 colunas ao lado da T006.

### 5.2 Nomenclatura

Letra e número: C para sistema e configuração, D para cadastro, T para movimento, R para tabelas de relatório. Coluna no formato `<TABELA>_<Nome>`. Sufixo `A` para extensão um-para-um da tabela larga (T004A, T005A, T006A, T008A). O nome não diz o que a tabela é; só 142 das 775 têm comentário.

### 5.3 Entidades centrais

| Entidade | Tabela | Observação |
|---|---|---|
| Empresa | C004 (`fotografia:176–332`), C020 marcas por Empresa (`:838–945`), C031 chave e valor por Empresa (`:1590–1600`) | C004 em cp1250 |
| Usuário | C007 (`:396–509`) | Alçada em `C007_Flag_Vendedor` (`:432`) |
| Perfil e acesso | C021, C029, C030, C019 | Ver seção 2, item 2. C030 não tem chave estrangeira |
| Cliente e Fornecedor | D024 (`:4506–4773`), contatos em D013 | Limite e crédito em `D024_Valor_Limite` (`:4608`), `D024_Flag_Liberado` (`:4605`), `D024_Cnpj_Matriz` (`:4694`) |
| Produto e estoque | D001, D049, D009, T001, T066 | Saldos gravados em D009 por gatilho |
| Orçamento | T003, T004, T004A, T089, T250 | Situação em `T003_Flag_Status_Orcamento` (`:14723`) |
| Pedido | T005, T006, T005A, T006A, T090, T251, T178 | Situação em `T005_Flag_Status`, inteiro (`:15296`). Vínculo com o orçamento em `T005_T003_Id` (`:15229`) e `T006_T004_Id` (`:15628`) |
| NF de saída | T007, T008, T008A, T127, T321 | Vínculo com o pedido em `T007_T005_Id` (`:16262`) |
| NF de entrada | T013, T014 | |
| Título a receber e a pagar | T002, T015, T019, T020, T117 | |
| Contato de CRM | D085, T259, T260 | D085 aponta para T003, T005, T007 e D024 |
| Auditoria | T162 (`:23058–23074`) | Guarda a consulta e o registro alterado |

### 5.4 N001 e os Alertas

N001 existe (`fotografia:12214–12238`) e nenhum código insere nem lê suas linhas. Só há `UPDATE N001 SET N001_Flag_Excluido` (`bibliotecas/classes/NOTIFICACOES.php:1125`, `:1794`, `:2123` e outras) e DDL em `scripts/atualizacaoBase.php:7047–7066`. `NOTIFICACOES::processar` monta um vetor com nomes `N001_*`, inclusive três que não são colunas (`N001_Cor`, `N001_Prioridade`, `N001_Permite_Ocultar`), e chama `gravarNotificacaoArquivo`, que serializa em arquivo `cacheN001Notificacoes_<id>.txt` na pasta de dados (`NOTIFICACOES.php:3088–3122`, `:3183–3215`). A versão que gravaria em memória está comentada (`:3167–3181`) e o comentário do método ainda fala em gravar na N001 (`:3084–3087`). A leitura percorre a pasta (`prog/sistema/funcoes/mensagens.php:184–235`) e a marca de lido é gravada dentro do mesmo arquivo (`prog/sistema/funcoes/removerNotificacao.php:133–190`).

### 5.5 Multiempresa

- 212 tabelas têm coluna `_C004_Id`. A separação por Empresa é uma coluna, mais um `where` escrito à mão em cada consulta (`prog/ven/ven005/grid.php:61`).
- O vínculo Usuário, Empresa e Perfil é a C019, com chave única nos três (`fotografia:811–823`). O login carrega as Empresas do Usuário em `$g['C019']` e os acessos do Perfil em `$g['c029Ids']` (`sistema/index-framework.php:26`).
- O estoque é por Empresa: D009 tem `D009_C004_Id` (`fotografia:3614`), e o empenho compara a Empresa do item com a do estoque (`scripts/sql/D009_Quantidade_Pedido.sql:15`).
- Há Empresa fixa no código: `scripts/sql/D024_Ativar_Cliente.sql:33` usa a Empresa 1, e o gerador de Alertas roda como Usuário 63 na Empresa 1 (`scripts/notifications/notificacoes.php:34–35`).
- O banco não impõe o isolamento. Uma consulta sem o `where` vê todas as Empresas.

### 5.6 Tabelas de prefixo P e a Personalização

A fotografia tem 43 tabelas de prefixo P (entre as linhas 12263 e 13288), várias com comentário que cita a Fixar. **Hipótese:** pertencem à Personalização. Não foram descritas aqui. O mesmo vale para os 6 arquivos versionados em `outros/personalizacoes/fixar/`. O carregador de Personalização lê a pasta de dados de cada banco, fora do git (`referencia/sistema/personalizacoes.php:16–20`), de modo que a maior parte dela não está neste repositório.

### 5.7 O que manter e o que normalizar

Manter enquanto os dois sistemas convivem:

- O MariaDB 11 e as tabelas do Hardness como estão. O sistema novo lê por consultas próprias, sem alterar coluna.
- As chaves primárias numéricas, para que um orçamento tenha o mesmo número nos dois lados.
- C019, C021, C029 e C030 como fonte do Perfil, até o FX ERP assumir o login.
- As rotinas SQL de totalização e de saldo, chamadas como estão, enquanto a tabela for gravada pelos dois lados.

Normalizar quando o FX ERP virar dono de uma tabela:

- Nome que diz o que é (`orcamento`, `orcamento_item`) no lugar de T003 e T004.
- Chave estrangeira declarada. Hoje são 77 para 775 tabelas.
- utf8mb4 e InnoDB em tudo.
- Data nula no lugar de `0000-00-00`; booleano no lugar de `enum('S','N')`; situação como lista fechada com nome, no lugar de letra e de número (regras 4 e 14).
- Tabela larga dividida por assunto. T005 tem 272 colunas e ainda uma T005A.
- Configuração tipada e versionada no lugar das 634 chaves da C031 em `longblob`.
- Alerta em tabela, com destinatário, leitura por Usuário e índice. A N001 mostra a intenção e não a forma: o vetor em uso tem campos que a tabela não tem.
- Saldo de estoque calculado do movimento (T001), não por gatilho que regrava D009.
- Empresa como regra do banco ou da camada de dados, não como `where` manual.

---

## 6. Arquitetura atual e dívidas

### 6.1 Sessão e estado global

- Login por sessão em arquivo. `sistema/index-framework.php:2` abre a sessão e aceita o identificador de sessão vindo da requisição. As linhas 3 a 18 gravam Usuário, Perfil, Empresa e alçada em `$_SESSION`.
- `bibliotecas/sessao.php:51–91` copia a sessão para `$g` e para variáveis soltas com `$$variavel`.
- A trava do arquivo de sessão é solta cedo, em `sistema/index-framework.php:66`, para todo programa menos `prog/api/api.php`. Por isso a sessão em arquivo não deve enfileirar as requisições de um mesmo usuário; o limite dela é prender a produção a um contêiner.
- A produção só pode ter um contêiner web: "a sessão em arquivo e o APCu não se repartem" (`docker-compose.prod.yml:1`). O limite é de 20 processos (`docker/php/mpm-prefork.prod.conf:6`) com 192 MB cada (`docker/php/php.prod.ini:9`).
- `$g` é o estado de tudo: 827 arquivos com `global $g` e 74.244 ocorrências de `$g[`. A permissão é checada em linha, `$g['c029Ids'][N]`, 335 vezes em 81 arquivos, com 121 números distintos.
- `index-framework.php:2` faz `extract($_REQUEST …)` com prefixo `r_`, e toda tela lê `$r_…`.
- O Usuário 63 é raiz por número fixo (`index-framework.php:26`; `bibliotecas/menu.php:138–156`), e o Menu dele tem um item que executa SQL (`menu.php:143`).

### 6.2 Banco

- Uma conexão `mysqli` por requisição, com apelidos `mysql_*` por cima (`bibliotecas/conexaoBanco.php:48–61`; apelidos reaplicados por `bin/adaptar-hardness.py:49–82`).
- 15.021 chamadas de `mysql_query` ou `mysqli_query` em `prog/` e `bibliotecas/classes/`, com SQL montada por concatenação de variável (exemplo em `bibliotecas/classes/VEN002.php:6973`). Nessas duas pastas há uma única consulta preparada e 3.123 escapes feitos à mão com `mysql_real_escape_string`. O valor que não passa por escape entra direto na SQL.
- Regra em três camadas que se chamam: gatilho PHP em `configuracoes/`, que chama procedure, que dispara gatilho de banco (`configuracoes/T003.php:113–169`; `scripts/sql/D009_trigger_estoque_real.sql`).
- Credencial de banco por Usuário guardada na sessão (`bibliotecas/conexaoBanco.php`). Senha antiga aceita em formato reversível ao lado do formato atual (`bibliotecas/segurancaSenha.php:44`).

### 6.3 `eval` no servidor e no navegador

- Servidor: as regras por tabela e a Personalização passam por `eval` (`referencia/sistema/confSistema.php:267`, `:386`, `:477`; `referencia/sistema/personalizacoes.php:370`, `:1052`; `referencia/sistema/automacaoBanco.php:172`, `:223`, `:909`; `referencia/sistema/interfaceUsuario.php:151`, `:1871`). Há mais 46 chamadas em `prog/` e nas classes.
- Navegador: o servidor devolve JavaScript em texto e o cliente executa. São 16 chamadas de `eval` na versão legível (`referencia/static/js/sistema.js:1004`, `:1019`, `:1062`, `:3184` e outras). O sino funciona assim: a resposta é uma chamada `alertAdicionar(...)` com uma função dentro (`prog/sistema/funcoes/mensagens.php:301–310`).
- O arquivo servido é uma linha ofuscada de 422.480 bytes. A leitura depende da cópia em `referencia/`.

### 6.4 Consulta periódica

Todos em `referencia/static/js/sistema.js`:

- Empresa atual a cada 10 s (`:975–991`).
- Alertas a cada 20 s, com `eval` da resposta (`:994–1054`).
- Chat a cada 60 s (`:1057–1069`).
- Laço a cada 2 s que recarrega grades visíveis com mais de 1 h (`:1072–1087`).

Cada consulta de Alertas abre sessão, carrega a configuração e percorre a pasta de arquivos (`prog/sistema/funcoes/mensagens.php:184–235`). Com N usuários abertos, são 3N requisições por minuto só de sino, num teto de 20 processos. **Hipótese:** isso pesa no desempenho percebido. Não há medição no repositório.

### 6.5 Processos de fundo

- `scripts/notifications/notificacoes.php` foi feito para ser chamado por `lynx -dump` no crontab (`:4–10`), percorre uma pasta de vários sites (`:42–49`) e gera os Alertas (`:82–84`).
- A fila de NF é um laço de shell que chama o PHP a cada segundo (`scripts/queueNF/queueNF.sh:1–6`), em cinco etapas (`scripts/queueNF/etapa1-…` a `etapa5-…`).
- `scripts/crontab/` tem as cargas diária e semanal e as tabelas de relatório.
- Nenhum desses está ligado no `docker-compose.yml` nem no `docker-compose.prod.yml`. **Hipótese:** na produção atual rodam por crontab do servidor.

### 6.6 PHP 8

O Hardness declara PHP 7.4 (`bibliotecas/composer/composer.json:16`) e roda em 8.3 (`docker/php/Dockerfile:1`) por duas camadas:

- `docker/php/compatibilidade.php`, carregado antes de tudo (`docker/php/php.ini:17`): silencia erros (`:3–9`), recria `each()` (`:11–31`), faz ponte de `apc_*` para `apcu_*` (`:33–88`), devolve as funções de aspas mágicas que o PHP removeu, respondendo sempre falso (`:90–102`) e, só no ambiente local, bloqueia saída de rede (`:104–145`; `docker/php/php.ini:19`).
- `bin/adaptar-hardness.py`, reaplicado a cada Publicação: troca `$x{0}` por `$x[0]` (`:309`), `=& new` (`:259`), `__autoload` (`:255`), constantes sem aspas (`:257`), apelidos de consulta (`:49`), correções pontuais em arquivos (`:167`) e, na seção `AREA` (`:212`), desliga as duas verificações de boleto do fornecedor e injeta a área do Shell. Sai com erro se uma âncora sumir.

### 6.7 Fluxo de Publicação

- `bin/publicacao olhar` lista os commits depois do SHA em `bin/ponto` (`bin/publicacao:281`).
- `bin/publicacao trazer` faz o merge de três vias numa branch `publicacao/<sha>` (`:427–494`), devolve as exclusões (`:329`), reaplica as adaptações (`:374`) e avança o ponto.
- Personalização, segredo e arquivo de outro cliente ficam fora (`:103`).
- A primeira busca trouxe mais de 600 commits (`docs/adr/0005-publicacao-entra-por-olhada-e-merge.md:7`).
- Coluna e índice ausentes entram na subida do contêiner; configuração não entra (`docs/adr/0007-coluna-e-indice-entram-na-subida.md`).
- `scripts/atualizacaoBase.php` tem 17.524 linhas de alteração de estrutura acumulada.

O fluxo funciona e é cuidadoso. O custo dele cresce com cada linha que a Fixar altera dentro de arquivo da Publicação. É o principal argumento para o código novo nascer fora de `src/hardness3`, como o Shell já faz (`docs/adr/0006-shell-fora-dos-arquivos-da-publicacao.md`).

### 6.8 Docker

- Local: `web` em PHP 8.3 com Apache e `db` em MariaDB 11, com rede interna sem saída (`docker-compose.yml:3–54`). O banco nasce da fotografia e recebe estrutura e rotinas ausentes (`docker/db/subir.sh`).
- Produção: Coolify, um `web` e um `db`, com 3 GB e 2,5 GB (`docker-compose.prod.yml:22`, `:45`), volumes para dados de usuários, sessões e banco (`:34–35`, `:54`).
- Não há serviço de fila, de agendador nem de tempo real.

### 6.9 Arquivos com credencial

Só o nome, sem valor: `bibliotecas/confUsuario.php`, `configuracoes/sysConfig.php`, `src/hardness3/ide/sysConfig.php`, `sistema/index-framework.php` (chave fixa de cifra de cookie), `outros/login/index.php` (comparação com senhas de suporte), `.env`, `bin/preparar-base.py`, `docker-compose.yml`, `docker/php/gravar-conf.php`.

### 6.10 Visual novo: o que está decidido e como encaixa

Decidido nos ADRs 0006 a 0011 e na spec (`.scratch/visual-novo/spec.md`):

- O Shell mora em `src/fx/`, fora dos arquivos da Publicação; `index.tpl` só recebe um gancho reaplicado pelo adaptador (ADR 0006; `bin/adaptar-hardness.py:363–397`).
- Tailwind v4 sem Preflight, com prefixo `tw:` e CSS gerado fora da imagem PHP (ADR 0008).
- Tokens nascem em `@theme`; Densidade em três passos, 12, 13 e 14 (ADR 0009).
- Vocabulário de cor por papel (ADR 0010) e ícone de ação por máscara (ADR 0011).
- Sidebar com marca FX ERP, Empresa e Menu do Perfil em seções; topo com lupa, sino, Apps e avatar; faixa de Abas; Paleta que busca só o Menu; um Tema.
- O sino mantém o comportamento do Hardness. A geração do Alerta ficou fora.
- Tentou-se trocar a posição absoluta dos formulários por grade no cadastro de Cliente, e a mudança foi recuada (`.scratch/visual-novo/issues/37-o-formulario-ganha-tema-e-o-de-cliente-vira-grade.md:8`).

Como encaixa num sistema novo:

- Reaproveita direto: tokens, Densidade, vocabulário de papéis, ícones, o desenho de sidebar, topo, Abas, Paleta e cartão de login. São decisões de produto, não de PHP.
- Reaproveita com ajuste: o prefixo `tw:` e a ausência de Preflight existem para conviver com o CSS do Hardness. Numa tela nova em documento próprio, deixam de ser necessários. Enquanto a tela nova abrir dentro do Shell atual, continuam valendo.
- Não reaproveita: `src/fx/shell.js` (81 KB) é feito para dirigir as Janelas do Hardness. Num sistema novo, o Shell é reescrito como componente, com o mesmo desenho.
- O episódio do formulário de Cliente é a evidência mais direta de que o Miolo do Hardness não se moderniza por tema. Tela nova de verdade exige tela escrita de novo.

---

## 7. Stack proposta

### 7.1 Restrições que decidem

- Duas pessoas em T.I., que também mantêm a produção (`docs/adr/0001-sistema-novo-nasce-do-legado.md:7`).
- As regras a reaproveitar estão em PHP e em SQL de MariaDB (seção 4).
- O repositório já tem PHP 8.3, MariaDB 11, Tailwind v4, testes em shell, PHP e `.mjs`, e implantação por `docker compose` no Coolify.
- **Hipótese:** a operação é de dezenas de usuários simultâneos, em horário comercial, com digitação intensa de orçamento por teclado. O número real é pergunta em aberto.

### 7.2 Duas opções comparadas

| Critério | A. PHP com Laravel, React por Inertia | B. TypeScript de ponta a ponta (NestJS e React) |
|---|---|---|
| Portar regra do Hardness | PHP para PHP. Dá para rodar a regra antiga e a nova no mesmo processo e comparar o resultado | Tradução entre linguagens, sem comparação lado a lado |
| NF-e, DANFE, eventos | `nfephp-org/sped-nfe` e `sped-da`, que o Hardness já declara (`bibliotecas/composer/composer.json:8`, `:11`) | Ecossistema fiscal brasileiro mais fraco. Na prática pede um serviço à parte em PHP ou uma API paga |
| Boleto e remessa | Há bibliotecas PHP de boleto e CNAB. A cobertura dos bancos da Fixar precisa ser conferida | Mesma ressalva, com menos opções |
| Linguagens que a equipe mantém | PHP no servidor, TypeScript na tela | TypeScript nos dois, mais PHP enquanto o Hardness existir |
| Convivência com o Hardness | Mesmo tipo de contêiner, mesma sessão PHP, mesmo banco | Ponte de sessão entre runtimes |
| Fila, agendador, tempo real | Do próprio framework: fila em banco ou Redis, agendador, servidor WebSocket | BullMQ com Redis e gateway WebSocket. Mais peças para montar |
| Tipagem de ponta a ponta | Parcial. Tipos gerados para a tela | Forte |
| Tela rica e teclado | Igual nas duas: React | Igual |
| Risco para duas pessoas | Menor. Menos peças e uma só linguagem de regra | Maior. Três runtimes durante a convivência |

Uma terceira opção, .NET, tem as bibliotecas fiscais mais maduras do mercado brasileiro, mas soma uma linguagem nova às duas que a equipe já precisa manter. Não foi adiante.

### 7.3 Recomendação: opção A

| Camada | Escolha | Justificativa para esta equipe |
|---|---|---|
| Backend | PHP atual com Laravel, em monólito modular: uma pasta por módulo (`Comercial`, `Credito`, `Estoque`…), sem microsserviço | Uma implantação, uma base de código. Módulo com fronteira clara é o que permite crescer ao lado do Hardness |
| Frontend | React com TypeScript, servido por Inertia, com Tailwind v4 e os tokens de `src/fx/` | Sem API separada para manter. Grade e teclado pedem componente de cliente. Os tokens e a Densidade já estão decididos |
| Banco | MariaDB 11, o mesmo da Base. Tabelas novas com prefixo próprio, em utf8mb4 e InnoDB, com chave estrangeira | Ler a mesma Base exige o mesmo motor. As 252 rotinas são dialeto MariaDB. Trocar de motor junto com a reescrita dobra o risco. PostgreSQL só volta à mesa depois que o Hardness sair |
| Autenticação | Durante a convivência, o FX ERP confia na sessão do Hardness por um ponto de ponte em `src/fx/` e lê Perfil e acessos de C019, C021 e C030. Depois, inverte: login no FX ERP, com senha só em formato atual, e segundo fator | Um login para a operação. Sem duplicar cadastro de Usuário |
| Permissão | Acesso com nome (`orcamento.liberar`) mapeado para o número da C029 enquanto conviver | Tira os 121 números mágicos do código novo sem quebrar o cadastro de Perfil |
| Filas e jobs | Fila do framework em tabela do banco no início; Redis quando o volume pedir. Agendador do framework no lugar de crontab com `lynx` | Zero infraestrutura nova no começo. Tentativa, repetição e registro de falha vêm prontos |
| Tempo real para Alertas | Alerta em tabela; entrega por WebSocket em serviço próprio; consulta periódica só como reserva | Tira 3 requisições por minuto por usuário do servidor web. Serviço próprio não ocupa os processos do Apache |
| Relatórios | Consulta sobre o mesmo banco, com exportação para planilha e PDF em job. Tabelas de relatório pré-calculadas só onde medir lento | Relatório é onde mais se gasta e menos se sabe do uso. Entra por demanda |
| NF-e | Fica no Hardness. Quando e se migrar, `sped-nfe` em job de fila, com as etapas de hoje como estados | Não assumir legislação antes da hora |
| Testes | Teste de caracterização: a mesma entrada passa pelo Hardness na Base e pelo código novo, e os valores gravados são comparados. Teste de contrato contra a fotografia | É o que transforma o Hardness em especificação executável. `testes/orcamento-pedido-nota.sh` já percorre a Base |

Ressalvas sobre esta recomendação: versões e bibliotecas de boleto precisam ser conferidas na hora de adotar. **Hipótese:** o emissor em uso hoje é o próprio do Hardness (`hardness/class/nfe4.class.php`, chamado a partir de `scripts/queueNF/queueNF.php:13`), com `sped-nfe` usado em partes (`hardness/class/nfe4_ext.class.php`). Qual dos dois assina e transmite não foi verificado.

### 7.4 Implantação em docker compose

Serviços, todos com `docker compose` sobre o Kiri, sem Docker Desktop:

```
web         Hardness, como hoje (PHP 8.3 + Apache)
fx          FX ERP novo (PHP + servidor de aplicação)
fx-fila     mesmo código do fx, executando a fila
fx-agenda   mesmo código do fx, executando o agendador
fx-tempo    servidor WebSocket dos Alertas
db          MariaDB 11, o mesmo banco
```

Um proxy na frente entrega um prefixo de endereço ao `fx` e o resto ao `web`. `fx`, `fx-fila` e `fx-agenda` saem da mesma imagem. O CSS e o JavaScript são gerados fora da imagem PHP, como o ADR 0008 já decide para o Shell.

### 7.5 Sobre "desempenho melhor"

Não há medição no repositório, então qualquer promessa é **Hipótese**. As causas prováveis de lentidão que o código mostra:

- consulta periódica de sino, chat e Empresa (seção 6.4);
- teto de 20 processos (seção 6.1);
- função SQL chamada por linha de grade (`prog/ven/ven005/grid.php:30`);
- gatilho que recalcula saldo com subconsulta a cada alteração (`scripts/sql/D009_trigger_estoque_real.sql:3–7`);
- tabelas MyISAM em caminho de escrita, como os itens do orçamento.

Várias dessas se corrigem dentro do Hardness, sem reescrever nada. Medir antes é o primeiro próximo passo. A T162 guarda tempo de execução por consulta (`fotografia:23058–23074`), e o Hardness traz uma pasta `observability/`.

---

## 8. Estratégia de migração

### 8.1 Três caminhos

| | Seguir o ADR 0001 | Crescer ao lado (strangler fig) | Trocar de uma vez (big bang) |
|---|---|---|---|
| O que é | O Hardness continua e diverge onde a operação pedir | O FX ERP assume um módulo por vez, lendo a mesma Base | O FX ERP só entra quando fizer tudo |
| Primeira entrega | Contínua | 6 a 12 meses | 3 a 5 anos |
| Risco de virada | Nenhum | Pequeno, por módulo | Um só, total |
| Migração de dados | Nenhuma | Nenhuma no começo; por tabela, quando mudar o dono | Completa, num fim de semana |
| Tela nova de verdade | Não. O Miolo muda só pelo Tema | Sim, nos módulos assumidos | Sim, em tudo |
| Custo de dois sistemas | Não há | Há, por anos | Há, escondido: o Hardness segue recebendo ajuste enquanto o novo não fica pronto |
| Pode parar no meio | Sim | Sim. O que entrou continua valendo | Não. Parar é perder tudo |

A troca de uma vez é a pior para duas pessoas: o período sem entrega é o mais longo, a legislação fiscal não espera e a virada concentra todo o risco num dia. Fica descartada.

A escolha real é entre seguir o ADR 0001 e crescer ao lado. Crescer ao lado só se justifica se a operação precisar de tela e fluxo que o Hardness não entrega por ajuste. O recuo do formulário de Cliente sugere que essa necessidade existe para a frente comercial.

### 8.2 Desenho do crescimento ao lado

Ordem proposta, do menor risco para o maior:

1. **Fundação.** Login por ponte, Perfil lido da Base, tela nova dentro de uma Aba, Alertas em tabela com tempo real.
2. **Televendas, só leitura.** Ficha do Cliente, carteira, histórico de orçamento, pedido e NF, agenda, consulta de produto e estoque. Nenhuma gravação nas tabelas do Hardness.
3. **Atividades e agenda.** Primeira gravação, em D085, T259 e T260, que têm poucas regras (regra 19).
4. **Orçamento.** O FX ERP vira dono de T003 e T004.
5. **Pedido e crédito.**
6. Estoque e almoxarifado, compras e financeiro, cada um decidido quando chegar a vez.
7. NF e fiscal por último, ou nunca.

A ordem começa pela leitura porque ela não tem o problema da seção seguinte.

### 8.3 O ponto técnico que decide: quem grava

As regras de `configuracoes/<TABELA>.php` são gatilhos PHP. Só rodam quando a gravação passa pelo núcleo do Hardness (`referencia/sistema/confSistema.php:477`). Um sistema novo que grave direto em T003 dispara os 56 gatilhos de banco, mas pula a totalização, a situação, a comissão e o contato de CRM que `configuracoes/T003.php:90–199` executa. O orçamento ficaria gravado e errado.

Três saídas:

| Saída | Como | Custo |
|---|---|---|
| Dono único por tabela | Em cada fase, o FX ERP passa a ser o único que grava as tabelas do módulo. As telas do Hardness que gravam nelas saem do Menu. As regras daquela tabela são portadas com teste de caracterização | Exige portar todas as regras da tabela antes de ligar. É a que escala |
| Gravar pelo Hardness | O FX ERP lê direto e grava chamando uma tela ou um ponto do Hardness | Rápido para começar, mas a tela nova herda a lentidão e o formato do Hardness. O `rest_api/` do Hardness tem só controladores genéricos (`rest_api/src/Controller/`: `Auth`, `Table`, `Tenant`, `Health`, `Docs`). **Hipótese:** não passa pelas regras de tela |
| Gravar dos dois lados | Os dois sistemas gravam a mesma tabela | Descartada. Duas implementações da mesma regra divergem |

Recomendação: dono único por tabela. A segunda saída serve só de ponte curta dentro de uma fase.

O protótipo de convivência precisa provar três coisas, nesta ordem: (a) o FX ERP reconhece o Usuário, a Empresa e o Perfil da sessão do Hardness; (b) uma tela do FX ERP abre dentro de uma Aba do Shell sem conflito de CSS e de script; (c) um orçamento gravado pelo FX ERP produz, campo a campo, os mesmos valores que o Hardness produz para a mesma entrada.

### 8.4 Convivência com a Publicação

- O código novo fica fora de `src/hardness3`, como `src/fx/`. A Publicação não o toca.
- O que o FX ERP lê da Base vira contrato: um teste confere, contra a fotografia, que cada tabela e coluna usada existe com o tipo esperado. O teste roda a cada `bin/publicacao trazer`.
- Quando o FX ERP vira dono de uma tabela, a Publicação que mexer naquela tabela ou nas telas dela passa a ser só informação: lê-se o que mudou e decide-se portar ou não.
- Enquanto fiscal e NF ficarem no Hardness, a Publicação continua valendo muito. Ela está entregando a reforma tributária: 72 arquivos `Calcular_*_CBS` e `Calcular_*_IBS` para orçamento, pedido, NF e compra (`scripts/sql/Calcular_T004A_Valor_CBS.sql` e semelhantes).
- Há uma tensão a resolver em ADR: o 0001 diz que o sistema "não acompanha release do fornecedor" (`:7`), e o 0005 organiza a entrada da Publicação.

### 8.5 Migração de dados

- Nas fases de leitura não há migração. É a vantagem de ler a mesma Base.
- Quando o FX ERP vira dono de um módulo, há duas formas. Continuar gravando nas tabelas do Hardness, com as regras portadas, que é o padrão recomendado enquanto outro módulo do Hardness ainda ler aquelas tabelas. Ou migrar para tabelas novas e deixar uma visão com o nome antigo para o Hardness ler.
- A normalização da seção 5.7 só é segura depois que nenhum módulo do Hardness grava na tabela. NF, expedição e financeiro leem orçamento e pedido (seção 3.3), então T003 a T006 ficam no formato atual por muito tempo.
- Cuidados conhecidos: latin1 para utf8mb4, datas `0000-00-00`, situações em letra e número, vínculos sem chave estrangeira que podem ter linha órfã. A qualidade real dos dados só aparece na produção.

### 8.6 O contrato com o Hardness

O repositório não traz o contrato. O que ele diz: a Fixar comprou o código (`docs/adr/0001-sistema-novo-nasce-do-legado.md:7`), recebe a Publicação (ADR 0005) e desliga, por adaptação, as duas verificações de boleto do fornecedor (`bin/adaptar-hardness.py:212`).

- Enquanto fiscal, NF ou financeiro rodarem no Hardness, o acesso à Publicação deve continuar.
- Saber se a licença permite escrever um sistema novo a partir das regras do código comprado é pergunta jurídica, para a diretoria, antes da fase 4.
- O fim do contrato só entra em pauta se e quando a NF sair do Hardness. Este estudo recomenda não planejar isso agora.

---

## 9. Riscos e estimativa

### 9.1 Estimativa por fase

Ordem de grandeza, para duas pessoas, com parte do tempo na produção. A base é o volume de código que carrega o comportamento hoje, não uma velocidade medida: o repositório tem 104 commits em 16 dias e nenhum módulo de negócio escrito do zero.

| Fase | Conteúdo | Ordem de grandeza | O que mais muda a conta |
|---|---|---|---|
| 0 | Levantamento de uso com a operação, inventário da Personalização, medição de desempenho, protótipo de convivência | 4 a 8 semanas | Acesso a dados da produção |
| 1 | Fundação: esqueleto, login por ponte, Perfil, Shell, fila, Alertas em tabela e tempo real, implantação | 2 a 3 meses | Resultado do protótipo de Aba |
| 2 | Televendas em leitura | 2 a 4 meses | Quantas abas do C.R.M. são usadas |
| 3 | Atividades e agenda | 1 a 2 meses | Uso real de e-mail integrado |
| 4 | Orçamento | 4 a 8 meses | Tamanho da Personalização de totalização; impostos no orçamento |
| 5 | Pedido e crédito | 3 a 6 meses | Número de situações de pedido em uso; alçada |
| 6 | Estoque, almoxarifado e expedição | 4 a 8 meses | Coletor e telas de tablet; mais de uma Empresa com estoque |
| 7 | Compras | 4 a 6 meses | Fluxo antigo ou novo |
| 8 | Financeiro, boleto e remessa | 6 a 10 meses | Bancos e carteiras; conciliação |
| 9 | NF de saída e de entrada | 9 a 15 meses, mais manutenção fiscal permanente | Reforma tributária; modelos de documento emitidos |

Somas: fases 0 a 5, que cobrem a frente comercial, ficam entre 1 e 2 anos. Tudo, entre 3 e 5 anos ou mais. É a mesma conclusão do ADR 0001.

O que muda a estimativa como um todo:

- **Escopo real.** Se a operação usar um terço das telas, a conta cai quase na mesma proporção. É o fator mais forte e o mais barato de descobrir.
- **Personalização.** Ela é a diferença entre "o Hardness" e "o que a Fixar tem hoje", e está fora do git.
- **Dedicação.** Duas pessoas em tempo integral ou meio período cada.
- **Teste de caracterização.** Com ele, portar uma regra é trabalho de dias. Sem ele, cada regra é uma investigação e um risco em produção.
- **Fiscal fora.** Tirar a fase 9 remove cerca de um quarto do total e o custo permanente.
- **Uma terceira pessoa** dedicada ao FX ERP muda a ordem de grandeza das fases 4 em diante.
- **Publicação.** Mudança de estrutura em tabela lida pelo FX ERP gera retrabalho.

### 9.2 Riscos

| Risco | Efeito | Como reduzir |
|---|---|---|
| Dois sistemas para duas pessoas | A manutenção do Hardness consome o tempo do novo | Só abrir fase nova com a anterior estável; critério de parada por fase |
| Regra portada com erro em valor | Orçamento, imposto ou comissão errados em produção | Teste de caracterização campo a campo; período em paralelo por módulo |
| Gravação que pula regra do Hardness | Dado inconsistente na Base | Dono único por tabela (seção 8.3) |
| Personalização desconhecida | O sistema novo reproduz o Hardness, não a Fixar | Inventário antes da fase 4 |
| Publicação muda tabela lida | Tela nova quebra depois de um `trazer` | Teste de contrato contra a fotografia |
| Uso real diferente do mapa | Esforço em tela que ninguém abre | Fase 0 com a operação e com dado de uso |
| Desempenho não melhora | A promessa principal não se cumpre | Medir antes; corrigir no Hardness o que for barato |
| Legislação fiscal | Reforma tributária durante a migração | Fiscal fica no Hardness |
| Licença do código | Impedimento jurídico tardio | Pergunta à diretoria antes da fase 4 |
| Sessão compartilhada | Falha de segurança na ponte de login | Ponte só no servidor, sem dado de sessão no navegador; revisão de segurança no protótipo |
| Dependência de pessoa | Conhecimento do legado em uma cabeça | Regras em teste e em documento, não em memória |
| Cronograma atual | A reescrita disputa tempo com a entrega de março de 2027 | Não começar a fase 1 antes da validação em produção |

### 9.3 Veredito, com as condições

- **Não vale** reescrever tudo e trocar de uma vez.
- **Vale** crescer ao lado, restrito à frente comercial, se as cinco condições do resumo forem atendidas.
- **Continua válido** seguir o ADR 0001 e melhorar o Hardness no lugar. Se a fase 0 mostrar que a dor da operação é lentidão e não fluxo, esse caminho é o mais barato e este estudo recomenda ficar nele.

---

## 10. Perguntas em aberto para a operação

Uso:

1. Quais telas cada setor abre num dia normal, por Perfil?
2. O televendas trabalha dentro do C.R.M.? Quais abas usa e quais nunca abriu?
3. CRM e e-mail integrado estão em uso? A spec da Base os deixou fora do ciclo e este pedido os incluiu.
4. O chat é usado? Por quem?
5. Quais Alertas do sino alguém espera receber, e quais são ruído?

Regras:

6. Como a Fixar fecha o valor de um orçamento e de um pedido, passo a passo, com um exemplo real?
7. Quais níveis de alçada existem e quais são os tetos de desconto e de prazo?
8. O que bloqueia um pedido no crédito hoje? Quais portadores passam sem análise?
9. Quais situações de pedido aparecem no dia a dia, das 26 que o Hardness tem?
10. Compras usa cotação e ordem pelo fluxo antigo ou pelo novo?

Estoque e fiscal:

11. Quantas Empresas têm estoque próprio? Alguma vende do estoque de outra?
12. O almoxarifado usa coletor, tablet de separação ou conferência cega?
13. Quais documentos a Fixar emite: NF-e, NFC-e, CT-e, MDF-e, NFS-e? Quantos por dia?
14. Quais bancos e carteiras de boleto estão ativos? Há remessa e retorno por arquivo?

Operação e desempenho:

15. Quantas pessoas usam o sistema ao mesmo tempo, e em que horário?
16. Quais são as cinco operações mais lentas hoje?
17. Quais relatórios são usados toda semana e todo mês?
18. Quais integrações estão ligadas: loja virtual, B2B, portal de representante, API?

Para a diretoria:

19. O que o contrato com o fornecedor do Hardness permite e por quanto tempo a Publicação continua?
20. Qual dor justifica o investimento: lentidão, tela, fluxo que não cabe, ou dependência do fornecedor?

Para a T.I.:

21. A auditoria T162 está ligada na produção? Ela responderia as perguntas 1 e 16 com dado.
22. Quantas chaves da C031 têm valor diferente do padrão na produção?
23. Qual é o tamanho da Personalização: quantas telas, quantas rotinas?

---

## 11. Próximos passos

1. Levar as perguntas da seção 10 ao percurso com a operação, que o cronograma já prevê até 12/12.
2. Decidir, com este estudo em mãos, se o ADR 0001 é reaberto. Se for, registrar um ADR novo dizendo o que muda e o que continua.
3. Medir o desempenho da produção antes de prometer melhora: tempo por tela, consultas mais lentas, carga da consulta periódica.
4. Fazer o inventário da Personalização em documento separado, fora deste.
5. Depois da validação em produção de março de 2027, fazer o protótipo de convivência da seção 8.3, com prazo fechado de 4 a 8 semanas e as três provas como critério.
6. Montar o primeiro teste de caracterização sobre o orçamento, usando a Base e `testes/orcamento-pedido-nota.sh` como ponto de partida. Ele serve aos dois caminhos: ao FX ERP novo e ao ajuste do Hardness.
7. Só então abrir a spec da fase 1 em `.scratch/`.

O que este estudo não fez: não leu a Personalização, não consultou banco, não mediu nada em execução, não abriu `graphify-out/` nem os protótipos HTML do visual novo, e não espelhou tarefa no Orbit, porque não publica ticket.
