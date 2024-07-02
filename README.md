# Laratrust

[![Build Status][ci-img]][ci]
[![Total Downloads][icon-downloads]][link-packagist]
[![Latest Version on Packagist][icon-version]][link-packagist]
[![Software License][icon-license]][link-license]

Laratrust is a PHP 8.1+ framework agnostic fully-featured authentication
and authorization system. It also provides additional features such as
user roles and additional security features.

**Laratrust's key features are:**

- Authentication.
- Authorization.
- Registration.
- Users & Roles Management.
- Driver based permission system.
- Flexible activation scenarios.
- Reminders (password reset).
- Inter-account throttling with DDoS protection.
- Custom hashing strategies.
- Multiple sessions.
- Multiple login columns.
- Integration with Laravel.
- Allow use of multiple ORM implementations.
- Native facade for easy usage outside Laravel.
- Interface driven (your own implementations at will).

## Installation

```sh
composer require focela/laratrust
```

## Quick Start

Laratrust packages are framework agnostic and as such can be integrated easily
natively or with your favorite framework.

The Laratrust package has optional support for Laravel 10, and it comes bundled
with a Service Provider and a Facade for easy integration.

After installing the package, open your Laravel config file located at
`config/app.php` and add the following lines.

In the `$providers` array add the following service provider for this package.

```php
Focela\Laratrust\Laravel\LaratrustServiceProvider::class,
```

In the `$aliases` array add the following facades for this package.

```php
'Activation' => Focela\Laratrust\Laravel\Facades\Activation::class,
'Reminder'   => Focela\Laratrust\Laravel\Facades\Reminder::class,
'Laratrust'   => Focela\Laratrust\Laravel\Facades\Laratrust::class,
```

### Assets

Run the following command to publish the migrations and config file.

```bash
php artisan vendor:publish --provider="Focela\Laratrust\Laravel\LaratrustServiceProvider"
```

### Migrations

Run the following command to migrate Laratrust after publishing the assets.

> **Note:** Before running the following command, please remove the default
> Laravel migrations to avoid table collision.

```bash
php artisan migrate
```

### Configuration

After publishing, the laratrust config file can be found under `config/focela.laratrust.php `
where you can modify the package configuration.

## Contributing

We encourage and support an active, healthy community of contributors &mdash;
including you! Details are in the [contribution guide](CONTRIBUTING.md) and
the [code of conduct](CODE_OF_CONDUCT.md). The laratrust maintainers keep an eye on
issues and pull requests, but you can also report any negative conduct to
opensource@focela.com. That email list is a private, safe space; even the laratrust
maintainers don't have access, so don't hesitate to hold us to a high
standard.

<hr>

Released under the [MIT License](LICENSE).

[ci-img]: https://github.com/focela/laratrust/actions/workflows/tests.yml/badge.svg

[ci]: https://github.com/focela/laratrust/actions/workflows/tests.yml

[icon-downloads]: https://poser.pugx.org/focela/laratrust/downloads

[icon-version]: https://poser.pugx.org/focela/laratrust/version

[icon-license]: https://poser.pugx.org/focela/laratrust/license

[link-packagist]: https://packagist.org/packages/focela/laratrust

[link-license]: https://opensource.org/license/mit
