<?php namespace BapCat\Parallel;

use Exception;
use RuntimeException;

/**
 * The mother forker
 */
class Mother {
  private $maxWorkers;
  private $pids = [];

  public function __construct($maxWorkers) {
    $this->maxWorkers = $maxWorkers;
  }

  public function start(callable $boss, callable $onWorkerFinished, callable $onWorkerFailed) {
    while(true) {
      foreach($this->pids as $pid) {
        $ret = pcntl_waitpid($pid, $status, WNOHANG);

        if($ret === -1) {
          throw new Exception('Failed to query worker');
        }

        if($ret === 0) {
          continue;
        }

        unset($this->pids[$ret]);
        $onWorkerFinished($ret);
      }

      $boss();
    }
  }

  public function spawn(callable $worker) {
    if(count($this->pids) >= $this->maxWorkers) {
      throw new Exception('No workers available');
    }

    $pid = pcntl_fork();

    if($pid === -1) {
      throw new RuntimeException('Could not fork');
    }

    if($pid === 0) {
      $worker();
      return;
    }

    $this->pids[] = $pid;
  }
}
