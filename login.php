<?php

include 'frontend/header.php';
?>
    <div class="container-fluid">
        <div class="row margin-bottom-30">
            <div class="col-xs-12 content" style="width:90%;">
                <h1 class="mb-4">Actionstep Integration</h1>
                <div class="border-control" style="width:25%;">
                    <div>
                        <img src="frontend/img/actionstep-logo.webp" class="mt-5" style="padding:10px; width:45%; display: block; margin-left: auto; margin-right: auto;">
                        <div class="text-center pt-5 pb-5">
                            <h5>Actionstep organization</h5>
                            <?php if (empty($token_details)) { ?>
                            <button class="btn btn-success mt-3" id="connect">Connect</button>
                            <?php } else { ?>
                            <button class="btn btn-default mt-3" id="disconnect">Disconnect</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    <?php if (empty($token_details)) { ?>
        document.getElementById("connect").onclick = function () {
            location.href = "backend/pages/connect.php";
        };
    <?php } else { ?>
        document.getElementById("disconnect").onclick = function () {
            location.href = "backend/pages/disconnect.php";
        };
    <?php } ?>
    </script>

<?php include 'frontend/footer.php';?>
