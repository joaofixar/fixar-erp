# Task 34 — Transmissão da GNRE guiada pela interface

Foi incluído o modal **Transmissão da GNRE**, aberto pelo botão principal **Transmitir GNRE** na página da guia. Ele apresenta as etapas Validar XML, Transmitir, Aguardar resultado, Consultar resultado e PDF.

O modal lê `task34-modal-state.php`, um endpoint local somente de leitura: ele reconstrói o estado a partir de `GNRE001`, `GNRE004`, `GNRE005`, SOAP persistido e `GNRE008`; não instancia cliente do Portal e não tem efeitos externos.

As ações continuam sendo os POSTs já existentes, com CSRF e escopo de empresa: geração/validação, transmissão, consulta pela fila e obtenção idempotente do PDF. Ao usar **Transmitir GNRE**, o navegador registra uma autorização transitória: gera o XML, exige confirmação explícita antes da transmissão, conserva a autorização durante o aguardo/consulta e a encerra após PDF ou estado terminal. A consulta ocorre uma única vez por horário persistido; se o Portal mantiver o retorno pendente, a fila registra uma nova tentativa para cinco segundos depois. O PDF também ocorre uma única vez por ciclo. Fechar ou reabrir sem essa autorização apenas lê estado local. Se a sessão perder a validação do XML, o modal pede nova validação e nunca transmite automaticamente.

Não há cron, daemon, worker contínuo ou execução depois que o navegador fecha. A automação é exclusivamente assistida pelo navegador, enquanto o modal permanece aberto, depende da autorização transitória e respeita o horário persistido em `GNRE005_Proxima_Tentativa`. Os botões individuais da aba Processar continuam disponíveis como alternativa manual; ela mostra somente a próxima ação pública aplicável e reserva estado técnico, fila e SOAP ao Histórico. O roteiro manual é orientativo; esta tarefa não deve transmitir, consultar ou obter PDF de guias reais.

Correção visual/de estado: as cinco etapas são chips numerados em grade responsiva (lista vertical em telas pequenas), sem marcadores sobrepostos. O redirect só reabre o modal quando a URL contém `gnre34=1` e há ciclo ativo; fechar remove o ciclo e o parâmetro da URL com `history.replaceState`.

Prévia da transmissão: após a validação local, o mesmo modal mostra uma prévia somente leitura da guia, itens e XML temporário escapado. A prévia não chama o Portal, não gera XML adicional e desaparece se a sessão do XML for perdida.
