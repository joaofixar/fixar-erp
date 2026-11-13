CREATE FUNCTION `Quantidade`(xQuantidade decimal(10,3), xUnidade varchar(3), xPeso_Unitario decimal(10,3), xPeso_Pacote decimal(10,3), xQuantidade_Pacote int(10), xUnidade_Retorno varchar(3)) RETURNS decimal(10,3)
BEGIN
   declare xQte_KLG decimal(10,3);
   declare xQte_PCT decimal(10,3);
   declare xQte_UND decimal(10,3);
   if xUnidade='KLG' THEN
       set xQte_KLG = xQuantidade;
       set xQte_PCT = xQuantidade/xPeso_Pacote;
       set xQte_UND = xQuantidade/xPeso_Unitario;
   elseif xUnidade='PCT' THEN
       set xQte_KLG = xQuantidade*xPeso_Pacote;
       set xQte_PCT = xQuantidade;
       set xQte_UND = xQuantidade*xQuantidade_Pacote;
   elseif xUnidade='UND' THEN
       set xQte_KLG = xQuantidade*xPeso_Unitario;
       set xQte_PCT = xQuantidade/xQuantidade_Pacote;
       set xQte_UND = xQuantidade;
   elseif xUnidade!='XXX' THEN
       set xQte_KLG = xQuantidade*xPeso_Unitario;
       set xQte_PCT = xQuantidade/xQuantidade_Pacote;
       set xQte_UND = xQuantidade;
   end if;
   if xUnidade_Retorno='KLG' THEN
      return xQte_KLG;
   elseif xUnidade_Retorno='PCT' THEN
      return xQte_PCT;
   elseif xUnidade_Retorno='UND' THEN
      return xQte_UND;
   elseif xUnidade_Retorno!='XXX' THEN
      return xQte_UND;
   end if;
END;
