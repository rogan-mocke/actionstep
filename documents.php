<?php

include 'frontend/header.php';

if (empty($token_details)) {
    \header('Location: login.php');
    exit;
}

require_once 'backend/ActionStep.php';
require_once 'backend/DB.php';

$actionstep = new ActionStep();
$url = 'http://localhost/actionstep_api/frontend/frontendList/documentList.php';
$documents = $actionstep->getPage($url, []);
?>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-xs-12" style="padding: 100px 0 0 230px; width: 90%;">
                <h1 class="mb-4"></h1>
                <div class="border-control p-3" id="getDiv" style="width: 100%;">
                    <div style="width: 49%; display: inline-block;">
                        <h5 class="pb-1">Upload document to matter</h5>
                        <select class="form-control mb-3 mr-2" id="fileName" style="width: 49%; display: inline-block;">
                            <option value="" selected disabled>Please select a document</option>
                            <?php
                            $request = \array_filter(\glob($actionstep->getLocalDocumentPath('*')), 'is_file');
                            $file_path = $actionstep->getLocalDocumentPath();
                            if (!empty($request)) {
                                foreach ($request as $value) {
                                    $file_name = \str_replace($file_path, '', $value);
                                    echo "<option value='{$file_name}'>{$file_name} - " . \filesize($file_path . $file_name) . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <input class="form-control mb-3" id="matterId" style="width: 49%; display: inline-block;">

                        <button class="btn btn-success" id="postDocument">Upload</button>
                    </div>
                </div>

                <div class="border-control mt-3" id="outputRequest" style="overflow: scroll; width: 100%; min-height: 150px;"></div>

                <div class="border-control mt-3" style="width: 100%; min-height: 200px;">
                    <h5 class="p-3">Currently available documents in Actionstep</h5>
                    <div class="border-control mx-3 my-3">
                        <div class="d-flex" style="padding-left: 50px;">
                            <div style="width: 50px;">ID</div>
                            <div style="width: 70px;">Folder ID</div>
                            <div style="width: 300px;">File Name</div>
                            <div style="width: 200px;">Identifier</div>
                            <div style="width: 80px;">Size (bytes)</div>
                        </div>
                        <div>
                            <ul>
                                <?php
                                $documentsArray = \json_decode($documents)->documents;
                                if (!empty($documentsArray)) {
                                    echo '<div id="documentList"><ul>';
                                    if (!\is_array($documentsArray)) {
                                        $documentsArray = [$documentsArray];
                                    }
                                    foreach ($documentsArray as $value) {
                                        $file_data = [
                                            'id' => $value->file,
                                            'name' => $value->name . $value->extension,
                                            'size' => $value->fileSize ?? '0'
                                        ];
                                        ?>
                                        <li class="d-flex mt-2">
                                            <div style="width: 50px;"><?php echo $value->id; ?></div>
                                            <div style="width: 70px;"><?php echo $value->links->folder ?? 'None'; ?></div>
                                            <div style="width: 300px;"><?php echo $value->name . $value->extension; ?></div>
                                            <div style="width: 200px;"><?php echo $value->file; ?></div>
                                            <div style="width: 80px;"><?php echo $value->fileSize ?? '0'; ?></div>
                                            <button class="btn btn-default icon" onclick='downloadDocument(<?php echo \json_encode($file_data); ?>)'>
                                                <img width="20px" src="frontend/img/download-icon.png">
                                            </button>
                                            <button class="btn btn-default icon ml-1" onclick="deleteDocument('<?php echo $value->id; ?>')">
                                                <img width="20px" src="frontend/img/delete-icon.png">
                                            </button>
                                        </li>
                                        <?php
                                    }
                                    echo '</ul></div>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById("postDocument").onclick = function () {
            const fileName = document.getElementById("fileName").value;
            const matterId = document.getElementById("matterId").value;
            const getDiv = document.getElementById("getDiv");
            const url = `frontend/frontendList/documentPost.php?fileName=${fileName}&matterId=${matterId}`;

            if (fileName === '' || matterId === '') {
                getDiv.style.border = "2px solid red";
            } else {
                toggleLoader(true);

                handlePost(url, null, function(resp) {
                    location.reload();
                });
            }
        };

        function downloadDocument(data) {
            const url = `frontend/frontendList/documentGet.php?documentData=${JSON.stringify(data)}`;

            handlePost(url, null, function(resp) {
                document.getElementById('outputRequest').innerHTML = resp;
            });
        }

        function deleteDocument(id) {
            const url = `frontend/frontendList/documentDelete.php?document_id=${id}`;

            handlePost(url, null, function() {
                location.reload();
            });
        }
    </script>

<?php include 'frontend/footer.php';?>
