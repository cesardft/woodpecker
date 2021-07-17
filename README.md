# Woodpecker

# Requirements

   * PHP 8.0
   * Lumen
   * Docker
   * Docker-compose

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Setup

**1) Wake up containers:**
   
~~~Bash
> docker-compose up -d
~~~

**2). Create user and grant access on database**

~~~Bash
> docker-compose exec db bash

> mysql -uroot -proot

> GRANT ALL ON picpay.* TO 'picpay'@'%' IDENTIFIED BY 'picpay';
~~~

**3). Migrating data**

~~~Bash
docker-compose exec app bash
php artisan migrate
~~~

## References

* [Lumen Framework Docs](https://lumen.laravel.com/docs)
* [Authentication with Lumen-Passport](https://github.com/dusterio/lumen-passport)
* [Lumen REST API with Passport and JWT](https://www.youtube.com/watch?v=g_22EUfibJ8)
* [Setting up an Laravel application with docker-compose](https://lumen.laravel.com/docs)

