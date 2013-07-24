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

class Message
{

    const TYPE_A = 1;

    const TYPE_NS = 2;

    const TYPE_CNAME = 5;

    const TYPE_SOA = 6;

    const TYPE_PTR = 12;

    const TYPE_MX = 15;

    const TYPE_TXT = 16;

    const CLASS_IN = 1;

    const OPCODE_QUERY = 0;

    const OPCODE_IQUERY = 1; // inverse query
    const OPCODE_STATUS = 2;

    const RCODE_OK = 0;

    const RCODE_FORMAT_ERROR = 1;

    const RCODE_SERVER_FAILURE = 2;

    const RCODE_NAME_ERROR = 3;

    const RCODE_NOT_IMPLEMENTED = 4;

    const RCODE_REFUSED = 5;

    public $data = '';

    public $header;

    public $questions = array();

    public $answers = array();

    public $authority = array();

    public $additional = array();

    public $consumed = 0;

    public function __construct()
    {
        $this->header = new HeaderBag();
    }

    public function prepare()
    {
        $this->header->populateCounts($this);
    }
}
