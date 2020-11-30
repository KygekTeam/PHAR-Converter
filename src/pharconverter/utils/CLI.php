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

namespace pharconverter\utils;

class CLI {

    // WRITE MODES
    public const NONE = 0;
    public const INFO = 1;
    public const WARNING = 2;
    public const ERROR = 3;

    /**
     * @return string
     */
    public static function read() : string {
        return trim((string) fgets(fopen("php://stdin", "r")));
    }

    /**
     * @param string $message
     * @param int $mode
     */
    public static function write(string $message, int $mode = self::NONE) {
        switch ($mode) {
            case self::INFO:
                $mode = "[INFO] ";
                break;
            case self::WARNING:
                $mode = "[WARNING] ";
                break;
            case self::ERROR:
                $mode = "[ERROR] ";
                break;
            default:
                $mode = "";
        }

        // PHP 8 Replacement
        /*$mode = match ($mode) {
            self::INFO => "INFO: ",
            self::WARNING => "WARNING: ",
            self::ERROR => "ERROR: ",
            default => ""
        };*/

        echo $mode . $message;
    }

    /**
     * @param string $message
     * @param int $mode
     */
    public static function writeLine(string $message, int $mode = self::NONE) {
        self::write($message . PHP_EOL, $mode);
    }

}