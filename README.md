# POC - Consume Rest API 
Fetching and manipulate fake Json data obtained from REST API.

## Introduction
* This example is using Docker, Nginx, PHP 8.0 (fpm), Composer and Sqlite to work.
* Everything has been written in pure PHP with little help of additional libs handled by 'composer'
* It wasn't tested on Windows, Linux only.
* This is just for demo purpose and is far from perfect, do not use it in production ;)


## Prerequisites 
* You need to have Docker already installed
* A bit of knowledge how to work with Sqlite
* Nginx is set to use port 80, make sure this port is available. You can change it by editing .env
 
## Installation 

* Download this repository
* All Docker files are stored in the /docker folder
* Copy .env.example to .env. This file is required during a build.  
* To start you need to call ```docker-compose -d --build```, it will take few mins to download dependencies and build new images.
* If all goes well you should get message like this below,if not check the 'Troubleshooting' below.
```
Creating dnp-fpm ... done
Creating dnp-nginx ... done
Attaching to dnp-fpm, dnp-nginx
dnp-fpm      | [22-Apr-2021 17:43:47] NOTICE: fpm is running, pid 1
dnp-fpm      | [22-Apr-2021 17:43:47] NOTICE: ready to handle connections
dnp-nginx    | /docker-entrypoint.sh: /docker-entrypoint.d/ is not empty, will attempt to perform configuration
dnp-nginx    | /docker-entrypoint.sh: Looking for shell scripts in /docker-entrypoint.d/
dnp-nginx    | /docker-entrypoint.sh: Launching /docker-entrypoint.d/10-listen-on-ipv6-by-default.sh
dnp-nginx    | 10-listen-on-ipv6-by-default.sh: info: Getting the checksum of /etc/nginx/conf.d/default.conf
dnp-nginx    | 10-listen-on-ipv6-by-default.sh: info: /etc/nginx/conf.d/default.conf differs from the packaged version
dnp-nginx    | /docker-entrypoint.sh: Launching /docker-entrypoint.d/20-envsubst-on-templates.sh
dnp-nginx    | /docker-entrypoint.sh: Launching /docker-entrypoint.d/30-tune-worker-processes.sh
dnp-nginx    | /docker-entrypoint.sh: Configuration complete; ready for start up
```
* Now, we need to install our vendors to make the application to work. In separate terminal call this command
  ```docker exec -it dnp-fpm composer install``` if it does not work then another approach will be:
  * get containerID by calling ```docker ps```
  it will look like this
```
CONTAINER ID   IMAGE                           COMMAND                  CREATED          STATUS          PORTS                                            NAMES
6c8bc5c3db26   nginx:latest                    "/docker-entrypoint.…"   11 minutes ago   Up 11 minutes   0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp         dnp-nginx
bafd17c0b929   poc-consumer-rest-api_dnp-fpm   "docker-php-entrypoi…"   11 minutes ago   Up 11 minutes   0.0.0.0:9000->9000/tcp                           dnp-fpm
```
then use the container ID allocated to the 'dnp-fpm', under NAME column. 
 
```docker exec -it bafd17c0b929 composer install```

if all goes well, output should be like this below
```
No lock file found. Updating dependencies instead of installing from lock file. Use composer update over composer install if you do not have a lock file.
Loading composer repositories with package information
Updating dependencies
Lock file operations: 6 installs, 0 updates, 0 removals
  - Locking guzzlehttp/guzzle (7.3.0)
  - Locking guzzlehttp/promises (1.4.1)
  - Locking guzzlehttp/psr7 (1.8.1)
  - Locking psr/http-client (1.0.1)
  - Locking psr/http-message (1.0.1)
  - Locking ralouphie/getallheaders (3.0.3)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 6 installs, 0 updates, 0 removals
  - Installing psr/http-message (1.0.1): Extracting archive
  - Installing psr/http-client (1.0.1): Extracting archive
  - Installing ralouphie/getallheaders (3.0.3): Extracting archive
  - Installing guzzlehttp/psr7 (1.8.1): Extracting archive
  - Installing guzzlehttp/promises (1.4.1): Extracting archive
  - Installing guzzlehttp/guzzle (7.3.0): Extracting archive
2 package suggestions were added by new dependencies, use `composer suggest` to see details.
Generating autoload files
1 package you are using is looking for funding.
Use the `composer fund` command to find out more!
```
In your root folder you should get /vendor with all the dependencies.

* To start or stop your containers you can use:
```
docker-compose up -d 
```
or 
```
docker-compose up --build 
```
To stop, if the containers are running in the background then 
```
docker-compose down
```
or press ``` CTRL+C ``` if containers are not running in the background.

## Setup
* Sqlite DBs are already created and stored in db/ folder, but you can recreate them using schemas stored in the db/storage.sql
* Login to the dnp-fpm container and set permissions   

```
root@f0c27f660433:/app#  chown 1000:1000 storage.sqlite
root@f0c27f660433:/app#  chmod o+w storage.sqlite
root@f0c27f660433:/app#  chown 1000:1000 aggregatedStorage.sqlite
root@f0c27f660433:/app#  chmod o+w aggregatedStorage.sqlite
```
## DB
* To create DB in Sqlite execute statement as below:
```
sqlite3 storage.sqlite
sqlite3 aggregatedStorage.sqlite
```

## How it works
The app is split into 2 parts, 'command'ish and 'query'ish parts, command.php and query.php files are located in the /public folder.

* On 'command' we have:
```
/command/auth/authorise
/command/posts/process
/command/posts/aggregate
```
* On 'query' we have:
```
/query/posts/stats
```
Call the pages like this:
```http://localhost/command/auth/authorise```

> NOTE : On 'command' side there is no need to call /command/auth/authorise. 
> 
> Please start from ```/command/posts/process``` and then with ```/command/posts/aggregate```
> Parameters can be set either in the public/command.php or public/query.php files.
> All should be self-explanatory.
>


## Troubleshooting

* Getting Error:
  WARNING: The NGINX_HTTP variable is not set. Defaulting to a blank string.
  ERROR: The Compose file './docker-compose.yaml' is invalid because:
  services.dnp-nginx.ports contains an invalid type, it should be a number, or an object
    
* Solution:
  Create .env file using provided template .env.example
  ```
  cp .env.example .env
  ```
