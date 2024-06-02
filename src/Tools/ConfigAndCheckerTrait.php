<?php

namespace DatabaseImporter\Tools;

use DatabaseImporter\model\Database;

trait ConfigAndCheckerTrait
{
    /**
     * @param Database ...$databases
     * @return bool
     */
    private function isDatabaseConfigured(Database ...$databases): bool
    {
        if (empty($databases)) {
            return false;
        }

        $requiredAttributes = ["host", "user", "name"];
        foreach ($requiredAttributes as $attribute) {
            $method = "get" . ucfirst($attribute);

            foreach ($databases as $database) {
                if (empty($database->$method())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $systemDumpDir
     * @param string $dumpDirGiven
     * @return string
     */
    public function createDumpDir(string $systemDumpDir, string $dumpDirGiven): string
    {
        if (!empty($dumpDirGiven) && $dumpDirGiven != $systemDumpDir) {
            if (!is_dir($dumpDirGiven)) {
                @mkdir($dumpDirGiven, 0777, true);
            }
            $systemDumpDir = $dumpDirGiven;
        }

        return $systemDumpDir;
    }
}