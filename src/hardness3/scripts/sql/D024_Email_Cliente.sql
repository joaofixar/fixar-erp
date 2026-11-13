CREATE FUNCTION `D024_Email_Cliente`(xD024_Id INT(10),xTipo VARCHAR(15)) RETURNS VARCHAR(100) CHARSET utf8
BEGIN
    DECLARE xD024_E_Mail VARCHAR(40) DEFAULT '';
    DECLARE xD024_E_Mail_Nota VARCHAR(40) DEFAULT '';
    DECLARE xD024_E_Mail_Boleto VARCHAR(40) DEFAULT '';
    DECLARE xD024_Email_Selecionado VARCHAR(40) DEFAULT '';
    
    SELECT D024_E_Mail,
           D024_E_Mail_Nota,
           D024_E_Mail_Boleto 
           INTO 
           xD024_E_Mail,
           xD024_E_Mail_Nota,
           xD024_E_Mail_Boleto
           FROM D024 WHERE D024_Id=xD024_Id;

    
   IF xTipo="boleto" THEN
      SET xD024_Email_Selecionado = xD024_E_Mail_Boleto;
   ELSEIF xTipo="nota" THEN
      SET xD024_Email_Selecionado = xD024_E_Mail_Nota;
   ELSEIF xTipo="geral" THEN
      SET xD024_Email_Selecionado = xD024_E_Mail;
   ELSE
      SET xD024_Email_Selecionado = xD024_E_Mail;
   END IF;

   IF IFNULL(xD024_Email_Selecionado,'') = '' THEN
      IF IFNULL(xD024_E_Mail,'') = '' THEN
         SET xD024_Email_Selecionado = "<font color='red'>Email nao preenchido</font>";
      ELSE
         SET xD024_Email_Selecionado = xD024_E_Mail;
      END IF;
   END IF;   
   
   RETURN xD024_Email_Selecionado;
END;

