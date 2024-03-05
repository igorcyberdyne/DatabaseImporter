# ekolotech/database-importer

**Auteur :** [@igorcyberdyne](https://github.com/igorcyberdyne), [@EKOLOTECH](https://ekolotech.fr)

**ekolotech/database-importer** est un composant commande permettant d'importer une base de données dite `source` vers une autre base de données dite de `destination`

### Base de données prise en charge
- Mysql
- MariaDB

Vous devez avoir ces base de données installées sur votre machine ou serveur

### Comment installer ?
Vous pouvez
> Télécharger le composant sur [`github` ekolotech/database-importer](https://github.com/igorcyberdyne/DatabaseImporter.git)

OU exécuter la commande ci-dessous dans la console

    composer require ekolotech/database-importer


### ------------------------------------- CAS D'UTILISATION ------------------------------------
Pour effectuer l'import vous devez créer une commande, la configurée puis l'exécuter.
La commande que vous créez doit hériter la classe `DatabaseImporterCommand` 
et implémenter l'interface de configuration des bases de données source et de destination `DatabaseImporterCommandConfigInterface`.

Les méthodes de l'interface permettent de renseigner les données de connexion aux bases de données source et de destination.

#### 1. Présentation du modèle :

 `DatabaseImporterCommandConfigInterface`
```php
interface DatabaseImporterCommandConfigInterface
{
    public function getSource(): Database;
    public function getDestination(): Database;
}
```

`Database`
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

#### 2. Exemple d'implémentation
`- Fichier principale (DatabaseImporter > demo > console)`
```php
#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

class ExampleDatabaseImporterCommand extends DatabaseImporterCommand implements DatabaseImporterCommandConfigInterface
{
    public function getSource(): Database
    {
        return new Database(
            "source_database_test",
            "127.0.0.1",
            "username",
            "password"
        );
    }
    
    public function getDestination(): Database
    {
        return new Database(
            "destination_database_test",
            "127.0.0.1",
            "username",
            "password"
        );
    }
}

$application = new Application();
$application->add(new ExampleDatabaseImporterCommand());
$application->run(new ArgvInput());
```

`- Exécution de la commande depuis la racine du projet`

Un fichier `MigrationV.*.sql` sera créé dans le répertoire temporaire de votre machine ou serveur

    php demo/console app:database-importer

En ajoutant le paramètre `migrationDir` contenant le chemin vers le répertoire de destination du dump sql, le fichier `MigrationV.*.sql` sera créé dans ce dernier. 
Dans le cas de la commande ci-dessous, il sera créé à la racine du projet.

    php demo/console app:database-importer --migrationDir='C:\Project\DatabaseImporter'