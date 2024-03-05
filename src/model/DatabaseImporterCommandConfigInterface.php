<?php

namespace DatabaseImporter\model;

interface DatabaseImporterCommandConfigInterface
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