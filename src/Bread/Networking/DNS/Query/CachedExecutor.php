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
use Bread\Promises\When;

class CachedExecutor implements Interfaces\Executor
{

    private $executor;

    private $cache;

    public function __construct(Interfaces\Executor $executor, RecordCache $cache)
    {
        $this->executor = $executor;
        $this->cache = $cache;
    }

    public function query($nameserver, Query $query)
    {
        $that = $this;
        $executor = $this->executor;
        $cache = $this->cache;
        
        return $this->cache->lookup($query)->then(function ($cachedRecords) use ($that, $query) {
            return $that->buildResponse($query, $cachedRecords);
        }, function () use ($executor, $cache, $nameserver, $query) {
            return $executor->query($nameserver, $query)->then(function ($response) use ($cache, $query) {
                $cache->storeResponseMessage($query->currentTime, $response);
                return $response;
            });
        });
    }

    public function buildResponse(Query $query, array $cachedRecords)
    {
        $response = new Message();
        
        $response->header->set('id', $this->generateId());
        $response->header->set('qr', 1);
        $response->header->set('opcode', Message::OPCODE_QUERY);
        $response->header->set('rd', 1);
        $response->header->set('rcode', Message::RCODE_OK);
        
        $response->questions[] = new Record($query->name, $query->type, $query->class);
        
        foreach ($cachedRecords as $record) {
            $response->answers[] = $record;
        }
        
        $response->prepare();
        
        return $response;
    }

    protected function generateId()
    {
        return mt_rand(0, 0xffff);
    }
}
