### Using the Generic Interface REST of the ((OTRS)) Community Edition with PHP

#### Requirements
- ((OTRS)) Community Edition version 6
- PHP 7.x with composer

#### Prepare your ticket system
First, download the web service configuration from [GitHub](https://raw.githubusercontent.com/OTRS/otrs/rel-6_0/development/webservices/GenericTicketConnectorREST.yml). Navigate as an admin to `Admin` => `Web Service Management` => `Add Web Service` => `Import web service`. Enter a name for the web service. I suggest to use `GenericTicketConnectorREST` because this is used in the example.

#### Install example client
Clone this repository and run `composer update` to add the [Unirest](https://github.com/Mashape/unirest-php) library:

```bash
$ git clone https://github.com/rkaldung/otrs-gi-rest-php.git php-rest-client
$ cd php-rest-client
$ composer update
```

#### Prepare the client
Edit client.php and complete the baseURL and configure [FQDN](https://github.com/rkaldung/otrs-gi-rest-php/blob/master/client.php#L10), [web service name](https://github.com/rkaldung/otrs-gi-rest-php/blob/master/client.php#L11) and valid [agent credentials](https://github.com/rkaldung/otrs-gi-rest-php/blob/master/client.php#L16).

#### Run you client
Your client is ready to go and can be executed by `php client.php`

#### Misc
An introduction for the Generic Interface for the latest ((OTRS)) Community Editon is available in the [online manual](https://doc.otrs.com/doc/manual/admin/6.0/en/html/genericinterface.html).

The default opererations TicketCreate and TicketUpdate are not able to send a new article via e-mail. For this use case you could install the free add-on [Znuny4OTRS-GIArticleSend](https://github.com/znuny/Znuny4OTRS-GIArticleSend).




