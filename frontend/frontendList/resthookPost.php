<?php

require_once '../../backend/ActionStep.php';
require_once '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$event = $_GET['event'];
$target_url = $_GET['targetURL'];

if (!empty($event) && !empty($target_url)) {
    echo $actionstep->createResthook($event, $target_url);
}
