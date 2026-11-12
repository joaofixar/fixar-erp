object MainFrm: TMainFrm
  Left = 215
  Top = 203
  Caption = 'Hardness - NBioBSP_IndexSearchTest_DP'
  ClientHeight = 202
  ClientWidth = 347
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -11
  Font.Name = 'MS Sans Serif'
  Font.Style = []
  OldCreateOrder = False
  Position = poScreenCenter
  OnClose = FormClose
  OnCreate = FormCreate
  PixelsPerInch = 96
  TextHeight = 13
  object GroupBox1: TGroupBox
    Left = 13
    Top = 16
    Width = 329
    Height = 56
    Caption = 'Registration'
    TabOrder = 0
    object Label1: TLabel
      Left = 16
      Top = 24
      Width = 42
      Height = 13
      Caption = 'User ID :'
    end
    object EdtUserID: TEdit
      Left = 65
      Top = 22
      Width = 98
      Height = 21
      ImeName = 'Microsoft IME 2003'
      TabOrder = 0
      Text = '1'
    end
    object BtnRegistor: TButton
      Left = 171
      Top = 22
      Width = 145
      Height = 23
      Caption = 'Registor FP to DB'
      TabOrder = 1
      OnClick = BtnRegistorClick
    end
  end
  object BtnRemoveData: TButton
    Left = 13
    Top = 340
    Width = 163
    Height = 23
    Caption = 'Remove Data'
    TabOrder = 1
    OnClick = BtnRemoveDataClick
  end
  object BtnSaveFileDB: TButton
    Left = 179
    Top = 340
    Width = 163
    Height = 23
    Caption = 'Save DB to file'
    TabOrder = 2
    OnClick = BtnSaveFileDBClick
  end
  object BtnClearDB: TButton
    Left = 13
    Top = 366
    Width = 163
    Height = 23
    Caption = 'Clear DB'
    TabOrder = 3
    OnClick = BtnClearDBClick
  end
  object BtnLoadFileDB: TButton
    Left = 179
    Top = 366
    Width = 163
    Height = 23
    Caption = 'Load DB from file'
    TabOrder = 4
    OnClick = BtnLoadFileDBClick
  end
  object LVUsers: TListView
    Left = 13
    Top = 101
    Width = 664
    Height = 221
    Columns = <
      item
        Caption = 'User ID'
        Width = 105
      end
      item
        Caption = 'FP ID'
        Width = 110
      end
      item
        Caption = 'Sample NO.'
        Width = 110
      end>
    GridLines = True
    ReadOnly = True
    RowSelect = True
    TabOrder = 5
    ViewStyle = vsReport
  end
  object BtnCancel: TButton
    Left = 566
    Top = 366
    Width = 113
    Height = 23
    Caption = 'Close'
    TabOrder = 6
    OnClick = BtnCancelClick
  end
  object ProgressDB: TProgressBar
    Left = 13
    Top = 324
    Width = 330
    Height = 6
    Step = 1
    TabOrder = 7
  end
  object SaveDialog1: TSaveDialog
    DefaultExt = '.ISDB'
    Options = [ofOverwritePrompt, ofHideReadOnly]
    Left = 392
    Top = 369
  end
  object OpenDialog1: TOpenDialog
    DefaultExt = '.ISDB'
    Options = [ofOverwritePrompt, ofHideReadOnly]
    Left = 360
    Top = 369
  end
end
