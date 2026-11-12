unit Unit1;

interface

uses
  Windows, Messages, SysUtils, Variants, Classes, Graphics,
  Controls, Forms, Dialogs, Printers, HTTPSend, StdCtrls, WinSpool;

type
  TForm1 = class(TForm)
    BtnImprimir: TButton;
    BtnAtualizar: TButton;
    ComboBox1: TComboBox;
    Label1: TLabel;
    procedure FormCreate(Sender: TObject);
    procedure BtnImprimirClick(Sender: TObject);
    procedure BtnAtualizarClick(Sender: TObject);
  private
    { Private declarations }
  public
    { Public declarations }
  end;

const
  Codes64 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+/';

var
  Form1: TForm1;
  ComboItems: array of integer;

type
  TStrArray = array of string;
  TPassThroughData = packed record
   nLen: Integer;
   Data: array[0..10000] of AnsiChar;
end;

type
  TPrnBuffRec = packed record
   bufflength: Word;
   Buff_1: array[0..255] of AnsiChar;
end;

implementation

{$R *.dfm}

function Explode(var a: TStrArray; Border, S: string): Integer;
var
  S2: string;
begin
  Result  := 0;
  S2 := S + Border;
  repeat
    SetLength(A, Length(A) + 1);
    a[Result] := Copy(S2, 0,Pos(Border, S2) - 1);
    Delete(S2, 1,Length(a[Result] + Border));
    Inc(Result);
  until S2 = '';
end;

function loadIni(item: String): String;
var
  ini: string;
  SomeTxtFile : TextFile;
  buffer : string;
begin
  Result := '';
  AssignFile(SomeTxtFile, 'C:\hardness\hardness.ini') ;
  Reset(SomeTxtFile);
  ReadLn(SomeTxtFile, buffer);
  if ('http' = item) then
      Result := buffer;
  ReadLn(SomeTxtFile, buffer);
  if ('dir' = item) then
      Result := buffer;
  CloseFile(SomeTxtFile);
end;

function removeNulls(S: String): String;
var
  i: Integer;
  S2: String;
begin
  i := 1;
  while i <= Length(S) do
  begin
    if S[i]=#0 then
      S2 := S2 + #13 + #10
    else
      S2 := S2 + S[i];
    i := i + 1;
  end;
  Result := S2 + #0;
end;

procedure RemoveNullCharacters(var s: WideString);
var
  i, j: Integer;
begin
  j := 0;
  for i := 1 to Length(s) do
    if s[i] <> #0 then begin
      Inc(j);
      s[j] := s[i];
    end;
  if j < Length(s) then
    SetLength(s, j);
end;

function DirectToPrinter(S: String; NextLine: Boolean): Boolean;
var
  Buff: TPrnBuffRec;
  TestInt: Integer;
begin
  TestInt := PassThrough;
  if Escape(Printer.Handle, QUERYESCSUPPORT, SizeOf(TESTINT), @testint, nil) > 0 then
  begin
    if NextLine then  S := S + #13 + #10;
    //S := removeNulls(S);
    //RemoveNullCharacters(S);
    //StrPCopy(Buff.Buff_1, S);
    //Buff.bufflength := StrLen(Buff.Buff_1);
    //Escape(Printer.Canvas.Handle, Passthrough, 0, @Buff, nil);
    Escape(Printer.Canvas.Handle, Passthrough, 0, PChar(S), nil);
    Result := True;
  end
  else
    Result := False;
end;

procedure TForm1.FormCreate(Sender: TObject);
begin
  BtnAtualizar.Click;
  //Application.Terminate;
end;

procedure TForm1.BtnImprimirClick(Sender: TObject);
var
  printDialog    : TPrintDialog;
  myFile : TextFile;
  fileOpen : TextFile;
  HTTP: THTTPSend;
  StringStream: TStringStream;
  PTBlock : TPassThroughData;
  F: TextFile;
  S, S1: String;
  A: TStrArray;
  AnzTokens, i, i2: Integer;
  ArrayOfBytes: pByteArray;
  N: DWORD;
begin
  if ComboBox1.ItemIndex = -1 then
  begin
    ShowMessage('Nenhuma etiqueta selecionada.');
  end
  else
  begin

