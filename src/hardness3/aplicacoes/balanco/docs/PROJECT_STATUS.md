# Balanço — status do projeto

> **Regra de continuidade:** se houver divergência entre este documento e o código atual, o código é a fonte de verdade e a divergência deve ser informada antes de continuar.

## Objetivo e fluxo vigente

O módulo moderniza a operação de balanço para tablet/coletor, com entrada prioritária por leitura de etiqueta. Não existe mais listagem geral de produtos como entrada.

`bipar produto → localizar por código/EAN → visualizar locações → bipar ou selecionar locação → reservar T066 → visualizar estoque atual → informar quantidade → confirmar → efetivar balanço → retornar para nova bipagem`

`T066` é a unidade operacional: um registro de **produto + locação**.

Após uma finalização bem-sucedida, o contexto de produto/locação é limpo e a tela retorna à bipagem do próximo produto.

## Referências legadas

- Grid/regra de balanço: `prog/est/est003/grid.php`, caso `est003GridBalancoCiclico`.
- Efetivação oficial: `bibliotecas/classes/EST003.php`, `est003BalancoCiclico()`.
- Reprocessamento: `bibliotecas/classes/CAD002.php`, `D001_reprocessa_historico()`.
- Referência mobile: `mobile/phone/inventario.php`.
- Referência visual e de scanner: `mobile/tablet_separacao`.

## Estrutura atual

```text
aplicacoes/balanco/
├── bootstrap/app.php       # bootstrap, contexto, consultas e helpers
├── config/module.php       # base_path, permissão 206 e paginação legada
├── docs/
│   ├── etapa-1.md
│   └── PROJECT_STATUS.md
└── public/
    ├── index.php           # bipagem/identificação de produto
    ├── locacoes.php        # produto e suas locações
    ├── contar.php          # tela de contagem e prévias de confirmação
    ├── login.php / logout.php
    ├── select-company.php   # seleção e confirmação explícita de empresa
    ├── change-company.php   # reinicia a seleção/troca segura por POST
    ├── qr-login.php         # autentica e gera QR para abertura no celular
    ├── abrir-qr.php         # consome o QR e cria a nova sessão
    ├── gerar_etiqueta.php   # gera etiqueta do T066 reservado
    ├── resultados.php      # compatibilidade: redireciona para index.php
    └── assets/
        ├── css/app.css
        └── js/app.js
```

## Publicação

- URL pública canônica: `/balanco`.
- O QR Code sempre monta sua URL pública a partir de `$confUsuario['urlRaiz']`,
  resultando em `{urlRaiz}/balanco/abrir-qr.php?...`. O módulo não contém
  domínio ou túnel temporário; eventual acesso de desenvolvimento é
  configuração do ambiente.
- O `.htaccess` raiz faz rewrite interno apenas para `/balanco` e seus assets/arquivos públicos; a URL do navegador não expõe `aplicacoes/balanco/public`.
- Acessos diretos aos caminhos físicos atual e anterior são redirecionados temporariamente para a URL canônica.
- `bootstrap`, `config` e `docs` não são públicos.
- O módulo usa autenticação híbrida: uma sessão Hardness válida identifica o operador; no acesso direto sem sessão, o login próprio autentica o operador e cria a sessão compatível e `$_SESSION['balanco']`. Em ambos os casos a empresa nunca é assumida da sessão do ERP: as empresas são buscadas em `C019` e o operador deve selecionar/confirmar uma antes de iniciar. A permissão `206` é obrigatória; um usuário autenticado sem ela recebe resposta `403`, sem ciclo para o login.
- `Sair do Balanço` encerra apenas o contexto do módulo e conserva a sessão global Hardness. Um marcador vinculado ao usuário impede a reentrada automática imediata; para entrar novamente, o operador informa as credenciais no login do Balanço. O logout global do sistema não é acionado pelo módulo nesta etapa.

