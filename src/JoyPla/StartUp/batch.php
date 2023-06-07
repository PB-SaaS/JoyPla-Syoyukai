<?php

require_once 'framework/Bootstrap/autoload.php';
require_once 'JoyPla/require.php';
/** */

/** components */
use framework\Batch\BatchScheduler;
use JoyPla\Batch\PayoutCorrection;
use JoyPla\Batch\ReservationPriceBatch;

$batchScheduler = new BatchScheduler();
$batchScheduler->addJob((new ReservationPriceBatch())->everyMinute());
$batchScheduler->addJob((new PayoutCorrection())->everyMinute());

$batchScheduler->runJobs();
