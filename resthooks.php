<?php

include 'frontend/header.php';

if (empty($token_details)) {
    \header('Location: login.php');
}

require_once 'backend/ActionStep.php';
require_once 'backend/DB.php';

$actionstep = new ActionStep();
$url = 'http://localhost/actionstep_api/frontend/frontendList/resthookList.php';
$rest_hooks = $actionstep->getPage($url, []);
?>
    <div class="container-fluid">
        <div class="row margin-bottom-30">
            <div class="col-xs-12" style="padding:100px 0 0 230px; width:90%;">
                <h1 class="mb-4"></h1>
                <div class="border-control mb-3" id="getDiv" style="padding: 20px; width: 100%;">
                    <h5>Create a new event.</h5>
                    <select class="form-control mb-3" id="resthookEvent" style="width: 500px;">
                        <option value="" selected disabled>Please select event</option>
                        <option value="ActionDocumentDeleted">Action Document Deleted</option>
                        <option value="DataCollectionRecordValueUpdated">Data Collection Record Value Updated</option>
                        <option value="ActionParticipantAdded">Action Participant Added</option>
                        <option value="ActionParticipantDeleted">Action Participant Deleted</option>
                        <option value="TimeEntryCreated">Time Entry Create</option>
                        <option value="DisbursementCreated">Disbursement Create</option>
                        <option value="DisbursementUpdated">Disbursement Updated</option>
                        <option value="TaskCreated">Task Create</option>
                        <option value="TaskUpdated">Task Updated</option>
                        <option value="FileNoteCreated">File Note Create</option>
                        <option value="FileNoteUpdated">File Note Updated</option>
                        <option value="ActionCreated">Matter Create</option>
                        <option value="ActionUpdated">Matter Updated</option>
                        <option value="StepChanged">Step Changed</option>
                        <option value="ParticipantCreated">Participant Create</option>
                        <option value="ParticipantUpdated">Participant Update</option>
                        <option value="ActionDocumentCreated">Matter Document Created</option>
                    </select>
                    <input class="form-control mb-3" type="text" id="resthookURL" placeholder="Target URL: https://your-application-url.com/resthook-callbacks?callback_id=12345" style="width: 60%;" value="">
                    <button class="btn btn-success" id="postEvent">POST</button>
                </div>

                <div class="border-control" id="outputRequest" style="overflow: scroll; width: 100%; min-height: 150px;"></div>

                <div class="border-control mt-3" style="width: 100%; min-height: 200px;">
                    <h5 style="padding: 20px;">Currently registered REST hooks</h5>
                    <div class="border-control" id="currentDocuments" style="margin: 0 20px 20px 20px;">
                        <div style="padding-left: 50px;">
                            <div style="width: 50px;">ID</div>
                            <div style="width: 250px;">Event Name</div>
                            <div style="width: 500px;">Target URL</div>
                            <div style="width: 70px;">Status</div>
                            <div style="width: 200px;">Last Triggered</div>
                        </div>
                        <div>
                            <ul>
                                <?php if (!empty($rest_hooks)) { ?>
                                    <?php
                                    $hooks = \json_decode($rest_hooks)->resthooks;
                                    $hooks = !\is_array($hooks) ? array($hooks) : $hooks;
                                    ?>
                                    <div id="documentList">
                                        <ul>
                                            <?php foreach ($hooks as $key => $value) { ?>
                                                <li>
                                                    <div style="width: 50px;"><?php echo $value->id; ?></div>
                                                    <div style="width: 250px;"><?php echo $value->eventName; ?></div>
                                                    <div style="width: 500px;"><?php echo $value->targetUrl; ?></div>
                                                    <div style="width: 70px;"><?php echo $value->status; ?></div>
                                                    <div style="width: 200px;"><?php echo $value->triggeredLastTimestamp == null ? 'NULL' : $value->triggeredLastTimestamp; ?></div>
                                                    <button class="btn btn-default icon" onclick="deleteResthook('<?php echo $value->id; ?>')">
                                                        <img width="20px" src="frontend/img/delete-icon.png">
                                                    </button>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById("postEvent").onclick = function () {
            const resthookEvent = document.getElementById("resthookEvent").value;
            const resthookURL = document.getElementById("resthookURL").value;

            if (resthookEvent === '' || resthookURL === '') {
                getDiv.style.border = "2px solid red";
            } else {
                toggleLoader(true);

                handlePost(
                    `frontend/frontendList/resthookPost.php?event=${resthookEvent}&targetURL=${resthookURL}`,
                    null,
                    function(resp) {
                        location.reload();
                    }
                );
            }
        };

        function deleteResthook(id) {
            handlePost(
                `frontend/frontendList/resthookDelete.php?resthook_id=${id}`,
                null,
                function(resp) {
                    location.reload();
                }
            );
        }
    </script>

<?php include 'frontend/footer.php';?>
