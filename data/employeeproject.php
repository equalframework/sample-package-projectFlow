<?php
use projectFlow\EmployeeProject;

list($params, $providers) = announce([
    'description'   => 'Retrieve the list of existing EmployeeProject',
    'params'        => [],
    'response'      => [
                        'content-type'  => 'application/json',
                        'charset'       => 'utf-8',
                        'accept-origin' => ['*']
                       ],
    'providers'     => ['context']
]);

list($context) = [ $providers['context'] ];

$list = EmployeeProject::search([])
        ->read(['project_id','employee_id','hours'])
        ->adapt('txt')
        ->get(true);

$context->httpResponse()
        ->body($list)
        ->send();
