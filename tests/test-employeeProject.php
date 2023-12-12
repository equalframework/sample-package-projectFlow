<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/
use projectFlow\Employee;
use projectFlow\Company;
use projectFlow\Client;
use projectFlow\EmployeeProject;
use projectFlow\Project;

$providers = eQual::inject(['context', 'orm', 'auth', 'access']);

$tests = [

    '401'      => array(
        'description'       =>  'Create Employee Project.',
        'return'            =>  ['string'],
        'arrange'           =>  function () {
            $companyData = [
                'name'          => 'Company test',
                'direction'     => 'direction test',
                'phone'         => 123456789
            ];

            $company =  Company::create($companyData)->first();

            $employeeData = [
                'firstname'     => 'first test',
                'lastname'      => 'last test ',
                'direction'     => 'direction test',
                'email'         => 'email@gmail.com'
            ];

            if($company){
                $employeeData['company_id'] = $company['id'];
            }

            $employee = Employee::create($employeeData)->read('name')->first();

            $clientData = [
                'name' => 'client test',
                'direction' => 'direction test',
                'phone' => 123456789,
                'isactive' => true
            ];

            $client = Client::create($clientData)->first();

            $projectData = [
                'name' => 'project test',
                'description' => 'description test',
                'direction' => 'direction test'
            ];

            if($client){
                $projectData['client_id'] = $client['id'];
            }

            $project= Project::create($projectData)->first();

            $employeeProjectData['project_id'] = $project['id'];
            $employeeProjectData['employee_id'] = $employee['id'];

            return $employeeProjectData;
        },
        'act'              =>  function ($data) {

            $employee = Employee::id($data['employee_id'])->read('id')->first(true);

            $project = Project::id($data['project_id'])->read('id')->first(true);

            if($project && $employee){
                $employeeProjectData['project_id'] = $project['id'];
                $employeeProjectData['employee_id'] = $employee['id'];
                $employeeProjectData['hours'] = 50;
            }

            $employeeProject=EmployeeProject::create($employeeProjectData)->first();

            if($employeeProject){
                $employeeProject =  EmployeeProject::id($employeeProject['id'])->read(['id','project_id' => 'name'])->first();
            }

            $name_project = $employeeProject['project_id']['name'];

            return ($name_project);
        },
        'assert'            =>  function ($name_project) {
            return ($name_project == 'project test');
        },
        'rollback'          => function() {
            Project::search(['name', '=', 'project test'])->delete(true);
            Client::search(['name', '=', 'client test'])->delete(true);
            Employee::search(['name', '=', 'first test last test'])->delete(true);
            Company::search(['name', '=', 'Company test'])->delete(true);

        }
    ),
    '402'      => array(
        'description'       =>  'Search the projects by employee',
        'help'              =>  'The test uses data from the employee, be sure to initialize the projectFlow package with your data.',
        'return'            =>  ['integer'],
        'arrange'            =>  function () {
            $employee = Employee::search(['name', 'like', '%'. 'Daniel Petit' .'%' ])->read('id')->first();

            return($employee);
        },
        'act'               =>  function ($employee) {

            if($employee){
                $employeeProject_ids = EmployeeProject::search(['employee_id', '=', $employee['id']])->ids();
            }

            if($employeeProject_ids){
                $count_id_employeeProjects = count($employeeProject_ids);
            }

            return ( (int) $count_id_employeeProjects);
        },
        'assert'            =>  function ($count_id_employeeProjects) {
            return ($count_id_employeeProjects == 3);
        }
    )

];