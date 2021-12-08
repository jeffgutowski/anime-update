<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TrophiesRequest as StoreRequest;
use App\Http\Requests\TrophiesRequest as UpdateRequest;
use Cache;
use App\Models\Platform;

/**
 * Class TrophiesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TrophiesCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Trophy');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/trophies');
        $this->crud->setEntityNameStrings('trophies', 'trophies');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        /*
         * Filters
         */
        $this->crud->addFilter([
            'name' => 'region',
            'type' => 'select2',
            'label'=> 'Region'
        ],
            [
                'regionless' => "Regionless",
                'ntsc_u' => 'NTSC-U',
                'ntsc_j' => 'NTSC-J',
                'pal' => 'PAL'
            ],
            function($value) { // if the filter is active
                if ($value == 'regionless') {
                    $this->crud->addClause('where', 'region', '=', null);
                } else {
                    $this->crud->addClause('where', 'region', '=', $value);
                }
            }
        );
        $platformSelect = Cache::remember('platformSelect', now()->addDays(1), function () {
            $platforms = Platform::select('id', 'name')->orderBy('name')->get();
            $select = ['' => ''];
            foreach ($platforms as $platform) {
                $select[$platform->id] = $platform->name;
            }
            return $select;
        });
        $this->crud->addFilter([
            'name' => 'platform',
            'type' => 'select2',
            'label'=> 'Platform'
        ],
            $platformSelect,
            function($value) { // if the filter is active
                $this->crud->addClause('where', 'platform_id', $value);
            }
        );

        $this->crud->addFilter([
            'name' => 'type',
            'type' => 'select2',
            'label'=> 'Type'
        ],
            [
                'collector' => 'Collector',
                'gamer' => 'Gamer',
            ],
            function($value) { // if the filter is active
                $this->crud->addClause('where', 'type', $value);
            }
        );

        // TODO: remove setFromDb() and manually define Fields and Columns
//        $this->crud->setFromDb();

        /*
         * Columns
         */
        $this->crud->addColumn(['name' => 'name']);
        $this->crud->addColumn(['name' => 'threshold']);
        $this->crud->addColumn(['name' => 'tier']);
        $this->crud->addColumn(['name' => 'type']);
        $this->crud->addColumn(['name' => 'region']);



//        $this->crud->addColumn(['name' => 'platform_id', 'label' => 'Platform', 'type' => 'select', 'entity' => 'platform', 'attribute' => 'name', 'model' => 'App\Models\Platform']);

        /*
         * Fields
         */
        $this->crud->addField(['name' => 'name']);
        $this->crud->addField(['name' => 'threshold', 'type' => 'number']);
        $this->crud->addField(['name' => 'tier', 'type' => 'number']);
        if (request()->segment(3) == 'create') {
            $this->crud->addField(['name' => 'type', 'type' => 'select2_from_array', 'options' => ['collector' => 'Collector', 'gamer' => 'Gamer']]);
            $this->crud->addField(['name' => 'region', 'type' => 'select2_from_array', 'options' => ['ntsc_u' => 'NTSC-U', 'pal' => 'PAL', 'ntsc_j' => 'NTSC-J', 'pa' => 'playasia']]);
            $this->crud->addField([
                'name' => 'platform_id',
                'label' => 'Platform',
                'type' => 'select2_from_array',
                'options' => $platformSelect,
                'allows_null' => true,
            ]);
        } else {
            $this->crud->addField(['name' => 'type', 'attributes' => ['disabled' => 'disabled']]);
            $this->crud->addField(['name' => 'region', 'attributes' => ['disabled' => 'disabled']]);
            $this->crud->addField([
                'name' => 'platform_id',
                'label' => 'Platform',
                'type' => 'select2_from_array',
                'options' => $platformSelect,
                'allows_null' => true,
                'attributes' => ['disabled' => 'disabled']
            ]);
        }


        // add asterisk for fields that are required in TrophiesRequest
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
