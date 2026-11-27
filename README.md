*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*

To make it work easly I used sqllite these are the steps to install with Laravel v13.11.0

1- composer install

make sure the composer install all the requirment 
it is ok to recreate sqllite again the test will work and other APIS


2- cp .env.example .env


3- php artisan key:generate

make sure to create php key so the test will work and passes all tests to all units

4- php artisan migrate


5- php artisan test

all the requirment are tested the file at /tests/feature/invoiceAPITest (include grocery.json for testing)

6- php artisan serve

POST
http://127.0.0.1:8000/api/invoices/
Json input example 
{
        "customer": "سارة المقطري",
        "items": [
          {
            "name": "دقيق 10 كيلو",
            "qty": 2,
            "price": 9500
          }
        ]
}

GET
http://127.0.0.1:8000/api/invoices/INV-20261127-XXXXX 
wither it is Invoice ID or invoice_number

GET
http://127.0.0.1:8000/api/invoices?search=customer&page=1&per_page=10
to search for invoices to specific cutomer_name with pagination 

GET
http://127.0.0.1:8000/api/invoices?search=customer&page=1&per_page=10
to get invoices all with pagination 


Explanition 
The code contains three important parts
HTTp/Requests 
to validate the data requirements in the request before anything else
HTTP/Resources
to convert JSON to PHP data or the opposite
HTTP/Services
to deal with mathematical taxes and prevent duplicate invoices

Controller/API
to call the functions for (store,index,show)