<?php

class ActionStep
{
    // Database instance
    private DB $_db;

    // HTTP client instance
    private HttpClient $_httpClient;

    // Maximum page size for API requests (limit 200)
    private int $_max_page_size = 50;

    // Maximum size for document chunks in bytes (5 MB)
    private int $_document_max_chunk = 5 * 1024 * 1024;

    // Default configuration values
    private array $_defaults;

    function __construct()
    {
        // Get the database instance
        $this->_db = DB::getInstance();

        // Get the HTTP client instance
        $this->_httpClient = new HttpClient();

        // Set the default configuration
        $this->_defaults = [
            'auth_uri' 					=> 'https://go.actionstep.com/api/oauth/authorize?',
            'token_uri' 				=> 'https://api.actionstep.com/api/oauth/token',
            'client_id'					=> '',
            'client_secret'				=> '',
            'response_type' 			=> 'code',
            'scope'						=> 'all',
            'redirect_uri'				=> 'http://localhost/actionstep_api/backend/pages/connect.php',
            'document_path'             => 'C:/Users/../'
        ];
    }

    /**
     * Makes an API request to get endpoint data based on provided parameters
     *
     * @param string $endpoint The API endpoint
     * @param string $record_id The record ID
     * @param array $params The request parameters
     *
     * @return string|null The API response
     */
    public function getEndpoint(string $endpoint, string $record_id, array $params): ?string
    {
        if (empty($record_id)) {
            return $this->apiRequest('GET',$endpoint . '/?pageSize='.$this->_max_page_size, $params);
        }

        return $this->apiRequest('GET', $endpoint . '/' . $record_id, $params);
    }

    /**
     * Lists all resthooks
     *
     * @return string|null The API response
     */
    public function listResthooks(): ?string
    {
        return $this->apiRequest('GET', 'resthooks', []);
    }

    /**
     * Creates a new resthook
     *
     * @param string $event The event name
     * @param string $target_url The target URL
     *
     * @return string|null The API response
     */
    public function createResthook(string $event, string $target_url): ?string
    {
        $params = [
            "resthooks" => [
                [
                    "eventName" => $event,
                    "targetUrl" => $target_url
                ],
            ]];

        return $this->apiRequest('POST', 'resthooks', \json_encode($params));
    }

    /**
     * Deletes a specified resthook
     *
     * @param int $resthook_id The resthook ID
     *
     * @return string|null The API response
     */
    public function deleteResthook(int $resthook_id): ?string
    {
        return $this->apiRequest('DELETE', 'resthooks/' . $resthook_id, []);
    }

    /**
     * Lists all documents
     *
     * @param array $params The request parameters
     *
     * @return array The list of documents
     */
    public function listDocuments(array $params = []): array
    {
        $request = $this->apiRequest('GET', 'actiondocuments?fields=:default,file,folder&sort=-id', $params);

        $array = [];
        if (!empty($request)) {
            $request = \json_decode($request)->actiondocuments;
            $array['documents'] = $request;
        }

        $request = $this->apiRequest('GET', 'actions?fields=:default', $params);

        if (!empty($request)) {
            $request = \json_decode($request)->actions;
            $array['actions'] = $request;
        }

        return $array;
    }

    /**
     * Downloads a specified document
     *
     * @param object $data The document data
     *
     * @return string|null The download result
     */
    public function downloadDocument(object $data): ?string
    {
        $part_count = \ceil($data->size / $this->_document_max_chunk);
        $part_number = 1;
        for ($c = 1; $c <= $part_count; $c++) {
            $request = $this->apiRequest('GET', 'files/' . $data->id . '?part_number=' . $part_number, []);
            if (!empty($request)) {
                \file_put_contents($this->getLocalDocumentPath($data->name), $request, FILE_APPEND | LOCK_EX);
                $part_number++;
            }
        }

        if ($part_number == ($part_count + 1)) {
            return 'Document downloaded!';
        }

        return null;
    }

    /**
     * Uploads a document to a specified matter (action)
     *
     * @param string $file_name The file name
     * @param int $matter_id The matter ID
     *
     * @return string|null The upload result
     */
    public function uploadDocument(string $file_name, int $matter_id): ?string
    {
        // Build file in Actionstep
        $request = $this->curlDocument($file_name);

        if (!empty($request)) {
            $params = [
                'actiondocuments' => [
                    'name' => \str_replace('.' . $this->getFileExtension($file_name),'',$file_name),
                    'file' => \json_decode($request)->files->id . ';' . $file_name,
                    'links' => [
                        'action' => $matter_id
                    ]
                ]
            ];

            // Assign built file to a matter (action)
            $request = $this->apiRequest('POST', 'actiondocuments', \json_encode($params));

            if (isset(\json_decode($request)->actiondocuments)) {
                return 'Document uploaded!';
            }
        }

        return null;
    }

