@ECHO OFF
title PHAR Converter

if exist bin\php\php.exe (
    bin\php\php.exe src/pharconverter/PHARConverter.php
) else (
    echo PHP binary not found
    echo Make sure you have downloaded our provided PHP binary and place it inside path\to\root\bin\php
    pause
)
