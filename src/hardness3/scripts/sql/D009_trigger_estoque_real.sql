CREATE TRIGGER `D009_trigger_estoque_real` BEFORE UPDATE ON `D009` FOR EACH ROW BEGIN
  SET NEW.D009_Quantidade_Estoque_Real=NEW.D009_Quantidade_Estoque,
      NEW.D009_Quantidade_Estoque_Loja=IFNULL((select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=NEW.D009_Id and UPPER(D146_Descricao)="LOJA"),0),
      NEW.D009_Quantidade_Estoque_Liquido=NEW.D009_Quantidade_Estoque_Real-D009_Quantidade_Pedido(NEW.D009_Id,1)-D009_Saldo_OE(NEW.D009_Id,0)-NEW.D009_Quantidade_Estoque_Loja,
      NEW.D009_Quantidade_Estoque_Fora=IFNULL((SELECT SUM(D009_Quantidade_Estoque_Loja+D009_Quantidade_Estoque_Liquido) FROM D009 WHERE D009_D049_Id=NEW.D009_D049_Id AND D009_Id!=NEW.D009_Id),0),
      NEW.D009_Quantidade_Estoque_B2B=D009_Quantidade_Estoque_B2B(NEW.D009_Id,NEW.D009_D049_Id,NEW.D009_Quantidade_Estoque_Liquido),
      NEW.D009_Quantidade_Compra_Emergencia=D009_Compra_Emergencia(NEW.D009_Quantidade_Minima_Estoque,NEW.D009_Quantidade_Estoque_Liquido,NEW.D009_Quantidade_OC);
END;



