<?php namespace BapCat\Parallel;

use RuntimeException;

abstract class ForkedChild {
  const MESSAGE_SIZE = 512;

  private $pid;
  private $msgQueue;
  private $running;

  public final function start() {
    $this->pid = getmypid();
    $this->msgQueue = msg_get_queue($this->pid, 0600);

    $this->onStartup();

    $this->running = true;
    while($this->running) {
      $this->waitForMessage();
      $this->onSpin();
    }
  }
  
  protected final function stop() {
    $this->running = false;
  }

  protected abstract function onStartup();
  protected abstract function onSpin();
  protected abstract function onMessage($msg);

  private function waitForMessage() {
    if(!msg_receive($this->msgQueue, 0, $type, self::MESSAGE_SIZE, $msg, true, 0, $error)) {
      throw new RuntimeException("Failed to receive message: $error");
    }

    $this->onMessage($msg);
  }
}
