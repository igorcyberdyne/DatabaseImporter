<?php

namespace DatabaseImporter\command;

use DatabaseImporter\model\Database;
use DatabaseImporter\model\ImportDatabaseCommandConfig;
use DatabaseImporter\Tools\ConfigAndCheckerTrait;
use DatabaseImporter\Tools\ImportAndExportTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDatabaseCommand extends Command
{
    use ImportAndExportTrait;
    use ConfigAndCheckerTrait;

    /**
     * @var Database
     */
    protected $destination;

    public function __construct(ImportDatabaseCommandConfig $commandConfig)
    {
        parent::__construct("database:import-from-file");

        $this->destination = $commandConfig->getDestination();
        $this->setDescription("to import the database contained in a file");
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->isDatabaseConfigured($this->destination)) {
            $io->error("Database config cannot be empty !");
            return Command::INVALID;
        }

        $output->writeln([
            $this->getDescription(),
            '============',
            '',
        ]);

        $dumpFilePath = $input->getOption("dumpFilePath") ?? "";
        if (!file_exists($dumpFilePath) || (pathinfo($dumpFilePath)["extension"] ?? "") != "sql") {
            $io->error("Filepath not exist does not exist or is not sql file !");
            return Command::FAILURE;
        }

        $code = $this->import($dumpFilePath, $this->destination, $output);

        if ($code != Command::SUCCESS) {
            $io->error("Error when importing database !!");
            return $code;
        }

        $io->success("Finish {$this->getDescription()}");

        return $code;
    }


    protected function configure(): void
    {
        $this
            ->setHelp("Allow to export database")
            ->setDescription("This command help to {$this->getDescription()}")
            ->addOption('dumpFilePath', "dfp", InputOption::VALUE_REQUIRED, 'Dump file path');
    }

}