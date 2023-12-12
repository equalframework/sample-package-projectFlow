<?php
namespace projectFlow;
use equal\orm\Model;

class Project extends Model {

    public static function getColumns() {
        return [
            'name' => [
                'type'              => 'string',
                'description'       => "Name of the project.",
                'required'          => true
            ],
            'description' => [
                'type'              => 'string',
                'description'       => "Description of the project."
            ],
            'startdate' => [
                'type'              => 'date',
                'description'       => "Date of creation of the project.",
                'default'           =>  time(),
            ],
            'budget' => [
                'type'              => 'float',
                'default'           => 1000,
            ],
            'status' => [
                'type'              => 'string',
                'default'           => 'draft',
                'selection'         => ['draft', 'approved','in_progress','cancelled','finished']
            ],
            'client_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'projectFlow\Client',
                'ondelete'          => 'cascade',
                'required'          => true,
            ],
            // Each employee can have projects
            'employee_projects_ids' => [
                'type'              => 'one2many',
                'foreign_object'    => 'projectFlow\EmployeeProject',
                'foreign_field'     => 'project_id'
            ],

            'employees_ids' => [
                'type'              => 'many2many',
                'foreign_object'    => 'projectFlow\Employee',
                'foreign_field'     => 'projects_ids',
                'rel_table'         => 'projectflow_employeeproject',
                'rel_foreign_key'   => 'employee_id',
                'rel_local_key'     => 'project_id',
                'description'       => 'Project the employee is assigned to.'
            ]

        ];
    }

    public static function getWorkflow() {
        return [
            'draft' => [
                'transitions' => [
                    'approve' => [
                        'description' => 'Update the project status based on the `approved` field.',
                        'help'        => "The `approved` field is set by a dedicated controller that manages project approval requests.",
                        'status'	  => 'approved',
                        'onafter'     => 'onafterApprove'
                    ]
                ]
            ],
            'approved' => [
                'transitions' => [
                    'cancel' => [
                        'description' => 'Set the project status as cancelled.',
                        'status'	  => 'cancelled'
                    ],
                    'start' => [
                        'description' => 'Update the project status based on the `in_progress` field.',
                        'help'        => "The `in_progress` field is set by a dedicated controller that handles the projec progression process.",
                        'status'	  => 'in_progress',
                        'onafter'     => 'onafterStart'
                    ],
                    'to_draft' => [
                        'description' => 'Redraft the project.',
                        'status'	  => 'draft'
                    ]
                ]
            ],
            'in_progress' => [
                'transitions' => [
                    'cancel' => [
                        'description' => 'Set the project status as cancelled.',
                        'status'	  => 'cancelled'
                    ],
                    'finish' => [
                        'watch'       => ['in_progress'],
                        'domain'      => ['in_progress', '=', true],
                        'description' => 'Update the project status based on the `finished` field.',
                        'help'        => "The `finished` field is set by a dedicated controller that handles the  project finilization requests.",
                        'status'	  => 'finished'
                    ]
                ]
            ],
        ];
    }

    /**
     * Handler run after successful transition to 'approved' status
     *
     */
    public static function onafterApprove($self) {

    }

    /**
     * Handler run after successful transition to 'in_progress' status
     *
     */
    public static function onafterStart($self) {

    }

}
