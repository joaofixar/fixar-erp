# Task 49 — revisão de textos da tela Nova guia

Implementada em 04/11/2026. A rota `?view=new` passou a apresentar uma orientação curta para selecionar a UF, configuração da UF em linguagem de ação e escolha do tipo de guia, sem expor termos internos.

O reinício continua apenas descartando o estado transitório da criação que ainda não gerou guia. A busca de configurações permanece um POST explícito com CSRF e não cria, transmite, consulta resultado nem gera PDF. Quando o certificado estiver indisponível, o aviso aparece no próprio contexto das configurações, sem modal, e permite nova tentativa após a regularização.

Não foram alterados actions, URLs, redirects, sessão, ambiente efetivo, persistência, regras de criação, GNRE001–GNRE009, Portal, XML, transmissão, consulta, fila ou PDF.

## Complemento — retorno de acesso ao Portal

O clique manual em Buscar configurações no Portal agora classifica a falha pública sem expor resposta técnica. Certificado ausente ou inutilizável recebe a categoria **Certificado digital indisponível**. Uma falha SOAP válida que declare explicitamente acesso negado, recusado, não autorizado ou não habilitado ao Web Service recebe **Acesso ao Portal GNRE não autorizado**. HTTP, TLS, timeout, SOAP inválido e qualquer retorno sem evidência inequívoca recebem a mensagem neutra **Não foi possível consultar as configurações da UF**.

Nenhuma dessas categorias cria guia; o botão de busca continua disponível para nova tentativa. A classificação não inventa código, descrição, SOAP ou resposta do Portal.

Também são inspecionados corpos de respostas HTTP não-2xx, sem tratar o status HTTP como evidência. Somente um Envelope SOAP válido, dentro do limite de tamanho, com Fault de recusa explícita recebe a categoria de acesso não autorizado. HTML, corpo vazio, XML inválido, Fault sem evidência de autorização, timeout, TLS e respostas grandes permanecem inconclusivos.

## Roteiro manual

1. Abra Nova guia sem UF e confira a orientação e o seletor.
2. Reinicie a criação antes de criar uma guia e confira a mensagem de descarte do estado transitório.
3. Selecione uma UF sem configuração e confira o botão de busca e a ausência de criação.
4. Com certificado indisponível, confira o aviso contextual, sem modal.
5. Abra uma UF configurada e confira tipo de guia e o botão Criar guia.
6. Confirme que a tela não exibe termos internos nem referência a NF-e.
7. Em Homologação, com empresa que tenha certificado mas ainda não esteja habilitada, clique uma única vez em Buscar configurações no Portal. Registre somente a categoria pública apresentada; se não houver recusa explícita do Portal, mantenha o resultado inconclusivo.
