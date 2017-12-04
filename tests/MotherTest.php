<?php

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

    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStart']);
    $this->mother->spawn([$this, 'childStartFail']);
  }

  public function motherSpin() {

  }

  public function onChildExit($pid) {
    echo getmypid() . ": $pid exited successfully\n";
  }

  public function onChildDeath($pid, $code) {
    echo getmypid() . ": $pid exited with code $code\n";
  }

  public function childStart() {
    echo getmypid() . ": Child starting\n";
    sleep(1);
  }

  public function childStartFail() {
    echo getmypid() . ": Child starting\n";
    sleep(1);
    exit(1);
  }
}