### Implantação Apache: DocumentRoot `/var/www/html`

Em servidores cuja estrutura seja `DocumentRoot /var/www/html` e Hardness em
`/var/www/html/hardness3`, a requisição pública `/balanco` chega primeiro em
`/var/www/html/.htaccess`. Portanto, este é o arquivo que deve receber a
regra; `hardness3/.htaccess` não é o primeiro ponto de entrada dessa URL.

No deploy, inserir o bloco abaixo imediatamente após `RewriteEngine On` e
antes das regras existentes de `mcp` e do fallback genérico para
`/hardness3/index.php`. Não alterar as regras de MCP, separação ou qualquer
outra rota já existente.

```apache
# URL pública canônica do módulo Balanço.
RewriteCond %{THE_REQUEST} \s/+hardness3/balanco(?:/[^\s?]*)? [NC]
RewriteRule ^hardness3/balanco/?(.*)$ /balanco/$1 [R=302,L,NE]
RewriteRule ^balanco/?$ /hardness3/aplicacoes/balanco/public/index.php [END]
RewriteRule ^balanco/abrir-qr/?$ /hardness3/aplicacoes/balanco/public/abrir-qr.php [END]
RewriteRule ^balanco/(.*)$ /hardness3/aplicacoes/balanco/public/$1 [END]
```

O último `RewriteRule` do bloco atende as páginas internas e os arquivos
públicos do módulo: `login.php`, `logout.php`, `select-company.php`, `change-company.php`,
`gerar_etiqueta.php`, `qr-login.php`, `abrir-qr.php` e `assets/...`. As regras
fazem rewrite interno; `/balanco` permanece visível no navegador.

Procedimento manual obrigatório:

```bash
# Backup antes de editar.
sudo cp -a /var/www/html/.htaccess "/var/www/html/.htaccess.bak.$(date +%Y%m%d-%H%M%S)"

# Validar a configuração Apache após a edição manual.
sudo apache2ctl -t

# Se a distribuição usar o nome alternativo do binário:
sudo apachectl -t
```

Uma alteração em `.htaccess` é lida pelo Apache nas requisições seguintes;
não alterar VirtualHost, MCP ou outras rotas para publicar o Balanço. Confirmar
que o VirtualHost permite a leitura de `.htaccess` com `AllowOverride FileInfo`
(ou `AllowOverride All`) e que `mod_rewrite` está habilitado.

Após a alteração, testar sem uma sessão autenticada:

```bash
curl -sS -D - -o /dev/null https://dominio-cliente/balanco
curl -sS -D - -o /dev/null https://dominio-cliente/balanco/
curl -sS -D - -o /dev/null https://dominio-cliente/balanco/login.php
curl -sS -D - -o /dev/null https://dominio-cliente/balanco/assets/css/app.css
curl -sS -D - -o /dev/null "https://dominio-cliente/balanco/abrir-qr.php?codigo=invalido"
```

Os três primeiros acessos devem atender o módulo (normalmente redirecionando
para o login quando não houver sessão), o asset deve responder normalmente e
`abrir-qr.php` deve responder como código inválido, sem expor o caminho físico
`/hardness3/aplicacoes/balanco/public/...` ao navegador.

Para rollback, restaurar o arquivo criado no backup, usando o nome gerado no
deploy, e repetir os testes acima:

```bash
sudo cp -a /var/www/html/.htaccess.bak.AAAAMMDD-HHMMSS /var/www/html/.htaccess
```

## Serviços e consultas existentes

Em `bootstrap/app.php`:

- Bootstrap de sessão Hardness, usuário, empresa, perfil e permissões.
- `balanco_product_matches()`: resolve produto na empresa ativa.
- `balanco_product_by_d009()`: recupera produto/estoques por `D009`.
- `balanco_locations_for_product()`: recupera `T066` do produto.
- `balanco_location_by_scan()`: resolve locação dentro do produto atual.
- `balanco_acquire_t066()`: marca ou retoma, atomicamente, a posse do `T066`.
- `balanco_abandon_t066()`: libera a marcação do próprio usuário.
- `balanco_finalize_t066()`: valida a quantidade e delega a efetivação individual ao `EST003`.
- Helpers de formatação de quantidade, data e texto.

