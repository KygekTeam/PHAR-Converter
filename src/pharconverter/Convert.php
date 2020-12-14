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

namespace pharconverter;

use Phar;
use pharconverter\exception\InvalidDirNameException;
use pharconverter\exception\InvalidPHARNameException;
use pharconverter\utils\CLI;

class Convert {

    /** @var array */
    private $config;

    // Internal directories that are not allowed to be converted
    /** @var string[] */
    private $unallowedDirs = ["bin", "src", "temp"];

    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * @param string $pharname
     * @throws InvalidPHARNameException
     */
    public function toDir(string $pharname) {
        if (!file_exists($pharname . ".phar")) {
            throw new InvalidPHARNameException("PHAR file " . $pharname . ".phar was not found");
        } else {
            if (is_dir($pharname)) {
                CLI::write("Another directory with the same name exists. Do you want to overwrite it? [Y/n]: ", CLI::WARNING);
                $read = strtolower(CLI::read());
                if ($read == "n" || $read == "no") return;
                $this->deleteDir($pharname);
            }

            CLI::write("Converting " . $pharname . ".phar to directory...", CLI::INFO);
            usleep(250000); // Sleeps for 0.25 seconds, for psychological reasons
            if (!is_dir("./temp")) {
                mkdir("./temp"); // Creates a new directory for temporary files if not exists
            }
            $phar = basename($pharname);
            copy($pharname . ".phar", "./temp/" . $phar . ".phar");

            $phar = new Phar("./temp/" . $phar . ".phar");
            $phar2 = $phar->convertToExecutable(Phar::TAR, Phar::NONE);
            $phar2->extractTo($pharname, null, true);

            unlink("./temp/" . basename($pharname) . ".phar.tar");
            unlink("./temp/" . basename($pharname) . ".phar");

            if ($this->config["delete-old-files"]) $this->deletePhar($pharname . ".phar");

            CLI::writeLine("");
            CLI::writeLine("Done! Your converted directory is located at " . realpath($pharname), CLI::INFO);
        }
    }

    /**
     * @param string $dirname
     * @throws InvalidDirNameException
     */
    public function toPhar(string $dirname) {
        if (!is_dir($dirname)) {
            throw new InvalidDirNameException("Directory " . $dirname . " was not found");
        } elseif (array_search($dirname, $this->unallowedDirs) !== false) {
            throw new InvalidDirNameException("Cannot convert " . $dirname . " directory to PHAR because it is an internal directory!");
        } else {
            if (file_exists($dirname . ".phar")) {
                CLI::write("Another PHAR file with the same name exists. Do you want to overwrite it? [Y/n]: ", CLI::WARNING);
                $read = strtolower(CLI::read());
                if ($read == "n" || $read == "no") return;
                $this->deletePhar($dirname . ".phar");
            }

            switch (strtolower((string) $this->config["compress"])) {
                case "gz":
                case "gzip":
                    $compress = Phar::GZ;
                    $ext = "gz";
                    $mode = "GZIP";
                    break;
                case "bz":
                case "bz2":
                case "bzip":
                case "bzip2":
                    $compress = Phar::BZ2;
                    $ext = "bz2";
                    $mode = "BZIP2";
                    break;
                default:
                    $compress = Phar::NONE;
                    $ext = "";
                    $mode = "NONE";
            }

            // PHP 8 Replacement
            /*$compress = match ($this->config["compress"]) {
                "gz", "gzip" => Phar::GZ,
                "bz", "bz2", "bzip", "bzip2" => Phar::BZ2,
                default => Phar::NONE
            };*/

            CLI::writeLine("PHAR compression mode is set to " . $mode, CLI::INFO);
            CLI::write("Converting " . $dirname . " to PHAR...", CLI::INFO);
            usleep(250000);
            if (!is_dir("./temp")) {
                mkdir("./temp");
            }

            $pharname = basename($dirname);
            $phar = new Phar("./temp/" . $pharname . ".phar");
            $phar->buildFromDirectory($dirname);
            if ($compress !== Phar::NONE) {
                $phar->compress($compress);
                copy("./temp/" . $pharname . ".phar." . $ext, "./temp/" . $pharname . ".phar");
                unlink("./temp/" . $pharname . ".phar." . $ext);
            }

            copy("./temp/" . $pharname . ".phar", $dirname . ".phar");
            unlink("./temp/" . $pharname . ".phar");

            if ($this->config["delete-old-files"]) $this->deleteDir($dirname);

            CLI::writeLine("");
            CLI::writeLine("Done! Your converted PHAR file is located at " . realpath($dirname . ".phar"), CLI::INFO);
        }
    }

    private function deletePhar(string $pharpath) {
        unlink($pharpath);
    }

    private function deleteDir(string $dir) {
        foreach (array_diff(scandir($dir), [".", ".."]) as $content) {
            if (is_dir($dir . "/" . $content)) {
                $this->deleteDir($dir . "/" . $content);
            } else {
                unlink($dir . "/" . $content);
            }
        }
        rmdir($dir);
    }

}