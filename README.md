## Pasos para crear proyecto con docker

Este es un crud basico utilizando docker

## 1 

Crear carpeta mysql_data en la misma linea de carpetas de mysql, dockerfiles, nginx etc

## 2
Crear imagenes de docker
docker-compose up -d

## 3 
Puede vacia la carpeta .src y crear proyecto laravel este lo mapeara en .src
docker-compose run composer create-project laravel/laravel:^10.0 .

## 4 
Correr migraciones
docker-compose run --rm artisan migrate

phpmyadmin: http://localhost:8090/
server: http://localhost:8080/