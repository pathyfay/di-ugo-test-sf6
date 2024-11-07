SHELL = /bin/bash
############################ COMMANDE DOCKER BACKEND ############################################
start:
	docker-compose up --build -d
stop:
	docker-compose stop
down:
	docker-compose down
mysql:
	docker exec -it db-mysql mysql -u root -proot
grant:
	docker exec -it db-mysql mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS `sf6_app`;GRANT ALL PRIVILEGES ON `sf6_app`.* TO `fayette`@`localhost` IDENTIFIED BY `patrician` WITH GRANT OPTION; FLUSH PRIVILEGES;"



