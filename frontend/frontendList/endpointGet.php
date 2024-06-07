<?php

require '../../backend/ActionStep.php';
require '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$endpoint = $_GET['endpoint'];

if (!empty($endpoint)) {
    $record_id = $_GET['recordId'];
    echo $actionstep->getEndpoint($endpoint, $record_id, []);
}
