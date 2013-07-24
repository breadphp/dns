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
namespace Bread\Networking\DNS\Protocol;

use Bread\Networking\DNS\Model\Message;
use Bread\Networking\DNS\Model\HeaderBag;

class BinaryDumper
{

    public function toBinary(Message $message)
    {
        $data = '';
        
        $data .= $this->headerToBinary($message->header);
        $data .= $this->questionToBinary($message->questions);
        
        return $data;
    }

    private function headerToBinary(HeaderBag $header)
    {
        $data = '';
        
        $data .= pack('n', $header->get('id'));
        
        $flags = 0x00;
        $flags = ($flags << 1) | $header->get('qr');
        $flags = ($flags << 4) | $header->get('opcode');
        $flags = ($flags << 1) | $header->get('aa');
        $flags = ($flags << 1) | $header->get('tc');
        $flags = ($flags << 1) | $header->get('rd');
        $flags = ($flags << 1) | $header->get('ra');
        $flags = ($flags << 3) | $header->get('z');
        $flags = ($flags << 4) | $header->get('rcode');
        
        $data .= pack('n', $flags);
        
        $data .= pack('n', $header->get('qdCount'));
        $data .= pack('n', $header->get('anCount'));
        $data .= pack('n', $header->get('nsCount'));
        $data .= pack('n', $header->get('arCount'));
        
        return $data;
    }

    private function questionToBinary(array $questions)
    {
        $data = '';
        
        foreach ($questions as $question) {
            $labels = explode('.', $question['name']);
            foreach ($labels as $label) {
                $data .= chr(strlen($label)) . $label;
            }
            $data .= "\x00";
            
            $data .= pack('n*', $question['type'], $question['class']);
        }
        
        return $data;
    }
}
