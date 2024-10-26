SHELL = /bin/bash
############################ COMMANDE DOCKER BACKEND ############################################
start:
	docker-compose up --build -d
stop:
	docker-compose down
start_php:
	php -S localhost:8080

