CREATE TRIGGER T001_trigger_atualiza_estoque BEFORE INSERT ON `T001` FOR EACH ROW BEGIN
	DECLARE xD009_Saldo_Atual DECIMAL(20,3);
	DECLARE xCount INT;
	UPDATE D009 SET D009_Quantidade_Estoque=IF(NEW.T001_Flag_Operacao='S',D009_Quantidade_Estoque-NEW.T001_Quantidade,IF(NEW.T001_Flag_Operacao='E',D009_Quantidade_Estoque+NEW.T001_Quantidade,IF(NEW.T001_Flag_Operacao='B',NEW.T001_Quantidade,D009_Quantidade_Estoque))),
	              D009_Quantidade_Estoque_Real=D009_Quantidade_Estoque-D009_Quantidade_Pedido(D009_Id,2)-D009_Quantidade_Pendencia(D009_Id),
	              D009_Quantidade_Estoque_Liquido=D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(D009_Id,1),
	              D009_Valor_Custo_Unitario=IF(NEW.T001_Flag_Atualiza_Custo='S',NEW.T001_Valor_Custo_Unitario,D009_Valor_Custo_Unitario),
	              D009_Valor_Custo_Medio_Unitario=IF(NEW.T001_Flag_Atualiza_Custo='S',NEW.T001_Valor_Custo_Medio_Unitario,D009_Valor_Custo_Medio_Unitario)
	WHERE D009_Id=NEW.T001_D009_Id;
	SELECT D009_Quantidade_Estoque INTO xD009_Saldo_Atual FROM D009 WHERE D009_Id=NEW.T001_D009_Id;
	IF xD009_Saldo_Atual IS NULL THEN
		SET xD009_Saldo_Atual=0;
	END IF;
	SET NEW.T001_Quantidade_Saldo=xD009_Saldo_Atual;
	-- o T066 será atualizar na trigger da tabela T186 (T186_T001_Id)
	-- -- se tiver T185 é sistema de estoque novo (multi-locação), o T066 será atualizar na trigger da tabela T186 (T186_T001_Id)
	-- SELECT COUNT(*) INTO xCount FROM T185 WHERE T185_T006_Id = NEW.T001_T006_Id;
	-- IF xCount IS NULL OR xCount = 0 THEN
	--	-- senão será sempre utilizada a primeira locação (código antigo)
	--	UPDATE T066 SET T066_Quantidade_Estoque=IF(NEW.T001_Flag_Operacao='S',T066_Quantidade_Estoque-NEW.T001_Quantidade,IF(NEW.T001_Flag_Operacao='E',T066_Quantidade_Estoque+NEW.T001_Quantidade,IF(NEW.T001_Flag_Operacao='B',NEW.T001_Quantidade,T066_Quantidade_Estoque))) WHERE T066_Id=NEW.T001_T066_Id;
	-- END IF;
END;