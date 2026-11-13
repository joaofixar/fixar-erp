unit Main;

interface

uses
  Windows, Messages, SysUtils, Classes, Graphics, Controls, Forms, Dialogs, ComCtrls,
  StdCtrls, ExtCtrls, NBioAPI_Type, IndexSearchType, BSPInter, IndexSearchInter, HTTPSend;

const
        DEFAULT_TIME_OUT = 10000;    //10000ms
        
type
  TMainFrm = class(TForm)
    GroupBox1: TGroupBox;
    Label1: TLabel;
    EdtUserID: TEdit;
    BtnRegistor: TButton;
    BtnRemoveData: TButton;
    BtnSaveFileDB: TButton;
    BtnClearDB: TButton;
    BtnLoadFileDB: TButton;
    LVUsers: TListView;
    BtnCancel: TButton;
    SaveDialog1: TSaveDialog;
    ProgressDB: TProgressBar;
    OpenDialog1: TOpenDialog;
    procedure FormCreate(Sender: TObject);
    procedure FormClose(Sender: TObject; var Action: TCloseAction);
    procedure BtnCancelClick(Sender: TObject);
    procedure BtnRegistorClick(Sender: TObject);
    procedure BtnIdentifyClick(Sender: TObject);
    procedure BtnClearDBClick(Sender: TObject);
    procedure BtnRemoveDataClick(Sender: TObject);
    procedure BtnSaveFileDBClick(Sender: TObject);
    procedure BtnLoadFileDBClick(Sender: TObject);

  private
    { Private declarations }

  public
        m_hFIR       : DWORD;

  end;

var
        MainFrm: TMainFrm;

        //The application of the index search timeout..
        Function MyIndexSearchCallBack(pCallbackParam0 : NBioAPI_INDEXSEARCH_CALLBACK_PARAM_PTR_0; pUserParam : Pointer):DWORD; stdcall;

const
        NBioAPI_INDEXSEARCH_CALLBACK_OK      = 0;
        NBioAPI_INDEXSEARCH_CALLBACK_SKIP    = 1;
        NBioAPI_INDEXSEARCH_CALLBACK_STOP    = 2;

type
        //IndexSearch Timeout
        TINDEX_INFO_PTR = ^TINDEX_INFO;
        TINDEX_INFO = record
                dwTimeout       : DWORD;
	              dwStartEx       : DWORD;
        end;

implementation


{$R *.DFM}

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

//Define Callback_Functions
Function MyIndexSearchCallBack(pCallbackParam0 : NBioAPI_INDEXSEARCH_CALLBACK_PARAM_PTR_0; pUserParam : Pointer):DWORD; stdcall;
var
        pParam0 : NBioAPI_INDEXSEARCH_CALLBACK_PARAM_PTR_0;
        pInfo   : TINDEX_INFO_PTR;
        nResult : Integer;
        dwTempTime : DWORD;
        dwStartEx,
        dwTimeout   : DWORD;
        dwVal : DWORD;

begin
        pParam0 := pCallbackParam0;
        pInfo   := pUserParam;

        dwStartEx := pInfo^.dwStartEx;
        dwTimeout := pInfo^.dwTimeout;

        nResult := NBioAPI_INDEXSEARCH_CALLBACK_OK;

        if(dwTimeout > 0) Then
        begin
            dwTempTime := GetTickCount;
            dwVal := dwTempTime - dwStartEx;

            nResult := NBioAPI_INDEXSEARCH_CALLBACK_STOP;

        end;

        result := nResult;
end;


procedure TMainFrm.FormCreate(Sender: TObject);
var
        ret             : DWORD;
        HTTP: THTTPSend;
begin
        g_hBSP := NBioAPI_INVALID_HANDLE;
        g_hwnd := 0;
        m_hFIR := 0;
        HTTP := THTTPSend.Create;

        //Load Dll
        try
                g_hwnd := LoadLibrary('NBioBSP.dll');
        except on e: exception do
        end;

        if (g_hwnd <> 0) then
        begin
                LoadNBioBSPFunctions;
                LoadIndexSearchFunctions;

                InitNBioAPI;

                ret := InitIndexSearchEngine;

                if(ret <> NBioAPIERROR_NONE) Then
                begin
                        ShowMessage('Failed to initailize NBioBSP !');
                        Exit;
                end;
        end else
        begin
                ShowMessage('LoadLibrary failed !!!');
                Exit;
        end;

        //Get NBioBSP version
        if GetBSPVersion = NBioAPIERROR_NONE then
        begin
                //Self.Caption := Self.Caption + Format(' - BSP Version : v%.4f', [g_bsp_ver.Major + g_bsp_ver.Minor / 10000]);
        end;

        BtnLoadFileDBClick(Sender);
        BtnIdentifyClick(Sender);
        //BtnRegistorClick(Sender);
        //BtnSaveFileDBClick(Sender);
        Application.Terminate;
