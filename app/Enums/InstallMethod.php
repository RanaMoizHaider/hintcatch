<?php

namespace App\Enums;

enum InstallMethod: string
{
    case FilePath = 'file_path';
    case CliCommand = 'cli_command';
    case Custom = 'custom';

    public static function default(): self
    {
        return self::FilePath;
    }
}
