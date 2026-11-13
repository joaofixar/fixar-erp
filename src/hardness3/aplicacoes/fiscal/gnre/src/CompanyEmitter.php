<?php
declare(strict_types=1);

/** Leitura própria do cadastro ERP para contribuinteEmitente; não usa NF-e. */
final class GnreCompanyEmitter
{
    /** Dados seguros para a prévia da tela; não alteram o cadastro ERP. */
    public static function preview(array $emitter): array
    {
        return ['cnpj'=>(string)($emitter['cnpj']??''),'name'=>(string)($emitter['name']??''),'address'=>(string)($emitter['address']??''),'city'=>(string)($emitter['city']??''),'city_name'=>(string)($emitter['city_name']??''),'city_erp_code'=>(string)($emitter['city_erp_code']??''),'uf'=>(string)($emitter['uf']??''),'cep'=>(string)($emitter['cep']??''),'phone'=>(string)($emitter['phone']??''),'errors'=>array_values($emitter['errors']??[])];
    }
    public function load(int $companyId, array $rules): array
    {
        $db=gnre_connect(); if(!$db) return ['errors'=>['base ERP indisponível.']];
        try {$s=mysqli_prepare($db,"SELECT C004.C004_Cnpj,C004.C004_Nome_Empresa,C004.C004_Logradouro,C004.C004_Numero,C004.C004_Bairro,C004.C004_Cep,C004.C004_DDD_Telefone,C004.C004_Telefone,D020.D020_Codigo_IBGE,D020.D020_Nome_Cidade,D018.D018_UF FROM C004 LEFT JOIN D020 ON D020.D020_Id=C004.C004_D020_Id LEFT JOIN D018 ON D018.D018_Id=C004.C004_D018_Id WHERE C004.C004_Id=? AND C004.C004_Flag_Ativo='S' LIMIT 1"); if(!$s) throw new RuntimeException(); mysqli_stmt_bind_param($s,'i',$companyId);mysqli_stmt_execute($s);$q=mysqli_stmt_get_result($s);$row=$q?mysqli_fetch_assoc($q):null;mysqli_stmt_close($s);} catch(Throwable $e){$row=null;} finally{mysqli_close($db);} if(!$row)return ['errors'=>['empresa ativa não encontrada.']];
        $v=static function($x):string{return preg_replace('/\D/','',(string)$x)??'';}; $raw=$v($row['D020_Codigo_IBGE']); $out=['cnpj'=>$v($row['C004_Cnpj']),'name'=>trim((string)$row['C004_Nome_Empresa']),'address'=>trim(trim((string)$row['C004_Logradouro']).', '.trim((string)$row['C004_Numero']).' - '.trim((string)$row['C004_Bairro'])), 'city'=>GnreMunicipality::gnreCode($raw),'city_erp_code'=>$raw,'city_name'=>trim((string)$row['D020_Nome_Cidade']),'uf'=>trim((string)$row['D018_UF']),'cep'=>$v($row['C004_Cep']),'phone'=>$v($row['C004_DDD_Telefone'].$row['C004_Telefone']),'errors'=>[]];
        foreach(['cnpj'=>'CNPJ','name'=>'razão social'] as $k=>$label)if($out[$k]===''||($k==='cnpj'&&!preg_match('/^\d{14}$/',$out[$k])))$out['errors'][]=$label.' ausente ou inválido no cadastro da empresa.';
        if(($rules['address_required']['cnpj']??'N')==='S')foreach(['address'=>'endereço','city'=>'município IBGE','uf'=>'UF','cep'=>'CEP','phone'=>'telefone'] as $k=>$label)if($out[$k]===''||($k==='city'&&!preg_match('/^\d{5}$/',$out[$k]))||($k==='cep'&&!preg_match('/^\d{8}$/',$out[$k]))||($k==='phone'&&!preg_match('/^\d{6,11}$/',$out[$k])))$out['errors'][]=$label.' ausente ou inválido no cadastro da empresa.';
        return $out;
    }
}
