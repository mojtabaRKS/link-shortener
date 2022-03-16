# Link Shortener

php pure lighweight, fast and simple link shortener.

## Table of Contents

- [Prerequisites](#prerequesties)
- [Install](#install)

## Prerequisites

- docker
- docker-compose

## Install

1. clone project.
2. copy the `.env.example` to `.env`
3. `docker-compose up -d`
4. `docker-compose exec -it link_shortenere_php bash`
5. `$ composer install`
6. set environment variables.
7. `$ php setup.php`
8. go to redis container with `docker exec -it link_shortenere_redis bash`
9. `$ redis-cli`
10. `$ CONFIG SET requirepass "password"
11. import from `docs/Link shortener.postman_collection.json` into postman
12. try urls :)
