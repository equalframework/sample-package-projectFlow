<?php
use projectFlow\Employee;

list($params, $providers) = announce([
    'description'   => 'Retrieve the list of existing employees',
    'params'        => [],
    'response'      => [
                        'content-type'  => 'application/json',
                        'charset'       => 'utf-8',
                        'accept-origin' => ['*']
                       ],
    'providers'     => ['context']
]);

list($context) = [ $providers['context'] ];

$list = Employee::search([])
        ->read(['id', 'name','firstname', 'lastname', 'direction', 'salary','company_id'])
        ->adapt('txt')
        ->get(true);

$context->httpResponse()
        ->body($list)
        ->send();
