.PHONY: build test example

build:
	@docker build -t code-wallet-php:latest .

test: build
	@docker run --rm code-wallet-php:latest ./vendor/bin/phpunit --colors=always --verbose /app/tests

example: build
	@docker run --rm -p 3000:3000 code-wallet-php:latest php -S 0.0.0.0:3000 -t /app/example

validate: build
	@docker run --rm -v ${PWD}:/app code-wallet-php:latest composer validate

package: validate test
	@echo "Package is valid and tests are passing"

