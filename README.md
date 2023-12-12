
[![CircleCI](https://dl.circleci.com/status-badge/img/circleci/S4gF5Pqj58B3FRfUk8sp63/F7aVBC9PhP4Tzm8HSn877w/tree/main.svg?style=shield&circle-token=f30f62483353ca8d8c726adf7b646d76e411b0aa)](https://dl.circleci.com/status-badge/redirect/circleci/S4gF5Pqj58B3FRfUk8sp63/F7aVBC9PhP4Tzm8HSn877w/tree/main)
[![eQual](https://img.shields.io/badge/framework-eQualFramework-brightgreen)](https://github.com/equalframework/equal)
[![Maintainer](https://img.shields.io/badge/maintainer-alexandraYesbabylon-blue)](https://github.com/alexandraYesbabylon)

# eQual Framework and ProjectFlow Application Documentation

Welcome to the documentation for the eQual Framework and the ProjectFlow application. This document will guide you through the setup and usage of the eQual Framework, as well as the specific features and components of the ProjectFlow application.

## About ProjectFlow
ProjectFlow is an application built on top of the eQual Framework. It is designed to help manage projects, companies, employees, and more. This documentation will provide insights into the key aspects of the ProjectFlow application.

Now, let's dive into the details of the eQual Framework and ProjectFlow application.

### Model Entity relational

<img src=".\assets\img\DiagramModel.drawio.png" alt="DiagramModel.drawio" style="zoom:100%;" />


## 1.- Installation
Prerequisite

ProjectFlow requires [eQual framework](https://github.com/equalframework/equal)

Clone Project

Go to `/package` and run this command.

```
$ git clone https://github.com/alexandraYesbabylon/projectFlow.git
```

Initialization package

```
$ ./equal.run --do=init_package --package=projectFlow
```

## 2.- Application structure

The application is organized into various components, which are stored within a package folder located under the `/packages` directory. In this example, the package is named `/projectFlow`.

Each package is structured as follows:
```
projectFlow
├── classes
│   └── */*.class.php
├── data
│   └── *.json
├── init
│   └── data
│   	└── *.json
├── views
│   └── *.json
├── manifest.json
```

## 3.- Initial application data

To initialize the database, navigate to `/data/init`, where you can find all the information that will be stored in the database.
The files in this directory follow a generic filename format: `{project_name}_{class_name}.json`.

Here's an example: `projectflow_Company.json.`.

```json
[
  {
    "name": "projectFlow\\Company",
    "lang": "en",
    "data": [
      {
        "id": 1,
        "name": "Yesbabylon",
        "direction": "Bd du Souverain 24",
        "creationdate": "2023-06-01",
        "phone": "0486152419"
      },
      {
        "id": 2,
        "name": "Company Flee",
        "direction": "rue de la reine",
        "creationdate": "2013-06-27",
        "phone": "0485963215"
      }
    ]
  }
]
```



## 3.- Configuration

### Config file

eQual Framework expects an optional root configuration file in the `/config` directory.
```
{
    "DB_DBMS": "MYSQL",
    "DB_HOST": "equal_db",
    "DB_PORT": "3306",
    "DB_USER": "root",
    "DB_PASSWORD": "test",
    "DB_NAME": "equal",
    "DEFAULT_RIGHTS": "QN_R_CREATE | QN_R_READ | QN_R_DELETE | QN_R_WRITE",
    "DEBUG_MODE": "QN_MODE_PHP | QN_MODE_ORM | QN_MODE_SQL",
    "DEBUG_LEVEL": "E_ALL | E_ALL",
    "DEFAULT_PACKAGE": "core",
    "AUTH_SECRET_KEY": "my_secret_key",
    "AUTH_ACCESS_TOKEN_VALIDITY": "5d",
    "AUTH_REFRESH_TOKEN_VALIDITY": "90d",
    "AUTH_TOKEN_HTTPS": false,
    "ROOT_APP_URL": "http://equal.local"
}
```
### Initiate your package with initial data in DB

```
$ ./equal.run --do=init_package --package=projectFlow --import=true
```
You can see the tables created in  `equal` data base. The names tables are `{{package_name}}_{{entity}}`
You can see the all data, open the table `projectflow_client` with your prefect DBMS.

### Consistency with Database
Performs consistency checks between DB and class as well as syntax validation for classes (PHP), views and translation files (JSON). Typing this command.

```
$ ./equal.run --do=test_package-consistency --package=projectFlow

```
## 4.- Authentication
To create an account, use the following command:

```
$ ./equal.run --do=model_create --entity=core\\User --fields[login]='project@example.com' --fields[password]='project'
```

Please note that the user must be validated to gain access. To validate a user, use the following command. Make sure to have the user's ID:

```
$ ./equal.run --do=model_update --entity='core\User' --ids=3 --fields='{validated:true}'
```

You can also add a user as a member of a specific group using the following command:

```
$ ./equal.run --do=group_add-user --group=users --user=3
```

After completing these steps, go to http://equal.local/apps/, log in with your user credentials, and click on the "Project" application.


## 5.- Model definition

Each model is defined in a `.class.php` file located in the `/packages/projectFlow/classes` directory. All classes inherit from a common ancestor: the Model class, which is declared in the `equal\orm` namespace and defined in `/lib/equal/orm/Model.class.php`.

In this context, a class is always referred to as an entity and belongs to a specific package. Packages and their subdirectories are used as namespaces with the format `package_name`.

The standard filename format for these class files is: `{class_name}.class.php`.

### Company.class.php

The `creationdate` field is automatically set to the current date by default, so you can use `time()` to capture it.

Feel free to continue with additional information or explanations about the "Company" class and its structure.

```php
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
```
### Client.class.php
-   The `name` field is mandatory and must be unique.
-   The `isactive` field is set to `true` by default.

### Project.class.php

-   The `name` field is mandatory.
-   The `startdate` field defaults to the current date.
-   The `budget` field defaults to 1000.
-   The `status` field has options: `['draft', 'approved', 'in_progress', 'cancelled', 'finished']`.

### Employee.class.php

- The `firstname` and `lastname` fields are mandatory.
- The `name`field is derived from the concatenation of `firstname` and `lastname`. This is achieved through the `calcName` function.
-   Additional dependencies are added to the `firstname` and `lastname` fields.
- The `salary` field defaults to 1000.

### EmployeeProject.class.php
- The `hours` field is mandatory,

## 6.- Views

For each entity, default views for both `list` and `form` types should be defined. These views can be found in the `/views` folder within the `projectFlow` package.

The standard filename format for these views is: `{class_name}.{view_type}.{view_name}.json`.

Here's an example of both a list and a form view for the "Company" entity:

**Company.list.default.json**

```json
{
  "name": "Companies",
  "description": "All information companies.",
  "domain": [],
  "order": "creationdate",
  "layout": {
    "items": [
      {
        "type": "field",
        "value": "id",
        "width": "25%"
      },
      {
        "type": "field",
        "value": "name",
        "width": "25%"
      },
      {
        "type": "field",
        "value": "direction",
        "width": "25%"
      },
      {
        "type": "field",
        "value": "creationdate",
        "label": "Creation",
        "width": "25%"
      },
      {
        "type": "field",
        "value": "phone",
        "width": "25%"
      }
    ]
  }
}
```

**Company.form.default.json**: This view is designed for displaying detailed information about a company, including an additional section for listing employees associated with the company.

### Client
**Client.list.default.json**:
The results are sorted by the `name` field. Pagination is implemented with a `limit` of 10 entries per page.

**Client.form.default.json**: In this form view, a new section called "Projects" has been added. This section displays the list of projects associated with each client, providing a comprehensive view of the client's projects.

### Project
**Project.list.default.json**: The list of projects is sorted by the `startdate` field, and the view includes the total budgets for all projects.

**Project.form.default.json**: This form view now includes a dedicated "Employees" section. This section provides information about the employees working on each project.

### Employee
**Employee.list.default.json**: The result is sorted by `lastname` and `fistname`  and shows the total employees

### EmployeeProject
**EmployeeProject.list.default.json**: The result is group by  `employee`  and shows the total hours


## 7.- Menu

See the `menu.app.left.json` file in `/views `.

```json
{
  "name": "Project menu",
  "access": {
    "groups": [
      "project.default.projectFlow"
    ]
  },
  "layout": {
    "items": [
      {
        "id": "project.project.test",
        "label": "ProjectFlow",
        "description": "",
        "icon": "menu_book",
        "type": "parent",
        "children": [
          {
            "id": "project.project.company",
            "type": "entry",
            "label": "Companies",
            "description": "",
            "context": {
              "entity": "projectFlow\\Company",
              "view": "list.default"
            }
          }
        ]
      }
    ]
  }
}
```

## 8.- Manifest

See  `manifest.json`  file  in the `/projectFlow` directory.

```json
{
  "name": "Project",
  "version": "1.0",
  "author": "Yesbabylon",
  "depends_on": [
    "core"
  ],
  "apps": [
    {
      "id": "project",
      "name": "Project",
      "extends": "app",
      "description": "Applitation project flow",
      "icon": "ad_units",
      "color": "#3498DB",
      "access": {
        "groups": [
          "users"
        ]
      },
      "params": {
        "menus": {
          "left": "app.left"
        }
      }
    }
  ]
}
```

## 9.- Project Status and Workflow

### Status Flow of the Project

The project status flow is represented in the following image. It indicates the different states and transitions within the project life cycle.

<img src=".\assets\img\StatusProject.drawio.png" alt="StatusProject.drawio" style="zoom:100%;" />


### Managing Project Statuses

In the project management code, the 'getWorkflow' method within the `Project.class.php` class is responsible for handling project statuses. By reviewing this method, you can gain insight into how project statuses are managed and manipulated programmatically.

### Project Form Actions

The `Project.form.default.json` file defines the available actions related to projects. Below is an example of an action configuration:


```json
 "actions": [
    {
      "id": "action.draft",
      "label": "Draft",
      "description": "Draft project.",
      "controller": "core_model_transition",
      "visible": [
        "status",
        "=",
        "approved"
      ],
      "confirm": true,
      "params": {
        "entity": "projectFlow\\Project",
        "transition": "to_draft",
        "ids": []
      }
    },
 ]
```

This example demonstrates an action named 'Draft', which allows you to transition a project from the 'approved' status to the 'draft' status. The action is triggered via the `core_model_transition` controller, and it includes various parameters for effective project management.

## 10.-  Controller View List

The controller property specifies the controller responsible for retrieving the model collection to be displayed in the view.

Example:

```json
"controller": "projectFlow_project-collect"
```
The `project-collect` controller performs advanced project search, allowing users to filter and retrieve project collections based on multiple parameters.

In the case of the `project-collect` controller, it corresponds to a `project-collect.php` file located in the `/data` directory and a `project-collect.search.default.json` file in the `/view` directory.


Here `project-collect.php` :
```php
<?php

use equal\orm\Domain;
use projectFlow\Project;

list($params, $providers) = eQual::announce([
    'description'   => 'Advanced search for Reports: returns a collection of Reports according to extra paramaters.',
    'extends'       => 'core_model_collect',
    'params'        => [
        'entity' =>  [
            'description'   => 'name',
            'type'          => 'string',
            'default'       => 'projectFlow\Project'
        ],
        'status' => [
            'type'              => 'string',
            'selection'         => ['all','draft', 'approved','in_progress','cancelled','finished'],
            'description'       => 'Projects with a specific status.'
        ],
        'budget_min' => [
            'type'              => 'integer',
            'description'       => 'Minimal budget for the project.'
        ],
        'budget_max' => [
            'type'              => 'integer',
            'description'       => 'Maximal budget for the project.'
        ],
        'client_id' => [
            'type'              => 'many2one',
            'foreign_object'    => 'projectFlow\Client',
            'description'       => 'client of project to which the reports relate.'
        ]
    ],
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'utf-8',
        'accept-origin' => '*'
    ],
    'providers'     => [ 'context', 'orm' ]
]);
/**
 * @var \equal\php\Context $context
 * @var \equal\orm\ObjectManager $orm
 */
list($context, $orm) = [ $providers['context'], $providers['orm'] ];



//   Add conditions to the domain to consider advanced parameters
$domain = $params['domain'];

//status
if(isset($params['status']) && strlen($params['status']) > 0 && $params['status']!= 'all') {
    $domain = Domain::conditionAdd($domain, ['status', '=', $params['status']]);
}

if(isset($params['budget_min']) && $params['budget_min'] > 0) {
    $domain = Domain::conditionAdd($domain, ['budget', '>=', $params['budget_min']]);
}

if(isset($params['budget_max']) && $params['budget_max'] > 0) {
    $domain = Domain::conditionAdd($domain, ['budget', '<=', $params['budget_max']]);
}

if(isset($params['client_id']) && $params['client_id'] > 0) {
    $domain = Domain::conditionAdd($domain, ['client_id', '=', $params['client_id']]);
}

$params['domain'] = $domain;
$result = eQual::run('get', 'model_collect', $params, true);

$context->httpResponse()
        ->body($result)
        ->send();

```