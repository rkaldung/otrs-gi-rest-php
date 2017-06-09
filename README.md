### Using OTRS Generic Interface REST with PHP

The file client.php contains an example for Generic Interface operations. The web service can be configured with the  web service configuration from [Github](https://github.com/OTRS/otrs/blob/rel-5_0/development/webservices/GenericTicketConnectorREST.yml). Upload this file to create a new REST web service.

An introduction for the Generic Interface for the latest stable OTRS version is available in the  [online manual](http://doc.otrs.com/doc/manual/admin/stable/en/html/genericinterface.html).

Clone this repository and run `composer update` to add the [Unirest](https://github.com/Mashape/unirest-php) library.

```bash
$ git clone git@github.com:rkaldung/otrs-gi-rest-php.git myclient
$ cd myclient
$ composer update
```

Edit client .php and complete the baseURL by set the proper [FQDN](https://github.com/rkaldung/otrs-gi-rest-php/blob/master/client.php#L10) and use proper [credentials](https://github.com/rkaldung/otrs-gi-rest-php/blob/master/client.php#L14). 

Your client is ready to go and can be executed by `php client.php`
