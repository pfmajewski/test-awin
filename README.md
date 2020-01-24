
Test AWIN
=========



Introduction
------------




Installation
------------

### Requirements

1. docker
2. docker-compose

### Initial setup

```shell script

docker-compose build
docker-compose run php composer install

```

```shell script
docker-compose run php sh ./reset-data.sh
```


Usage
-----

Import data from CSV file:

```shell script
docker-compose run php bin/console  app:import-csv  ./data/data.csv
```

*) as command runs inside container that maps volume to project directory 
the csv file has to be inside the project too. You can copy any desired csv file
inside data folder and use that path.

  