{
      // Carregar texto para imprimir
      HTTP := THTTPSend.Create;
      try
        HTTP.ProxyHost := '';
        HTTP.ProxyPort := '';
        HTTP.HTTPMethod('GET', loadIni('http') + 'sistema/funcoes/impWin/imprimir/?ajax=true&noEmp=true&p=true&C007_Id=' + IntToStr(ComboItems[ComboBox1.ItemIndex]));
        StringStream := TStringStream.Create('');
        StringStream.CopyFrom(HTTP.Document, HTTP.Document.Size);
        S := StringStream.DataString;
      finally
        HTTP.Free;
      end;

      //  example:
printDialog := TPrintDialog.Create(Form1);
    if printDialog.Execute then
    begin
printer.BeginDoc;
try
  DirectToPrinter(S, false);
finally
  printer.EndDoc;
end;
    end;
}


    //Versão Imp. Windows
    printDialog := TPrintDialog.Create(Form1);
    if printDialog.Execute then
    begin
      // Carregar texto para imprimir
      HTTP := THTTPSend.Create;
      try
        HTTP.ProxyHost := '';
        HTTP.ProxyPort := '';
        HTTP.HTTPMethod('GET', loadIni('http') + 'sistema/funcoes/impWin/imprimir/?ajax=true&noEmp=true&p=true&C007_Id=' + IntToStr(ComboItems[ComboBox1.ItemIndex]));
        StringStream := TStringStream.Create('');
        StringStream.CopyFrom(HTTP.Document, HTTP.Document.Size);
        // Trim(StringStream.DataString)

        // Imprimir
        //Versão Imp. Windows
        S := StringStream.DataString;

{
    // Try to open a printer file
    AssignPrn(myFile);

    // Now prepare to write to the printer
    ReWrite(myFile);

    // Write a couple of well known words to this file -
    // they will be printed instead
    Write(myFile, S);

    // Close the file
    CloseFile(myFile);
}

        AnzTokens := Explode(A, '---------', S);
        for i := 0 to AnzTokens -1 do begin
          // Debug
          AssignFile(fileOpen, 'C:\hardness\debug-' + IntToStr(i) + '.txt');
          ReWrite(fileOpen);
          WriteLn(fileOpen, A[i]);
          CloseFile(fileOpen);
          // Print
          Printer.Title := 'Hardness - Impressão - Nr. ' + IntToStr(i);
          Printer.BeginDoc;

          {
            for i2 := 1 to Length(A[i]) -1 do begin
              ArrayOfBytes^[i2] := Ord(A[i][i2]);
            end;
            WritePrinter(Printer.Handle, ArrayOfBytes, Length(A[i]), N);
            }

            PTBlock.nLen := Length(A[i]);
            StrPCopy(@PTBlock.Data, A[i]);
            //PTBlock.nLen := StrLen(PTBlock.Data);
            Escape(Printer.Handle, PASSTHROUGH, 0, @PTBlock, nil);
          Printer.EndDoc;

        end;

      finally
        HTTP.Free;
        BtnAtualizar.Click;
      end;
    end;



  end;
end;

procedure TForm1.BtnAtualizarClick(Sender: TObject);
var
  HTTP: THTTPSend;
  StringStream: TStringStream;
  S: string;
  A, A2: TStrArray;
  C, I: Integer;
begin
    ComboBox1.Text := 'Atualizando...';
    // Carregar texto para imprimir
    HTTP := THTTPSend.Create;
    try
      HTTP.ProxyHost := '';
      HTTP.ProxyPort := '';
      HTTP.HTTPMethod('GET', loadIni('http') + 'sistema/funcoes/impWin/listaFilaDeImpressao/?ajax=true&noEmp=true&p=true');
      StringStream := TStringStream.Create('');
      StringStream.CopyFrom(HTTP.Document, HTTP.Document.Size);
      // Trim(StringStream.DataString)
      //SetLength(ComboItems,0);
      ComboBox1.Items.Clear;
      S := StringStream.DataString;
      if S = 'VAZIO' then
      begin
        ComboBox1.Text := 'Nenhuma etiqueta para imprimir.';
      end
      else
      begin
        ComboBox1.Text := 'Selecione:';
        C := Explode(A, '---------', S);
        SetLength(ComboItems, C);
        for I := 0 to C -1 do begin
          Explode(A2, '|', A[I]);
          ComboItems[I] := StrToInt(A2[0]);
          ComboBox1.Items.Add(A2[1] + ' - ' + A2[2] + ' etiqueta(s)');
        end;
      end;
    finally
      HTTP.Free;
    end;
end;

end.