Não há camada de repositório separada; as consultas estão concentradas no bootstrap.

## Funcionalidades operacionais

- Login/logout e contexto da sessão Hardness.
- Permissão `206` para usar o fluxo.
- Entrada sem listagem geral: campo com foco automático para bipar com leitor físico, sem abrir o teclado virtual automaticamente. Ao tocar no campo, o operador pode digitar; a câmera também preenche esse mesmo campo. Enter e o botão `Localizar` submetem o mesmo formulário de busca.
- As telas de bipagem de produto e de locação aceitam digitação, leitor físico e leitura por câmera. A câmera reutiliza `html5-qrcode@2.3.8`, a mesma biblioteca e o modal de `mobile/tablet_separacao`; ao decodificar, apenas preenche o respectivo campo existente e submete seu formulário, mantendo `balanco_product_matches()` e `balanco_location_by_scan()` como as únicas regras de identificação. Cancelamento fecha e libera a câmera; ausência/negação de permissão informa o operador e mantém a entrada manual e o leitor físico disponíveis.
- Identificação de produto por:
  - `D001_Codigo_Produto`;
  - `D001_Codigo_Barras`;
  - `D001_Codigo_Barra1`.
- EAN/código de fornecedor (`D083`) **não** faz parte da identificação do produto; a leitura de código de barras/EAN usa os campos de código de barras do produto.
- Se houver mais de um resultado de produto, o operador escolhe explicitamente.
- Tela de produto com código, descrição limitada visualmente, unidade, marca, `D009_Quantidade_Estoque` e `D009_Quantidade_Estoque_Liquido`.
- Lista somente as locações/T066 daquele produto na empresa ativa, mostrando estoque da locação, último balanço e ação `Contar`/`Retomar`.
- Bipagem de locação por `D004_Local` ou `T066_Id`, limitada às locações do produto selecionado.
- Seleção manual por botão `Contar`/`Retomar`; o restante do card não abre a contagem.
- Tela de contagem com foco na quantidade, produto, código/EAN, locação, unidade, marca, último balanço e estoque atual da locação.
- Quantidade física `0` é aceita pela interface; vazio é diferente de zero.
- Uma confirmação visual de efetivação:
  - `Finalizar balanço`: após a confirmação final, efetiva individualmente o `T066` reservado.
- `Usar estoque atual` é somente um atalho visual que preenche a quantidade com o estoque atual; não gera lançamento nem efetiva o balanço.
- O botão final de efetivação fica desabilitado enquanto o `POST` está em andamento, evitando múltiplos cliques na interface.
- Após sucesso, apresenta mensagem e retorna à bipagem do próximo produto. Em erro, mantém o operador no contexto da contagem.
- A tela também possui `Abandonar contagem`, com confirmação em diálogo próprio.
- A tela de contagem possui uma ação auxiliar de etiqueta, isolada das ações de balanço. Ela permite gerar PDF de `Produto 100x40`, `Locação 100x40` ou `Locação Caixa 100x40` pelo motor Low Code; o endpoint recebe apenas `T066_Id` e uma chave de modelo permitida, recompõe `T066 → D009 → D001 → D004` e exige que a reserva ainda pertença ao usuário atual. O mapeamento fechado é `produto_100x40 → modelo 0b21c4ea16c39ba82439aae2c02b5917 + D001_Id`, `locacao_100x40 → modelo 41e7bf859c5cf39ef9a8e14941b556e4 + T066_D004_Id/D004_Id` e `locacao_caixa_100x40 → modelo f8c5fa7021ceb9f5b8da5b6ff6ddfbf9 + D001_Id`. Em tablet, a ação abre o endpoint em nova aba e fecha o modal imediatamente, reproduzindo o padrão de `tablet_separacao` e preservando o estado da contagem. A etiqueta de locação usa exclusivamente o `D004` daquele `T066`; a etiqueta de locação-caixa é o modelo de produto/caixa existente e usa o `D001` do mesmo `T066`.

