<?php
declare(strict_types=1);

namespace Beauty\Module\Console;

use Beauty\Cli\Console\Contracts\CommandsRegistryInterface;
use Beauty\Module\Console\Commands\Generate\ModuleCommand;

class RegisterCommands implements CommandsRegistryInterface
{

    /**
     * @return \class-string[]
     */
    public static function commands(): array
    {
        return [
            ModuleCommand::class,
        ];
    }
}