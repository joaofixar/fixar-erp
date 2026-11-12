<?php
namespace hardness;

final class SegurancaSenha
{
    const TAMANHO_MINIMO = 8;
    const TAMANHO_MAXIMO = 20;

    public static function dataMarcoExpiracao($dataConfigurada = '')
    {
        $data = trim((string) $dataConfigurada);
        if ($data === '') return null;
        $marco = \DateTime::createFromFormat('!Y-m-d', $data);
        return $marco && $marco->format('Y-m-d') === $data ? $marco : null;
    }

    public static function vencimento($dataAlteracao, $dataMarcoConfigurada, $prazoVencimentoDias, $prazoInicialMarcoDias)
    {
        if (empty($dataAlteracao)) return null;
        try {
            $alteracao = new \DateTime($dataAlteracao);
        } catch (\Exception $e) {
            return null;
        }
        $alteracao->setTime(0, 0, 0);
        $prazoVencimentoDias = (int) $prazoVencimentoDias;
        $prazoInicialMarcoDias = (int) $prazoInicialMarcoDias;
        if ($prazoVencimentoDias < 1 || $prazoInicialMarcoDias < 1) return null;
        $marco = self::dataMarcoExpiracao($dataMarcoConfigurada);
        if (!$marco) return null;
        if ($alteracao < $marco) return (clone $marco)->modify('+' . $prazoInicialMarcoDias . ' days');
        return (clone $alteracao)->modify('+' . $prazoVencimentoDias . ' days');
    }

    public static function senhaEhHash($senhaArmazenada)
    {
        if (!is_string($senhaArmazenada) || $senhaArmazenada === '') return false;
        return !empty(password_get_info($senhaArmazenada)['algo']);
    }

    public static function validarSenhaArmazenada($senhaInformada, $senhaArmazenada)
    {
        if (!is_string($senhaInformada) || !is_string($senhaArmazenada) || $senhaArmazenada === '') return false;
        return self::senhaEhHash($senhaArmazenada) ? password_verify($senhaInformada, $senhaArmazenada) : hash_equals($senhaArmazenada, base64_encode($senhaInformada));
    }

    public static function validarNovaSenha($novaSenha)
    {
        if (!is_string($novaSenha)) return array('Informe uma senha válida.');
        $erros = array();
        if (strlen($novaSenha) < self::TAMANHO_MINIMO || strlen($novaSenha) > self::TAMANHO_MAXIMO) $erros[] = 'A senha deve ter entre 8 e 20 caracteres.';
        if (!preg_match('/[A-Z]/', $novaSenha)) $erros[] = 'A senha deve conter ao menos uma letra maiúscula.';
        if (!preg_match('/[a-z]/', $novaSenha)) $erros[] = 'A senha deve conter ao menos uma letra minúscula.';
        if (!preg_match('/[0-9]/', $novaSenha)) $erros[] = 'A senha deve conter ao menos um número.';
        return $erros;
    }

    public static function senhaReutilizada($senha, array $historico)
    {
        foreach ($historico as $senhaArmazenada) if (!empty($senhaArmazenada) && self::validarSenhaArmazenada($senha, $senhaArmazenada)) return true;
        return false;
    }

    public static function validarTrocaSenha($atual, $nova, $confirmacao, array $C007)
    {
        $erros = array();
        $armazenada = isset($C007['C007_Senha']) ? $C007['C007_Senha'] : '';
        if (!self::validarSenhaArmazenada($atual, $armazenada)) $erros[] = 'A senha atual informada não confere.';
        if ($nova !== $confirmacao) $erros[] = 'A confirmação da nova senha não confere.';
        $erros = array_merge($erros, self::validarNovaSenha($nova));
        if (self::senhaReutilizada($nova, array($armazenada, isset($C007['C007_Senha_Anterior_1']) ? $C007['C007_Senha_Anterior_1'] : '', isset($C007['C007_Senha_Anterior_2']) ? $C007['C007_Senha_Anterior_2'] : '', isset($C007['C007_Senha_Anterior_3']) ? $C007['C007_Senha_Anterior_3'] : ''))) $erros[] = 'A nova senha não pode ser igual à atual ou a uma das três senhas anteriores.';
        return $erros;
    }

    public static function gerarHash($senha) { return password_hash($senha, PASSWORD_BCRYPT); }
    public static function gerarHashDaSenhaArmazenada($senha)
    {
        if (empty($senha)) return null;
        if (self::senhaEhHash($senha)) return $senha;
        $legada = base64_decode($senha, true);
        return $legada === false ? null : self::gerarHash($legada);
    }
    public static function prepararAtualizacaoSenha($nova, array $C007)
    {
        return array('C007_Senha' => self::gerarHash($nova), 'C007_Senha_Anterior_1' => self::gerarHashDaSenhaArmazenada(isset($C007['C007_Senha']) ? $C007['C007_Senha'] : ''), 'C007_Senha_Anterior_2' => isset($C007['C007_Senha_Anterior_1']) ? $C007['C007_Senha_Anterior_1'] : null, 'C007_Senha_Anterior_3' => isset($C007['C007_Senha_Anterior_2']) ? $C007['C007_Senha_Anterior_2'] : null, 'C007_Data_Alteracao_Senha' => date('Y-m-d'));
    }
    public static function prepararMigracaoSenhaLegada($informada, $armazenada)
    {
        return self::senhaEhHash($armazenada) || !self::validarSenhaArmazenada($informada, $armazenada) ? null : self::gerarHash($informada);
    }
}
