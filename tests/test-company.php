<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/

use projectFlow\Employee;
use projectFlow\Company;

$providers = eQual::inject(['context', 'orm', 'auth', 'access']);

$tests = [

    '101'      => array(
        'description'       =>  'Create company.',
        'return'            =>  ['string'],
        'act'              =>  function () {

            $company = Company::create([
                'name'        => 'Company test',
                'direction'   => 'direction test',
                'phone'       => 123456789
                ])->first(true);

            if($company){
                $company = Company::id($company['id'])->read('name')->first(true);
            }

            return ($company['name']);
        },
        'assert'            =>  function ($name) {
            return ($name == 'Company test');
        },
        'rollback'          => function() {
            Company::search(['name', '=', 'Company test'])->delete(true);
        }
    ),
    '102'      => array(
        'description'       =>  'Assigning  five employees to the company.',
        'return'            =>  ['integer'],
        'arrange'           => function () {

            $company = Company::create([
                'name'        => 'Company test',
                'direction'   => 'direction test',
                'phone'       => 123456789
                ])->first();

            return($company);
        },
        'act'               =>  function ($company){

            $num_employees =  ($company)?  5 : 0;

            for($i = 1; $i <= $num_employees ; $i++) {
                Employee::create([
                    'firstname'        => 'first '. $i,
                    'lastname'         => 'last '. $i,
                    'direction'        => 'direction client' . $i,
                    'company_id'       => $company['id'],
                    'email'            => $i.'email@gmail.com'
                ])->read('name');
            }

            $employees = Company::id($company['id'])->read(['employees_ids'])->first(true);
            $count_employees= count($employees['employees_ids']);

            return ($count_employees);
        },
        'assert'            =>  function ($count_employees) {
            return ($count_employees == 5);
        },
        'rollback'          => function() {
            Company::search(['name', '=', 'Company test'])->delete(true);
        }

    )
];