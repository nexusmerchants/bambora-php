<?php

namespace NexusMerchants\Bambora\Communications;

use NexusMerchants\Bambora\Exceptions\ApiException;
use NexusMerchants\Bambora\Exceptions\ConnectorException;

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
        $this->auth = $auth;
    }

    /**
     * processTransaction() function - Public facing function to send a request to an endpoint.
     *
     * @param string $http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param string $endpoint Incoming API Endpoint
     * @param array $data Data for POST requests, not needed for GETs
     *
     * @access    public
     * @return    array    Parsed API response from private request method
     *
     */
    public function processTransaction($http_method, $endpoint, $data)
    {
        return $this->request($http_method, $endpoint, $data);
    }

    /**
     * request() function - Internal function to send a request to an endpoint.
     *
     * @param string|null $http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param string $url Incoming API Endpoint
     * @param array|null $data Data for POST requests, not needed for GETs
     *
     * @access    private
     * @return    array Parsed API response
     *
     * @throws ApiException
     * @throws ConnectorException
     */
    private function request($http_method = null, $url, $data = null)
    {
        //check to see if we have curl installed on the server
        if (!extension_loaded('curl')) {
            //no curl
            throw new ConnectorException('The cURL extension is required', 0);
        }

        //init the curl request
        //via endpoint to curl
        $req = curl_init($url);

        //set request headers with encoded auth
        curl_setopt($req, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Passcode ' . $this->auth,
        ]);

        //set other curl options
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($req, CURLOPT_TIMEOUT, 30);

        //set http method
        //default to GET if data is null
        //default to POST if data is not null
        if (is_null($http_method)) {
            if (is_null($data)) {
                $http_method = 'GET';
            } else {
                $http_method = 'POST';
            }
        }

        //set http method in curl
        curl_setopt($req, CURLOPT_CUSTOMREQUEST, $http_method);

        //make sure incoming payload is good to go, set it
        if (!is_null($data)) {
            curl_setopt($req, CURLOPT_POSTFIELDS, json_encode($data));
        }

        //execute curl request
        $raw = curl_exec($req);

        if (false === $raw) { //make sure we got something back
            throw new ConnectorException(curl_error($req), -curl_errno($req));
        }

        $info = curl_getinfo($req);

        //decode the result
        $res = json_decode($raw, true);
        if (is_null($res)) { //make sure the result is good to go
            throw new ConnectorException('Unexpected response format', 0);
        }

        //check for return errors from the API
        if (isset($res['code']) && 1 < $res['code'] && !($info['http_code'] >= 200 && $info['http_code'] < 300)) {
            $message = $res['message'];

            if (!empty($res['details'])) {
                $message .= ' Details: ' . json_encode($res['details']);
            }

            throw new ApiException($message, $res['code']);
        }

        return $res;
    }
}
