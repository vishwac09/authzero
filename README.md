# AuthZero

[![Latest Stable Version](http://poser.pugx.org/vishwac09/authzero/v)](https://packagist.org/packages/vishwac09/authzero) [![Total Downloads](http://poser.pugx.org/vishwac09/authzero/downloads)](https://packagist.org/packages/vishwac09/authzero) [![Latest Unstable Version](http://poser.pugx.org/vishwac09/authzero/v/unstable)](https://packagist.org/packages/vishwac09/authzero) [![License](http://poser.pugx.org/vishwac09/authzero/license)](https://packagist.org/packages/vishwac09/authzero)

### Usage
Integrate Drupal 8/9 website with the Auth0 Single Sign-On (SSO) platform.

### Motivation
The Auth0 team has announced the retirement of their official [Drupal 8 module](https://www.drupal.org/project/auth0). The [Github repository](https://github.com/auth0-community/auth0-drupal) will be available until March 8, 2022, after which it will be removed. The auth0 team recommends using the [OpenID Connect / OAuth client](https://www.drupal.org/project/openid_connect) Drupal module as an alternative. 

There was a need to include similar functionality in one of the projects, but because the official module is deprecated, it was decided to write this module and make it public. 

> I would ask developers to download the module and then customize it to fit the needs of the project. There will be no security audits or third-party testing for this module, so treat it as you would write a custom module developed for your project. 

If you wish to go through the working of this module or how Auth0 SSO works check my blog [Drupal 8/9: Integrating with auth0](https://medium.com/@vishwa.chikate/integrating-drupal-with-auth0-2074bda2e22) on Medium.

# Getting Started

## Table of Contents

- [Dependencies](#Dependencies)
- [Install](#Install)
- [Config](#Config)
- [Versions](#Versions)

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

##### when using version 1.0.x
```bash
$ composer require vishwac09/authzero:1.0.5
```

##### when using version 2.0.x
```bash
$ composer require vishwac09/authzero:2.0.0
```

## Config
The modules provide with a config form navigate to https://SITE_DOMAIN/admin/config/auth0/settings and enter all the needed values.


## Drupal Enable

1. Make sure the required library (auth0/auth0-php:7.x) is added to the projects composer.json before enabling this module.
2. All other OAuth/openid modules must be uninstalled in order to use this module with no conflicts.

## Versions

Stable releases for the module -:
- [1.0.x](https://github.com/vishwac09/authzero/releases/tag/1.0.5)
- [2.0.x](https://github.com/vishwac09/authzero/releases/tag/1.0.5)

### 1.0.x

Version 1.0.x works only with **auth0/auth0-php:7.x** PHP library, so make sure you download the correct version when working with this module. Latest stable is the **1.0.5**.

### 2.0.x

Version 2.0.x works with **auth0/auth0-php:7.x** PHP library, so make sure you download the correct version when working with this module. Additionally this version of authzero module also has a custom Drupal hook implementation which is invoked just before the module/code logs in the user.

Hook Details -:

```php
/**
 * Hook invoked just before authzero module validates the user.
 *
 * @param array $authUserDetails
 *   A renderable array representing the user details from authzero.
 *   $user = [
 *     'nickname' => 'abc',
 *     'name' => 'abc',
 *     'picture' => 'url to picture added in auth0',
 *     'updated_at' => 2022-10-01T12:17:27.938Z,
 *     'email' => 'abc@xyz.com',
 *     'email_verified' => 1 || 0,
 *     'iss' => 'Domain of auth0 application',
 *     'sub' => 'auth0|61e3f37eb1392e00699aacc4',
 *     'aud' => 'IJmS0Sw41snjCP2OS5pzAqCI1C1zygzj',
 *     'iat' => '1664626668',
 *     'exp' => '1664626668',
 *     'acr' => 'http://schemas.openid.net/pape/policies/2007/06/multi-factor',
 *     'amr' => [],
 *     'sid' => 8FfPt9kf8aQpgKIZXvEXIcMG0tkXfWM1
 *     'nonce' => ed02edcbcefb71a2902d65f9ab729a14
 *   ];
 */
function hook_authzero_pre_validate_user(array $authUserDetails = []) {
  if (isset($authUserDetails['email']) && !empty($authUserDetails['email'])) {
    $user = User::create();
    $user->setPassword($authUserDetails['email'] . '.' . time());
    $user->enforceIsNew();
    $user->setEmail($authUserDetails['email']);
    $user->setUsername($authUserDetails['name']);
    $user->set("init", 'mail');
    $user->activate();
    //Save user account
    $user->save();
  }
}
```

The module does not login any users who do not have an account on the site. You can achieve the same functionality using the hook mentioned above.

Implement the hook in any custom module and write the code to create the user account.