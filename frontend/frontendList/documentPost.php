<?php

require_once '../../backend/ActionStep.php';
require_once '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$file_name = $_GET['fileName'];
$matter_id = $_GET['matterId'];

if (!empty($file_name) && !empty($matter_id)) {
    $actionstep->uploadDocument($file_name, $matter_id);
}
