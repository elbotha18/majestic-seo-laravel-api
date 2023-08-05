# Laravel Majestic API Connector

This package provides a convenient way to interact with the Majestic SEO API in your Laravel applications.

## Installation

You can install the package via Composer:

```bash
composer require elbotha18/majestic-seo-laravel-api
```

## Usage

First, make sure you have configured your Majestic API key in your Laravel configuration file (`config/services.php`):

```php
'majestic' => [
    'api_key' => env('MAJESTIC_API_KEY'),
]
```
Create an instance of MajesticService with your API key, then prepare the parameters required for command and the call the command:
(Note, the "app_api_key" and "cmd" will be added)
```php
use Elbotha18\Majestic\ApiService;

private function getIndexItemInfo() {
    $apiKey = config('services.majestic.api_key');
    $majesticService = new ApiService($apiKey); // live
    // $majesticService = new ApiService($apiKey, $sandbox = true); // dev
    
    $string_urls = "example.com, example2.com, example3.com"; // string format urls
    $parameters = $this->prepareItemsParameter($string_urls); // gets items count and structures each item
    $parameters["DesiredTopics"] = 5; // add any custom parameters you would like to set
    
    $response = $majesticService->executeCommand("GetIndexItemInfo", $parameters); // execute command
    
    if ($response->isOK()) {
        $output = $this->processResponse($response);
    } else {
        $error = $response->getErrorMessage();
        // Handle the error
    }
}

```
# Helper Functions
You can utilize the following helper functions within your controller:

prepareItemsParameter($itemsToQuery): Prepares the items parameter for the API request.
processResponse($response): Processes the API response to extract the data.
Here's an example implementation of these helper functions:
```php
private function prepareItemsParameter($itemsToQuery) {
    $items = preg_split("/, /", $itemsToQuery, -1);

    $parameters = [];
    for ($i = 0; $i < count($items); $i++) {
        $parameters["item" . $i] = $items[$i];
    }

    $parameters["items"] = count($items);
    $parameters["datasource"] = "fresh";

    return $parameters;
}

private function processResponse($response) {
    $results = $response->getTableForName('Results');
    $output = [];

    foreach ($results->getTableRows() as $row) {
        $item = $row['Item'];
        $itemInfo = [];

        $keys = array_keys($row);
        sort($keys);
        foreach ($keys as $key) {
            if ($key != "Item") {
                $value = $row[$key];
                $itemInfo[$key] = $value;
            }
        }

        $output[$item] = $itemInfo;
    }

    return $output;
}
```

# License
This package is open-source software licensed under the MIT license.
