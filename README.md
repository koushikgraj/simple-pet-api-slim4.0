# Wipfli Digital Assignment

This is a simple API application to share pet information across multiple resources. 

## Technologies used to develop
* PHP 7.4
* SQLite 3
* Slim 4.x framework
* Composer 2.x
* Notepad++ editor
* Git
* Postman

## Installation steps
Execute below composer command after getting code from git.
```
composer update
```

Database file is placed under db/ folder. 
Application will atomatically points to the database file tofetach the data.

## Start web server
Execute below command to start the web server.
Please go to application root folder before executing.
```
php -S 127.0.0.1:3000 -t public
```

## API Endpoints
Below end points are used to get pet information based on different conditions.
All output will be in JSON format. Same data can be shared between multiple platforms including UI.


### List all pets
**URL:** http://127.0.0.1:3000/pet-list
**Methiod:** GET

### Get pet details
ID parameter in the URL is different for different pets.
**Sample URL:** http://127.0.0.1:3000/pet-details/20758
**Methiod:** GET
**Sample Output:**
```json
{
  "pet_id": 20758,
  "pet_name": "Aaliyah",
  "breed": "Keeshond",
  "age": 6,
  "personality": "Agreesive",
  "shelter_id": 119,
  "location_id": 119,
  "location_name": "PetSupplies Plus #4061 ",
  "address": "1336 S. Milwaukee Avenue ",
  "city": "Libertyville",
  "state": "IL",
  "phone": "(847) 573-1630 ",
  "zip": "60048",
  "county": "Lake ",
  "pet_created_date": "null",
  "pet_updated_date": "null"
}
```

### Filter & Sort pets
Input parameter should be in HTTP post body(raw) type
**URL:** http://127.0.0.1:3000/pet-list
**Methiod:** POST
**Sample Input:**
```json
{
  "sort":"age ASC",
  "filter": {
    "breed": "German",
    "age":"2"
  }
}
```