<?php
namespace projectFlow;
use equal\orm\Model;

class EmployeeProject extends Model {

    public static function getColumns() {
        return [
            'project_id' => [
                'type'           => 'many2one',
                'foreign_object' => 'projectFlow\Project',
                'ondelete'          => 'cascade',
            ],

            'employee_id' => [
                'type'           => 'many2one',
                'foreign_object' => 'projectFlow\Employee',
                'ondelete'          => 'cascade',
            ],
            

            'hours' => [
                'type'           => 'float',
                'required'       => true,
            ]

        ];
    }
}