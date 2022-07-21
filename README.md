# Azurello


## **Installation du projet :** 
git clone <gitprojetssh> *=> clone repos*
composer update *=> install dépendances*
add .env *=> config serveur*

## **Commande git**
git add . *=> enregistrer état du projet*
git commit -m "detail commit" *=> commiter l'état*
git push
git pull *=> récupérer l'état actuel*

### Branch
git checkout -b <branch> *=> créer et se déplace sur la nouvelle branche*
git checkout <branch> *=> se déplace sur la branche*
git branch (-av) *=> liste les branches*
git branch -D <branch> *=> supprime une branche en local*
git branch --delete --remotes <remote>/<branch> *=> supprime le suivis local*
git push gitprojetssh --delete <branch> *=> supprime une branche distante dans git*

## Base de donnée
*renseigner les info de la bdd sur le .ENV*
bin/console doctrine:database:create *=> créer la base de donnée*
bin/console make:entity *=> créer l'entité en fonction du schéma UML*
*gérer les relations*
bin/console make:migration *=> créer le fichier de la migration*
bin/console doctrine:migration:migrate *=> update la base de donnée avec le fichier migration*
bin/console doctrine:migrations:sync-metadata-storage *=> synchroniser base de donnée*
git push <gitprojetssh> --delete <branch> *=> supprime une branche distante dans git*

## Serveur
php -S localhost:8080 -t public/ *=> lancer le serveur*

## Problèmes rencontrés

# Symfony

driver not found lors d'une commande doctrine => vérifier les informations de connexion à la base de donnée dans .env. Php.ini dans php et/ou xampp décommenter ligne "extension=pdo_mysql"

