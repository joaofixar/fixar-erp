//*****************************************************************************
//
// IndexSearchDemoDP : 'NBioBSP.DLL' Sample Code
//
//*****************************************************************************
// Dev. Tool   : Delphi 5
// Copyright   : NITGen Co., Ltd.
//*****************************************************************************

program hardnessCadDigital;

uses
  Forms,
  Main in 'Main.pas' {MainFrm},
  NBioAPI_Type in 'NBioAPI_Type.pas',
  BSPInter in 'BSPInter.pas',
  IndexSearchType in 'IndexSearchType.pas',
  IndexSearchInter in 'IndexSearchInter.pas';

{$R *.RES}

begin
  Application.Initialize;
  Application.Title := 'IndexSearchDemoDP';
  Application.CreateForm(TMainFrm, MainFrm);
  Application.Run;
end.
