<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Cache;
use App\Models\Platform;
use App\Models\Product;
use App\Models\AccessoriesHardwareType;
use App\Models\AccessoriesHardwareCompanies;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AccessoriesHardwareRequest as StoreRequest;
use App\Http\Requests\AccessoriesHardwareRequest as UpdateRequest;

/**
 * Class AccessoriesHardwareCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AccessoriesHardwareCrudController extends CrudController
{
    public function setup()
    {
        $types = AccessoriesHardwareType::orderBy('name')->get();
        $typeSelect = ['' => ''];
        foreach ($types as $type) {
            $typeSelect[$type->slug] = $type->name;
        }

        $companies = AccessoriesHardwareCompanies::orderBy('name')->get();
        $companySelect = ['' => ''];
        foreach ($companies as $company) {
            $companySelect[$company->name] = $company->name;
        }

        $platformSelect = Cache::remember('platformSelect', now()->addDays(1), function () {
            $platforms = Platform::select('id', 'name')->orderBy('name')->get();
            $select = ['' => ''];
            foreach ($platforms as $platform) {
                $select[$platform->id] = $platform->name;
            }
            return $select;
        });
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\AccessoriesHardware');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/accessorieshardware');
        $this->crud->setEntityNameStrings('Accessory/Hardware', 'accessories & hardware');
        $this->crud->addColumn(['name' => 'Name', 'type' => 'model_function','function_name' => 'getNameAdmin',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('name', 'like', "%$searchTerm%")
                    ->orWhere('catalog_number', 'like', "%$searchTerm%")
                    ->orWhere('upc', 'like', "%$searchTerm%");
            }
        ]);
        $this->crud->addColumn(['name' => 'platform_id', 'type' => 'model_function', 'function_name' => 'getPlatformAdmin']);
        $this->crud->addColumn(['name' => 'type']);
        $this->crud->addColumn(['name' => 'ntsc_u', 'type' => 'date', 'label' => 'NTSC-U']);
        $this->crud->addColumn(['name' => 'ntsc_j', 'type' => 'date', 'label' => 'NTSC-J']);
        $this->crud->addColumn(['name' => 'pal', 'type' => 'date', 'label' => 'PAL']);

        $this->crud->addFilter([
                'name' => 'type',
                'type' => 'dropdown',
                'label'=> 'Type'
            ],
            $typeSelect,
            function($value) { // if the filter is active
                $this->crud->addClause('where', 'type', $value);
            }
        );
        $this->crud->addFilter([
                'name' => 'platform',
                'type' => 'select2',
                'label'=> 'Platform'
            ],
            $platformSelect,
            function($value) { // if the filter is active
                $this->crud->addClause('where', 'platform_id', $value);
                $this->crud->addClause('orWhere', 'other_platforms', 'like', '%"'.$value.'"%');
            }
        );
        $this->crud->addFilter([
            'name' => 'region',
            'type' => 'select2',
            'label'=> 'Region'
        ],
            [
                'ntsc_u' => 'NTSC-U',
                'ntsc_j' => 'NTSC-J',
                'pal' => 'PAL'
            ],
            function($value) { // if the filter is active
                $this->crud->addClause('where', $value, '!=', null);
            }
        );
        $this->crud->orderBy('name', 'asc');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addField(['name'  => 'name' ,'attributes' => ['required' => 'required']]);

        $this->crud->addField([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'select2_from_array',
            'options' => $typeSelect,
            'allows_null' => false,
            'attributes' => ['required' => 'required']
        ]);

        $this->crud->addField([
            'name' => 'platform_id',
            'label' => 'Primary Platform',
            'type' => 'select2_from_array',
            'options' => $platformSelect,
            'allows_null' => false,
            'attributes' => ['required' => 'required']
        ]);
        $this->crud->addField(['label' => 'Ratings Group', 'placeholder' => 'Search Games', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'ratingsGroup', 'attribute' => 'name_and_platform', 'model' => 'App\Models\Product', 'pivot' => false, 'data_source' => url("api/accessories/search")], 'update');

        $this->crud->addField([
            'name'            => 'other_platforms',
            'label'           => 'Other Platforms',
            'type'            => 'select2_from_array',
            'options'         => $platformSelect,
            'allows_null'     => true,
            'allows_multiple' => true,
        ]);

        $this->crud->addField([
            'name' => 'company',
            'label' => 'Company',
            'type' => 'select2_from_array',
            'options' => $companySelect,
            'allows_null' => true,
        ]);

        $this->crud->addField(['name'  => 'model_number' , 'label' => 'Model Number']);

        $this->crud->addField(['name'  => 'description', 'type' => 'textarea']);
        $this->crud->addField([
            'name' => 'components',
            'label' => 'Components',
            'type' => 'table',
            'entity_singular' => 'Component',
            'columns' => [
                'name' => 'Component Name',
            ],
        ]);
        $this->crud->addField(['name' => 'cover_us', 'label' => 'US Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'US']);
        $this->crud->addField(['name' => 'cover_us_url', 'label' => 'US Cover Image URL', 'type' => 'url_image', 'tab' => 'US']);
        $this->crud->addField(['name' => 'upc_us', 'label' => 'US UPC', 'tab' => 'US']);

        $this->crud->addField(['name' => 'cover_jp', 'label' => 'Japan Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'JP']);
        $this->crud->addField(['name' => 'cover_jp_url', 'label' => 'Japan Cover Image URL', 'type' => 'url_image', 'tab' => 'JP']);
        $this->crud->addField(['name' => 'upc_jp', 'label' => 'JP UPC', 'tab' => 'JP']);

        $this->crud->addField(['name' => 'cover_eu', 'label' => 'Europe Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'EU']);
        $this->crud->addField(['name' => 'cover_eu_url', 'label' => 'Europe Cover Image URL', 'type' => 'url_image', 'tab' => 'EU']);
        $this->crud->addField(['name' => 'upc_eu', 'label' => 'EU UPC', 'tab' => 'EU']);

        $this->crud->addField(['name' => 'ntsc_u', 'type' => 'date_clear', 'label' => 'US Release Date', 'tab' => 'US']);
        $this->crud->addField(['name' => 'ntsc_j', 'type' => 'date_clear', 'label' => 'JP Release Date', 'tab' => 'JP']);
        $this->crud->addField(['name' => 'pal', 'type' => 'date_clear', 'label' => 'EU Release Date', 'tab' => 'EU']);
        $this->crud->addField(['label' => 'Extra Images', 'type' => 'browse_multiple', 'name' => 'extra_images', 'upload' => true, 'crop' => true, 'mime_types' => ['image']]);
        $this->crud->addColumn(['label' => 'Extra Images', 'name' => 'extra_images']);

        // add asterisk for fields that are required in AccessoriesHardwareRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // find the earliest release date and set it as release_date
        $release_dates = $request->only(regionCodes());
        $main_release_date = null;
        foreach ($release_dates as $release_date) {
            if (empty($main_release_date) && !empty($release_date)) {
                $main_release_date = $release_date;
            }
            if (!empty($release_date) && $main_release_date > $release_date) {
                $main_release_date = $release_date;
            }
        }
        $request->request->set('release_date', $main_release_date);

        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // This workaround can be removed when Laravel >= 5.8 & Backpack >= 3.6
        if (!$request->has('extra_images')) {
            $request->request->set('extra_images', [0]);
        }
        if (!$request->has('other_platforms')) {
            $request->request->set('other_platforms', null);
        }
        // see: https://github.com/Laravel-Backpack/CRUD/issues/2397#issuecomment-577786222

        // A has many relationship on the same table has problems saving. Updating the relationship through a model function and unsetting the key on the request.
        Product::saveRatingsGroup($request->get('id'), $request->get('ratingsGroup'));
        unset($request['ratingsGroup']);

        // find the earliest release date and set it as release_date
        $release_dates = $request->only(regionCodes());
        $main_release_date = null;
        foreach ($release_dates as $release_date) {
            if (empty($main_release_date) && !empty($release_date)) {
                $main_release_date = $release_date;
            }
            if (!empty($release_date) && $main_release_date > $release_date) {
                $main_release_date = $release_date;
            }
        }
        $request->request->set('release_date', $main_release_date);

        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
