.PHONY: build test package publish

build:
	@docker build -t code-wallet-php:latest .

test: build
	@docker run --rm code-wallet-php:latest
