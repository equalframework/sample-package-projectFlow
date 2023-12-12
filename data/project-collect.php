<?php
/*
    This file is part of the Discope property management software.
    Author: Yesbabylon SRL, 2020-2022
    License: GNU AGPL 3 license <http://www.gnu.org/licenses/>
*/

use equal\orm\Domain;
use projectFlow\Project;

list($params, $providers) = eQual::announce([
    'description'   => 'Advanced search for Reports: returns a collection of Reports according to extra paramaters.',
    'extends'       => 'core_model_collect',
    'params'        => [
        'entity' =>  [
            'description'   => 'name',
            'type'          => 'string',
            'default'       => 'projectFlow\Project'
        ],
        'employee_id' => [
            'type'              => 'many2one',
            'foreign_object'    => 'projectFlow\Employee',
            'description'       => 'Employee of project to which the reports relate.'
        ],
        'status' => [
            'type'              => 'string',
            'selection'         => ['all','draft', 'approved','in_progress','cancelled','finished'],
            'description'       => 'Projects with a specific status.'
        ],
        'budget_min' => [
            'type'              => 'integer',
            'description'       => 'Minimal budget for the project.'
        ],
        'budget_max' => [
            'type'              => 'integer',
            'description'       => 'Maximal budget for the project.'
        ],
        'client_id' => [
            'type'              => 'many2one',
            'foreign_object'    => 'projectFlow\Client',
            'description'       => 'client of project to which the reports relate.'
        ],
        'date_from' => [
            'type'          => 'date',
            'description'   => "First date of the time interval.",
            'default'       => strtotime("-10 Years")
        ],
        'date_to' => [
            'type'          => 'date',
            'description'   => "Last date of the time interval.",
            'default'       => time()
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

//status
if(isset($params['status']) && strlen($params['status']) > 0 && $params['status']!= 'all') {
    $domain = Domain::conditionAdd($domain, ['status', '=', $params['status']]);
}

if(isset($params['budget_min']) && $params['budget_min'] > 0) {
    $domain = Domain::conditionAdd($domain, ['budget', '>=', $params['budget_min']]);
}

if(isset($params['budget_max']) && $params['budget_max'] > 0) {
    $domain = Domain::conditionAdd($domain, ['budget', '<=', $params['budget_max']]);
}

if(isset($params['client_id']) && $params['client_id'] > 0) {
    $domain = Domain::conditionAdd($domain, ['client_id', '=', $params['client_id']]);
}

if(isset($params['date_from']) && $params['date_from'] > 0) {
    $domain = Domain::conditionAdd($domain, ['startdate', '>=', $params['date_from']]);
}

if(isset($params['date_to']) && $params['date_to'] > 0) {
    $domain = Domain::conditionAdd($domain, ['startdate', '<=', $params['date_to']]);
}

//   employee_id : filter on Project related employe
if(isset($params['employee_id']) && $params['employee_id'] > 0) {
    $projects_ids = [];
    $projects_ids = Project::search(['employees_ids', 'contains', $params['employee_id']])->ids();
    if(count($projects_ids)) {
        $domain = Domain::conditionAdd($domain, ['id', 'in', $projects_ids]);
    }
}


$params['domain'] = $domain;
$result = eQual::run('get', 'model_collect', $params, true);

$context->httpResponse()
        ->body($result)
        ->send();
