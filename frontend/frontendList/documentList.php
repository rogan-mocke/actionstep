<?php

require_once '../../backend/ActionStep.php';
require_once '../../backend/DB.php';
require_once '../../backend/HttpClient.php';

$actionstep = new ActionStep();
echo \json_encode($actionstep->listDocuments());
