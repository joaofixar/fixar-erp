# Task 63 — Fila de transmissão pela interface

Foi adicionada a opção **Fila de transmissão** ao módulo GNRE. Ela mostra rascunhos da empresa ativa que possuem snapshot, não estão arquivados e ainda não possuem tentativa, recibo ou fila.

Correção posterior: a consulta da tela é independente da projeção da Central de Guias e não depende de XML local, itens, receita, vencimento ou valor. Ela consulta diretamente os rascunhos da empresa GNRE ativa, com snapshot da mesma empresa/UF e ambiente `1`/`2`, excluindo tentativas e filas.

A projeção de vencimento preserva o cabeçalho quando informado; sem ele, uma consulta adicional única carrega os vencimentos dos itens para exibir um vencimento único, múltiplos vencimentos ou ausência de informação, de forma compatível com o MySQL legado.

O navegador mantém apenas a seleção e executa uma guia por vez. A Fila de transmissão é processamento sequencial individual: cada guia tem validação, tentativa, recibo, consulta e PDF próprios. Antes de validar e antes de transmitir, o servidor confere novamente empresa, rascunho, snapshot e ausência de histórico iniciado. O ambiente é sempre lido do snapshot GNRE007; a confirmação recusa seleção mista de Homologação e Produção.

Cada item reutiliza a validação XML, `gnre_transmit_atomic`, GNRE004 e GNRE005. XML inválido não cria tentativa nem chama o Portal. Depois de um recibo, a consulta usa exclusivamente o worker e a fila persistida, sem retransmissão. PDF é solicitado somente após o worker informar autorização individual e pelo serviço idempotente existente.

Falhas inesperadas da Fila recebem feedback público neutro; detalhes de transporte ou infraestrutura não são devolvidos ao modal e permanecem restritos ao Histórico técnico já persistido quando houver.

Fechar/interromper o modal impede o início dos itens seguintes; nada já persistido é revertido. Não há tabela de fila, migration, ZIP ou processamento em segundo plano. A Task 64 permanece a referência para um futuro lote GNRE real.

Roteiro seguro: em Homologação, crie dois rascunhos com snapshots homologados; abra a Fila de transmissão, confirme que rascunhos incompletos estão disponíveis para validação, confira o ambiente e os textos de processamento individual. Não transmita nenhuma guia durante a validação manual. Nunca use Produção como teste.
