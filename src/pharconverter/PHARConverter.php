<?php

/*
 * Simple to use PHAR Converter utility written in PHP CLI
 * Copyright (C) 2020 KygekTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pharconverter {

    require_once __DIR__ . "/autoload/Autoloader.php";

    use pharconverter\exception\InvalidDirNameException;
    use pharconverter\exception\InvalidPHARNameException;
    use pharconverter\utils\CLI;
    use pharconverter\utils\Config;

    const VERSION = "1.0.0-BETA2";
    const IS_DEV = false;

    if (version_compare((string) PHP_MAJOR_VERSION, "7", "<") === -1) {
        CLI::writeLine("You're using PHP below version 7. Please use our provided PHP binary.", CLI::ERROR);
        terminate();
    }

    if (!extension_loaded("yaml")) {
        CLI::writeLine("YAML extension is not installed or loaded. Please use our provided PHP binary.", CLI::ERROR);
        terminate();
    }

    if (!extension_loaded("bz2")) {
        CLI::writeLine("BZIP2 extension is not installed or loaded. Please use our provided PHP binary.", CLI::ERROR);
        terminate();
    }

    CLI::writeLine(PHP_EOL . "PHAR Converter v" . VERSION);
    CLI::writeLine("Copyright (C) 2020 KygekTeam" . PHP_EOL);
    if (IS_DEV) CLI::writeLine("You are using Dev version. Major bugs and issues may be present. Use this version on your own risk." . PHP_EOL, CLI::WARNING);

    CLI::writeLine(<<<EOT
    Modes:
    - ptd: Converts PHAR to directory
    - dtp: Converts directory to PHAR
    - exit: Exit PHAR Converter
    EOT . PHP_EOL);
    CLI::write("Enter the mode that you want [exit]: ", CLI::INFO);
    $mode = CLI::read();

    $cfg = new Config();
    if (!$cfg->exists()) $cfg->copy();
    $cfg->parse();
    $cfg->checkVersion();
    $config = $cfg->get();

    switch (strtolower($mode)) {
        case "ptd":
        case "phartodir":
            convertPharToDir($config);
            break;
        case "dtp":
        case "dirtophar":
            convertDirToPhar($config);
            break;
        default:
            terminate();
    }

    function convertPharToDir(array $config) {
        phartodir:
        CLI::write("Enter the PHAR file name that you want to convert: ", CLI::INFO);
        $pharname = CLI::read();
        try {
            $convert = new Convert($config);
            $convert->toDir($pharname);
        } catch (InvalidPHARNameException $exception) {
            CLI::writeLine($exception->getMessage(), CLI::ERROR);
            goto phartodir;
        }
        terminate();
    }

    function convertDirToPhar(array $config) {
        dirtophar:
        CLI::write("Enter the directory name that you want to convert: ", CLI::INFO);
        $dirname = CLI::read();
        try {
            $convert = new Convert($config);
            $convert->toPhar($dirname);
        } catch (InvalidDirNameException $exception) {
            CLI::writeLine($exception->getMessage(), CLI::ERROR);
            goto dirtophar;
        }
        terminate();
    }

    function terminate() {
        CLI::write("PHAR Converter will close automatically in 2 seconds", CLI::INFO);
        sleep(2);
        CLI::writeLine("");
        exit();
    }

}
