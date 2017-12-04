<?php namespace BapCat\Parallel;

use RuntimeException;

class Child {
  private $pid;
  private $msgQueue;

  public function __construct($pid) {
    $this->pid = $pid;

    $this->msgQueue = msg_get_queue($pid, 0600);
  }

  public function message($msg) {
    if(!@msg_send($this->msgQueue, 1, $msg, true, true, $error)) {
      throw new RuntimeException("Failed to send message: $error");
    }
  }
}
