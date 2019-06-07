<?php

namespace Beanstream;

/**
 * HTTPConnector class to handle HTTP requests to the REST API
 *
 * @author Kevin Saliba
 */
class HttpConnector
{
    /**
     * Base64 Encoded Auth String
     *
     * @var string $auth
     */
    protected $auth;

    /**
     * Constructor
     *
     * @param string $auth base64 encoded string to assign to the http header
     */
    public function __construct($auth)
    {
        // Set auth for this connection only
        $this->auth = $auth;
    }

    /**
     * processTransaction() function - Public facing function to send a request to an endpoint.
     *
     * @param   string  $http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param   string  $endpoint Incoming API Endpoint
     * @param   array   $data Data for POST requests, not needed for GETs
     * @access  public
     * @return  array   Parsed API response from private request method
     *
     */
    public function processTransaction($http_method, $endpoint, $data)
    {
        // Call internal request function
        return $this->request($http_method, $endpoint, $data);
    }

    /**
     * request() function - Internal function to send a request to an endpoint.
     *
     * @param   string|null $http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param   string $url Incoming API Endpoint
     * @param   array|null  $data Data for POST requests, not needed for GETs
     * @access  private
     * @return  array Parsed API response
     *
     * @throws ApiException
     * @throws ConnectorException
     */
    private function request($http_method, $url, $data = null)
    {
        // Check to see if we have curl installed on the server
        if (!extension_loaded('curl')) {
            // No curl
            throw new ConnectorException('The cURL extension is required', 0);
        }

        // Init the curl request
        // Via endpoint to curl
        $req = curl_init($url);

        // Set request headers with encoded auth
        curl_setopt($req, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Passcode '.$this->auth,
        ]);

        // Set other curl options
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($req, CURLOPT_TIMEOUT, 30);

        // Test ssl3 (remember to set platform to 'ssltest')
        // Should no longer work after 01/01/2015
        // curl_setopt($req, CURLOPT_SSLVERSION, 3);

        // Set http method
        // Default to GET if data is null
        // Default to POST if data is not null
        if (is_null($http_method)) {
            $http_method = is_null($data) ? 'GET' : 'POST';
        }

        // Set http method in curl
        curl_setopt($req, CURLOPT_CUSTOMREQUEST, $http_method);

        // Make sure incoming payload is good to go, set it
        if (!is_null($data)) {
            curl_setopt($req, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Execute curl request
        $raw = curl_exec($req);

        if (false === $raw) { // Make sure we got something back
            throw new ConnectorException(curl_error($req), -curl_errno($req));
        }

        // Decode the result
        $res = json_decode($raw, true);
        if (is_null($res)) { // Make sure the result is good to go
            throw new ConnectorException('Unexpected response format', 0);
        }

        // Check for return errors from the API
        if (isset($res['code']) && 1 < $res['code'] && !($req['http_code'] >= 200 && $req['http_code'] < 300)) {
            throw new ApiException($res['message'], $res['code']);
        }

        return $res;
    }
}
