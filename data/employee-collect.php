<?php
/*
    This file is part of the Discope property management software.
    Author: Yesbabylon SRL, 2020-2022
    License: GNU AGPL 3 license <http://www.gnu.org/licenses/>
*/

use equal\orm\Domain;

list($params, $providers) = eQual::announce([
    'description'   => 'Advanced search for Reports: returns a collection of Reports according to extra paramaters.',
    'extends'       => 'core_model_collect',
    'params'        => [
        'entity' =>  [
            'description'   => 'name',
            'type'          => 'string',
            'default'       => 'projectFlow\Employee'
        ],
        'direction' => [
            'type'          => 'string',
            'description'   => 'Employee direction.',
            'default'       => null
        ]
    ],
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'utf-8',
        'accept-origin' => '*'
    ],
    'providers'     => [ 'context', 'orm' ]
]);
/**
 * @var \equal\php\Context $context
 * @var \equal\orm\ObjectManager $orm
 */
list($context, $orm) = [ $providers['context'], $providers['orm'] ];

//   Add conditions to the domain to consider advanced parameters
$domain = $params['domain'];

if(isset($params['direction']) && strlen($params['direction']) > 0 ) {
    $domain = Domain::conditionAdd($domain, ['direction', 'like','%'.$params['direction'].'%']);
}

$params['domain'] = $domain;
$result = eQual::run('get', 'model_collect', $params, true); //always true, it return array.

$context->httpResponse()
        ->body($result)
        ->send();
