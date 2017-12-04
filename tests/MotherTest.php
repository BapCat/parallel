<?php

use BapCat\Parallel\ForkedChild;
use BapCat\Parallel\Mother;
use PHPUnit\Framework\TestCase;

class MotherTest extends TestCase {
  /**
   * @var  Mother
   */
  private $mother;

  public function testMother() {
    ob_end_flush();

    $this->mother = new Mother(10);
    $this->mother->start([$this, 'motherStart'], [$this, 'motherSpin'], [$this, 'onChildExit'], [$this, 'onChildDeath']);

    echo getmypid() . ": exiting";
  }

  public function motherStart() {
    echo getmypid() . ": Mother starting\n";

    $child = $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'spawnChild']);
    $this->mother->spawn([$this, 'failToSpawnChild']);

    $child->message(['key' => 'val']);
  }

  public function motherSpin() {

  }

  public function onChildExit($pid) {
    echo getmypid() . ": $pid exited successfully\n";
  }

  public function onChildDeath($pid, $code) {
    echo getmypid() . ": $pid exited with code $code\n";
  }

  public function spawnChild() {
    return new class extends ForkedChild {
      public function onStartup() {
        echo getmypid() . ": Child starting\n";
      }

      public function onSpin() {
        echo getmypid() . ": Child spinning\n";
        $this->stop();
      }

      public function onMessage($msg) {
        echo getmypid() . ": Got message " . var_export($msg, true) . "\n";
      }
    };
  }

  public function failToSpawnChild() {
    echo getmypid() . ": Child starting\n";
    sleep(1);
    exit(1);
  }
}
