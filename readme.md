# GAAPFC 
GAAPFC (Gestión Académica y Administrativa para los Postgrados de la Facultad de Ciencias) es un api que presta 
servicios académicos y administrativos para aplicaciones web asociadas a los postgrados de la Facultad de Ciencias hecha
 con el framework de desarrollo [Laravel 6](https://laravel.com/docs/6.x).

<img src="https://laravel.com/assets/img/components/logo-laravel.svg">

##Instalación 

###Requisitos
- node v14.3.0
- php v7.4.3
- composer v1.9.0
- mysql v8..22
- docker v19.03.13
- docker-compose v1.25.0

###Despliegue

Configurar el archivo .env con las variables necesarias para que funcione y luego ejecutar los siguientes comandos en 
caso de ser necesario limpiar cache

####Servidor Local
```bash
#Cargador de clases optimizado
php artisan optimize
composer dump-autolad

#Borrar valor de fachada de caché
php artisan cache:clear

#Borrar caché de ruta
php artisan route:clear
php artisan route:cache

#Borrar vista de caché
php artisan view:clear

#Borrar caché de configuración
php artisan config:clear
php artisan config:cache

#Instalar dependencias
composer install

#Levantar servidor
php artisan serve
```

####Docker
```bash
docker-compose up
```

###Documentación

Para generar la documentacion mediante el uso de [PHPDocumentor](https://www.phpdoc.org/) necesitas ejecutar el comando 
```bash
php phpDocumentor.phar -t docs -d ./database/migrations/ -d ./app/{*}.php -d ./app/Http/Middleware/RoleAuthorization.php
 -d ./app/Http/Middleware/AppAuthorization.php -d ./app/Exports/ -d ./app/Notifications/ -d ./app/Console/ 
 -d ./app/Services/ -d ./app/Http/Controllers/

```
Esto da como resultado que el el directorio docs se genere un [index.html](./docs/index.html)

Ademas existe una colección de [Postman](https://www.postman.com/) tanto de ambiente como de peticiones las cuales 
contiene ejemplos de como hacer llamadas a cada endpoint del api y todas las posibles respuestas de cada endpoint, esta 
se ubica en la carpeta collections llamadas 
[GAAPFC.postman_collection.json](./collections/GAAPFC.postman_collection.json) y 
[GAAPFC_Develop.postman_environment.json](./collections/GAAPFC_Develop.postman_environment.json)

## Autores

- [Héctor Alayón](mailto:hector.alayon@ciens.ucv.ve)

## Licencia

 [MIT license](https://opensource.org/licenses/MIT).