    /**
     * Deletes a specified document
     *
     * @param int $document_id The document ID
     * @param array $params The request parameters
     *
     * @return void
     */
    public function deleteDocument(int $document_id, array $params = []): void
    {
        $this->apiRequest('DELETE', 'actiondocuments/' . $document_id, $params);
    }

    /**
     * Generates the authorization URL for OAuth
     *
     * @return string The authorization URL
     */
    public function authorize(): string
    {
        $url = $this->_defaults['auth_uri'];
        $response_type = $this->_defaults['response_type'];
        $client_id = $this->_defaults['client_id'];
        $scope = $this->safeEncode($this->_defaults['scope']);
        $redirect_url = $this->safeEncode($this->_defaults['redirect_uri']);

        return $url . 'response_type=' . $response_type . '&client_id=' . $client_id . '&scope=' . $scope . '&redirect_uri=' . $redirect_url . '&state=TestingState';
    }

    /**
     * Requests an access token using the authorization code
     *
     * @param string $code The authorization code
     *
     * @return void
     */
    public function accessToken(string $code): void
    {
        $params = [
            'client_id' 	=> $this->_defaults['client_id'],
            'client_secret' => $this->_defaults['client_secret'],
            'code'			=> $code,
            'redirect_uri' 	=> $this->_defaults['redirect_uri'],
            'grant_type'	=> 'authorization_code',
        ];

        if ($curl = $this->curlRequest($params)) {
            if (!isset($curl->error)) {
                // Catch access token
                $this->insertTokenDetails($curl);
            }
        }
    }

    /**
     * Makes a cURL request to the given URL with provided parameters
     *
     * @param string $url The request URL
     * @param array $params The request parameters
     *
     * @return string|null The response string
     */
    public function getPage(string $url, array $params): ?string
    {
        return $this->_httpClient->request('POST', $url, \http_build_query($params), ['Content-Type: text/html']);
    }

    /**
     * Makes an API request to a specified endpoint with provided method and parameters
     *
     * @param string $method The HTTP method
     * @param string $endpoint The API endpoint
     *
     * @return string|null The API response
     */
    public function apiRequest(string $method, string $endpoint, $params = null): ?string
    {
        $token_details = $this->getTokenDetails();

        // Make API call
        if (!empty($token_details->token)) {
            $headers = [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => 'Bearer '.$token_details->token
            ];

            return $this->_httpClient->request($method, $token_details->api_uri  . 'rest/' . $endpoint, $params, $headers);
        }

        return null;
    }

    /**
     * Inserts resthook callback data into the database
     *
     * @param string $data The resthook callback data
     *
     * @return void
     */
    public function insertResthookCallback(string $data): void
    {
        if (!empty($data)) {
            $resthook_details = \json_decode($data)->meta->debug->resthook;

            $this->_db->query('INSERT INTO db_resthooks_data (id, event, data) VALUES (?,?,?)',
                [$resthook_details->id, $resthook_details->event_name, $data]);
        }
    }

    /**
     * Retrieves token details from the database and refreshes the token if necessary
     *
     * @return object|null The token details
     */
    public function getTokenDetails(): ?object
    {
        if (!empty($details = $this->_db->query('SELECT * FROM db_tokens', [])->first())) {
            // Check if token needs refreshing
            if (\time() - $details->last_updated > ($details->expires - 300)) {
                $refresh = $this->refreshToken($details->refresh);

                $details->token = $refresh['token'];
                $details->api_uri = $refresh['api_uri'];
            }

            return $details;
        }

        return null;
    }

    /**
     * Removes all token details from the database
     *
     * @return void
     */
    public function removeTokenDetails(): void
    {
        $this->_db->query('TRUNCATE TABLE db_tokens', []);
    }

    /**
     * Constructs the local document path for a given file name
     *
     * @param string|null $file_name The file name
     *
     * @return string The local document path
     */
    public function getLocalDocumentPath(?string $file_name = null): string
    {
        if ($file_name != null) {
            return $this->_defaults['document_path'] . $file_name;
        } else {
            return $this->_defaults['document_path'];
        }

    }

