<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AccessoriesHardwareTypeRequest as StoreRequest;
use App\Http\Requests\AccessoriesHardwareTypeRequest as UpdateRequest;

/**
 * Class AccessoriesHardwareTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AccessoriesHardwareTypeCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\AccessoriesHardwareType');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/accessorieshardwaretype');
        $this->crud->setEntityNameStrings('Accessories & Hardware Type', 'Accessories & Hardware Types');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addField('name');
        $this->crud->addColumn('name');
        $this->crud->addColumn('slug');

        // add asterisk for fields that are required in AccessoriesHardwareTypeRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $request->merge(['slug' => slugify($request->name)]);
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
