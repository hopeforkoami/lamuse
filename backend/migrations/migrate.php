<?php

require __DIR__ . '/runner.php';

$runner = new MigrationRunner();
$runner->runAll();
