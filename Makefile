PROJECT_NAME=textmagic
DOCKER=docker compose -p ${PROJECT_NAME} -f ./docker/docker-compose.yml
DOCKER_EXEC=$(DOCKER) exec app
DOCKER_RUN=$(DOCKER) run app

up:
	${DOCKER} up -d

down:
	${DOCKER} down

rebuild:
	${DOCKER} build --no-cache

# Run container shell
cli:
	${DOCKER_EXEC} /bin/sh

# Quality Tools
cs-fixer:
	${DOCKER_EXEC} php ./vendor/bin/php-cs-fixer --config=.php-cs-fixer.php --path-mode=intersection --using-cache=no --dry-run --diff fix .

cs-fix:
	${DOCKER_EXEC} php ./vendor/bin/php-cs-fixer --config=.php-cs-fixer.php --path-mode=intersection --using-cache=no --show-progress=dots --verbose fix .

psalm:
	${DOCKER_EXEC} php ./vendor/bin/psalm --no-cache --update-baseline $(ARGS)

psalm2baseline:
	${DOCKER_EXEC} php ./vendor/bin/psalm --no-cache --set-baseline=psalm.bugs $(ARGS)
