@ECHO OFF
title PHAR Converter
set ORIGINDIR=%CD%
cd /d %~dp0

if exist bin\php\php.exe (
    if "%~1" == "" goto noargs
    bin\php\php.exe src/pharconverter/PHARConverter.php %*
    cd %ORIGINDIR%
    exit /B
    :noargs
    bin\php\php.exe src/pharconverter/PHARConverter.php
    cd %ORIGINDIR%
    exit /B
) else (
    echo PHP binary not found
    echo Make sure you have downloaded our provided PHP binary and place it inside path\to\root\bin\php
    pause
)
