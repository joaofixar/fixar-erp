# Segurança

Destinos exigem HTTPS e são validados contra SSRF antes do envio. O Dispatcher usa pinning DNS, TLS e não segue redirects. Bearer é opcional e cifrado em repouso.

HMAC é opcional: somente uma delivery que referencia `WHK007` recebe `Hardness-Timestamp` e `Hardness-Signature`. O Dispatcher assina `timestamp + "." + raw_body` com HMAC-SHA256. Não há UI nem endpoint administrativo que gere, rotacione, revogue ou devolva segredo em texto puro.

`WEBHOOKS_MASTER_KEY` é Base64URL de exatamente 32 bytes e só é necessária no processo que cifra/decifra credenciais ou secrets — tipicamente worker e administração que use Bearer. Em produção ela deve ser injetada pelo ambiente e nunca registrada em logs, Git ou resposta HTTP.

A administração usa o contexto autenticado do ERP e C029 307. A empresa é selecionada entre C004 ativas na navegação multiempresa; `company_id` limita a operação, mas não concede autoridade por si só.
