lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
	composer exec --verbose phpstan -- --level=8 analyse src tests
test:
	composer exec --verbose phpunit tests
autoload:
	composer dump-autoload