CREATE FUNCTION `T018_lancado_banco`(xid_empresa int, xid_conta int, xmes varchar(2), xano varchar(4), xTipo varchar(1)) RETURNS decimal(10,2)
BEGIN
    declare xretorno1 decimal(10,2);
    declare xretorno2 decimal(10,2);
    declare xretorno decimal(10,2);
    if xTipo = 'E' then
       select ifnull(sum(T019_Valor_Lancamento),0) into xretorno1 from T019 where T019_C004_Id=xid_empresa and T019_Data_Lancamento like concat(xano,'-',xmes,'%') and T019_D014_Id=xid_conta and T019_Valor_Lancamento>0;
    end if;
    if xTipo = 'S' then
       select ifnull(sum(T019_Valor_Lancamento),0) into xretorno1 from T019 where T019_C004_Id=xid_empresa and T019_Data_Lancamento like concat(xano,'-',xmes,'%') and T019_D014_Id=xid_conta and T019_Valor_Lancamento<0;
    end if;
    set xretorno=ifnull(xretorno1,0);
    return xretorno;
END;
