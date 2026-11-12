CREATE FUNCTION `T018_lancado`(xid_empresa int, xid_conta int, xmes varchar(2), xano varchar(4), xTipo varchar(1)) RETURNS decimal(10,2)
BEGIN
    declare xretorno decimal(10,2);
    if xTipo = 'E' then
       select ifnull(sum(T002_Valor_Saldo),0) into xretorno from T002 where T002_C004_Id=xid_empresa and T002_Data_Vencimento like concat(xano,'-',xmes,'%') and T002_D014_Id=xid_conta and T002_Data_Recebimento='0000-00-00' and T002_Flag_Status='1' and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null);
    end if;
    if xTipo = 'S' then
       select ifnull(sum(T015_Valor_Saldo),0) into xretorno from T015 where T015_C004_Id=xid_empresa and T015_Data_Vencimento like concat(xano,'-',xmes,'%') and T015_D014_Id=xid_conta and T015_Data_Pagamento='0000-00-00' and T015_Flag_Cancelada != 'S';
    end if;
    return xretorno;
END;