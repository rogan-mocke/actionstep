<?php

require '../../backend/ActionStep.php';
require '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$endpoint = $_GET['endpoint'];

if (!empty($endpoint)) {
    $params = include 'payloads/post_' . $endpoint . '.php';
    echo $actionstep->apiRequest('POST', $endpoint, $params);
}
