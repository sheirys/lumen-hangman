db-update:
	cd src &&\
	php artisan migrate:refresh --seed

composer:
	cd src && composer install

test:
	cd src &&\
	php artisan migrate:refresh --seed &&\
	phpunit

install: composer db-update

serve:
	cd src/public &&\
	php -S localhost:8000

all: install db-update test
