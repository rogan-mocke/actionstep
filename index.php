<?php

include 'frontend/header.php';

if (empty($token_details)) {
    \header('Location: login.php');
}
?>
    <div class="container-fluid">
        <div class="row margin-bottom-30">
            <div class="col-xs-12" style="padding:100px 0 0 230px; width:90%;">
                <h1 class="mb-4"></h1>
                <div class="border-control mb-3" id="getDiv" style="padding: 20px; width: 100%;">
                    <h5 class="pb-1">Select an endpoint to get started.</h5>
                    <select class="form-control mr-2 mb-3" id="getEndpoint" style="width: 300px; display: inline-block;">
                        <option value="" selected disabled>Please select endpoint</option>
                        <option value="actions">Actions</option>
                        <option value="actiondocuments">Action Documents</option>
                        <option value="actionfolders">Action Folders</option>
                        <option value="actionparticipants">Action Participants</option>
                        <option value="actiontypes">Action Types</option>
                        <option value="actiontypeparticipanttypes">Action Type Participants Type</option>
                        <option value="actionbillsettings">Action Bill Settings</option>
                        <option value="contactnotes">Contact Notes</option>
                        <option value="datacollections">Data Collections</option>
                        <option value="datacollectionfields">Data Collection Fields</option>
                        <option value="datacollectionrecords">Data Collection Records</option>
                        <option value="datacollectionrecordvalues">Data Collection Record Values</option>
                        <option value="disbursements">Disbursements</option>
                        <option value="filenotes">File Notes</option>
                        <option value="participants">Participants</option>
                        <option value="participanttypes">Participant Type</option>
                        <option value="resthooks">REST Hooks</option>
                        <option value="steps">Steps</option>
                        <option value="steptasks">Step Tasks</option>
                        <option value="stepparticipanttypes">Step Participant Type</option>
                        <option value="tasks">Tasks</option>
                        <option value="timeentries">Time Entries</option>
                        <option value="users">Users</option>
                    </select>
                    <input class="form-control" id="recordId" value="" placeholder="Get single record by ID" style="width: 300px; display: inline-block;">
                    <button class="btn btn-success" id="goGet" style="display: block;">GET</button>
                </div>
                <pre class="border-control" id="outputRequest" style="overflow: scroll; width: 100%; min-height: 400px;"></pre>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById("goGet").onclick = function () {
            const getEndpoint = document.getElementById("getEndpoint").value;
            const getDiv = document.getElementById("getDiv");
            const recordId = document.getElementById("recordId").value.replace('&', "%26");

            if (getEndpoint === '') {
                getDiv.style.border = "2px solid red";
            } else {
                toggleLoader(true);

                handlePost(
                    `frontend/frontendList/endpointGet.php?endpoint=${getEndpoint}&recordId=${recordId}`,
                    null,
                    function(resp) {
                        getDiv.style.border = "1px solid #c4cfd7";
                        formatJson(resp, $("#outputRequest"));
                        toggleLoader(false);
                    }
                );
            }
        };
    </script>

<?php include 'frontend/footer.php';?>
