<?php

namespace DatabaseImporter\command;

use DatabaseImporter\model\Database;
use DatabaseImporter\model\ExportDatabaseCommandConfig;
use DatabaseImporter\Tools\ConfigAndCheckerTrait;
use DatabaseImporter\Tools\ImportAndExportTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportDatabaseCommand extends Command
{
    use ImportAndExportTrait;
    use ConfigAndCheckerTrait;

    /**
     * @var string
     */
    protected $defaultDumpDir = null;

    /**
     * @var string
     */
    private $dumpFilePath;

    /**
     * @var Database
     */
    protected $source;


    public function __construct(ExportDatabaseCommandConfig $commandConfig)
    {
        parent::__construct("database:export-to-file");

        $this->source = $commandConfig->getSource();
        $this->defaultDumpDir = sys_get_temp_dir();
        $this->setDescription("to export a database");
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->isDatabaseConfigured($this->source)) {
            $io->error("Database config cannot be empty !");
            return Command::INVALID;
        }

        $output->writeln([
            $this->getDescription(),
            '============',
            '',
        ]);

        $dumpDirGiven = $input->getOption("migrationDir") ?? "";
        $this->defaultDumpDir = $this->createDumpDir($this->defaultDumpDir, $dumpDirGiven);

        $code = $this->export($this->defaultDumpDir, $this->dumpFilePath, $this->source, $output);
        if ($code != Command::SUCCESS) {
            return $code;
        }

        if (!file_exists($this->dumpFilePath) || (pathinfo($this->dumpFilePath)["extension"] ?? "") != "sql") {
            $io->error("Filepath not exist does not exist or is not a sql file !");
            return Command::FAILURE;
        }

        $io->success("Finish {$this->getDescription()} in '$this->defaultDumpDir' dir path");

        return $code;
    }


    protected function configure(): void
    {
        $this
            ->setHelp("Allow to export database")
            ->setDescription("This command help to {$this->getDescription()}")
            ->addOption('migrationDir', "md", InputOption::VALUE_OPTIONAL, 'Migration dir', $this->defaultDumpDir);
    }

}