# Moneyes API 
Lien de la documentation https://docs.moneyes.fr/get-started/introduction
## Lancer le projet
1. Lancer l'installation complète de docker :
`make install`
2. Créer la base de données : 
`make db`
## Se connecter au php
``make connect``
## URL
``http://localhost:8080/``
## EasyAdmin (Non à jour)
``http//localhost:8080/admin``
## Un fois le dev terminé faire
1. `make pre-commit` pour tester et formater le code
2. `make db-validate-schema` pour valider la base de données
3. `make test`pour lancer les tests unitaires

Si tout est ok, faire un commit et un push
## Rebase lors d'un conflit
``git pull --rebase origin nomdelabranch``
``git push --force-with-lease``
