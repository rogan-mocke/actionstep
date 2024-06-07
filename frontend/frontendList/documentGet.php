<?php

require_once '../../backend/ActionStep.php';
require_once '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$data = \json_decode($_GET['documentData']);

if (!empty($data->id) && !empty($data->name)) {
    echo $actionstep->downloadDocument($data);
}
