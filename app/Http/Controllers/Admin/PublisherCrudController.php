<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PublisherRequest as StoreRequest;
use App\Http\Requests\PublisherRequest as UpdateRequest;
use Illuminate\Validation\Rule;

/**
 * Class PublisherCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PublisherCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Publisher');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/publisher');
        $this->crud->setEntityNameStrings('publisher', 'publishers');
        $this->crud->orderBy('name', 'asc');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addColumn(['name' => 'name']);
        $this->crud->addColumn(['name' => 'created_at', 'type' => 'date']);
        $this->crud->addField(['label' => 'Name', 'name'  => 'name']);

        // add asterisk for fields that are required in PublisherRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $request->validate([
            'name' => ['required', 'unique:publishers,name,NULL,id,deleted_at,NULL'],
        ]);
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $request->validate([
            'name' => ['required', Rule::unique('publishers')->ignore($request->id)->whereNull('deleted_at')],
        ]);
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
