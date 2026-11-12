CREATE TRIGGER `D001_trigger_antes_alteracao` AFTER UPDATE ON `D001` FOR EACH ROW BEGIN
	IF OLD.D001_Codigo_Produto != NEW.D001_Codigo_Produto OR OLD.D001_Descricao_Produto != NEW.D001_Descricao_Produto THEN

		UPDATE T004 
		LEFT JOIN D009 ON D009_Id = T004_D009_Id
		LEFT JOIN D049 ON D049_Id = D009_D049_Id
		LEFT JOIN D001 ON D001_Id = D049_D001_Id
		LEFT JOIN T003 ON T003_Id = T004_T003_Id
		SET T004_Codigo_Produto = NEW.D001_Codigo_Produto,
		T004_Descricao_Produto = NEW.D001_Descricao_Produto
		WHERE (T004_Codigo_Produto != NEW.D001_Codigo_Produto OR T004_Descricao_Produto != NEW.D001_Descricao_Produto)
		AND (T003_Flag_Status_Orcamento = 'P' OR T003_Flag_Status_Orcamento = 'V') AND D001_Id = NEW.D001_Id;

		UPDATE T006 
		LEFT JOIN T005 ON T005_Id = T006_T005_Id
		LEFT JOIN T008 ON T008_T006_Id = T006_Id
		SET T006_Codigo_Produto = NEW.D001_Codigo_Produto,
		T006_Descricao_Produto = NEW.D001_Descricao_Produto
		WHERE (T006_Codigo_Produto != NEW.D001_Codigo_Produto OR T006_Descricao_Produto != NEW.D001_Descricao_Produto)
		AND (T008_Id IS NULL) AND T006_Codigo_Produto = OLD.D001_Codigo_Produto;

		UPDATE T219 
		LEFT JOIN D009 ON D009_Id = T219_D009_Id
		LEFT JOIN D049 ON D049_Id = D009_D049_Id
		LEFT JOIN D001 ON D001_Id = D049_D001_Id
		LEFT JOIN T218 ON T218_Id = T219_T218_Id
		SET T219_Codigo_Produto = NEW.D001_Codigo_Produto,
		T219_Descricao_Produto = NEW.D001_Descricao_Produto
		WHERE (T219_Codigo_Produto != NEW.D001_Codigo_Produto OR T219_Descricao_Produto != NEW.D001_Descricao_Produto)
		AND T218_Flag_Status_Orcamento = 'P' AND D001_Id = NEW.D001_Id;

		SET @disable_trigger = 1;
		UPDATE T225 
		LEFT JOIN D009 ON D009_Id = T225_D009_Id
		LEFT JOIN D049 ON D049_Id = D009_D049_Id
		LEFT JOIN D001 ON D001_Id = D049_D001_Id
		LEFT JOIN T224 ON T224_Id = T225_T224_Id
		SET T225_Codigo_Produto = NEW.D001_Codigo_Produto,
		T225_Descricao_Produto = NEW.D001_Descricao_Produto
		WHERE (T225_Codigo_Produto != NEW.D001_Codigo_Produto OR T225_Descricao_Produto != NEW.D001_Descricao_Produto)
		AND (T224_Flag_Status <= 0 OR T224_Flag_Status = '') AND D001_Id = NEW.D001_Id;
		SET @disable_trigger = NULL;
	END IF;
END;