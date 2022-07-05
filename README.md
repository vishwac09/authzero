# AuthZero

[![Latest Stable Version](http://poser.pugx.org/vishwac09/authzero/v)](https://packagist.org/packages/vishwac09/authzero) [![Total Downloads](http://poser.pugx.org/vishwac09/authzero/downloads)](https://packagist.org/packages/vishwac09/authzero) [![Latest Unstable Version](http://poser.pugx.org/vishwac09/authzero/v/unstable)](https://packagist.org/packages/vishwac09/authzero) [![License](http://poser.pugx.org/vishwac09/authzero/license)](https://packagist.org/packages/vishwac09/authzero)

### Usage
Integrate Drupal 8/9 website with the Auth0 Single Sign-On (SSO) platform.

### Motivation
Auth0 team announced the end of life of their official [Drupal 8 module](https://www.drupal.org/project/auth0). The [Github repository](https://github.com/auth0-community/auth0-drupal) would be available until March 8, 2022 further, it will also be removed. Alternate suggested by the auth0 team is to use the [OpenID Connect / OAuth client](https://www.drupal.org/project/openid_connect) Drupal module.

There was a need to include similar functionality in one of the project, but as the official module is deprecated so decided to write this module and make it public.

> I would request developers to download the module and then customize it as per the projects need. There wont be any security audits, third party teting for this module hence treat it similar to a custom module which you would normally develop for your project.

If you wish to go through the working of this module or how Auth0 SSO works check my block on [Drupal 8/9: Integrating with auth0](https://medium.com/@vishwa.chikate/integrating-drupal-with-auth0-2074bda2e22) on Medium.

# Getting Started

## Table of Contents

- [Dependencies](#Dependencies)
- [Install](#Install)
- [Config](#Config)

### Dependencies
The following PHP library is used to access the Auth0 authentication and management API's.
```sh
composer require auth0/auth0-php:7.5
```

## Install

#### Get from Github

Installing from Github requires Composer ([installation instructions](https://getcomposer.org/doc/00-intro.md)).

1. Navigate to your site's modules directory and clone this repo:

```bash
$ cd PATH/TO/DRUPAL/ROOT/modules
$ git clone https://github.com/vishwac09/authzero.git authzero
```

#### Get from Packagist with Composer

1. From the root of your Drupal project run. Link to [Packagist](https://packagist.org/packages/vishwac09/authzero)
```bash
$ composer require vishwac09/authzero:1.0.3
```

## Config
The modules provide with a config form navigate to https://SITE_DOMAIN/admin/config/auth0/settings and enter all the needed values.


## Drupal Enable

1. Make sure the required library (auth0/auth0-php:7.x) is added to the projects composer.json before enabling this module.
2. All additional Oauth/openid modules must be uninstalled in order to use this module with no conflicts.