# Minerva : projet de gestion de bibliothèque

But : approfondir des notions Symfony 7

pas de Docker pour l'instant (merci Windows ...)

## Installation
```shell
composer install
```

### Ajouter le .env.* avec les variables

``` dotenv
APP_SECRET=be147348de343f8abdea745b73ead9af
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
MAILER_DSN=null://null
GOOGLE_BOOK_API_KEY=
```

Penser à se créer une clé api pour Google Api, puis :

### Créer la bdd
```shell
make new-db
```
Un user de test est créé
```
email : toto@toto.fr
mdp : toto
```
L'envoi de mail se fait via messenger. On peut utiliser Doctrine Messenger; j'ai utilisé RabbitMq (php-amqp + symfony/amqp-messenger).