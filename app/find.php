<?php

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

require_once('vendor/autoload.php');

const INDEX = 'list';
$options = getopt("w:");
$prefix = $options['w'] ?? 'lmn';

$http = new Client([
    'base_uri' => 'http://el_1:9200/',
]);
try {
    $http->delete(INDEX);
} catch (Exception $e) {
}

$data = json_decode(
    '{
            "mappings": {
                "properties": {
                    "word": { 
                        "type": "completion"
                    }                    
                }
            }
        }',
    true
);

$http->put(INDEX, [
    RequestOptions::JSON => $data,
]);

$words = [
    'Lemons',
    'Strawberries',
    'Oranges',
    'Limes',
    'Grapefruit',
    'Blackberries',
    'Apples',
    'Pomegranate',
];
foreach ($words as $k => $word) {
    $r = $http->put(INDEX . '/_doc/' . (string)$k + 1, [
        RequestOptions::JSON => [
            'word' => $word,
        ],
        RequestOptions::QUERY => [
            'refresh' => ''
        ]
    ]);
}

$r = $http->get(INDEX . '/_search', [
    RequestOptions::JSON =>  [
        "suggest" => [
            "word_suggest" => [
                "prefix" => $prefix,
                "completion" => [
                    "field" => "word",
                    "fuzzy" => [
                        "fuzziness" => "auto:1,3",
                        "min_length" => 3,
                        "prefix_length" => 1
                    ]
                ]
            ]
        ]
    ]
]);
$result = json_decode($r->getBody()->getContents(), true);
foreach ($result['suggest']['word_suggest'] as $item) {
    foreach ($item['options'] as $option) {
        echo  $option['text'] . PHP_EOL;
    }
}
