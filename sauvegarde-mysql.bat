@echo off
REM ================================================================
REM  SAUVEGARDE MYSQL - SIRH Khadija Consulting
REM  Windows / Laragon - cree un dump SQL dans le dossier sauvegardes
REM  Utilisation : double-clic ou ligne de commande
REM ================================================================
setlocal
set "MYSQLDUMP=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe"
set "DB_USER=root"
set "DB_PASS="
set "DB_NAME=sirh_khadija"
set "BACKUP_DIR=%~dp0sauvegardes"

for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value ^| find "="') do set "DT=%%I"
set "STAMP=%DT:~0,8%_%DT:~8,6%"

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

if "%DB_PASS%"=="" (
  "%MYSQLDUMP%" -u "%DB_USER%" --routines --triggers --single-transaction "%DB_NAME%" > "%BACKUP_DIR%\%DB_NAME%_%STAMP%.sql"
) else (
  "%MYSQLDUMP%" -u "%DB_USER%" -p"%DB_PASS%" --routines --triggers --single-transaction "%DB_NAME%" > "%BACKUP_DIR%\%DB_NAME%_%STAMP%.sql"
)
if errorlevel 1 (
  echo ERREUR : la sauvegarde a echoue. Verifiez que MySQL est demarre dans Laragon.
  exit /b 1
)
echo SAUVEGARDE OK : %BACKUP_DIR%\%DB_NAME%_%STAMP%.sql

REM Rotation automatique : supprime les dumps de plus de 30 jours
forfiles /p "%BACKUP_DIR%" /m *.sql /d -30 /c "cmd /c del @path" 2>nul
endlocal