<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/
use projectFlow\Client;

$providers = eQual::inject(['context', 'orm', 'auth', 'access']);

$tests = [

    '001'      => array(
        'description'       =>  'Create client.',
        'return'            =>  ['integer'],
        'act'               =>  function () {

            $client = Client::create([
                    'name'        => 'test client',
                    'direction'   => 'direction  tests',
                    'phone'       => 123456789,
                    'isactive'    => true
                ])->first();

            $client = Client::id($client['id'])->read('phone')->first(true);

            return ($client['phone']);
        },
        'assert'            =>  function ($phone) {
            return ($phone == 123456789);
        },
        'rollback'          => function() {
            Client::search(['name', '=', 'test client'])->delete(true);
        }
    ),
    '002'      => [
        'description'       => 'Update direction of the client.',
        'return'            => ['string'],
        'arrange'            => function () {

            $client = Client::create([
                    'name'        => 'test client update',
                    'direction'   => 'direction test',
                    'phone'       => 123456789,
                    'isactive'    => true
                ])->first();

            return ($client);

        },
        'act'              => function ($client) {
            if($client){
                $client = Client::id($client['id'])->update(['direction'   => 'New direction test'])->first();
            }

            return ($client['direction']);
        },
        'assert'            =>  function ($direction) {
            return ($direction == 'New direction test');
        },
        'rollback'          => function() {
            Client::search(['name', '=', 'test client update'])->delete(true);
        }
    ],

    '003'      => array(
        'description'      => 'Search the project of the client.',
        'help'             => 'The test uses data from the client, be sure to initialize the projectFlow package with your data.',
        'return'           => ['integer'],
        'act'              => function () {

            $client = Client::search(['name' , 'like' , '%'. 'Jean Duran'. '%'])->read(['id'])->first();

            if ($client) {
                $projects = Client::search(['id', '=', $client['id']])->read(['projects_ids'])->first(true);
            }

            if ($projects && isset($projects['projects_ids'])){
                $count_project = count($projects['projects_ids']);
            }
            return (int) $count_project;

        },
        'assert'            =>  function ($count_project) {
            return ($count_project == 3);
        }
    )
];