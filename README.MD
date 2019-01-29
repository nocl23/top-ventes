# Projet Symfony 4 Top Ventes

## Steps à reproduire

``git clone git@github.com:nocl23/top-ventes.git``

Dans le fichier .env, adapter la ligne de connexion à la base de données "DATABASE_URL".

``composer install``

Création la base de données
``php bin/console doctrine:database:create``

``php bin/console doctrine:migrations:migrate``

## Ajout de données à la base de données

``php bin/console doctrine:fixtures:load``

## Commande pour que Jeannine exporte ces données

``php bin/console export-top-ventes``

Le fichier ".csv" est alors généré dans "./public/top_trois_ventes.csv".

NB : Le séparateur de données est ";".
