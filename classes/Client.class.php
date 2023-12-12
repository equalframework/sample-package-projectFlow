<?php
namespace projectFlow;
use equal\orm\Model;

class Client extends Model {

    public static function getColumns() {
        return [
            'name' => [
                'type'              => 'string',
                'description'       => "Name of the client.",
                'required'          => true,
                'unique'            => true,
            ],
            'direction' => [
                'type'              => 'string',
                'description'       => "Direction of the client.",
            ],
            'phone' => [
                'type'              => 'string',
                'description'       => "Phone of the client.",
                'usage'             => 'phone',
            ],
            'isactive' => [
                'type'              => 'boolean',
                'default'           => true,
                'description'       => 'Flag telling if the Client is active.',
            ],
            // Each client can have projects
            'projects_ids' => [
                'type'              => 'one2many',
                'foreign_object'    => 'projectFlow\Project',
                'foreign_field'     => 'client_id'
            ]
        ];
    }

}