default: cs phpstan phpunit

cs:
	php vendor/bin/phpcs --standard=PSR12 ./src ./tests

phpstan:
	php vendor/bin/phpstan

phpunit:
	php vendor/bin/phpunit
