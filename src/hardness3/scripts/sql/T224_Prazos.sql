CREATE FUNCTION `T224_Prazos`(xT224_Id int(11)) RETURNS varchar(255)
BEGIN
    DECLARE xTotal_Parcelas INT(2) DEFAULT 0;
    DECLARE xTotal_Prazos VARCHAR(255);
    DECLARE xRetorno VARCHAR(255) DEFAULT 'A COMBINAR';
    DECLARE xPrazos_Dias INT(5) DEFAULT 0;
    DECLARE xNumero_Parcela  INT(3) DEFAULT 0;
     SELECT SUM(1),
            CONCAT(GROUP_CONCAT(T226_Prazos_Dias SEPARATOR '/'),' dias'),
            T226_Prazos_Dias,
            T226_Numero_Parcela
       INTO xTotal_Parcelas,
            xTotal_Prazos,
            xPrazos_Dias,
            xNumero_Parcela
       FROM T226
      WHERE T226_T224_Id=xT224_Id
      GROUP BY T226_T224_Id;

      IF xTotal_Parcelas > 0 THEN
         IF xTotal_Parcelas = 1 AND xPrazos_Dias = 0 THEN
            SET xRetorno = 'A VISTA';
         ELSE
            SET xRetorno = xTotal_Prazos;
         END IF;
      ELSE
         SET xRetorno = '';
      END IF;
    RETURN xRetorno;
END
