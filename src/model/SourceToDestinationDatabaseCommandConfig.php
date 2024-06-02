<?php

namespace DatabaseImporter\model;

interface SourceToDestinationDatabaseCommandConfig
{
    /**
     * @return Database
     */
    public function getSource(): Database;

    /**
     * @return Database
     */
    public function getDestination(): Database;
}