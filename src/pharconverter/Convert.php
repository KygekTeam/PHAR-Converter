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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        if (!file_exists("./" . $pharname . ".phar")) {
            throw new InvalidPHARNameException("Invalid PHAR file name!");
        } else {
            if (is_dir("./" . $pharname)) {
                CLI::write("Another directory with the same name exists. Do you want to overwrite it? [Y/n]: ", CLI::WARNING);
                $read = strtolower(CLI::read());
                if ($read == "n" || $read == "no") return;
                $this->deleteDir("./" . $pharname);
            }

            CLI::write("Converting " . $pharname . ".phar to directory...", CLI::INFO);
            usleep(250000); // Sleeps for 0.25 seconds, for psychological reasons
            if (!is_dir("./temp")) {
                mkdir("./temp"); // Creates a new directory for temporary files if not exists
            }
            copy("./" . $pharname . ".phar", "./temp/" . $pharname . ".phar");

            $phar = new Phar("./temp/" . $pharname . ".phar");
            $phar2 = $phar->convertToExecutable(Phar::TAR, Phar::NONE);
            $phar2->extractTo("./" . $pharname, null, true);

            unlink("./temp/" . $pharname . ".phar.tar");
            unlink("./temp/" . $pharname . ".phar");

            if ($this->config["delete-old-files"]) $this->deletePhar("./" . $pharname . ".phar");

            CLI::writeLine("");
            CLI::writeLine("Done! Your converted directory is located at " . realpath("./" . $pharname), CLI::INFO);
        }
    }

    /**
     * @param string $dirname
     * @throws InvalidDirNameException
     */
    public function toPhar(string $dirname) {
        if (!is_dir("./" . $dirname)) {
            throw new InvalidDirNameException("Invalid directory name!");
        } elseif (array_search($dirname, $this->unallowedDirs) !== false) {
            throw new InvalidDirNameException("Cannot convert " . $dirname . " directory to PHAR because it is an internal directory!");
        } else {
            if (file_exists("./" . $dirname . ".phar")) {
                CLI::write("Another PHAR file with the same name exists. Do you want to overwrite it? [Y/n]: ", CLI::WARNING);
                $read = strtolower(CLI::read());
                if ($read == "n" || $read == "no") return;
                $this->deletePhar("./" . $dirname . ".phar");
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

            $phar = new Phar("./temp/" . $dirname . ".phar");
            $phar->buildFromDirectory("./" . $dirname);
            if ($compress !== Phar::NONE) {
                $phar->compress($compress);
                copy("./temp/" . $dirname . ".phar." . $ext, "./temp/" . $dirname . ".phar");
                unlink("./temp/" . $dirname . ".phar." . $ext);
            }

            copy("./temp/" . $dirname . ".phar", "./" . $dirname . ".phar");
            unlink("./temp/" . $dirname . ".phar");

            if ($this->config["delete-old-files"]) $this->deleteDir("./" . $dirname);

            CLI::writeLine("");
            CLI::writeLine("Done! Your converted PHAR file is located at " . realpath("./" . $dirname . ".phar"), CLI::INFO);
        }
    }

    public function deletePhar(string $pharpath) {
        unlink($pharpath);
    }

    public function deleteDir(string $dirpath) {
        // Credits to alcuadrado (https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it)
        $it = new RecursiveDirectoryIterator($dirpath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) rmdir($file->getRealPath());
            else unlink($file->getRealPath());
        }

        rmdir($dirpath);
    }

}