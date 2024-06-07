<?php

require_once '../../backend/ActionStep.php';
require_once '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
$resthook_id = $_GET['resthook_id'];

if (!empty($resthook_id)) {
    echo $actionstep->deleteResthook($resthook_id);
}
