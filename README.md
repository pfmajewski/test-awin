
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

**Step 1.** 
Build container and install required php dependencies.

```shell script
docker-compose build
docker-compose run php composer install
```

**Step 2.**
Prepare database and load initial currency data.

```shell script
docker-compose run php sh ./reset-data.sh
```


Usage
-----

### Import data from CSV file

```shell script
docker-compose run php bin/console app:import-csv ./data/data.csv
```

*) as command runs inside container that maps volume to project directory 
the csv file has to be inside the project too. You can copy any desired csv file
inside data folder and use that path.

### Generate report of transaction in desired currency  

Use the `app:get-report` command. It is required to pass merchant id, e.g.:

```shell script
docker-compose run php bin/console app:get-report 1
```

The command accept additional options to:
* use different currency,
* specify time of the exchange rate date,
* use different format (JSON, CSV).

Run below to get detailed list available options:

```shell script
docker-compose run php bin/console app:get-report -h
```

