<?php

namespace DatabaseImporter;

use DatabaseImporter\model\Database;
use DatabaseImporter\model\DatabaseImporterCommandConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class DatabaseImporterCommand extends Command
{
    /**
     * @var string
     */
    protected $migrationDir;

    /**
     * @var string
     */
    private $fileToImport;

    /**
     * @var Database
     */
    protected $source;

    /**
     * @var Database
     */
    protected $destination;


    public function __construct()
    {
        parent::__construct('app:database-importer');
        $this->setDescription("to import a database into another database");

        $this->source = $this->destination = new Database("", "", "", "");

        if ($this instanceof DatabaseImporterCommandConfigInterface) {
            $this->source = $this->getSource();
            $this->destination = $this->getDestination();
        }

        $this->migrationDir = sys_get_temp_dir();
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->isDatabaseConfigured()) {
            $io->error("Database config cannot be empty ! Setup source or destination config.");
            return Command::INVALID;
        }


        $output->writeln([
            $this->getDescription(),
            '============',
            '',
        ]);

        $migrationDir = $input->getOption("migrationDir");
        $removeTempFile = empty($migrationDir) || $migrationDir == $this->migrationDir;

        if (!empty($migrationDir) && is_dir($migrationDir)) {
            $this->migrationDir = $migrationDir;
        }

        $code = $this->export($output);
        if ($code != Command::SUCCESS) {
            return $code;
        }

        if (!file_exists($this->fileToImport)) {
            $io->error("Dump file to import does not exist.");
            return Command::FAILURE;
        }

        $code = $this->import($output);

        $io->success("Finish {$this->getDescription()}");

        if ($removeTempFile) {
            unlink($this->fileToImport);
        }

        return $code;
    }

    private function export(OutputInterface $output): int
    {
        if (empty($this->migrationDir)) {
            return Command::INVALID;
        }

        $dumpFile = $this->migrationDir . "\\" . "MigrationVersion" . uniqid() . ".sql";

        $cmd = sprintf('mysqldump -h %s -u %s --password=%s %s > %s',
            ...array_values([
                "host" => $this->source->getHost(),
                "user" => $this->source->getUser(),
                "password" => $this->source->getPassword(),
                "name" => $this->source->getName(),
                "filename" => $dumpFile,
            ])
        );

        $output->writeln([
            "Start export...",
            ""
        ]);

        exec($cmd, $exportOutput, $exitStatus);

        if ($exitStatus === Command::SUCCESS) {
            $this->fileToImport = $dumpFile;
        }

        $output->writeln([
            "Finish export...",
            ""
        ]);

        return $exitStatus;
    }

    private function import(OutputInterface $output): int
    {
        $output->writeln([
            "Start import...",
            ""
        ]);

        $cmd = sprintf('mysql -h %s -u %s --password=%s %s < %s',
            ...array_values([
                "host" => $this->destination->getHost(),
                "user" => $this->destination->getUser(),
                "password" => $this->destination->getPassword(),
                "name" => $this->destination->getName(),
                "filename" => $this->fileToImport,
            ])
        );
        exec($cmd, $importOutput, $exitStatus);

        $output->writeln([
            "Finish import...",
            ""
        ]);

        return $exitStatus;
    }

    protected function configure(): void
    {
        $this
            ->setHelp("Allow to import a database into another database")
            ->setDescription("This command help to {$this->getDescription()}")
            ->addOption('migrationDir', "md", InputOption::VALUE_REQUIRED, 'Migration dir', $this->migrationDir);
    }

    private function isDatabaseConfigured(): bool
    {
        $requiredAttributes = ["host", "user", "name"];
        foreach ($requiredAttributes as $attribute) {
            $method = "get" . ucfirst($attribute);

            if (empty($this->source->$method()) || empty($this->destination->$method())) {
                return false;
            }
        }

        return true;
    }

}