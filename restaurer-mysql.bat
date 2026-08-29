@echo off
REM ================================================================
REM  RESTAURATION MYSQL - SIRH Khadija Consulting
REM  Windows / Laragon - restaure la base a partir d'un dump SQL
REM  Utilisation : restaurer-mysql.bat chemin\vers\dump.sql
REM  ATTENTION : remplace TOUT le contenu actuel de la base.
REM ================================================================
setlocal
set "MYSQL=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe"
set "DB_USER=root"
set "DB_PASS="
set "DB_NAME=sirh_khadija"
set "FILE=%~1"

if "%FILE%"=="" (
  echo Usage : restaurer-mysql.bat chemin\vers\fichier.sql
  exit /b 1
)
if not exist "%FILE%" (
  echo Fichier introuvable : %FILE%
  exit /b 1
)

if "%DB_PASS%"=="" (
  "%MYSQL%" -u "%DB_USER%" "%DB_NAME%" < "%FILE%"
) else (
  "%MYSQL%" -u "%DB_USER%" -p"%DB_PASS%" "%DB_NAME%" < "%FILE%"
)
if errorlevel 1 (
  echo ERREUR : la restauration a echoue.
  exit /b 1
)
echo RESTAURATION OK : %FILE%
endlocal