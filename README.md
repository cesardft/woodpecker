# Woodpecker

## Requirements

   * PHP 8.0
   * Composer
   * Docker
   * Docker-compose

## Setup

**1) Wake up containers**
   
~~~Bash
 docker-compose up -d
~~~

**2) Install the dependencies**

~~~Bash
  composer install
~~~


**3) Create user and grant access on database**

~~~Bash
 docker-compose exec db bash

 mysql -uroot -proot

 GRANT ALL ON user.* TO 'user'@'%' IDENTIFIED BY 'user';
~~~

**4) Migrating data**

~~~Bash
docker-compose exec app bash

php artisan migrate
~~~

## API Doc

### Request

`/transaction`

    curl -i -H 'Accept: application/json' -H "Authorization: Bearer ${TOKEN}" -X POST -d 'provider=users&payee_id=2&amount=100' http://{url}/transaction  

### Response
~~~JSON
    {
    "transaction_id": 8853,
    "payer_wallet_id": 4284,
    "payee_wallet_id": 6317,
    "amount": "100"
    }
~~~
%% todo %%

## References

* [Lumen Framework Docs](https://lumen.laravel.com/docs)
* [Authentication with Lumen-Passport](https://github.com/dusterio/lumen-passport)
* [Lumen REST API with Passport and JWT](https://www.youtube.com/watch?v=g_22EUfibJ8)
* [Setting up an Laravel application with docker-compose](https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose-pt)

