<?php

require_once '../../backend/ActionStep.php';
require_once '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$document_id = $_GET['document_id'];

if (!empty($document_id)) {
    $actionstep->deleteDocument($document_id);
}