## Isolamento e validação de empresa

A sessão global do Hardness identifica o usuário autenticado, mas o Balanço mantém o contexto operacional em `$_SESSION['balanco']`: usuário vinculado, banco, empresas autorizadas, `active_company_id`, `active_profile_id`, confirmação da empresa e a origem da autenticação. A fonte de autorização é sempre `C019`: cada requisição recarrega as empresas ativas permitidas para o usuário e obtém, da mesma relação, o perfil da empresa ativa. Uma sessão Hardness autenticada não fornece empresa ao Balanço; com uma empresa disponível a confirmação continua obrigatória e, com várias, a seleção é obrigatória.

O QR Code carrega somente usuário e banco, nunca empresa ou perfil. Após seu consumo no celular, o operador também passa pela seleção/confirmação. A empresa do navegador que gerou o QR não é reutilizada.

A troca é iniciada por `POST` em `change-company.php`, protegido por CSRF, e não por alteração direta de combo. Ela limpa o contexto operacional e abre a seleção/confirmação. No `Continuar`, o ID é validado novamente contra `C019`; perfil, C004/C020/C031/C021 e permissões C030/C029 são reconstruídos no backend na requisição seguinte. A troca não altera `$_SESSION['Empresa_Atual']` nem `$_SESSION['Perfil_Usuario']` e sempre redireciona para `/balanco`, descartando URLs de produto, locação ou T066 da empresa anterior.

A empresa ativa nunca vem de parâmetros do navegador nas consultas operacionais.

- Produto: `D009_C004_Id` deve ser a empresa ativa.
- Quando configurado, `SeparaProdutoPorEmpresa` também restringe `D001_C004_Id`.
- Locação: `D004_C004_Id` deve ser a empresa ativa.
- Toda consulta de `T066` valida `D009_C004_Id` e `D004_C004_Id` pela empresa ativa.
- A abertura da contagem valida de novo `T066`, `D009` e `D004` no backend.

Assim, produto + locação resolvem um `T066` somente dentro da empresa ativa.

## Reserva, posse e concorrência

A posse começa somente no `POST` de entrada da contagem — por botão `Contar`/`Retomar` ou por bipagem da locação — e usa exclusivamente a estrutura legada:

- `T066_Flag_Balanco='S'`;
- `T066_C007_Id_Marcou_Balanco=usuário atual`.

`balanco_acquire_t066()` abre transação InnoDB, faz `SELECT ... FOR UPDATE` do `T066` validado pela empresa ativa e decide sob o mesmo lock:

- item livre (`Flag != 'S'`): grava a flag `S` e o usuário atual;
- marcado pelo próprio usuário: não substitui a marcação e permite `Retomar`;
- marcado por outro usuário: faz rollback, bloqueia no backend e informa nome/login quando disponível.

Assim, duas tentativas simultâneas não obtêm sucesso: a segunda espera o lock e lê o estado já definido pela primeira. A interface exibe `Retomar` somente quando a flag é `S` e o usuário marcado é o atual.

Durante uma contagem ativa, o botão Voltar do navegador é interceptado no cliente e exibe a confirmação “Existe uma contagem em andamento. Deseja abandonar a contagem?”. Confirmar submete o mesmo formulário `abandon_count` da ação `Abandonar contagem`; portanto, a liberação continua centralizada em `balanco_abandon_t066()` e, após o POST, a tela retorna às locações. Cancelar mantém a contagem aberta.

