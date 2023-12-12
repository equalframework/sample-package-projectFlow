<?php
use projectFlow\Client;

list($params, $providers) = announce([
    'description'   => 'Retrieve the list of existing clients',
    'params'        => [],
    'response'      => [
                        'content-type'  => 'application/json',
                        'charset'       => 'utf-8',
                        'accept-origin' => ['*']
                       ],
    'providers'     => ['context']
]);

list($context) = [ $providers['context'] ];

$list = Client::search([])
        ->read(['id', 'name', 'direction', 'phone', 'isactive'])
        ->adapt('txt')
        ->get(true);

$context->httpResponse()
        ->body($list)
        ->send();
