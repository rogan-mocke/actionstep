<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <title>Actionstep API Client</title>
    <link rel="shortcut icon" type="image/png" href="frontend/img/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
   	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="frontend/css/style.css" rel="stylesheet" />

    <script src="frontend/utils.js"></script>
  	<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>
<body>
<?php

require_once 'backend/ActionStep.php';
require_once 'backend/DB.php';
require_once 'backend/HttpClient.php';

$actionstep = new ActionStep();
$token_details = $actionstep->getTokenDetails();
?>
	<div class="container-fluid header">
		<div class="row">
			<div class="col-6">
				<img class="logo" style="z-index:1005; padding:0 50px 0 0; float:left;" src="frontend/img/logo.webp" height="30" alt="Actionstep"/>
			</div>
			<div class="text-right profile" style="position:absolute; right:0;">
                <ul class="account-menu">
                    <li class="text" style="cursor:pointer;"><strong><?php echo empty($token_details) ? 'Connect' : 'Org: ' . $token_details->org_key;?><span class="ml-3 fas fa-bars"></span></strong>
                        <ul>
                            <li> <a href="login.php"><?php echo empty($token_details) ? 'Connect' : 'Disconnect';?></a> </li>
                            <li> <a href="https://docs.actionstep.com/" target="_blank">Resources</a></li>
                        </ul>
                    </li>
                </ul>
			</div>
		</div>
	</div>

<?php include 'menu.php'; ?>

    <!-- Loading spinner -->
    <div class="cssloader-overlay">
        <div class="cssloader-container">
            <div class="cssload-whirlpool"></div>
        </div>
    </div>
