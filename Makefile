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