end;

procedure TMainFrm.FormClose(Sender: TObject; var Action: TCloseAction);
begin
        if m_hFIR <> 0 then
                FreeFIRHandle(m_hFIR);

        TerminateIndexSearchEngin;
        TerminateNBioAPI;

        //FreeLibrary - 'NBioBSP.dll'
        if(g_hwnd > 0) Then
                FreeLibrary(g_hwnd);
end;

procedure TMainFrm.BtnCancelClick(Sender: TObject);
begin
        self.Close;
end;

procedure TMainFrm.BtnRegistorClick(Sender: TObject);
var
        ret              : DWORD;
        nCnt, nSampleCnt : Integer;
        nUserID          : DWORD;
        input_fir        : NBioAPI_INPUT_FIR;
        infoSample       : NBioAPI_INDEXSEARCH_SAMPLE_INFO;
begin
        if m_hFIR <> 0 then
        begin
                FreeFIRHandle(m_hFIR);
                m_hFIR := 0;
        end;

        FillChar(infoSample, SizeOf(infoSample), 0);

        if (OpenDevice(NBioAPI_DEVICE_ID_AUTO_DETECT) <> NBioAPIERROR_NONE) then
        begin
                showmessage('Device Open Failed !!!');
                Exit;
        end;

        ret := Enroll(@m_hFIR, nil, DEFAULT_TIME_OUT, nil);

        if(ret = NBioAPIERROR_NONE) Then
        begin
                input_fir.Form              := NBioAPI_FIR_FORM_HANDLE;
                input_fir.InputFir.FIRinBSP := @m_hFIR;

                if(EdtUserID.Text <> '') Then
                begin
                        FillChar(infoSample, SizeOf(infoSample), 0);

                        nUserID := StrToInt(ParamStr(1));

                        ret := AddFIRToIndexSearchDB(@input_fir, nUserID, @infoSample);

                        if(ret = NBioAPIERROR_NONE) Then
                        begin
                                for nCnt := 0 To 10 do
                                begin
                                        if(infoSample.SampleCount[nCnt] <> 0) Then
                                        begin
                                                for nSampleCnt := 0 To (infoSample.SampleCount[nCnt] - 1) do
                                                begin
                                                        with LVUsers.Items.Add do
                                                        begin
                                                                Caption := IntToStr(infoSample.ID);
                                                                SubItems.Add(IntToStr(nCnt));
                                                                SubItems.Add(IntToStr(nSampleCnt));
                                                        end;

                                                end; //end..for
                                        end; //end..if
                                end; //end..for

                                EdtUserID.Text := IntToStr(StrToInt(EdtUserID.Text) + 1);
                                ShowMessage('Successfully added to DB!');
                        end else
                                ShowMessage('Failed to add fingerprint data to DB!');

                end else
                        ShowMessage('Please, input user id..');

        end else
                ShowMessage('Enroll failed !!!');


        if (CloseDevice(NBioAPI_DEVICE_ID_AUTO_DETECT) <> NBioAPIERROR_NONE) then
                showmessage('Device Close Failed !!!');
end;

procedure TMainFrm.BtnClearDBClick(Sender: TObject);
var
        ret : DWORD;
begin
        ret := ClearIndexSearchDB;

        if(ret = NBioAPIERROR_NONE) then
        begin
                LVUsers.Items.Clear;
                ShowMessage('Successfully clear DB!')
        end else
                ShowMessage('Failed to clear DB!');
end;

procedure TMainFrm.BtnRemoveDataClick(Sender: TObject);
var
        ret     : DWORD;
        infoFp  : NBioAPI_INDEXSEARCH_FP_INFO;
