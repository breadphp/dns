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
use Bread\Networking\DNS\Model\Message;
use Bread\Networking\DNS\Model\Record;
use Bread\Caching\Interfaces\Engine as Cache;
use Bread\Promises;

class RecordCache {
  private $cache;
  private $expiredAt;

  public function __construct(Cache $cache) {
    $this->cache = $cache;
  }

  public function lookup(Query $query) {
    $id = $this->serializeQueryToIdentity($query);

    $expiredAt = $this->expiredAt;

    return $this->cache->get($id)->then(function ($value) use ($query,
      $expiredAt) {
      $recordBag = unserialize($value);

      if (null !== $expiredAt && $expiredAt <= $query->currentTime) {
        return Promises\When::reject();
      }

      return $recordBag->all();
    });
  }

  public function storeResponseMessage($currentTime, Message $message) {
    foreach ($message->answers as $record) {
      $this->storeRecord($currentTime, $record);
    }
  }

  public function storeRecord($currentTime, Record $record) {
    $id = $this->serializeRecordToIdentity($record);

    $cache = $this->cache;

    $this->cache->get($id)->then(function ($value) {
      return unserialize($value);
    }, function ($e) {
      return new RecordBag();
    })->then(function ($recordBag) use ($id, $currentTime, $record, $cache) {
      $recordBag->set($currentTime, $record);
      $cache->set($id, serialize($recordBag));
    });
  }

  public function expire($currentTime) {
    $this->expiredAt = $currentTime;
  }

  public function serializeQueryToIdentity(Query $query) {
    return sprintf('%s:%s:%s', $query->name, $query->type, $query->class);
  }

  public function serializeRecordToIdentity(Record $record) {
    return sprintf('%s:%s:%s', $record->name, $record->type, $record->class);
  }
}
