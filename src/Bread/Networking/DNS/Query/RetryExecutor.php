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

namespace Bread\Networking\DNS\Query;

use Bread\Networking\DNS\Query;
use Bread\Promises\Deferred;

class RetryExecutor implements Interfaces\Executor {
  private $executor;
  private $retries;

  public function __construct(Interfaces\Executor $executor, $retries = 2) {
    $this->executor = $executor;
    $this->retries = $retries;
  }

  public function query($nameserver, Query $query) {
    $deferred = new Deferred();

    $this->tryQuery($nameserver, $query, $this->retries, $deferred->resolver());

    return $deferred->promise();
  }

  public function tryQuery($nameserver, Query $query, $retries, $resolver) {
    $that = $this;
    $errorback = function ($error) use ($nameserver, $query, $retries,
      $resolver, $that) {
      if (!$error instanceof TimeoutException) {
        $resolver->reject($error);
        return;
      }
      if (0 >= $retries) {
        $error = new \RuntimeException(sprintf("DNS query for %s failed: too many retries", $query->name), 0, $error);
        $resolver->reject($error);
        return;
      }
      $that->tryQuery($nameserver, $query, $retries - 1, $resolver);
    };

    $this->executor->query($nameserver, $query)->then(array(
      $resolver,
      'resolve'
    ), $errorback);
  }
}
