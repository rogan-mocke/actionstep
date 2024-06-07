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
                        <option value="actioncreate">Action Create</option>
                        <option value="contactnotes">Contact Notes</option>
                        <option value="filenotes">File Notes</option>
                        <option value="participants">Participants</option>
                        <option value="timeentries">Time Entries</option>
                    </select>
                    <button class="btn btn-success" id="goPost" style="display: block;">POST</button>
                </div>
                <pre class="border-control" id="outputRequest" style="overflow: scroll; width: 100%; min-height: 400px;"></pre>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById("goPost").onclick = function () {
            const getEndpoint = document.getElementById("getEndpoint").value;

            if (getEndpoint === '') {
                getDiv.style.border = "2px solid red";
            } else {
                toggleLoader(true);

                handlePost(
                    `frontend/frontendList/endpointPost.php?endpoint=${document.getElementById("getEndpoint").value}`,
                    null,
                    function (resp) {
                        getDiv.style.border = "1px solid #c4cfd7";
                        formatJson(resp, $("#outputRequest"));
                        toggleLoader(false);
                    }
                );
            }
        };
    </script>

<?php include 'frontend/footer.php';?>
