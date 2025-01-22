## Pasos para crear proyecto con docker

Este es un crud basico utilizando docker y sql server

## 1 

Clonar repositorio una vez realizada dicha accion se debe 
crear carpeta data_sql_server en la misma linea de carpetas de dockerfiles, nginx etc

## 2
Crear imagenes de docker
docker-compose up -

## 3 
Puede vaciar la carpeta .src y crear proyecto laravel este lo mapeara en .src
docker-compose run composer create-project laravel/laravel:^10.0 .

Si quiere continuar con este proyecto debe generar la key de laravel y instalar paquetes npm install

## 4 
Correr migraciones
docker-compose run --rm artisan migrate

server: http://localhost:8080/