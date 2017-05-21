<?php

require_once __DIR__ . '/vendor/autoload.php';

use Chadicus\Psr\SimpleCache\RedisCache;

$client = new Predis\Client();

$cache = new RedisCache($client);

$date = new DateTime();

$cache->set('foo', $date);

$data = $cache->get('foo');

var_export($data);
