<?php

use BapCat\Parallel\Mother;
use PHPUnit\Framework\TestCase;

class MotherTest extends TestCase {
  public function testMother() {
    $mother = new Mother(10);

    $mother->start(function() use($mother) {
      $mother->spawn(function() {
        sleep(5);
      });
    }, function($ret) {
      echo "$ret exited\n";
    }, function() {

    });
  }
}
