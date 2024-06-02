# ekolotech/database-importer [![PHP version >= 7.2](https://github.com/igorcyberdyne/DatabaseImporter)](https://www.php.net/releases/7_2_0.php)

**ekolotech/database-importer** est un composant commande permettant :
- d'importer une base de données A dite `source` vers une base de données B dite de `destination`
- d'exporter une base de données dite `source` vers un fichier `.sql`
- d'importer une base de données depuis un fichier `.sql` vers une base de données dite de `destination`

**Auteur :** [@igorcyberdyne](https://github.com/igorcyberdyne), [@EKOLOTECH](https://ekolotech.fr)

## Bases de données prises en charge
- Mysql
- MariaDB

## Installation
Vous pouvez
> Télécharger le composant sur [`github` ekolotech/database-importer](https://github.com/igorcyberdyne/DatabaseImporter.git)

OU exécuter la commande ci-dessous dans la console

    composer require ekolotech/database-importer


## Description
Pour effectuer les opérations décrites dans le tableau ci-dessous, selon le besoin vous devez implémenter 
les interfaces de configuration des bases de données, utiliser l'instance `CommandHandler`, ajouter les commandes pour déclencher les opérations
([voir 2. Exemple d'implémentation](#implementation-example)).


`- Liste des commandes`

| Nom                                                                                  | description                                                | options                                         | Interface de config                        |
|--------------------------------------------------------------------------------------|------------------------------------------------------------|-------------------------------------------------|--------------------------------------------|
| database:import-from-another-database ([exemple ici](#import-from-another-database)) | Importer une  base de données A vers une base de données B | --dumpFilePath="path\to\dir" (facultatif)       | `SourceToDestinationDatabaseCommandConfig` |
| database:export-to-file ([exemple ici](#export-to-file))                             | Exporter une base de données    vers un fichier            | --migrationDir="path\to\dir" (facultatif)       | `ExportDatabaseCommandConfig`              |
| database:import-from-file ([exemple ici](#import-from-file))                         | Importer une base de données depuis un fichier             | --dumpFilePath="path\to\file.sql" (obligatoire) | `ImportDatabaseCommandConfig`              |


### 1. Présentation des interfaces de config selon les commandes et le modèle de la base de données :
Les méthodes de l'interface permettent de renseigner les données de connexion aux bases de données source et de destination.

```php
interface SourceToDestinationDatabaseCommandConfig
{
    public function getSource(): Database;
    public function getDestination(): Database;
}

interface ExportDatabaseCommandConfig
{
    public function getSource(): Database;
}

interface ImportDatabaseCommandConfig
{
    public function getDestination(): Database;
}
```

```php
class Database
{
    public function __construct(
        string $name, // Nom de la base
        string $host, // le host du serveur
        string $user, // l'utilisateur sur lequel se connecter
        string $password, // le mot de passe de l'utilisateur
    )
    {
    }
}
```

### <a id="implementation-example">2. Exemple d'implémentation</a>
`- Fichier principale (DatabaseImporter > demo > console)`

```php
<?php

use DatabaseImporter\Argv;
use DatabaseImporter\CommandHandler;
use DatabaseImporter\model\Database;
use DatabaseImporter\model\ExportDatabaseCommandConfig;
use DatabaseImporter\model\ImportDatabaseCommandConfig;
use DatabaseImporter\model\SourceToDestinationDatabaseCommandConfig;

class ExampleSourceToDestinationDatabaseCommandConfig implements SourceToDestinationDatabaseCommandConfig
{
    public function getSource(): Database
    {
        return new Database(
            "source_database",
            "127.0.0.1",
            "root",
            ""
        );
    }

    public function getDestination(): Database
    {
        return new Database(
            "destination_database",
            "127.0.0.1",
            "root",
            ""
        );
    }
}

class ExampleExportDatabaseCommandConfig implements ExportDatabaseCommandConfig
{
    public function getSource(): Database
    {
        return new Database(
            "source_database",
            "127.0.0.1",
            "root",
            ""
        );
    }
}

class ExampleImportDatabaseCommandConfig implements ImportDatabaseCommandConfig
{
    public function getDestination(): Database
    {
        return new Database(
            "destination_database",
            "127.0.0.1",
            "root",
            ""
        );
    }
}

$commandHandler = new CommandHandler();
$commandHandler->add(new ExampleSourceToDestinationDatabaseCommandConfig());
$commandHandler->add(new ExampleImportDatabaseCommandConfig());
$commandHandler->add(new ExampleExportDatabaseCommandConfig());

try {
    $commandHandler->run(new Argv());
} catch (Exception $e) {
    die($e->getMessage());
}
```

### 3. Exécution de la commande depuis la racine du projet

    php demo/console [commandName]


#### <a id="import-from-another-database">Exemple 1 : Importer une base de données A vers une base de données B</a>

Si vous précisez l'option `--migrationDir` la base de données A sera enrégistrée dans le répertoire précisé dans un fichier `SQL`

    php demo/console database:import-from-another-database

#### <a id="export-to-file">Exemple 2 : Exporter une base de données vers un fichier</a>

Le fichier exporté se trouve dans le répertoire indiqué, le nom du fichier correspond à ce format `MigrationV.*.sql`.
Si l'option n'est pas précisée le fichier sera exporté dans le répertoire temporaire de votre système,
le chemin vers ce repertoire sera indiqué dans la console à la fin de l'exécution.

    php demo/console database:export-to-file --migrationDir='C:\Project\DatabaseImporter'


#### <a id="import-from-file">Exemple 3 : Importer une base de données depuis un fichier</a>

Si vous précisez l'option `--migrationDir` la base de données A sera enrégistrée dans le répertoire précisé dans un fichier `SQL`

    php demo/console database:import-from-file --dumpFilePath='C:\Project\DatabaseImporter\database.sql'
