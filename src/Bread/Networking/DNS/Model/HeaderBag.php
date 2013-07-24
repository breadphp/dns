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
namespace Bread\Networking\DNS\Model;

class HeaderBag
{

    public $data = '';

    public $attributes = array(
        'qdCount' => 0,
        'anCount' => 0,
        'nsCount' => 0,
        'arCount' => 0,
        'qr' => 0,
        'opcode' => Message::OPCODE_QUERY,
        'aa' => 0,
        'tc' => 0,
        'rd' => 0,
        'ra' => 0,
        'z' => 0,
        'rcode' => Message::RCODE_OK
    );

    public function get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function isQuery()
    {
        return 0 === $this->attributes['qr'];
    }

    public function isResponse()
    {
        return 1 === $this->attributes['qr'];
    }

    public function isTruncated()
    {
        return 1 === $this->attributes['tc'];
    }

    public function populateCounts(Message $message)
    {
        $this->attributes['qdCount'] = count($message->questions);
        $this->attributes['anCount'] = count($message->answers);
        $this->attributes['nsCount'] = count($message->authority);
        $this->attributes['arCount'] = count($message->additional);
    }
}
