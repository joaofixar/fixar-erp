CREATE FUNCTION `D137_Quantidade_Regras`(xD137_Id INT(11)) RETURNS INT(11)
BEGIN
   DECLARE D137_Quantidade INT(10) default 0;
	DECLARE xD137_D024_Flag_Contribuinte INT(10);
	DECLARE xD137_Produzido_Adquirido CHAR(5);
	DECLARE xD137_Estados_Especificos CHAR(5); 
	DECLARE xD137_Dentro_Fora_Estado CHAR(5);
	DECLARE xD137_UF_Tem_ST CHAR(5); 
	DECLARE xD137_D005_Id INT(10);
	DECLARE xD137_NCM_Tem_ST CHAR(5); 
	DECLARE xD137_Pessoa_Fisica_Juridica CHAR(5); 
	DECLARE xD137_Flag_Cabecalho_Item CHAR(5);
	DECLARE xD137_Flag_Inativar CHAR(5);
	DECLARE xD137_C004_Codigo_Regime_Tributario INT(10);
   DECLARE xD137_C004_Id INT(10);
   DECLARE xD137_Empresas VARCHAR(20);
   DECLARE xD137_Flag_Revenda_Consumidor VARCHAR(20);
   DECLARE xD137_NCM VARCHAR(200);
   DECLARE xD137_Flag_UF_Convenio_NCM_ST VARCHAR(1);
   DECLARE xD137_Flag_Origem_Pedido VARCHAR(1);
   DECLARE xD137_Empresas_Destinatario VARCHAR(500);
   DECLARE xD137_D024_Codigo_Regime_Tributario INT(10);
   DECLARE xD137_Origem_Mercadoria VARCHAR(20);
   DECLARE xD137_Flag_Destino_Produto VARCHAR(1);
   DECLARE xD137_Peso_Minimo DECIMAL(10,3);
   DECLARE xD137_Peso_Maximo DECIMAL(10,3);
   DECLARE xD137_ST_Retido CHAR (1);
   DECLARE xD137_Flag_Tipo_IPI CHAR (1);
   DECLARE xD137_D022_Ids VARCHAR(100);
   DECLARE xD137_D060_Ids VARCHAR(100);
   DECLARE xD137_D003_Ids VARCHAR(100);

   SET D137_Quantidade = 0; 
   SELECT D137_C004_Id,
          D137_Empresas,
          D137_Flag_Revenda_Consumidor,
          D137_C004_Codigo_Regime_Tributario,
     	    D137_D024_Flag_Contribuinte,
			 D137_Produzido_Adquirido,
			 D137_Estados_Especificos,
			 D137_Dentro_Fora_Estado,
			 D137_UF_Tem_ST,
			 D137_D005_Id,
			 D137_NCM_Tem_ST,
			 D137_Pessoa_Fisica_Juridica,
          D137_Flag_UF_Convenio_NCM_ST,
			 D137_Flag_Cabecalho_Item,
			 D137_Flag_Inativar,
          D137_Flag_Origem_Pedido,
          D137_NCM,
          D137_Empresas_Destinatario,
          D137_D024_Codigo_Regime_Tributario,
          D137_Origem_Mercadoria,
          D137_Flag_Destino_Produto,
          D137_Peso_Minimo,
          D137_Peso_Maximo,
          D137_ST_Retido,
          D137_Flag_Tipo_IPI,
          D137_D022_Ids,
          D137_D060_Ids,
          D137_D003_Ids
	  INTO xD137_C004_Id,
          xD137_Empresas,
          xD137_Flag_Revenda_Consumidor,
          xD137_C004_Codigo_Regime_Tributario,   
	   	 xD137_D024_Flag_Contribuinte,
			 xD137_Produzido_Adquirido,
			 xD137_Estados_Especificos,
			 xD137_Dentro_Fora_Estado,
			 xD137_UF_Tem_ST,
			 xD137_D005_Id,
			 xD137_NCM_Tem_ST,
			 xD137_Pessoa_Fisica_Juridica,
          xD137_Flag_UF_Convenio_NCM_ST,
          xD137_Flag_Cabecalho_Item,
          xD137_Flag_Inativar,
          xD137_Flag_Origem_Pedido,
          xD137_NCM,
          xD137_Empresas_Destinatario,
          xD137_D024_Codigo_Regime_Tributario,
          xD137_Origem_Mercadoria,
          xD137_Flag_Destino_Produto,
          xD137_Peso_Minimo,
          xD137_Peso_Maximo,
          xD137_ST_Retido,
          xD137_Flag_Tipo_IPI,
          xD137_D022_Ids,
          xD137_D060_Ids,
          xD137_D003_Ids
     FROM D137
    WHERE D137_Id=xD137_Id;

   IF xD137_Empresas != "" THEN
      SET D137_Quantidade = D137_Quantidade + 10;
   END IF;

   IF xD137_C004_Id > 0 THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Flag_Revenda_Consumidor != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Flag_UF_Convenio_NCM_ST != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

 	IF xD137_C004_Codigo_Regime_Tributario != 0 THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_D024_Flag_Contribuinte != 0 THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Produzido_Adquirido != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Estados_Especificos != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Dentro_Fora_Estado != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_UF_Tem_ST != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_D005_Id != 0 THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_NCM_Tem_ST != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Pessoa_Fisica_Juridica != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Flag_Origem_Pedido != "" THEN
      SET D137_Quantidade = D137_Quantidade + 5;
   END IF;

   IF xD137_NCM != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Empresas_Destinatario != "" THEN
      SET D137_Quantidade = D137_Quantidade + 10;
   END IF;

   IF  xD137_D024_Codigo_Regime_Tributario != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_Origem_Mercadoria != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF  xD137_Flag_Destino_Produto != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF  xD137_Peso_Minimo != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF  xD137_Peso_Maximo != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF  xD137_ST_Retido != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF  xD137_Flag_Tipo_IPI != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_D022_Ids != "" THEN
      SET D137_Quantidade = D137_Quantidade + 10;
   END IF;

   IF xD137_D060_Ids != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   IF xD137_D003_Ids != "" THEN
      SET D137_Quantidade = D137_Quantidade + 1;
   END IF;

   RETURN D137_Quantidade;
END;