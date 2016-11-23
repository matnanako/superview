# superview

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


## Install

Via Composer

``` bash
$ composer require xzwh/superview@dev
```

## Usage
Firstly, add "SuperView\Providers\SuperViewModelProvider::class" into the providers array in laravel's config/app.php.
``` php
$superview = new SuperView\SuperView($configs);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Security

If you discover any security related issues, please email huangyukun@njxzwh.com instead of using the issue tracker.

## Credits

- [yukun][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/xzwh/superview.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/xzwh/superview/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/xzwh/superview.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/xzwh/superview.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/xzwh/superview.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/xzwh/superview
[link-travis]: https://travis-ci.org/xzwh/superview
[link-scrutinizer]: https://scrutinizer-ci.com/g/xzwh/superview/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/xzwh/superview
[link-downloads]: https://packagist.org/packages/xzwh/superview
[link-author]: https://coding.net/u/huangyukun
[link-contributors]: ../../contributors
