<?php

namespace DatabaseImporter\command;

use DatabaseImporter\model\Database;
use DatabaseImporter\model\SourceToDestinationDatabaseCommandConfig;
use DatabaseImporter\Tools\ConfigAndCheckerTrait;
use DatabaseImporter\Tools\ImportAndExportTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSourceToDestinationDatabaseCommand extends Command
{
    use ImportAndExportTrait;
    use ConfigAndCheckerTrait;

    /**
     * @var string
     */
    protected $defaultDumpDir;

    /**
     * @var string
     */
    private $dumpFilePath;

    /**
     * @var Database
     */
    protected $source;

    /**
     * @var Database
     */
    protected $destination;


    public function __construct(SourceToDestinationDatabaseCommandConfig $commandConfig)
    {
        parent::__construct("database:import-from-another-database");

        $this->source = $commandConfig->getSource();
        $this->destination = $commandConfig->getDestination();
        $this->defaultDumpDir = sys_get_temp_dir();
        $this->setDescription("to import a database into another database");
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->isDatabaseConfigured($this->source, $this->destination)) {
            $io->error("Database config cannot be empty !");
            return Command::INVALID;
        }


        $output->writeln([
            $this->getDescription(),
            '============',
            '',
        ]);


        // Export section
        $dumpDirGiven = $input->getOption("migrationDir") ?? "";
        $this->defaultDumpDir = $this->createDumpDir($this->defaultDumpDir, $dumpDirGiven);

        $code = $this->export($this->defaultDumpDir, $this->dumpFilePath, $this->source, $output);
        if ($code != Command::SUCCESS) {
            $io->error("Error when exporting database!");
            return $code;
        }
        if (!file_exists($this->dumpFilePath)) {
            $io->error("Dump file to import does not exist.");
            return Command::FAILURE;
        }


        // Import section
        $code = $this->import($this->dumpFilePath, $this->destination, $output);

        // Check and remove dump file create in the system temp dir
        $message = " Dir path '$this->defaultDumpDir'";
        if ($dumpDirGiven != $this->defaultDumpDir) {
            $message = "";
            unlink($this->dumpFilePath);
        }

        if ($code != Command::SUCCESS) {
            $io->error("Error when importing database !");
            return $code;
        }

        $io->success("Finish {$this->getDescription()}. $message");

        return $code;
    }


    protected function configure(): void
    {
        $this
            ->setHelp("Allow to import a database into another database")
            ->setDescription("This command help to {$this->getDescription()}")
            ->addOption('migrationDir', "md", InputOption::VALUE_OPTIONAL, 'Migration dir', $this->defaultDumpDir);
    }

}