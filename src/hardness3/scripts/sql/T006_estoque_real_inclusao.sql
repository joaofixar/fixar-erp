CREATE TRIGGER T006_estoque_real_inclusao AFTER INSERT ON `T006` FOR EACH ROW BEGIN
	UPDATE D009 SET D009_Quantidade_Estoque_Real=D009_Quantidade_Estoque-D009_Quantidade_Pedido(D009_Id,2)-D009_Quantidade_Pendencia(D009_Id),
                  D009_Quantidade_Estoque_Liquido=D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(D009_Id,1) WHERE D009_Id=NEW.T006_D009_Id;
END;