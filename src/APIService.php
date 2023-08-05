<?php 

namespace Elbotha18\Majestic;

use Illuminate\Support\Facades\Http;

class APIService {

    private $BASE_ENDPOINT = "https://api.majestic.com/api/xml";
    protected $API_KEY;
    
    public function __construct($apiKey, $sandbox = false)
    {
        if($sandbox == true) {
            $this->BASE_ENDPOINT = "https://developer.majestic.com/api/xml";
        }
        $this->API_KEY = $apiKey;
    }

    public function executeCommand($command_name, $parameters, $timeout = 5)
    {
        $parameters["app_api_key"] = $this->API_KEY;
        $parameters["cmd"] = $command_name;

        return $this->executeRequest($parameters, $timeout);
    }

    public function executeOpenAppRequest($command_name, $parameters, $access_token, $timeout = 5)
    {
        $parameters["accesstoken"] = $access_token;
        $parameters["cmd"] = $command_name;
        $parameters["privatekey"] = $this->API_KEY;

        return $this->executeRequest($parameters, $timeout);
    }

    private function executeRequest($parameters, $timeout)
    {
        $endpoint = $this->BASE_ENDPOINT . "?";
        foreach ($parameters as $key => $value) {
            $endpoint .= $key . "=" . urlencode($value) . "&";
        }
        $endpoint = rtrim($endpoint, "&");

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->timeout($timeout)->post($endpoint, $parameters);

        $xml = $response->body();

        return new Response($xml);
    }
}