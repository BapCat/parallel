<?php namespace BapCat\Parallel;

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

  public function start(callable $init, callable $loop, callable $onWorkerFinished, callable $onWorkerFailed) {
    $init();

    while(true) {
      foreach($this->pids as $pid) {
        $ret = pcntl_waitpid($pid, $status, WNOHANG);

        if($ret === -1) {
          throw new RuntimeException("Failed to query worker $pid");
        }

        if($ret === 0) {
          continue;
        }

        unset($this->pids[$ret]);

        $code = pcntl_wifexited($status) ? pcntl_wexitstatus($status) : -1;

        if($code === 0) {
          $onWorkerFinished($ret);
        } else {
          $onWorkerFailed($ret, $code);
        }
      }

      $loop();

      usleep(1000);
    }
  }

  public function canSpawn() {
    return count($this->pids) < $this->maxWorkers;
  }

  public function spawn(callable $worker) {
    if(!$this->canSpawn()) {
      throw new RuntimeException('No workers available');
    }

    $pid = pcntl_fork();

    if($pid === -1) {
      throw new RuntimeException('Could not fork');
    }

    if($pid === 0) {
      $worker();
      exit(0);
    }

    $this->pids[$pid] = $pid;

    return new Child($pid);
  }
}
