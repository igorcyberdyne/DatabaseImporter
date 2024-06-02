<?php

namespace DatabaseImporter\model;

interface ImportDatabaseCommandConfig
{
    /**
     * @return Database
     */
    public function getDestination(): Database;
}