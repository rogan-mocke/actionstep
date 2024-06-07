<?php

require 'backend/ActionStep.php';
require 'backend/DB.php';

$actionstep = new ActionStep();

$data = \file_get_contents("php://input");
$actionstep->insertResthookCallback($data);
