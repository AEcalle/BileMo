# BileMo-API

API with Symfony 6. Work carried out as part of the training course "Application Developer - PHP / Symfony" on OpenClassrooms.

## Table of Contents
1.  __[Prerequisite](#prerequisite)__
2.  __[Installation](#installation)__
*   [Clone](#clone)
*   [Configure environment variables](#configure-environment-variables)
*   [Install the project](#install-the-project)
*   [Create the database](#create-the-database)
3.  __[Access API documentation](#access-api-documentation)__

---
## PREREQUISITE

*   PHP >=8.0.2
*   Composer
*   A database management system (ex MYSQL)

See more information on technical requirements in the [Symfony official documentation](https://symfony.com/doc/current/setup.html#technical-requirements).

## INSTALLATION

### __Clone__
Copy project on your system
```bash
git clone https://github.com/AEcalle/BileMo.git
```

### __Configure environment variables__
Create an env.local file, copy this line and fill the DATABASE_URL with your own values :
```env.local
...
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

### __Install the project__
1.  In your cmd, go to the directory where you want to install the project and install dependencies with composer:
```bash
$ composer install
```
Dependencies should be installed in your project (check _vendor_ directory).  
  
To generate your own SSL keys for LexikJWTAuthentication bundle, see [the bundle documentation](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.md#generate-the-ssl-keys).  

### __Create the database__
If you are in a dev environment, you can create the database and fill it with fake contents with the following command:
```
$ composer prepare
```

__Alternatively__, follow the following steps:
1. 
```
$ php bin/console doctrine:database:drop --if-exists -f
```
2. 
```
$ php bin/console doctrine:database:create
```
3. 
```
$ php bin/console doctrine:schema:update -f
```
4. 
```
$ php bin/console doctrine:fixtures:load -n
```

## Access API documentation
There are 2 available documentation formats:  
*   Json documentation: /api/doc.json
*   Html documentation: /api/doc