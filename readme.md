<img src="https://amchorcms-assets.s3.amazonaws.com/Screen+Shot+2020-07-20+at+1.02.50+AM.png">
<img src="https://amchorcms-assets.s3.amazonaws.com/swerve_logo.png">

# ZingFit


[![Latest Version on Packagist](https://img.shields.io/packagist/v/capeandbay/zingfit?style=plastic)](https://packagist.org/packages/capeandbay/zingfit)
[![Total Downloads](https://img.shields.io/packagist/dt/capeandbay/zingfit?color=green&style=plastic)](https://packagist.org/packages/capeandbay/zingfit)


ZingFit is a platform for Boutique Fitness Clubs with a RESTful API available to premium clients. 
This package will greatly assist a ZingFit client's Developer with Integration
when used with Laravel 6 or 7!

<small>This is a 3rd Party Package not Supported by ZingFit.</small>

## Table of Contents

<details><summary>Click to expand</summary>
<p>
- [Installation](#installation)
</p></details>

## Installation

> **Note**: Cape & Bay recomends PHP 7.3+ and Laravel/Eloquent 7.x
> 

### Installing CapeAndBay/ZingFit in a Laravel app

Install this package with [composer](https://getcomposer.org/doc/00-intro.md):

```bash
$ composer require capeandbay/zingfit
``` 
The package will automatically register itself.

Now, to run this package's migrations, first publish the migrations into your app's `migrations` directory, by running the following command:

    ```
    php artisan vendor:publish --tag="zingfit.migrations"
    ```

Finally, run the migrations:

    ```
    php artisan migrate
    ```
You can optionally publish the config file with:
```bash
php artisan vendor:publish --provider="CapeAndBay\ZingFit\ZingFitServiceProvider" --tag="config"
```
This is the contents of the published config file:
```php
return [
    'production_url' => 'https://api.zingfit.com',
    'sandbox_url' => 'https://api.zingfitlab.com',
    'client_id' => env('ZINGFIT_CLIENT_ID', '__CLIENT_ID__'),
    'client_secret' => env('ZINGFIT_CLIENT_SECRET', '__SECRET__'),
    'client_tenant_id' => env('ZINGFIT_TENANT_ID', '__TENANT__')
];
```
Note, you will need to add the env variables above to .env file.

## Usage

Use via dependency injection
 ```php
    use CapeAndBay\ZingFit\ZingFit;

    public function __construct(ZingFit $zingfit)
    {
        /*  ... code logic here ...*/
    }
```
On initialization, the ZingFit object will attempt to retrieve the latest non-expired access token.
Otherwise it will attempt to ping ZingFit using the ENV variables referenced in the zingfit config.

Here's a demo of how you can use it:
You can retrieve all of the Product Series for a Region's Club with the following:
```php
    $zingFit->getAllSeriesForSite($region_id, $site_id)
```

## Change log

7.20.2020 - First Draft.
<!-- Please see the [changelog](changelog.md) for more information on what has changed recently. -->

## Testing

None Available.
<!--
``` bash
$ composer test
```


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

-->

## Security

If you discover any security related issues, please email developers@capeandbay.com instead of using the issue tracker.

## Credits

- [Cape & Bay in Tampa, FL][https://github.com/capeandbay-devs]
- [Angel Gonzalez][https://github.com/projectsaturnstudios]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/capeandbay/zingfit.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/capeandbay/zingfit.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/capeandbay/zingfit/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/capeandbay/zingfit
[link-downloads]: https://packagist.org/packages/capeandbay/zingfit
[link-travis]: https://travis-ci.org/capeandbay/zingfit
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/capeandbay
[link-contributors]: ../../contributors
