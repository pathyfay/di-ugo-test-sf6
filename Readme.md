# Di-Ugo Test

## Description

Une application Backend en Symfony 6, qui va importer des clients et des commandes à partir de fichiers CSV;
Et il va les exposer par le biais d'un api.

## Installation

1. Cloner le dépôt du project: https://github.com/pathyfay/di-ugo-test-sf6.git
2. Exécuter `make start` ou `docker-compose up --build -d` pour lancer le docker-compose, installer les dépendances de symfony et démarrez les conteneurs.
3. Acceder au backend : http://127.0.0.1:8080

## Importer des données

Pour importer les données à partir des fichiers CSV, il faut :
1. Placer les fichiers dans le répertoire csv/
2. Lancer des commandes:

```bash
#import customers and purchases
php bin/console ugo:orders:import --customers --customersFile=path/to/customers.csv --purchases --purchasesFile=path/to/purchases.csv

#import customers
php bin/console ugo:customers:import --customers path/to/customers.csv
 
 #import purchases
php bin/console ugo:purchases:import path/to/purchases.csv

# test unitaire
php bin/phpunit