`Abandonar contagem` pede confirmação no diálogo do módulo e chama `balanco_abandon_t066()`. A operação revalida `T066`, `D009`, `D004`, empresa ativa, flag e proprietário; então grava flag `N` e limpa o usuário. Somente o proprietário pode liberar nesta V1.

Não há tabela, campo, heartbeat, lease ou expiração automática para reserva na V1. Fechar navegador, perder conexão ou sair do dispositivo mantém a marcação, como no legado: essa é a limitação conhecida de marcação órfã e sua liberação exige ação explícita compatível com as regras do ERP. Marcação criada pelo responsivo permanece uma marcação normal para o legado, e vice-versa.

## Efetivação individual implementada (MVP)

`Finalizar balanço` chama `EST003::est003EfetivarBalancoT066()` e reproduz a regra oficial do balanço cíclico para somente o `T066` reservado:

- inicia transação e revalida `T066`, `D009` e `D004` pela empresa da sessão, sob `FOR UPDATE`;
- rejeita o pedido se `T066_Flag_Balanco != 'S'` ou se `T066_C007_Id_Marcou_Balanco` não for o usuário da sessão;
- aceita quantidade física de `0` e valida até duas casas decimais, conforme `T066_Quantidade_Balanco` do legado;
- cria uma `T214` por efetivação individual, com usuário, empresa e `CURRENT_TIMESTAMP()`;
- cria `T001` oficial com `T001_Flag_Operacao='B'`, quantidade física informada, quantidade de balanço inicial `0`, valores/custos oficiais do balanço e vínculos de `D009`, `T066`, `D004`, empresa e produto; a descrição é `BL-{T214_Id} ({marcou}/{processou})`;
- chama `CAD002->D001_reprocessa_historico(false, D009_Id, 10)`;
- somente depois atualiza o `T066`: quantidade de balanço `0.00`, data `CURDATE()`, frequência `0`, flag `N` e proprietário vazio;
- em erro antes do `COMMIT`, faz rollback e mantém a reserva.

A semântica de `B` é a mesma do balanço legado: a quantidade representa o **saldo absoluto da locação**, não uma diferença de estoque. O responsivo não processa lotes: cada confirmação efetiva apenas um `T066`.

`EST003::est003InserirLancamentoBalanco()` é o método compartilhado que grava o lançamento oficial `T001/B`. `EST003::est003EfetivarBalancoT066()` é o método unitário do responsivo, responsável pela validação sob lock, `T214`, lançamento, reprocessamento, atualização do `T066` e commit. `EST003::est003BalancoCiclico()` passou a reutilizar somente o método compartilhado de criação de `T001/B`; seu fluxo de grid, seu processamento em lote e sua `T214` única por lote foram preservados funcionalmente.

## Testes manuais realizados

Os testes manuais do fluxo de efetivação foram realizados com sucesso. O fluxo confirmado cobre a confirmação individual e o retorno à nova bipagem, incluindo a efetivação oficial por `T001/B`, o reprocessamento e a liberação da reserva após sucesso.

Na leitura por câmera, foram validados estaticamente o carregamento condicional de `html5-qrcode`, a solicitação da câmera traseira (`facingMode: environment`), o cancelamento/liberação do leitor, o retorno ao formulário de bipagem existente e o fallback para leitura manual/leitor físico quando não houver API de câmera ou a biblioteca não estiver disponível. A autorização real da câmera depende do navegador/dispositivo: a homologação operacional ainda deve cobrir um celular Android, um tablet e um desktop sem câmera (que deve exibir o aviso e preservar o campo de leitura).

## Checklist de implantação/piloto

