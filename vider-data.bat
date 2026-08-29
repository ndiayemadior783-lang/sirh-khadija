@echo off
title Nettoyage donnees SIRH Khadija
cd /d "%~dp0"
echo ============================================
echo  Suppression des employes et formations de demo
echo  (les comptes admin / rh / manager sont conserves)
echo ============================================
echo.
call php artisan db:seed --class=CleanDatabaseSeeder --force
echo.
echo Termine. Les donnees de demonstration ont ete supprimees.
echo Vous pouvez maintenant creer vos propres employes et formations.
echo.
pause