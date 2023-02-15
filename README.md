# Moneyes API 
## Lancer le projet
1. Lancer l'installation complète de docker :
`make install`
2. Créer la base de données : 
`make db`
3. Installer les migrations :
`make migrate`
## Se connecter au php
``make connect``
## Créer les migrations
```bash
make connect
```
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
## Remplir la base de données avec les données de dév
```bash
make load-fixtures-dev
```
## URL
``http://localhost:8080/``
## EasyAdmin
``http//localhost:8080/admin``
## Un fois le dev terminé faire
``make pre-commit``
pour tester et formater le code
## Rebase lors d'un conflit
``git pull --rebase origin nomdelabranch``
``git push --force-with-lease``
