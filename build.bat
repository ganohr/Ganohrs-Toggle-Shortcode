@echo off
setlocal

for /f "delims=" %%a in (version.txt) do (
	set "version=%%a"
)

echo VERSION
echo %version%

set "outpath=.\trunk\%version%"
mkdir %outpath%\

rem copy *.md %outpath%\
copy *.txt %outpath%\
copy *.css %outpath%\
copy *.php %outpath%\

cd %outpath%

set "zipfile=..\..\release\ganohrs-toggle-shortcode-%version%.zip"
del %zipfile%

tar -a -c -f %zipfile% *

endlocal
pause
echo on
