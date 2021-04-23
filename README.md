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
