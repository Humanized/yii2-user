# Yii2-User - README
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Provides various interfaces to deal with routine user management tasks.

> This extension is under heavy development and requires the use of Yii framework version 2.0.7
> This version of the framework is currently in-active development  

> This module should be considered highly unstable and it's use is discouraged until further notice (really)

## Features

This module aims to be a clean, modular and simple user-administration module which can be used for Yii 2 projects version 2.0.7 and up.

A first goal is to wrap the user management functionalities provided by the yii2-advanced template to allow easy porting to other templates, such as the yii2-basic-template.
Essentially, this module can functionally achieve the same as provided by the advanced template, with little to no changes made to the interface provided and minor changes to made layout.

Other than providing the stock functionality, a lot has been made configurable:
- email-login by default, over storage of a username/email combination
- graceful status handling 

A next goal deals with the implementation of some missing core user account functionality:

- Account Confirmation
- Token Based Authentication
- RBAC Integration  

Functionality will be added to this module, as we require  it for commercial use.  

## Installation

### Install Using Composer

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require humanized/yii2-user "dev-master"
```

or add

```
"humanized/yii2-user": "dev-master"
```

to the ```require``` section of your `composer.json` file.


### Add Module to Configuration

Add following lines to the configuration file:

```php
'modules' => [
    'user' => [
        'class' => 'humanized\user\Module',
    ],
],
```

For full instructions how to configure this module, check the [CONFIG](CONFIG.md)-file.

### Run Migrations 

```bash
$ php yii migrate/up --migrationPath=@vendor/humanized/yii2-user/migrations
```

For full instructions on how to use this module, once configured, check the [USAGE](USAGE.md)-file.