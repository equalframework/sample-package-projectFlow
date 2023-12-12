<?php
namespace projectFlow;
use equal\orm\Model;

class Company extends Model {

    public static function getColumns() {
        return [
            'name' => [
                'type'              => 'string',
                'description'       => "Name of the company.",
                'required'          => true,
            ],
            'direction' => [
                'type'              => 'string',
                'description'       => "Direction of the company.",
            ],
            'creationdate' => [
                'type'              => 'date',
                'description'       => "Date of creation of the company.",
                'default'           => time(),
            ],
            'phone' => [
                'type'              => 'string',
                'description'       => "Phone of the company.",
                'usage'             => 'phone',
            ],
             // Each company can have employees
            'employees_ids' => [
                'type'              => 'one2many',
                'foreign_object'    => 'projectFlow\Employee',
                'foreign_field'     => 'company_id'
            ]
        ];
    }

}
