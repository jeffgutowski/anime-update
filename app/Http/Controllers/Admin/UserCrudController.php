<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
// VALIDATION
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Http\Request;
use Config;

class UserCrudController extends CrudController
{
    public function setup()
    {

        $this->crud->setModel("App\Models\User");
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(config('backpack.base.route_prefix').'/user');
        $this->crud->enableAjaxTable();
        $this->crud->removeButton('delete');

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'unconfirmed',
            'label'=> 'Unconfirmed'
        ], false, function ($values) { // if the filter is active
            $this->crud->addClause('where', 'confirmed', '0');
        });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'banned',
            'label'=> 'Banned'
        ], false, function ($values) { // if the filter is active
            $this->crud->addClause('where', 'status', '0');
        });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'trashed',
            'label'=> 'Trashed'
        ], false, function ($values) { // if the filter is active
            $this->crud->query = $this->crud->query->onlyTrashed();
        });


        $this->crud->setColumns([
            [
                'name'  => 'username',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'model_function',
                'function_name' => 'getUserAdmin',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('name', 'like', '%'.$searchTerm.'%');
                }
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'confirmed',
                'label' => 'Confirmed',
                'type'  => 'check',
            ],
            [
                'name'  => 'status',
                'label' => 'Active',
                'type'  => 'check',
            ],
            [ // n-n relationship (with pivot table)
               'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
               'type'      => 'select_multiple',
               'name'      => 'roles', // the method that defines the relationship in your Model
               'entity'    => 'roles', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user
               'model'     => "Backpack\PermissionManager\app\Models\Roles", // foreign key model
            ],
            [ // n-n relationship (with pivot table)
               'label'     => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
               'type'      => 'select_multiple',
               'name'      => 'permissions', // the method that defines the relationship in your Model
               'entity'    => 'permissions', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user
               'model'     => "Backpack\PermissionManager\app\Models\Permission", // foreign key model
            ],
            [
                'name'  => 'balance',
                'label' => 'Balance in ' . config('settings.currency'),
                'type'  => 'text',
            ],
        ]);


        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                'name'  => 'confirmed',
                'label' => 'eMail Confirmed',
                'type'  => 'checkbox',
            ],
            [
                'name'  => 'status',
                'label' => 'Active',
                'type'  => 'checkbox',
            ],
            [
            // two interconnected entities
            'label'             => trans('backpack::permissionmanager.user_role_permission'),
            'field_unique_name' => 'user_role_permission',
            'type'              => 'checklist_dependency',
            'name'              => 'roles_and_permissions', // the methods that defines the relationship in your Model
            'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'name', // foreign key attribute that is shown to user
                        'model'            => "Backpack\PermissionManager\app\Models\Role", // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label'          => ucfirst(trans('backpack::permissionmanager.permission_singular')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => "Backpack\PermissionManager\app\Models\Permission", // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @param StoreRequest $request - type injection used for validation using Requests
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->crud->hasAccessOrFail('create');

        // insert item in the db
        if ($request->input('password')) {
            $item = $this->crud->create(\Request::except(['redirect_after_save']));

            // now bcrypt the password
            $item->password = bcrypt($request->input('password'));
            $item->save();
        } else {
            $item = $this->crud->create(\Request::except(['redirect_after_save', 'password']));
        }

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // redirect the user where he chose to be redirected
        switch (\Request::input('redirect_after_save')) {
            case 'current_item_edit':
                return \Redirect::to($this->crud->route.'/'.$item->id.'/edit');

            default:
                return \Redirect::to(\Request::input('redirect_after_save'));
        }
    }

    public function update(UpdateRequest $request)
    {
        //encrypt password and set it to request
        $this->crud->hasAccessOrFail('update');

        $dataToUpdate = \Request::except(['redirect_after_save', 'password']);

        //encrypt password
        if ($request->input('password')) {
            $dataToUpdate['password'] = bcrypt($request->input('password'));
        }

        // update the row in the db
        $this->crud->update(\Request::get('id'), $dataToUpdate);

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        return \Redirect::to($this->crud->route);
    }


}
