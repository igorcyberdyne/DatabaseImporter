<?php

namespace DatabaseImporter\Tools;

use DatabaseImporter\model\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

trait ImportAndExportTrait
{
    /**
     * @param string $migrationDir
     * @param string|null $dumpFile
     * @param Database $source
     * @param OutputInterface $output
     * @return int
     */
    private function export(
        string          $migrationDir,
        ?string         &$dumpFile,
        Database        $source,
        OutputInterface $output
    ): int
    {
        if (empty($migrationDir)) {
            return Command::INVALID;
        }

        $exportFilePath = $migrationDir . "\\" . "MigrationVersion" . uniqid() . ".sql";

        $cmd = sprintf('mysqldump -h %s -u %s --password=%s %s > %s',
            ...array_values([
                "host" => $source->getHost(),
                "user" => $source->getUser(),
                "password" => $source->getPassword(),
                "name" => $source->getName(),
                "filename" => $exportFilePath,
            ])
        );

        $output->writeln([
            "Start export...",
            ""
        ]);

        @exec($cmd, $exportOutput, $exitStatus);

        if ($exitStatus === Command::SUCCESS) {
            $dumpFile = $exportFilePath;
        }

        $output->writeln([
            "Finish export...",
            ""
        ]);

        return $exitStatus;
    }


    /**
     * @param string $dumpFilePath
     * @param Database $destination
     * @param OutputInterface $output
     * @return int
     */
    private function import(
        string          $dumpFilePath,
        Database        $destination,
        OutputInterface $output
    ): int
    {
        $output->writeln([
            "Start import...",
            ""
        ]);

        $cmd = sprintf('mysql -h %s -u %s --password=%s %s < %s',
            ...array_values([
                "host" => $destination->getHost(),
                "user" => $destination->getUser(),
                "password" => $destination->getPassword(),
                "name" => $destination->getName(),
                "filename" => $dumpFilePath,
            ])
        );
        @exec($cmd, $importOutput, $exitStatus);

        $output->writeln([
            "Finish import...",
            ""
        ]);

        return $exitStatus;
    }

}