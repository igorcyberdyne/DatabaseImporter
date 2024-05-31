<?php

namespace DatabaseImporter;

use DatabaseImporter\model\DatabaseImporterCommandConfigInterface;
use Exception;
use Symfony\Component\Console\Application;

class CommandHandler
{
    /**
     * @var DatabaseImporterCommand
     */
    private $command = null;

    public function set(DatabaseImporterCommandConfigInterface $commandConfig): CommandHandler
    {
        $this->command = new DatabaseImporterCommand($commandConfig);

        return $this;
    }

    /**
     * @param Argv|null $arg
     * @return void
     * @throws Exception
     */
    public function run(?Argv $arg = null)
    {
        if (!$this->command) {
            throw new Exception('Command config not set');
        }

        $application = new Application();
        $application->add($this->command);
        $application->run($arg);
    }
}