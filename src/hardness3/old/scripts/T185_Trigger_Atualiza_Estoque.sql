CREATE TRIGGER `T185_Trigger_Atualiza_Estoque` AFTER UPDATE ON `T185` 
    FOR EACH ROW BEGIN
  		UPDATE T066 SET 
  			T066_Quantidade_Estoque_Real=T066_Quantidade_Estoque-T066_Quantidade_Separado(NEW.T185_T066_Id),
  			T066_Quantidade_Estoque_Liquido=T066_Quantidade_Estoque-T066_Quantidade_Liquido(NEW.T185_T066_Id)
  		WHERE T066_Id=NEW.T185_T066_Id;
END;