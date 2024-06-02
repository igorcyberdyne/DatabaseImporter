<?php

namespace DatabaseImporter;

use DatabaseImporter\command\ExportDatabaseCommand;
use DatabaseImporter\command\ImportDatabaseCommand;
use DatabaseImporter\command\ImportSourceToDestinationDatabaseCommand;
use DatabaseImporter\model\ExportDatabaseCommandConfig;
use DatabaseImporter\model\ImportDatabaseCommandConfig;
use DatabaseImporter\model\SourceToDestinationDatabaseCommandConfig;
use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class CommandHandler
{
    private $commands = [];

    /**
     * @param ImportDatabaseCommandConfig|ExportDatabaseCommandConfig|SourceToDestinationDatabaseCommandConfig $commandConfig
     * @return $this
     */
    public function add($commandConfig): CommandHandler
    {
        $command = null;

        if ($commandConfig instanceof ImportDatabaseCommandConfig) {
            $command = new ImportDatabaseCommand($commandConfig);
        } else if ($commandConfig instanceof ExportDatabaseCommandConfig) {
            $command = new ExportDatabaseCommand($commandConfig);
        } else if ($commandConfig instanceof SourceToDestinationDatabaseCommandConfig) {
            $command = new ImportSourceToDestinationDatabaseCommand($commandConfig);
        }

        if ($command instanceof Command) {
            $this->commands[] = $command;
        }

        return $this;
    }

    /**
     * @param Argv|null $arg
     * @return void
     * @throws Exception
     */
    public function run(?Argv $arg = null)
    {
        if (empty($this->commands)) {
            throw new Exception('Command config not set');
        }

        $application = new Application();
        $application->addCommands($this->commands);
        $application->run($arg);
    }
}