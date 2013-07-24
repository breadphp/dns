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

class Query {
  public $name;
  public $type;
  public $class;
  public $currentTime;

  public function __construct($name, $type, $class, $currentTime) {
    $this->name = $name;
    $this->type = $type;
    $this->class = $class;
    $this->currentTime = $currentTime;
  }
}
