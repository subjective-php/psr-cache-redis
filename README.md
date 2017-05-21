# Simple MongoDB Cache

[![Build Status](https://travis-ci.org/chadicus/psr-cache-redis.svg?branch=master)](https://travis-ci.org/chadicus/psr-cache-redis)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chadicus/psr-cache-redis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chadicus/psr-cache-redis/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/chadicus/psr-cache-redis/badge.svg?branch=master)](https://coveralls.io/github/chadicus/psr-cache-redis?branch=master)

[![Latest Stable Version](https://poser.pugx.org/chadicus/psr-cache-redis/v/stable)](https://packagist.org/packages/chadicus/psr-cache-redis)
[![Latest Unstable Version](https://poser.pugx.org/chadicus/psr-cache-redis/v/unstable)](https://packagist.org/packages/chadicus/psr-cache-redis)
[![License](https://poser.pugx.org/chadicus/psr-cache-redis/license)](https://packagist.org/packages/chadicus/psr-cache-redis)

[![Total Downloads](https://poser.pugx.org/chadicus/psr-cache-redis/downloads)](https://packagist.org/packages/chadicus/psr-cache-redis)
[![Daily Downloads](https://poser.pugx.org/chadicus/psr-cache-redis/d/daily)](https://packagist.org/packages/chadicus/psr-cache-redis)
[![Monthly Downloads](https://poser.pugx.org/chadicus/psr-cache-redis/d/monthly)](https://packagist.org/packages/chadicus/psr-cache-redis)

[![Documentation](https://img.shields.io/badge/reference-phpdoc-blue.svg?style=flat)](http://pholiophp.org/chadicus/psr-cache-redis)

[PSR-16 SimpleCache](http://www.php-fig.org/psr/psr-16/) Implementation using [Predis](https://github.com/nrk/predis/wiki)

## Requirements

Requires PHP 7.0 (or later).

## Composer
To add the library as a local, per-project dependency use [Composer](http://getcomposer.org)! Simply add a dependency on `chadicus/psr-cache-redis` to your project's `composer.json` file such as:

```sh
composer require chadicus/psr-cache-redis
```

## Contact
Developers may be contacted at:

 * [Pull Requests](https://github.com/chadicus/psr-cache-redis/pulls)
 * [Issues](https://github.com/chadicus/psr-cache-redis/issues)

## Project Build
With a checkout of the code get [Composer](http://getcomposer.org) in your PATH and run:

```sh
composer install
./vendor/bin/phpunit
```
