# DotaPro

### Instalación
DotaPro requiere [PHP](https://www.php.net/downloads.php#v7.4.19) 7.4 + y [nodejs](https://nodejs.org/) v13.12 + para ejecutarse.

Clonar Repositorio
```sh
$ git clone https://bitbucket.org/jgromero7/riskscoring.git

$ cd riskscoring
```

# Despliegue - Desarrollo

Crear contenedores Docker
```sh
# Mysql
$ docker run -d -p 33061:3306 --name mysql_home -e MYSQL_ROOT_PASSWORD=secret mysql
$ docker exec -it mysql_home bash
$ mysql -u root -p
    create schema matrizriesgo;
```

```sh
# Laravel
$ docker run --name dotapro -v [path]/dotapro:/var/www -v [path]/dotapro/public:/var/www/html -p 20080-20080:80 -p 20443-20443:443 -d -it jgromero7sds/ubuntu-apache2-php7.4-laravel:v1 bash

# Accedemos a la línea de comando del contenedor
$ docker exec -it dotapro_home bash

# Iniciamos el servicio de apache2 para que la pagina sea visible
$ service apache2 start

```

# Instalación de dependencias
```sh
# Configuración recomendada
$ nano /etc/php/7.4/apache2/php.ini
    post_max_size = 250M
    upload_max_filesize = 250M
    memory_limit = 4096M
    max_execution_time = 600

# Ingresamos al directorio de proyecto
$ cd var/www

# Instalamos las dependencias
$ composer install
$ npm install

# Generamos un nuevo APP_KEY para la aplicación
$ php artisan key:generate

# Damos los permisos para los directorios
$ chmod 777 bootstrap/cache
$ chmod 777 node_modules
$ chmod 777 html
$ chmod 777 public
$ chmod 777 public/css
$ chmod 777 public/js
$ chmod 777 -R storage
$ chown -R www-data\: storage bootstrap/cache

# Ejecutamos las migraciones y seeder
$ php artisan migrate --seed
```

# Configuración Apache2
```sh
# Ejecutamos el servidor apache2
$ service apache2 start
$ a2enmod rewrite
$ service apache2 restart

# Configuramos el viertualhost
$ nano /etc/apache2/sites-available/000-default.conf

# Debajo de DocumentRoot /var/www/html agregamos las siguientes lineas
<Directory /var/www/html>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

$ service apache2 restart
```
