@echo off
REM Compile translation files for Yaxii WooCommerce Shipping Importer

echo Compiling translations...
echo.

REM Check if msgfmt is available
where msgfmt >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Error: msgfmt command not found.
    echo Please install Poedit from https://poedit.net/
    echo Then use Poedit to compile the .po files to .mo files
    echo.
    pause
    exit /b 1
)

REM Compile Arabic
echo Compiling Arabic (ar)...
msgfmt -o languages\yaxii-wc-importer-ar.mo languages\yaxii-wc-importer-ar.po

REM Compile French
echo Compiling French (fr_FR)...
msgfmt -o languages\yaxii-wc-importer-fr_FR.mo languages\yaxii-wc-importer-fr_FR.po

echo.
echo All translations compiled successfully!
echo.
echo Generated files:
echo - languages\yaxii-wc-importer-ar.mo
echo - languages\yaxii-wc-importer-fr_FR.mo
echo.
pause

