<?php

require_once '../ActionStep.php';
require_once '../DB.php';
require_once '../HttpClient.php';

$actionstep = new ActionStep();

// Get access token
if (empty($_GET['code'])) {
    $query = $actionstep->authorize();

    \header("Location: $query");
} else {
    $access = $actionstep->accessToken($_GET['code']);

    \header("Location: http://localhost/actionstep_api/login.php");
}