- [ ] `confUsuario['urlRaiz']` aponta para o domínio público correto do cliente.
- [ ] A URL pública `/balanco` está acessível.
- [ ] O rewrite de `/balanco` e de seus arquivos públicos está funcionando.
- [ ] Foram validados o login direto e o acesso pela sessão Hardness existente.
- [ ] Foi validada a troca de empresa autorizada por `C019`.
- [ ] A permissão `206` foi validada para um perfil autorizado e outro sem autorização.
- [ ] O diretório e os arquivos de handoff do QR possuem permissões adequadas de criação, leitura e remoção pelo processo web.
- [ ] O QR Code abre no celular e autentica corretamente o operador.
- [ ] Foram validadas a digitação manual e a leitura por leitor físico.
- [ ] A leitura por câmera foi validada quando o ambiente estiver publicado em HTTPS.
- [ ] Foi validada a impressão das etiquetas de produto e locação.
- [ ] Foi executado um balanço completo de teste, confirmando `T001/B` e o reprocessamento de estoque.
- [ ] Foi executado o teste de reserva com dois usuários sobre o mesmo `T066`.

## Limitações deliberadas do MVP e evoluções futuras

Os itens abaixo são deliberadamente evoluções futuras e não bloqueiam o MVP nem o piloto controlado.

- **Idempotência persistente:** não há chave própria para reconhecer com segurança uma nova tentativa após queda de resposta ou reenvio posterior. `T001_Numero_Controle` não serve para isso: é usado por outros fluxos, tem 20 caracteres e não possui unicidade garantida.
- **Expiração automática:** não há heartbeat, lease ou expiração de reserva.
- **Reserva órfã:** não há liberação administrativa nem recuperação automática. Fechar navegador, perder conexão ou sair do dispositivo mantém a reserva; somente o proprietário consegue abandoná-la nesta versão.
- **Concorrência com o legado:** não há coordenação/serialização adicional entre reprocessamentos concorrentes disparados pelo responsivo e por outros fluxos legados. O MVP preserva deliberadamente o mesmo comportamento do legado, sem locks adicionais por `D009` nem alteração no reprocessamento.
- **Campo do proprietário:** `T066_C007_Id_Marcou_Balanco` mantém a estrutura legada nesta versão; não haverá alteração estrutural no MVP.
- **HTTPS/câmera:** a câmera depende de HTTPS e das permissões do navegador. Em HTTP, o operador continua com digitação manual e leitor físico; não há mecanismo alternativo nesta versão.
- **Evoluções de interface:** foto do produto continua fora do MVP. A câmera dedicada foi adicionada às bipagens de produto e locação, sem criar regra de resolução própria.
- **Manutenibilidade:** a extração atual foi propositalmente mínima. Uma centralização mais ampla da regra de balanço só deve ser considerada com testes de regressão do legado, sem refatoração prematura.

## Garantias atuais

Além da marcação legada de posse, o módulo efetiva individualmente o balanço somente mediante confirmação explícita: cria `T214` e `T001/B`, chama o reprocessamento oficial e altera os saldos exclusivamente por `CAD002->D001_reprocessa_historico()`. Não calcula diferença própria nem altera saldo diretamente no responsivo.

A marcação de posse é limpa somente após o reprocessamento e a atualização final do `T066` concluírem na transação. Produto, locação e empresa são revalidados no backend; a empresa vem exclusivamente da sessão Hardness.

## Próxima etapa

O MVP de efetivação individual e o suporte a múltiplas empresas estão concluídos. O cabeçalho mostra a empresa ativa e, quando C019 libera mais de uma empresa, permite trocá-la sem alterar a sessão global do Hardness. Uma reserva existente não é liberada pela troca: ao entrar na outra empresa, o operador sempre inicia um novo contexto em `/balanco`; ao voltar à empresa original, pode retomar a contagem conforme a regra de reserva vigente.

Uma evolução futura pode adicionar idempotência persistida, expiração e liberação administrativa de reservas, coordenação com reprocessamentos concorrentes, alterações estruturais em campos legados, foto do produto e melhorias de etiquetas, sem alterar a regra de estoque já preservada do balanço legado.
