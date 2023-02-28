# API

## Nom
API pour l'application d'émargement électronique
***
## Description
Cette API codée en PHP Symfony est commune aux applications Web et Mobile. Elle permet le dialogue avec la base de données.
***
## Installation
```
cd project/
git clone https://web.isen-ouest.fr/gitlab/projet-m1/application_emargement_electronique/api.git
cd api/
```
Check your environnement 
```
symfony check:requirements
```
Use composer to install the missing dependancies.
```
composer install
```
Create the .env file. Update the DATABASE_URL variable with your database credentials
```
cp .env.exemple .env
```
Run Symfony
```
symfony server:start
```
***

## Utilisation
Trouvez la documentation ici
```
ip_address:8000/api/doc
```
Ou ici en JSON
```
ip_address:8000/api/doc.json
```
***
## Auteurs
Projet réalisé en 2023 par Yohann LE CAM et Clément YZIQUEL dans le cadre d'un projet M1 à l'ISEN Brest. <br>
Projet proposé et supervisé par Olivier PODEUR, dévoloppeur informatique à l'ISEN Brest.


