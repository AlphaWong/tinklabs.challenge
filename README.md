# Setup Tink Labs Code Challenge

This page for Tink Labs code challenge setup.

## Install Docker on Mac/Windows
[https://www.docker.com/](https://www.docker.com/)

## Setup environment
Below code will setup Ubuntu, Nginx, Php-fpm and Mysql. Please run the command on `docker-compose.yml` file level.
```
docker-compose up -d
```

## Framework
Laravel 5.2

## UI Menu
After environment setup, please open [http://localhost:8080/](http://localhost:8080/) on your bowser. If you see `Can't connect to MySQL server on 'mysql'` please wait 1 ~ 2 min let two container fully connected.

## Run test case
Run below for the test case
1. Inside docker container after docker-compose up.
2. Go to project folder level.
3. Run test case
```
docker exec -it server /bin/bash
cd /var/www
./vendor/bin/phpunit
```

## Close project
```
docker-compose down
```