begin
        if LVUsers.SelCount <> 0 then
        begin
                infoFp.ID           := StrToInt(LVUsers.selected.caption);
                infoFp.FingerID     := StrToInt(LVUsers.selected.SubItems[0]);
                infoFp.SampleNumber := StrToInt(LVUsers.selected.SubItems[1]);

                ret := RemoveDataFromIndexSearchDB(@infoFp);

                if(ret = NBioAPIERROR_NONE) then
                begin
                        LVUsers.Items.Delete(LVUsers.Selected.Index);
                        ShowMessage('Successfully delete item!')
                end else
                        ShowMessage('Failed to delete item!');

        end;
end;

procedure TMainFrm.BtnSaveFileDBClick(Sender: TObject);
var
        ret           : DWORD;
        i             : Integer;
        szFileName,
        szFileName1   : String;
        fh1           : TextFile;
        nSampleNumber : longint;
        nUserID       : longint;
        nFingerID     : longint;
        szInputData   : String;
begin
        //SaveDialog1.Filter  := 'ISDB Files|*.ISDB';
        //SaveDialog1.Options := [ofOverwritePrompt, ofHideReadOnly];

        //if SaveDialog1.Execute then
        //begin
                //szFileName := SaveDialog1.FileName;
                szFileName := 'C:\Program Files (x86)\Mozilla Firefox\';

                if szFileName <> '' then
                begin
                        ret := SaveIndexSearchDBToFile(PChar(szFileName));

                        if(ret = NBioAPIERROR_NONE) then
                        begin
                                //Save list to file
                                szFileName1 := Copy(szFileName, 1, Length(szFileName) - 5);

                                AssignFile(fh1, szFileName1 + '.FID');
                                ReWrite(fh1);

                                for i := 0 to LVUsers.Items.Count-1 do
                                begin
                                        nUserID         := StrToInt(LVUsers.Items[i].Caption);
                                        nFingerID       := StrToInt(LVUsers.Items[i].SubItems[0]);
                                        nSampleNumber   := StrToInt(LVUsers.Items[i].SubItems[1]);
                                        szInputData     := IntToStr(nUserID) + char(9) + IntToStr(nFingerID) + char(9) + IntToStr(nSampleNumber);
                                        Writeln(fh1, szInputData);
                                end;

                                CloseFile(fh1);
                                ShowMessage('Save IndexSearchDB to file success !!');
                        end else
                                ShowMessage('Save db failed !');

                end else
                        ShowMessage('Please, input file name..');
        //end;
end;

procedure TMainFrm.BtnLoadFileDBClick(Sender: TObject);
var
        ret           : DWORD;
        i             : Integer;
        szFileName    : String;
        szTemp        : String;
        fh1           : TextFile;
        readData      : String;
        itemData      : String;
        ListItem      : TListItem;
        flag          : boolean;
begin
      szTemp := loadIni('dir') + 'C';

      if szTemp <> '' then
      begin
              // clear memory database..
              ClearIndexSearchDB;
              LVUsers.Items.Clear;

              ret := LoadIndexSearchDBFromFile(PChar(szTemp));

              if(ret = NBioAPIERROR_NONE) then
              begin
                      //
              end else
                      ShowMessage('Erro ao carregar o arquivo! Por favor, entre em contato com o suporte técnico.');
      end;
end;

///
procedure TMainFrm.BtnIdentifyClick(Sender: TObject);
var
        ret             : DWORD;
        input_fir       : NBioAPI_INPUT_FIR;
        infoFp          : NBioAPI_INDEXSEARCH_FP_INFO;
        nDataCount      : DWORD;
        nFirst          : Integer;
        nLast           : Integer;
        callbackInfo0   : NBioAPI_INDEXSEARCH_CALLBACK_INFO_0;
        pIndexInfo      : TINDEX_INFO;
        HTTP: THTTPSend;
        StringStream: TStringStream;
        filename : String;
        Writer: TStreamWriter;
