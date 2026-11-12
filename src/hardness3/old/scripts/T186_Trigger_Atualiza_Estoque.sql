CREATE TRIGGER T186_Trigger_Atualiza_Estoque BEFORE INSERT ON `T186` FOR EACH ROW BEGIN
	DECLARE xT186_Saldo_Atual DECIMAL(20,3);
	DECLARE xT001_Flag_Operacao VARCHAR(1);
	
	SELECT T001_Flag_Operacao INTO xT001_Flag_Operacao FROM T001 WHERE T001_Id=NEW.T186_T001_Id;
	UPDATE T066 SET 
		T066_Quantidade_Estoque 			= 
												IF(xT001_Flag_Operacao='S',T066_Quantidade_Estoque-NEW.T186_Quantidade,
												IF(xT001_Flag_Operacao='E',T066_Quantidade_Estoque+NEW.T186_Quantidade,
												IF(xT001_Flag_Operacao='B',NEW.T186_Quantidade,T066_Quantidade_Estoque))),
	    T066_Quantidade_Estoque_Real 		=	T066_Quantidade_Estoque-T066_Quantidade_Separado(NEW.T186_T066_Id),
	    T066_Quantidade_Estoque_Liquido		=	T066_Quantidade_Estoque-T066_Quantidade_Liquido(NEW.T186_T066_Id)
	WHERE T066_Id=NEW.T186_T066_Id;
	SELECT T066_Quantidade_Estoque INTO xT186_Saldo_Atual FROM T066 WHERE T066_Id=NEW.T186_T066_Id;
	IF xT186_Saldo_Atual IS NULL THEN
		SET xT186_Saldo_Atual=0;
	END IF;
	SET NEW.T186_Quantidade_Saldo=xT186_Saldo_Atual;
END;