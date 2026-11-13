CREATE FUNCTION `T007_Prazos`(xT007_Id int(11)) RETURNS varchar(255)
BEGIN
    DECLARE xTotal_Parcelas INT(2) DEFAULT 0;
    DECLARE xTotal_Prazos VARCHAR(255);
    DECLARE xRetorno VARCHAR(255) DEFAULT '';
    DECLARE xPrazos_Dias INT(5) DEFAULT 0;
    DECLARE xNumero_Parcela  INT(3) DEFAULT 0;
    DECLARE xFlag_ACP VARCHAR(1) DEFAULT ' ';
    DECLARE xFlag_Gera_Contas VARCHAR(1) DEFAULT ' ';
    SELECT T007_Flag_ACP,
           D006_Flag_Gera_Contas
      INTO xFlag_ACP,
           xFlag_Gera_Contas
      FROM T007
      LEFT JOIN D006 ON D006_Id=T007_D006_Id
     WHERE T007_Id=xT007_Id;
    IF xFlag_ACP!=2 AND xFlag_Gera_Contas!='N' THEN
       SELECT SUM(1),
              GROUP_CONCAT(datediff(T002_Data_Vencimento,T002_Data_Emissao) SEPARATOR '/'),
              T002_Total_Parcelas,
              sum(datediff(T002_Data_Vencimento,T002_Data_Emissao))
         INTO xTotal_Parcelas,
              xTotal_Prazos,
              xNumero_Parcela,
              xPrazos_Dias
         FROM T002
        WHERE T002_T007_Id=xT007_Id
        GROUP BY T002_T007_Id;

        IF xTotal_Parcelas > 0 THEN
           IF xTotal_Parcelas = 1 AND xPrazos_Dias = 0 THEN
              SET xRetorno = 'A VISTA';
           ELSE
              SET xRetorno = xTotal_Prazos;
           END IF;
        ELSE
           SET xRetorno = '';
        END IF;
    END IF;
    RETURN xRetorno;
END
