# PHAR Converter

[![Discord](https://img.shields.io/discord/735439472992321587.svg?label=&logo=discord&logoColor=ffffff&color=7389D8&labelColor=6A7EC2)](https://discord.gg/CXtqUZv)

PHAR Converter is an easy to use program that converts PHAR files into directory and vice versa. PHAR Converter has been tested to convert PocketMine-MP plugins.

## Features

- Several compression methods
- Direct conversion
- Environment variable support
- Configuration file
- Easy to use
- And much more!

## Requirements

To use PHAR Converter, you need Git installed. Currently, PHAR Converter only supports Windows 64-bit. PHAR Converter may work on Windows 32-bit, however it has not been tested.

## Installation & Running First Time

**WARNING:** PHAR Converter is currently under Beta. There may be some bugs and compatibility issues. Click [here](https://github.com/KygekTeam/PHAR-Converter#contributing) to read about contributing.

### Downloading packaged source

Packaged source is available to download directly [here](https://github.com/KygekTeam/PHAR-Converter/releases).

### Cloning from source

1. Clone the repository recursively using Git
```
git clone https://github.com/KygekTeam/PHAR-Converter
```
2. Change directory to `PHAR-Converter`
```
cd PHAR-Converter
```
3. Run `pharconverter` or open `pharconverter.cmd` directly to launch PHAR Converter!

## Converting Directly

To convert PHAR file or directory directly, PHAR Converter should be run from Command Prompt (`pharconverter`).

Parameters:
- `--mode="{MODE}"`: Specify the convert mode (Available modes: `ptd`, `phartodir`, `dtp`, `dirtophar`)
- `--name="{NAME}"`: Specify the PHAR file/directory name to be converted

Example:
```
pharconverter --mode=ptd --name=KygekJoinUI
```
Converts `KygekJoinUI.phar` to directory `KygekJoinUI`

## Converting via Environment Variable

To use PHAR Converter via environment variable, you need to add the PHAR Converter directory root to `Settings > System > About > Advanced System Settings > Environment Variables > System Variables > Path`.

Please note that you need to specify full PHAR/directory path that you want to convert if you use PHAR Converter via environment variable.

**Example:** Use `C:\Something.phar` instead of directly `Something.phar`.

## Contributing

We accept contributions! To contribute, please **fork the `dev` branch** and create a [pull request](https://github.com/KygekTeam/PHAR-Converter/pulls) **to the `dev` branch**. All pull requests to the `main` branch **will be closed immediately**.

If you found any bugs or want to give suggestions/feedbacks, please create an [issue](https://github.com/KygekTeam/PHAR-Converter/issues).

## Help & Support

Should you need any assistance, please join our [Discord server](https://discord.gg/CXtqUZv).
