# AuthZero

### Usage
Integrate Drupal 8/9 website with the Auth0 Single Sign-On (SSO) platform.

### Motivation
Auth0 team announced the end of life of their official [Drupal 8 module](https://www.drupal.org/project/auth0). The [Github repository](https://github.com/auth0-community/auth0-drupal) would be available until March 8, 2022 further, it will also be removed. Alternate suggested by the auth0 team is to use the [OpenID Connect / OAuth client](https://www.drupal.org/project/openid_connect) Drupal module, but as of Jan 2022 it __does not have support for the Auth0 IDP__.

There was a need to include similar functionality in one of the project, but as the official module is deprecated so decided to write this module and make it public

> Requesting folks to Fork the repo, suggest additional features, create issues if found any.

# Getting Started

## Table of Contents

- [Dependencies](#Dependencies)
- [Install](#Install)

### Dependencies
The following PHP library is used to access the Auth0 authentication and management API's.
```sh
composer require auth0/auth0-php:7.5
```

### Install

#### Github

Installing from Github requires Composer ([installation instructions](https://getcomposer.org/doc/00-intro.md)).

1. Navigate to your site's modules directory and clone this repo:

```bash
$ cd PATH/TO/DRUPAL/ROOT/modules
$ git clone https://github.com/auth0/auth0-drupal.git auth0
```

### Install from Drupal.org with Composer

1. From the root of your Drupal project run:
```bash
# not listed under packagist still #pending
$ composer require -------