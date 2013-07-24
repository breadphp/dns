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

namespace Bread\Networking\DNS;

use Bread\Networking\DNS\Query;
use Bread\Networking\DNS\Model\Message;
use Bread\Networking\DNS\Exceptions\RecordNotFound;

class Resolver {
  private $nameserver;
  private $executor;

  public function __construct($nameserver, Query\Interfaces\Executor $executor) {
    $this->nameserver = $nameserver;
    $this->executor = $executor;
  }

  public function resolve($domain) {
    $query = new Query($domain, Message::TYPE_A, Message::CLASS_IN, time());

    return $this->executor->query($this->nameserver, $query)->then(function (
      Message $response) {
      return $this->extractAddress($response, Message::TYPE_A);
    });
  }

  public function extractAddress(Message $response, $type) {
    $answer = $this->pickRandomAnswerOfType($response, $type);
    $address = $answer->data;
    return $address;
  }

  public function pickRandomAnswerOfType(Message $response, $type) {
    // TODO: filter by name to make sure domain matches
    // TODO: resolve CNAME aliases

    $filteredAnswers = array_filter($response->answers, function ($answer) use (
      $type) {
      return $type === $answer->type;
    });

    if (0 === count($filteredAnswers)) {
      $message = sprintf('DNS Request did not return valid answer. Received answers: %s', json_encode($response->answers));
      throw new RecordNotFound($message);
    }

    $answer = $filteredAnswers[array_rand($filteredAnswers)];

    return $answer;
  }
}
