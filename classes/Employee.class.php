<?php
namespace projectFlow;
use equal\orm\Model;

class Employee extends Model {

    public static function getColumns() {
        return [
            'name' => [
                'type'              => 'computed',
                'result_type'       => 'string',
                'description'       => "Name of the employee.",
                'store'             => true,
                'function'          => 'calcName'
            ],
            'firstname' => [
                'type'              => 'string',
                'required'          => true,
                'description'       => "First Name of the employee.",
                'dependencies'      => ['name']
            ],
            'lastname' => [
                'type'              => 'string',
                'required'          => true,
                'description'       => "Last Name of the employee.",
                'dependencies'      => ['name']
            ],
            'direction' => [
                'type'              => 'string',
                'description'       => "Direction of the employee."
            ],
            'salary' => [
                'type'              => 'float',
                'default'           => 1000,
                'description'       => "Gross salary of the employee."
            ],
            'email' => [
                'type'              => 'string',
                'description'       => "Email of the employee.",
                'usage'             => 'email',
            ],
            'company_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'projectFlow\Company',
                'ondelete'          => 'cascade',
                'required'          => true,
            ],
            //Each employee works in many projects
            'projects_ids' => [
                'type'              => 'many2many',
                'foreign_object'    => 'projectFlow\Project',
                'foreign_field'     => 'employees_ids',
                'rel_table'         => 'projectflow_employeeproject',
                'rel_foreign_key'   => 'project_id',
                'rel_local_key'     => 'employee_id',
                'description'       => 'Project the employee is assigned to.'
            ]

        ];
    }

    /**
     * Compute the value of the calcName of the employee (concatenate firstname and lastname).
     *
     * @param \equal\orm\Collection $self  An instance of a Employee collection.
     *
     */
    public static function calcName($self) {
        $result = [];
        $self->read(['firstname', 'lastname']);
        foreach ($self as $id  => $employee) {
            $result[$id] = $employee['firstname'] . ' ' . $employee['lastname'];
        }
        return $result;
    }
}