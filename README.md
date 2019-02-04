# Simple Phonebook REST API

### Requirements

PHP >= 7.1; Phalcon >= 3.4.*

### Installation

1. Install Phalcon Framework - https://phalconphp.com/en/
2. Clone this repository ```$ gir clone https://github.com/alexivanenko/phonebook_rest```
3. Go to your working directory. 
4. Run ```$ composer install```
5. Create new **MySQL DB**.
6. Edit ```phinx.yaml``` file - add your DB credentials to **production** section.
7. Run ```$ php vendor/bin/phinx migrate -e production```
8. Add new host to Apache/Nginx config and edit your *hosts* file.      

### Requests
##### GET:
* The list of all records: *<your_host>/api/items*
* You can pass page param: *<your_host>/api/items?page=2*
* Search records by first/last name or part of the name: *<your_host>/api/search/{name}*
* You can pass page param: *<your_host>/api/items/search/{name}?page=2*  
* Get record by ID: *<your_host>/api/items/get/{ID}*

##### POST, PUT, DELETE:

* Check ```test_client.php``` script for examples. Just change **API_URL** constant to your host.  