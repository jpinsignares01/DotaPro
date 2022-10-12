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