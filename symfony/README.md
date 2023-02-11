# Moneyes API 
## Lancer le projet
`make install`
`make db`
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
## Créer les clefs privés JWT
```bash
php bin/console lexik:jwt:generate-keypair
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
