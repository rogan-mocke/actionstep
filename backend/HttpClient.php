<?php

class HttpClient
{
    /**
     * Send an HTTP request.
     *
     * @param string $method The HTTP method (GET, POST, DELETE).
     * @param string $url The URL to send the request to.
     * @param mixed $params The parameters to send with the request.
     * @param array $headers The headers to send with the request.
     * @param bool $is_file Whether the request is a file upload.
     *
     * @return string|null The response from the server.
     */
    public function request(string $method, string $url, $params = null, array $headers = [],  bool $is_file = false): ?string
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($is_file) {
            \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            \curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        }

        switch (\strtoupper($method)) {
            case 'GET':
            case 'DELETE':
                \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

                if (!empty($params)) {
                    \curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                }
                break;

            case 'POST':
                \curl_setopt($ch, CURLOPT_POST, true);
                \curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;

            default:
                throw new InvalidArgumentException('Invalid HTTP method: ' . $method);
        }

        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = \curl_exec($ch);
        $error = \curl_error($ch);
        \curl_close($ch);

        if ($error) {
            throw new RuntimeException('Curl error: ' . $error);
        }

        return $response;
    }

    /**
     * Format headers for CURL.
     *
     * @param array $headers The headers to format.
     *
     * @return array The formatted headers.
     */
    private function formatHeaders(array $headers): array
    {
        $formattedHeaders = [];
        foreach ($headers as $key => $value) {
            $formattedHeaders[] = $key . ': ' . $value;
        }
        return $formattedHeaders;
    }
}
