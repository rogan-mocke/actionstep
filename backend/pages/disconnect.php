<?php

require_once '../ActionStep.php';
require_once '../DB.php';
require_once '../HttpClient.php';

$actionstep = new ActionStep();
$actionstep->removeTokenDetails();

\header("Location: http://localhost/actionstep_api/login.php");
