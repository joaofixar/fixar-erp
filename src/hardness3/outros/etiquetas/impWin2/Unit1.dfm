object Form1: TForm1
  Left = 661
  Top = 409
  Width = 599
  Height = 145
  Caption = 'Impress'#227'o de Etiquetas - Hardness Sistemas'
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -11
  Font.Name = 'Tahoma'
  Font.Style = []
  OldCreateOrder = False
  OnCreate = FormCreate
  PixelsPerInch = 96
  TextHeight = 13
  object Label1: TLabel
    Left = 32
    Top = 24
    Width = 115
    Height = 13
    Caption = 'Etiquetas para Imprimir:'
  end
  object BtnImprimir: TButton
    Left = 296
    Top = 66
    Width = 75
    Height = 25
    Caption = 'Imprimir'
    TabOrder = 0
    OnClick = BtnImprimirClick
  end
  object BtnAtualizar: TButton
    Left = 198
    Top = 66
    Width = 75
    Height = 25
    Caption = 'Atualizar'
    TabOrder = 1
    OnClick = BtnAtualizarClick
  end
  object ComboBox1: TComboBox
    Left = 167
    Top = 21
    Width = 369
    Height = 21
    AutoComplete = False
    ItemHeight = 13
    TabOrder = 2
    Text = 'Atualizando...'
  end
end
