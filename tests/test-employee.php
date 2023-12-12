<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/

use projectFlow\Employee;
use projectFlow\Company;
use projectFlow\Project;

$providers = eQual::inject(['context', 'orm', 'auth', 'access']);

$tests = [

    '301'      => array(
        'description'       =>  'Creating employee.',
        'return'            =>  ['string'],
        'arrange'           =>  function () {

            $company = Company::create([
                'name'        => 'Company test',
                'direction'   => 'direction test',
                'phone'       => 123456789
            ])->first();

            return($company);
        },
        'act'              =>  function ($company) {

            if($company){
                $employee = Employee::create([
                        'firstname'        => 'first test',
                        'lastname'         => 'last test',
                        'direction'        => 'direction client test',
                        'company_id'       => $company['id'],
                        'email'            => 'email@gmail.com'
                    ])->first(true);
            }
            if($employee){
                $employee = Employee::id($employee['id'])->read('name')->first(true);
            }

            return ($employee['name']);
        },
        'assert'            =>  function ($name) {
                return ($name == 'first test last test');
        },
        'rollback'          => function() {
            Employee::search(['name', '=', 'first test last test'])->delete(true);
            Company::search(['name', '=', 'Company test'])->delete(true);
        }
    ),
    '302'      => array(
        'description'       => 'Calculate the total the budget of the projects by the employee.',
        'help'              => 'The test uses data from the employee, be sure to initialize the projectFlow package with your data.',
        'return'            =>  ['integer'],
        'arrange'           => function () {
            $employee = Employee::search(['name' , 'like' , '%'. 'Marie Grand'. '%'])
                ->read(['projects_ids'])
                ->first(true);
            return($employee);
        },
        'act'              => function ($employee) {

            if($employee){
                $projects_ids = $employee['projects_ids'];
            }

            $budget = 0;
            foreach($projects_ids as $project_id) {
                $project = Project::id($project_id)->read(['budget'])->first(true);
                $budget += $project['budget'];

            }

            return((int) $budget);

        },
        'assert'            =>  function ($budget) {
            return ($budget == 60000);
        }
    )

];