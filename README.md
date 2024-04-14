# AuthZero

[![Latest Stable Version](http://poser.pugx.org/vishwac09/authzero/v)](https://packagist.org/packages/vishwac09/authzero) [![Total Downloads](http://poser.pugx.org/vishwac09/authzero/downloads)](https://packagist.org/packages/vishwac09/authzero) [![Latest Unstable Version](http://poser.pugx.org/vishwac09/authzero/v/unstable)](https://packagist.org/packages/vishwac09/authzero) [![License](http://poser.pugx.org/vishwac09/authzero/license)](https://packagist.org/packages/vishwac09/authzero)

### Usage
Integrate Drupal 9.5/10 website with the Auth0 Single Sign-On (SSO) platform.

### Motivation
The Auth0 team has announced the retirement of their official [Drupal 8 module](https://www.drupal.org/project/auth0). The [Github repository](https://github.com/auth0-community/auth0-drupal) will be available until March 8, 2022, after which it will be removed. The auth0 team recommends using the [OpenID Connect / OAuth client](https://www.drupal.org/project/openid_connect) drupal module as an alternative.

There was a need to include similar functionality in one of the project, but as the official module is deprecated we decided to write this module and make it public.

> I would request to download the module and then customize it to fit the needs of their project. There will be no security audits or third-party testing for this module, so treat it as you would write a custom module developed for your project.

If you wish to go through the working of this module or how Auth0 SSO works check my blog [Drupal 8/9: Integrating with auth0](https://medium.com/@vishwa.chikate/integrating-drupal-with-auth0-2074bda2e22) on Medium.

# Version
Active releases and their compatibility.

| Version | Drupal | Recommended | Docs |
| --- | --- | --- | --- |
| 3.0.x | 9.5 and above | Yes | [Docs](https://github.com/vishwac09/authzero/releases/tag/3.0.0) |
| 2.0.x | >=8.x <=10.x | Yes | [Docs](https://github.com/vishwac09/authzero/releases/tag/2.0.0) |
| 1.0.x | >= 8.x <= 10.x | No | [Docs](https://github.com/vishwac09/authzero/releases/tag/1.0.5) |

# Getting Started (v3.0.x)

>The Drupal "__authzero__" module works only with Drupal Core version 9.5 and above. It requires the "__auth0/auth0-php__"  version "__8.10.1__" of the library, which is added as a dependency in the drupal module composer.json file. Installing the drupal module will automatically install the correct version of the "__auth0/auth0-php__" library.

## Table of Contents

- [Upgrading to 3.0.0](#Upgrading-to-3.0.0)
- [Install](#Install)
- [Module Settings](#Module-Settings)
- [Versions](#Versions)

## Upgrading to 3.0.0
Version 3.0.0 is a complete rewrite / new implementation of the authzero drupal module. In the new version all the module settings are now stored using the "__State API__" and not as "__Config API__", this was done to avoid exporting "__sensitive__" auth0 credentials as config. New fields added in the settings form, allows for better control over the user actions _viz._ Login, Logout etc.

#### <ins>Important Notes when migrating to 3.0.0.</ins>
1. Backup all the credentials.
2. Uninstall the module.
3. Install the module and add the credentials again.


## Install

#### <ins>Get from Github</ins>

> Navigate to your site's modules directory and clone this repo:

  ```bash
  $ cd PATH/TO/DRUPAL/ROOT/modules
  $ git clone https://github.com/vishwac09/authzero.git authzero
  $ composer install
  ```

#### <ins>Get from Packagist with Composer</ins>
> From the root of your Drupal project run the below command. Link to [Packagist](https://packagist.org/packages/vishwac09/authzero)

```bash
$ composer require vishwac09/authzero:3.0.0
```

## Module Settings
> The modules come with a settings form. To use it, go to https://SITE_DOMAIN/admin/config/auth0/settings and fill it out with all the necessary information. The authzero module won't function properly without it.

## Notes

>The module does not login any users who do not have an account on the site. You can achieve the same functionality using the hook mentioned above.
Implement the hook in any custom module and write the code to create the user account.
