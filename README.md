<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>
# Shopify Amazon Dropshipper App

### Installation
Install the dependencies and devDependencies.

For both environment
```sh
$ composer install
$ cp .env.example .env 
$ nano .env // set all credentials(ex: database, shopify api key and secret, mail credentials)
$ php artisan key:generate
$ php artisan migrate
$ php artisan db:seed
```

Edit config in .env

    QUEUE_CONNECTION=database

    $ php artisan queue:table

For development environments...

```sh
$ npm install
$ npm run dev
```
For production environments...

```sh
$ npm install --production
$ npm run prod
```
create superviser

Extra commands

```sh
$ sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl restart [superviser-name]
```
### Used Shopify Tools

* Admin rest-api, graphQL api
* App-bridge

- https://packagist.org/packages/ajuchacko/sail-7.x
- https://github.com/ajuCubettech/sail-7.x

## Sail para php 7.2

## Usage

```sh
    php artisan sail:install
    php artisan sail:publish

    alias sail='bash vendor/bin/sail'

    sail up
    sail up -d
    sail down

    sail php --version
    sail artisan tinker
    sail composer require
    sail npm run dev

    sail share // ngrok tunnel url

    sail build --no-cache
```
