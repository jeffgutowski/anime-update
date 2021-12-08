<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SuggestionRequest as StoreRequest;
use App\Http\Requests\SuggestionRequest as UpdateRequest;

/**
 * Class SuggestionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SuggestionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Suggestion');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/suggestion');
        $this->crud->setEntityNameStrings('suggestion', 'suggestions');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addFilter([
            'type'  => 'simple',
            'name'  => 'unreviewed',
            'label' => 'All Suggestions'
        ],
            false,
            function() { // if the filter is active

            },
            function () {
                $this->crud->addClause('where', 'reviewed_at', '=', null);
            }
        );
        $this->crud->removeAllButtons();
        $this->crud->allowAccess('show');
        $this->crud->addButtonFromView('line', 'preview', 'show', 'beginning');
        $this->crud->addColumn(['name' => 'review', 'type' => 'model_function', 'function_name' => 'getReviewButton']);
        $this->crud->addColumn(['name' => 'name']);
        $this->crud->addColumn(['name' => 'type']);
        $this->crud->addColumn(['name' => 'platform.name', 'label' => 'Platform']);
        $this->crud->addColumn(['name' => 'user.name', 'label' => 'Suggester']);
        $this->crud->addColumn(['name' => 'approved', 'label' => 'Approved', 'type' => 'boolean']);
        $this->crud->addColumn(['name' => 'reviewed_at', 'label' => 'Reviewed Date', 'type' => 'date']);
        $this->crud->addColumn(['name' => 'created_at', 'label' => 'Suggestion Date', 'type' => 'date']);

            // add asterisk for fields that are required in SuggestionRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
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
