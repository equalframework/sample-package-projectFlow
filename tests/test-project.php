<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/

use projectFlow\Employee;
use projectFlow\Client;
use projectFlow\Project;
use projectFlow\Company;
use SebastianBergmann\Type\TrueType;

$providers = eQual::inject(['context', 'orm', 'auth', 'access']);

$tests = [

    '501'      => array(
        'description'       =>  'Create project',
        'return'            =>  ['string'],
        'arrange'           =>  function () {
            $client = Client::create([
                'name'        => 'client test',
                'direction'   => 'direction test',
                'phone'       => 123456789,
                'isactive'    => true
            ])->first();

            return($client);
        },
        'act'              =>  function ($client) {

            if($client){
                $project = Project::create([
                    'name'             => 'project test',
                    'description'      => 'description test',
                    'direction'        => 'direction test',
                    'client_id'        => $client['id']
                ])->first();
            }

            if($project){
                $project = Project::id($project['id'])->read(['status'])->first();
            }

            return ($project['status']);
        },
        'assert'            =>  function ($status) {
            return ($status == 'draft');
        },
        'rollback'          => function() {
            Project::search(['name', '=', 'project test'])->delete(true);
            Client::search(['name', '=', 'client test'])->delete(true);
        }
    ),
    '502'      => array(
        'description'       =>  'Create five projects for the client',
        'return'            =>  ['integer'],
        'arrange'           =>  function () {
            $client = Client::create([
                'name'        => 'client test',
                'direction'   => 'direction test',
                'phone'       => 123456789,
                'isactive'    => true
            ])->first();

            return($client);
        },
        'act'              =>  function ($client) {

            $num_projects =  ($client)?  5 : 0;

            for($i = 1; $i <= $num_projects ; $i++) {
                Project::create([
                        'name'             => 'project test ' . $i,
                        'description'      => 'description test',
                        'direction'        => 'direction test',
                        'client_id'        => $client['id']
                ]);
            }

            $projects = Project::search(['client_id', '=', $client['id']])->ids();

            if($projects){
                $count_project = count($projects);
            }
            return ((int) $count_project);
        },
        'assert'            =>  function ($count_project) {
            return ($count_project == 5);
        },
        'rollback'          => function() {
            Project::search(['name', '=', 'project test'])->delete(true);
            Client::search(['name', '=', 'client test'])->delete(true);
        }
    ),
    '503'      => array(
        'description'       =>  'Search the projects by client.',
        'help'              =>  'The test uses data from the client, be sure to initialize the projectFlow package with your data.',
        'return'            =>  ['string'],
        'arrange'           =>  function () {
            $client = Client::search(['name', 'like', '%'. 'Pierre Lopez' .'%' ])->read('id')->first();

            return ($client);
        },
        'act'              =>  function ($client) {

            if($client){
                $project = Project::search(['client_id', '=', $client['id']])->read('name')->first(true);
            }

            if($project){
                $project_name= $project['name'];
            }

            return ($project_name);
        },
        'assert'            =>  function ($project_name) {
            return ($project_name == 'flight reservations');
        }
    ),

    '504'      => array(
        'description'       =>  'Search the projects that works the employees of the the company.',
        'help'              =>  'The test uses data from the company, be sure to initialize the projectFlow package with your data.',
        'return'            =>  ['integer'],
        'arrange'           =>  function () {
            $company = Company::search(['name', 'like', '%'. 'Company Flee' .'%' ])->read('id')->first();

            return ($company);
        },
        'act'              =>  function ($company) {

            if($company){
                $employees = Employee::search(['company_id' , '=' , $company['id']])->ids();
            }
            if($employees){
                $projects = Project::search(['employees_ids', 'contains', $employees])->ids();
            }

            return (count($projects));
        },
        'expected' => 3
    ),
    '505'      => array(
        'description'       =>  'Search the project by a budget range.',
        'help'              =>  'The test uses data from the project, be sure to initialize the projectFlow package with your data.',
        'return'            =>  ['string'],
        'act'              =>  function () {
            $budget_min = 1000;
            $budget_max = 2000;

            $projects = Project::search([
                    ['budget', '>=', $budget_min],
                    ['budget', '<=', $budget_max]
                ])->read('name')->first(true);

            if($projects){
                $project_name = $projects['name'];
            }
            return ($project_name);
        },
        'assert'            =>  function ($project_name) {
            return ($project_name == 'client management');
        }
    )

];