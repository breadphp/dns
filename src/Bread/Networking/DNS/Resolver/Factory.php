<?php
/**
 * Bread PHP Framework (http://github.com/saiv/Bread)
 * Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 *
 * Licensed under a Creative Commons Attribution 3.0 Unported License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 * @link       http://github.com/saiv/Bread Bread PHP Framework
 * @package    Bread
 * @since      Bread PHP Framework
 * @license    http://creativecommons.org/licenses/by/3.0/
 */

namespace Bread\Networking\DNS\Resolver;

use Bread\Networking\DNS\Resolver;
use Bread\Networking\DNS\Query\Executor;
use Bread\Networking\DNS\Query\CachedExecutor;
use Bread\Networking\DNS\Query\RetryExecutor;
use Bread\Networking\DNS\Query\RecordCache;
use Bread\Networking\DNS\Protocol\Parser;
use Bread\Networking\DNS\Protocol\BinaryDumper;
use Bread\Event\Interfaces\Loop;
use Bread\Cache;

class Factory {
  public static function create($nameserver, Loop $loop) {
    $nameserver = static::addPortToServerIfMissing($nameserver);
    $executor = static::createRetryExecutor($loop);

    return new Resolver($nameserver, $executor);
  }

  public static function createCached($nameserver, Loop $loop) {
    $nameserver = static::addPortToServerIfMissing($nameserver);
    $executor = static::createCachedExecutor($loop);

    return new Resolver($nameserver, $executor);
  }

  protected static function createExecutor(Loop $loop) {
    return new Executor($loop, new Parser(), new BinaryDumper());
  }

  protected static function createRetryExecutor(Loop $loop) {
    return new RetryExecutor(static::createExecutor($loop));
  }

  protected static function createCachedExecutor(Loop $loop) {
    return new CachedExecutor(static::createRetryExecutor($loop), new RecordCache(Cache::factory()));
  }

  protected static function addPortToServerIfMissing($nameserver) {
    return false === strpos($nameserver, ':') ? "$nameserver:53" : $nameserver;
  }
}
