unit Unit1;

interface

uses
  Windows, Messages, SysUtils, Variants, Classes, Graphics,
  Controls, Forms, Dialogs, Printers, HTTPSend, StdCtrls;

type
  TForm1 = class(TForm)
    Label1: TLabel;
    procedure FormCreate(Sender: TObject);
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  Form1: TForm1;

type
  TStrArray = array of string;
  TPassThroughData = Record                                                     //Declaração utilizada em Impressao_Paralela
   nLen: Integer;
   Data: Array [0..2000] of byte;
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

procedure TForm1.FormCreate(Sender: TObject);
var
  printDialog    : TPrintDialog;
  myFile : TextFile;
  fileOpen : TextFile;
  HTTP: THTTPSend;
  StringStream: TStringStream;
  PTBlock : TPassThroughData;
  F: TextFile;
  S: string;
  A: TStrArray;
  AnzTokens, i: Integer;
begin
  //Versão Imp. Windows
  printDialog := TPrintDialog.Create(Form1);
  if printDialog.Execute then
  begin
    // Carregar texto para imprimir
    HTTP := THTTPSend.Create;
    try
      HTTP.ProxyHost := '';
      HTTP.ProxyPort := '';
      HTTP.HTTPMethod('GET', loadIni('http') + 'sistema/funcoes/impressaoGenerica/imprimir/?ajax=true&noEmp=true&p=true&id=' + ParamStr(1));
      StringStream := TStringStream.Create('');
      StringStream.CopyFrom(HTTP.Document, HTTP.Document.Size);
      // Trim(StringStream.DataString)

      // Imprimir
      //Versão Imp. Windows
      S := StringStream.DataString;
      AnzTokens := Explode(A, '---------', S);
      for i := 0 to AnzTokens -1 do begin
        Printer.Title := 'Hardness - Impressão - Nr. ' + IntToStr(i);
        Printer.BeginDoc;
          PTBlock.nLen := Length(A[i]);
          StrPCopy(@PTBlock.Data, A[i]);
          Escape(Printer.handle, PASSTHROUGH, 0, @PTBlock, nil);
        Printer.EndDoc;
      end;

      {AssignFile(F, 'LPT1'); // LPT2,COM1,COM2...
      Rewrite(F);
      Writeln(F, StringStream.DataString);
      CloseFile(F);}

      // Debug
      {Writer := TStreamWriter.Create('teste.txt', false, TEncoding.Default);
      Writer.Write(StringStream.DataString);
      Writer.Free();}
      {AssignFile(fileOpen, 'C:\hardness\teste.txt');
      ReWrite(fileOpen);
      WriteLn(fileOpen, 'teste');
      WriteLn(fileOpen, 'teste2');
      CloseFile(fileOpen);}
    finally
      HTTP.Free;
    end;

    {
    // Abrir um arquivo
    AssignFile(fileOpen, 'C:\hardness\argoxPPLA');
    Reset(fileOpen);
    while not Eof(fileOpen) do
    begin
      //ReadLn(fileOpen, text);
      //WriteLn(myFile, text);
      //Write(myFile, text);

      // Proces one line at a time
      while not Eoln(fileOpen) do
      begin
        Read(fileOpen, letter);   // Read and display one letter at a time
        Write(myFile, letter);
      end;
      ReadLn(fileOpen, text);
    end;
    CloseFile(fileOpen);
    }
    
  //Versão Imp. Windows
  end;
  Application.Terminate;
end;

end.
