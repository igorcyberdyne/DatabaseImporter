<?php

namespace DatabaseImporter\model;

interface ExportDatabaseCommandConfig
{
    /**
     * @return Database
     */
    public function getSource(): Database;
}