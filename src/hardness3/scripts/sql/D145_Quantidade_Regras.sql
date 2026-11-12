CREATE FUNCTION `D145_Quantidade_Regras`(xD145_Id INT(11)) RETURNS INT(11)
BEGIN
    DECLARE D145_Quantidade INT(10) default 0;
    DECLARE xD145_D024_Flag_Contribuinte INT(10);
    DECLARE xD145_Produzido_Adquirido CHAR(5);
    DECLARE xD145_Estados_Especificos CHAR(5);
    DECLARE xD145_Dentro_Fora_Estado CHAR(5);
    DECLARE xD145_UF_Tem_ST CHAR(5);
    DECLARE xD145_D005_Id INT(10);
    DECLARE xD145_NCM_Tem_ST CHAR(5);
    DECLARE xD145_Pessoa_Fisica_Juridica CHAR(5);
    DECLARE xD145_Flag_Cabecalho_Item CHAR(5);
    DECLARE xD145_Flag_Inativar CHAR(5);
    DECLARE xD145_C004_Codigo_Regime_Tributario INT(10);
    DECLARE xD145_C004_Id INT(10);
    DECLARE xD145_Empresas VARCHAR(20);
    DECLARE xD145_Flag_Revenda_Consumidor VARCHAR(20);
    DECLARE xD145_Flag_UF_Convenio_NCM_ST VARCHAR(1);
    DECLARE xD145_Empresas_Destinatario VARCHAR(60);
    DECLARE xD145_Origem_Mercadoria VARCHAR(20);
    DECLARE xD145_Empresas_Destinatario_Ignorar VARCHAR(200);
    DECLARE xD145_Codigo_Regime_Tributario VARCHAR (20);
    DECLARE xD145_NCM VARCHAR(200);
    DECLARE xD145_Flag_Destino_Produto VARCHAR(2);
    DECLARE xD145_D060_Ids VARCHAR(100);
    DECLARE xD145_D003_Ids VARCHAR(100);


    SET D145_Quantidade = 0;

    SELECT D145_C004_Id,
           D145_Empresas,
           D145_Flag_Revenda_Consumidor,
           D145_C004_Codigo_Regime_Tributario,
           D145_D024_Flag_Contribuinte,
           D145_Produzido_Adquirido,
           D145_Estados_Especificos,
           D145_Dentro_Fora_Estado,
           D145_UF_Tem_ST,
           D145_D005_Id,
           D145_NCM_Tem_ST,
           D145_Pessoa_Fisica_Juridica,
           D145_Flag_UF_Convenio_NCM_ST,
           D145_Flag_Cabecalho_Item,
           D145_Flag_Inativar,
           D145_Empresas_Destinatario,
           D145_Origem_Mercadoria,
           D145_Empresas_Destinatario_Ignorar,
           D145_Codigo_Regime_Tributario,
           D145_NCM,
           D145_Flag_Destino_Produto,
           D145_D060_Ids,
           D145_D003_Ids
      INTO xD145_C004_Id,
           xD145_Empresas,
           xD145_Flag_Revenda_Consumidor,
           xD145_C004_Codigo_Regime_Tributario,
           xD145_D024_Flag_Contribuinte,
           xD145_Produzido_Adquirido,
           xD145_Estados_Especificos,
           xD145_Dentro_Fora_Estado,
           xD145_UF_Tem_ST,
           xD145_D005_Id,
           xD145_NCM_Tem_ST,
           xD145_Pessoa_Fisica_Juridica,
           xD145_Flag_UF_Convenio_NCM_ST,
           xD145_Flag_Cabecalho_Item,
           xD145_Flag_Inativar,
           xD145_Empresas_Destinatario,
           xD145_Origem_Mercadoria,
           xD145_Empresas_Destinatario_Ignorar,
           xD145_Codigo_Regime_Tributario,
           xD145_NCM,
           xD145_Flag_Destino_Produto,
           xD145_D060_Ids,
           xD145_D003_Ids
      FROM D145
     WHERE D145_Id=xD145_Id;

    IF xD145_Empresas != "" THEN
        SET D145_Quantidade = D145_Quantidade + 10;
    END IF;

    IF xD145_C004_Id > 0 THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Flag_Revenda_Consumidor != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Flag_UF_Convenio_NCM_ST != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_C004_Codigo_Regime_Tributario != 0 THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_D024_Flag_Contribuinte != 0 THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Produzido_Adquirido != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Estados_Especificos != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Dentro_Fora_Estado != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_UF_Tem_ST != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_D005_Id != 0 THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_NCM_Tem_ST != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Pessoa_Fisica_Juridica != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Empresas_Destinatario != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Origem_Mercadoria != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Empresas_Destinatario_Ignorar != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Codigo_Regime_Tributario != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_NCM != "" THEN
        SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_Flag_Destino_Produto != "" THEN
      SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_D060_Ids != "" THEN
      SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    IF xD145_D003_Ids != "" THEN
      SET D145_Quantidade = D145_Quantidade + 1;
    END IF;

    RETURN D145_Quantidade;
END;

