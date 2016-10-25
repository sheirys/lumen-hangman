db-update:
	cd lumen &&\
	php artisan migrate:refresh --seed

composer:
	cd lumen && composer install

test:
	cd lumen &&\
	php artisan migrate:refresh --seed &&\
	phpunit

install: composer db-update

serve:
	cd lumen/public &&\
	php -S localhost:8000

all: install db-update test serve
