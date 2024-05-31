<?php

namespace DatabaseImporter\model;

interface DatabaseImporterCommandConfigInterface
{
    public function getCommandName(): ?string;

    /**
     * @return Database
     */
    public function getSource(): Database;

    /**
     * @return Database
     */
    public function getDestination(): Database;
}