CREATE TRIGGER `T256_trigger_apos_exclusao` AFTER DELETE ON `T256` FOR EACH ROW BEGIN
	-- Executa após a exclusão para que a função não considere o item removido.
	IF IFNULL(OLD.T256_D009_Id, 0) > 0 THEN
		UPDATE D009
		   SET D009_Quantidade_OP = D009_Quantidade_OP(D009_Id)
		 WHERE D009_Id = OLD.T256_D009_Id;

		INSERT INTO D009A (D009A_D009_Id, D009A_Flag_Atualizar_Estoque)
		VALUES (OLD.T256_D009_Id, 'S')
		ON DUPLICATE KEY UPDATE D009A_Flag_Atualizar_Estoque = 'S';
	END IF;
END;