    /**
     * Refreshes the access token using the refresh token
     *
     * @param string $refreshToken The refresh token
     *
     * @return array The new token details
     */
    private function refreshToken(string $refreshToken): array
    {
        $params = [
            'client_id' 	=> $this->_defaults['client_id'],
            'client_secret' => $this->_defaults['client_secret'],
            'refresh_token' => $refreshToken,
            'token_uri' 	=> $this->_defaults['token_uri'],
            'grant_type' 	=> 'refresh_token',
        ];

        if ($curl = $this->curlRequest($params)) {
            // Catch refresh token
            $this->updateTokenDetails($curl);
            return [
                'token' => $curl->access_token,
                'api_uri' => $curl->api_endpoint
            ];
        }

        return [];
    }

    /**
     * Makes a cURL request with the provided parameters
     *
     * @param array $params The request parameters
     *
     * @return object|null The response object
     */
    private function curlRequest(array $params): ?object
    {
        return \json_decode($this->_httpClient->request('POST', $this->_defaults['token_uri'], $params, ['Cache-Control: no-cache']));
    }

    /**
     * Uploads a document in chunks
     *
     * @param string $file_name The file name
     *
     * @return string|null The upload result
     */
    private function curlDocument(string $file_name): ?string
    {
        $token_details = $this->getTokenDetails();

        if (empty($token_details->token)) {
            return null;
        }

        $response = null;
        $file_path = $this->getLocalDocumentPath($file_name);
        $part_number = 1;
        $bytes = \filesize($file_path);
        $part_count = \ceil($bytes / $this->_document_max_chunk);
        $upload_id = '';
        $offset = 0;
        for ($c = 1; $c <= $part_count; $c++) {
            // Create multiple files from larger file
            $file_extension = $this->getFileExtension($file_name);
            $chunk = \file_get_contents($file_path, false, null, $offset, $this->_document_max_chunk);
            $new_name = \md5(\mt_rand() . \microtime()) . '.' . $file_extension;

            // Create file
            \file_put_contents($this->getLocalDocumentPath($new_name), $chunk);
            $file = ['file' => \curl_file_create($this->getLocalDocumentPath($new_name), \mime_content_type($file_path), $file_name)];

            // Make API call
            $url = $token_details->api_uri . 'rest/files' . $upload_id . '?part_count=' . $part_count . '&part_number=' . $part_number;

            $headers = [
                'Authorization: Bearer ' . $token_details->token,
                'Content-Type: multipart/form-data',
                'Accept: application/vnd.api+json'
            ];

            $response = $this->_httpClient->request('POST', $url, $file, $headers, true);

            if (($bytes - $offset) > $this->_document_max_chunk) {
                $offset += $this->_document_max_chunk;
                $part_number++;
            }

            // Delete newly created file
            \unlink($this->getLocalDocumentPath($new_name));

            if (isset(\json_decode($response)->files->id)) {
                // Set upload id for next POST
                $upload_id = '/' . \json_decode($response)->files->id;
            }
        }

        return $response;
    }

    /**
     * Inserts token details into the database
     *
     * @param object $data The token details
     *
     * @return void
     */
    private function insertTokenDetails(object $data): void
    {
        if (!empty($data)) {
            $this->_db->query('INSERT INTO db_tokens (org_key, api_uri, token, refresh, expires, last_updated) VALUES (?,?,?,?,?,?)',
                [$data->orgkey, $data->api_endpoint, $data->access_token, $data->refresh_token, $data->expires_in, \time()]);
        }
    }

    /**
     * Updates token details in the database
     *
     * @param object $data The new token details
     *
     * @return void
     */
    private function updateTokenDetails(object $data): void
    {
        if (!empty($data)) {
            $this->_db->query('UPDATE db_tokens SET org_key = ?, api_uri = ?, token = ?, refresh = ?, expires = ?, last_updated = ?',
                [$data->orgkey, $data->api_endpoint, $data->access_token, $data->refresh_token, $data->expires_in, time()]);
        }
    }

    /**
     * Encodes data safely for use in URLs
     */
    private function safeEncode($data)
    {
        if (\is_array($data)) {
            return \array_map([$this, 'safeEncode'], $data);
        }

        if (\is_scalar($data)) {
            return \str_ireplace(['+', '%7E'], [' ', '~'], \rawurlencode((string) $data));
        }

        return '';
    }

    /**
     * Retrieves the file extension of a given file name
     *
     * @param string $file_name The file name
     *
     * @return string|null The file extension
     */
    private function getFileExtension(string $file_name): ?string
    {
        $file_extension = \explode('.', $file_name);

        if (\is_array($file_extension)) {
            return \end($file_extension);
        }

        return null;
    }
}
