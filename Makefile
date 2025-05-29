.PHONY: start

start :
	symfony server:start

log :
	symfony server:log

reset-db:
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:database:create
	php bin/console make:migration
	php bin/console doctrine:migrations:migrate -n

seed :
	php bin/console app:import-books --themes
	php bin/console app:import-books --authors
	php bin/console doctrine:fixtures:load --append

new-db:
	php bin/console doctrine:database:create
	php bin/console make:migration
	php bin/console doctrine:migrations:migrate -n
	php bin/console app:import-books --themes
	php bin/console app:import-books --authors
	php bin/console doctrine:fixtures:load --append