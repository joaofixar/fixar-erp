"""Regressão offline: python3 scripts/testes/base-ibs-cbs-deducoes.py.

Avalia as expressões da chamada SQL e da função real, sem conexão com banco.
Não substitui a validação da procedure instalada no MySQL.
"""
import itertools
import re
import unittest
from decimal import Decimal, ROUND_HALF_UP
from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
PROCEDURE = (ROOT / 'scripts/sql/T008_Gravar_Totalizacao_4_Reforma.sql').read_text()
FUNCTION = (ROOT / 'scripts/sql/Calcular_T008A_Valor_Base_CBS_IBS.sql').read_text()
TAXES = {
    'PIS': 'T008_Valor_PIS',
    'COFINS': 'T008_Valor_COFINS',
    'ICMS': 'T008_Valor_ICMS',
    'DIFAL': 'T008_Valor_Partilha_ICMS_Destino',
    'FCP': 'T008_Valor_FCP',
    'FCP_Dest': 'T008_Valor_FCP_Partilha',
}


def split_arguments(text):
    result, start, depth = [], 0, 0
    for index, char in enumerate(text):
        if char == '(':
            depth += 1
        elif char == ')':
            depth -= 1
        elif char == ',' and depth == 0:
            result.append(text[start:index].strip())
            start = index + 1
    result.append(text[start:].strip())
    return result


CALL = split_arguments(re.search(
    r'T008A_Valor_Base_CBS_IBS\s*=\s*Calcular_T008A_Valor_Base_CBS_IBS\((.*?)\n\s*\),',
    PROCEDURE, re.S).group(1))
PARAMETERS = re.findall(r'^\s*(x\w+)\s+(?:bigint|decimal)', FUNCTION, re.M)
EXPRESSION = re.search(r'-- Cálculo da base\s+RETURN\s+(.*?);', FUNCTION, re.S).group(1)
SELECT, INTO = re.search(r'SELECT (.*?)\s+INTO (.*?)\s+FROM T008', PROCEDURE, re.S).groups()
CFOP_BINDINGS = dict(zip(split_arguments(INTO), split_arguments(SELECT)))


def evaluate(expression, values):
    # Somente expressões dos arquivos versionados, nunca conteúdo de usuário.
    expression = re.sub(r'\s+', ' ', expression).replace(' = ', ' == ')
    return eval(expression, {'__builtins__': {}}, dict(values,
        IFNULL=lambda value, default: default if value is None else value,
        IF=lambda condition, yes, no: yes if condition else no))


def calculate(item, flags):
    values = dict(item)
    for variable, column in CFOP_BINDINGS.items():
        if column.startswith('D006_Flag_Deduz_'):
            values[variable] = flags.get(column)
    assert len(CALL) == len(PARAMETERS), 'Assinatura da função incompatível'
    arguments = [evaluate(arg, values) for arg in CALL]
    return evaluate(EXPRESSION, dict(zip(PARAMETERS, arguments)))


def flag(key):
    return 'D006_Flag_Deduz_' + key + '_Base_IBS_CBS'


def item_fixture():
    item = {name[1:]: Decimal('0') for name in PARAMETERS}
    item.update({
        'T008_Valor_Total_Preco_Sem_Desconto': Decimal('4491.43'),
        'T008_Valor_Total_Frete': Decimal('123.26'),
        'T008A_Aliquota_CBS': Decimal('0.90'),
        'T008A_Aliquota_IBS_UF': Decimal('0.10'),
    })
    for key, amount in zip(TAXES, ['28.80', '132.90', '184.59', '738.35', '92.29', '92.29']):
        item[TAXES[key]] = Decimal(amount)
    return item


class BaseDeductionsTest(unittest.TestCase):
    def test_nota_232479(self):
        item = item_fixture()
        flags = {flag(key): 'S' for key in TAXES}
        self.assertEqual(calculate(item, flags), Decimal('3345.47'))
        flags[flag('FCP')] = 'N'
        base = calculate(item, flags)
        self.assertEqual(base, Decimal('3437.76'))
        for rate, expected in [('0.009', '30.94'), ('0.001', '3.44')]:
            self.assertEqual((base * Decimal(rate)).quantize(Decimal('.01'), rounding=ROUND_HALF_UP), Decimal(expected))
        self.assertEqual(item[TAXES['FCP']], Decimal('92.29'))
        self.assertEqual(item[TAXES['FCP_Dest']], Decimal('92.29'))

    def test_each_combination_only_removes_selected_deductions(self):
        item = item_fixture()
        # Valores diferentes identificam também troca acidental entre os dois FCPs.
        for key, amount in zip(TAXES, ['1', '2', '4', '8', '16', '32']):
            item[TAXES[key]] = Decimal(amount)
        item['T008_Valor_Total_Seguro'] = Decimal('7')
        item['T008_Valor_Total_Despesas_Acessorias'] = Decimal('11')
        item['T008_Valor_Total_Imposto_Importacao'] = Decimal('13')
        item['T008_Valor_Total_Desconto'] = Decimal('17')
        for choices in itertools.product(['S', 'N'], repeat=6):
            flags = dict(zip(map(flag, TAXES), choices))
            expected = Decimal('4628.69') - sum(item[TAXES[key]] for key, choice in zip(TAXES, choices) if choice == 'S')
            with self.subTest(choices=choices):
                self.assertEqual(calculate(item, flags), expected)

    def test_absent_null_and_blank_configuration_preserve_deductions(self):
        for default in [None, '', 'S']:
            self.assertEqual(calculate(item_fixture(), {flag(key): default for key in TAXES}), Decimal('3345.47'))
        self.assertEqual(calculate(item_fixture(), {}), Decimal('3345.47'))

    def test_null_and_zero_taxes(self):
        for value in [None, Decimal('0')]:
            item = item_fixture()
            for column in TAXES.values():
                item[column] = value
            for choice in ['S', 'N']:
                self.assertEqual(calculate(item, {flag(key): choice for key in TAXES}), Decimal('4614.69'))


if __name__ == '__main__':
    unittest.main()
