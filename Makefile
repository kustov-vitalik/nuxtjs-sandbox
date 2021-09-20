path ?= .

help: ## show this help
	@echo 'usage: make [target] ...'
	@echo ''
	@echo 'targets:'
	@egrep '^(.+)\:\ .*##\ (.+)' ${MAKEFILE_LIST} | sed 's/:.*##/#/' | column -t -c 2 -s '#'


artisan: ## make artisan command=list
	cd backend; \
    ./vendor/bin/sail run laravel.test ./artisan $(command); \
    cd ..;

backend-start: ## start BE
	cd backend; \
    php composer.phar install -o; \
    ./vendor/bin/sail up -d; \
    cd ..;

frontend-start: ## start FE
	cd frontend; \
    npm install; \
    npm run build; \
    npm run start;


start: ## start everything
	make backend-start; \
    make artisan command='db:seed'; \
    make frontend-start;
