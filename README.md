# DotaPro (Laravel v8 - VueJS v2.7) 
Aplicación para la administración de dotación en una empresa.

### Instalación
DotaPro requiere [PHP](https://www.php.net/downloads.php#v7.4.19) 7.4 + y [nodejs](https://nodejs.org/) v13.12 + para ejecutarse.

Clonar Repositorio
```sh
$ git clone https://github.com/jpinsignares01/DotaPro.git

$ cd dotapro
```

# Despliegue - Desarrollo

Crear contenedores Docker
```sh
# Mysql
$ docker run -d -p 33061:3306 --name mysql_home -e MYSQL_ROOT_PASSWORD=secret mysql
$ docker exec -it mysql_home bash
$ mysql -u root -p
    create schema dotapro;
```

```sh
# Laravel (Imagen Docker con las dependencias para funcionar sin problemas de versiones)
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

# Correr testing:
$ php artisan test

## Respuesta testing:
   PASS  Tests\Feature\AsignacionDispositivoTest
  ✓ vinculacion
  ✓ desvinculacion

  Tests:  2 passed
  Time:   4.16s

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

## EndPoints

### Obtener listado de empleados
#### Url:
    /api/v1/personas (GET)
#### Headers:
    - "Accept"=>"application/json"
#### Retorna el listado de empleados con los dispositivos vinculados (Ejemplo):
```sh
{
    [
        {
            "id": 1,
            "nombre": "Camilo",
            "correo": "camilo@company.com",
            "deleted_at": null,
            "created_at": "2022-10-15T00:10:42.000000Z",
            "updated_at": "2022-10-15T00:10:42.000000Z",
            "dispositivos": [
                {
                    "id": 5,
                    "serial": 6,
                    "nombre": "voluptate tempora a",
                    "tipo_dispositivo": "Parlantes",
                    "sistema_operativo": null,
                    "personas_id": 1,
                    "deleted_at": null,
                    "created_at": "2022-10-15T00:58:21.000000Z",
                    "updated_at": "2022-10-15T00:58:21.000000Z"
                },
                {
                    "id": 13,
                    "serial": 38,
                    "nombre": "ipsa assumenda",
                    "tipo_dispositivo": "Laptop",
                    "sistema_operativo": "Android",
                    "personas_id": 1,
                    "deleted_at": null,
                    "created_at": "2022-10-15T01:33:55.000000Z",
                    "updated_at": "2022-10-15T01:33:55.000000Z"
                }
            ]
        },
        ...
    ]
}
```

### Dispositivos disponibles para asignar y dispositivos ya asignados a la persona
#### Url:
    /api/v1/dispositivos/{personas_id} (GET)
#### Headers:
    - "Accept"=>"application/json"
#### Ejemplo:
```sh
{
    "code": 200,
    "data": {
        "no_asignados": {
            "13": {
                "createdAt": "2016-04-30T03:22:29Z",
                "id": 14,
                "nombre": "ipsa voluptatibus a",
                "sistema_operativo": "Android",
                "tipo_dispositivo": "Mouse",
                "serial": 14
            },
            "14": {
                "createdAt": "2018-09-12T09:29:20Z",
                "id": 15,
                "nombre": "repellat",
                "sistema_operativo": "",
                "tipo_dispositivo": "PC",
                "serial": 15
            },
            ...
        },
        "asignados": [
            {
                "id": 5,
                "serial": 6,
                "nombre": "voluptate tempora a",
                "tipo_dispositivo": "Parlantes",
                "sistema_operativo": null,
                "personas_id": 1,
                "deleted_at": null,
                "created_at": "2022-10-15T00:58:21.000000Z",
                "updated_at": "2022-10-15T00:58:21.000000Z"
            },
            {
                "id": 13,
                "serial": 38,
                "nombre": "ipsa assumenda",
                "tipo_dispositivo": "Laptop",
                "sistema_operativo": "Android",
                "personas_id": 1,
                "deleted_at": null,
                "created_at": "2022-10-15T01:33:55.000000Z",
                "updated_at": "2022-10-15T01:33:55.000000Z"
            }
        ]
    }
}
```


## EndPoints externos (mockend)

### Obtener listado de dispositivos de la compañía
#### Url:
    https://mockend.com/jpinsignares01/dispositivos_tecnologia/dispositivos (GET)
#### Ejemplo:
```sh
[
    {
        "createdAt": "2020-10-03T01:37:06Z",
        "id": 1,
        "nombre": "suscipit a",
        "sistema_operativo": "Mac OS",
        "tipo_dispositivo": "Mouse"
    },
    {
        "createdAt": "2012-03-03T05:50:11Z",
        "id": 2,
        "nombre": "adipisci",
        "sistema_operativo": "Android",
        "tipo_dispositivo": "Teléfono"
    },
    {
        "createdAt": "2014-11-14T14:16:47Z",
        "id": 3,
        "nombre": "corporis molestiae",
        "sistema_operativo": "",
        "tipo_dispositivo": "Parlantes"
    },
    ...
]
```