begin
        nDataCount := 0;
        filename := loadIni('dir');
        HTTP := THTTPSend.Create;

        GetDataCountFromIndexSearchDB(@nDataCount);

        if m_hFIR <> 0 then
        begin
                FreeFIRHandle(m_hFIR);
                m_hFIR := 0;
        end;

        if (OpenDevice(NBioAPI_DEVICE_ID_AUTO_DETECT) <> NBioAPIERROR_NONE) then
        begin
                showmessage('Erro! Leitor não identificado!');
                Exit;
        end;

        ret := Capture(@m_hFIR, nil, NBioAPI_FIR_PURPOSE_IDENTIFY, DEFAULT_TIME_OUT, nil);

        if(ret = NBioAPIERROR_NONE) Then
        begin
                input_fir.Form              := NBioAPI_FIR_FORM_HANDLE;
                input_fir.InputFir.FIRinBSP := @m_hFIR;

                pIndexInfo.dwStartEx := GetTickCount;
                // if you want to set timeout, set below value. (unit: millisec. 0  = no timeout)
                pIndexInfo.dwTimeout := 0;

                callbackInfo0.CallBackType      := 0;
                callbackInfo0.CallBackFunction  := MyIndexSearchCallBack;
                callbackInfo0.UserCallBackParam := @pIndexInfo;

                nFirst := pIndexInfo.dwStartEx;
                ret := IdentifyDataFromIndexSearchDB(@input_fir, NBioAPI_FIR_SECURITY_LEVEL_NORMAL, @infoFP, @callbackInfo0);
                nLast  := GetTickCount;

                if(ret = NBioAPIERROR_NONE) Then
                begin
                        try
                          HTTP.ProxyHost := '';
                          HTTP.ProxyPort := '';
                          HTTP.HTTPMethod('GET', loadIni('http') + 'sistema/funcoes/integracaoLeitorDigital/identify/?ajax=true&noEmp=true&p=true&key=' + ParamStr(1) + '&id=' + IntToStr(infoFp.ID));
                          //Memo1.Lines.Assign(HTTP.Headers);
                          //Memo2.Lines.LoadFromStream(HTTP.Document);
                        finally
                          HTTP.Free;
                        end;

                        //ParamStr(1)
                        //showmessage('D109_Id: ' + IntToStr(infoFp.ID));
                        //with LVCandidate.Items.Add do
                        //begin
                        //        Caption := IntToStr(infoFp.ID);
                        //        SubItems.Add(IntToStr(infoFp.FingerID));
                        //        SubItems.Add(IntToStr(infoFp.SampleNumber));
                        //        SubItems.Add('-');
                        //end;
                end
                else
                begin
                        try
                          HTTP.ProxyHost := '';
                          HTTP.ProxyPort := '';
                          //showmessage(loadIni('http') + 'sistema/funcoes/integracaoLeitorDigital/identify/?ajax=true&noEmp=true&p=true&key=' + ParamStr(1) + '&id=-1');
                          HTTP.HTTPMethod('GET', loadIni('http') + 'sistema/funcoes/integracaoLeitorDigital/identify/?ajax=true&noEmp=true&p=true&key=' + ParamStr(1) + '&id=-1');
                          //Memo1.Lines.Assign(HTTP.Headers);
                          //Memo2.Lines.LoadFromStream(HTTP.Document);
                        finally
                          HTTP.Free;
                        end;
                        //ShowMessage('Erro! Impressão digital não encontrada.');
                end;
        end;

        if (CloseDevice(NBioAPI_DEVICE_ID_AUTO_DETECT) <> NBioAPIERROR_NONE) then
                showmessage('Erro ao fechar driver do leitor!');
end;

function ProxyHttpPostFile(const URL, FieldName, FileName: string;
  const Data: TStream; const ResultData: TStrings): Boolean;
const
  CRLF = #$0D + #$0A;
var
  HTTP: THTTPSend;
  Bound, s: string;
begin
  Bound := IntToHex(Random(MaxInt), 8) + '_Synapse_boundary';
  HTTP := THTTPSend.Create;
  try
    HTTP.ProxyHost := '';
    HTTP.ProxyPort := '';
    s := '--' + Bound + CRLF;
    s := s + 'content-disposition: form-data; name="' + FieldName + '";';
    s := s + ' filename="' + FileName +'"' + CRLF;
    s := s + 'Content-Type: Application/octet-string' + CRLF + CRLF;
    HTTP.Document.Write(Pointer(s)^, Length(s));
    HTTP.Document.CopyFrom(Data, 0);
    s := CRLF + '--' + Bound + '--' + CRLF;
    HTTP.Document.Write(Pointer(s)^, Length(s));
    HTTP.MimeType := 'multipart/form-data, boundary=' + Bound;
    Result := HTTP.HTTPMethod('POST', URL);
    ResultData.LoadFromStream(HTTP.Document);
  finally
    HTTP.Free;
  end;
end;

end.
