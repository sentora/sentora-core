@echo off
cls
rem ZPanel Enviroment Configuration Tool for Microsoft Windows based systems.
rem Written by Bobby Allen, 19/02/2012

echo ZPanel Enviroment Configuration Tool
echo ====================================
echo.
echo We recommend you install ZPanel on drive 'C' otherwise you will have to make
echo manual changes to our pre-configured application configuration files for
echo Apache, Filezilla etc. as well as update all paths in the x_settings table
echo in the ZPanelX database.
echo.
echo If you need help, please visit our forums: http://forums.zpanelcp.com/
echo.
set /p zpd=What drive would you like to install ZPanel on? (eg: C): %=%
IF [%zpd%]==[] SET zpd=C
echo.
echo Creating folder structure...
mkdir %zpd%:\zpanel
mkdir %zpd%:\zpanel\panel
mkdir %zpd%:\zpanel\bin
mkdir %zpd%:\zpanel\configs
mkdir %zpd%:\zpanel\hostdata
mkdir %zpd%:\zpanel\logs
mkdir %zpd%:\zpanel\backups
mkdir %zpd%:\zpanel\temp
echo Complete!
echo Copying application configuration files...
xcopy /s/e contrib\win_specific\* %zpd%:\zpanel\configs
xcopy /s/e contrib\all_platforms\* %zpd%:\zpanel\configs
echo Complete!
echo.
echo You can access your new ZPanel folder layout here: %zpd%:\zpanel
echo.
pause
exit