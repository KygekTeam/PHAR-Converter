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

class Config {

    private const CONFIG_FILE = "./src/pharconverter/resources/config.yml";
    private const CONFIG_LOCATION = "./config.yml";
    private const OLD_CONFIG_LOCATION = "./config-old.yml";

    /** @var array */
    private $contents = [];

    public function exists() : bool {
        return file_exists(self::CONFIG_LOCATION);
    }

    public function copy() {
        copy(self::CONFIG_FILE, self::CONFIG_LOCATION);
    }

    public function rename() {
        rename(self::CONFIG_LOCATION, self::OLD_CONFIG_LOCATION);
    }

    public function parse() {
        $this->contents = yaml_parse_file(self::CONFIG_LOCATION);
    }

    public function get() : array {
        return $this->contents;
    }

    // Unsets config contents variable
    public function destroy() {
        unset($this->contents);
    }

    public function checkVersion() {
        if ($this->contents["config-version"] !== "1.0") {
            $this->rename();
            $this->copy();
            $this->destroy();
            $this->parse();
        }
    }

}