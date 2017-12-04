<?php namespace BapCat\Parallel;

use function msg_get_queue;

class Child {
  private $pid;
  private $msgQueue;

  public function __construct($pid) {
    $this->pid = $pid;

    $this->msgQueue = msg_get_queue($pid, 0600);
  }
}
