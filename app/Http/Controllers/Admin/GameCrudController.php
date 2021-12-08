<?php
namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\GameRequest as StoreRequest;
use App\Http\Requests\GameRequest as UpdateRequest;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Trophy;
use App\Models\Game;
use Cache;
use DB;

class GameCrudController extends CrudController
{
    public function setUp()
    {
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
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("App\Models\Game");
        $this->crud->setRoute("admin/game");
        $this->crud->setEntityNameStrings('game', 'games');
        $this->crud->enableAjaxTable();

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
            'name' => 'region',
            'type' => 'select2',
            'label'=> 'Region'
        ],
            [
                'ntsc_u' => 'NTSC-U',
                'ntsc_j' => 'NTSC-J',
                'pal' => 'PAL',
                'pa' => "playasia"
            ],
            function($value) { // if the filter is active
                $this->crud->addClause('where', $value, '!=', null);
            }
        );

        $this->crud->addFilter([
            'name' => 'esrb',
            'type' => 'select2',
            'label'=> 'ESRB'
        ],
            [
                'unrated' => 'No Rating',
                'EC' => 'EC',
                'E' => 'E',
                'E10' => 'E10+',
                'T' => 'T',
                'M' => 'M',
                'AO' => 'AO',
                'RP' => 'RP',
            ],
            function($value) { // if the filter is active
                if ($value == 'unrated') {
                    $this->crud->addClause('where', 'esrb', '=', null);
                } else {
                    $this->crud->addClause('where', 'esrb', '=', $value);
                }
            }
        );

        $this->crud->addFilter([
            'name' => 'pegi',
            'type' => 'select2',
            'label'=> 'PEGI'
        ],
            [
                'unrated' => 'No Rating',
                '3' => '3',
                '7' => '7',
                '12' => '12',
                '16' => '16',
                '18' => '18',
            ],
            function($value) { // if the filter is active
                if ($value == 'unrated') {
                    $this->crud->addClause('where', 'pegi', '=', null);
                } else {
                    $this->crud->addClause('where', 'pegi', '=', $value);
                }
            }
        );

        $this->crud->orderBy('name', 'asc');
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        //$this->crud->setFromDb();

        $this->crud->addField(['label' => 'Name', 'name'  => 'true_name' ,'attributes' => ['required' => 'required']]);

        $this->crud->addField(['name'  => 'description', 'type' => 'textarea']);

        $this->crud->addField(['label' => 'Number of Players', 'name'  => 'player_count', 'type' => 'number']);
        $this->crud->addField(['label' => 'Local Multiplayer', 'name'  => 'multiplayer_local', 'type' => 'number']);
        $this->crud->addField(['label' => 'LAN Multiplayer', 'name'  => 'multiplayer_lan', 'type' => 'number']);
        $this->crud->addField(['label' => 'Online Multiplayer', 'name'  => 'multiplayer_online', 'type' => 'number']);
        $this->crud->addField(['label' => 'Online Multiplayer No Limit', 'name'  => 'multiplayer_online_no_limit', 'type' => 'checkbox']);
        $this->crud->addField(['label' => 'ESRB Rating', 'name'  => 'esrb', 'type' => 'select2_from_array', 'options' => [
            null => 'No Rating',
            'EC' => 'EC',
            'E' => 'E',
            'E10' => 'E10+',
            'T' => 'T',
            'M' => 'M',
            'AO' => 'AO',
            'RP' => 'RP',
        ]]);
        $this->crud->addField(['label' => 'PEGI Rating', 'name'  => 'pegi', 'type' => 'select2_from_array', 'options' => [
            null => 'No Rating',
            '3' => '3',
            '7' => '7',
            '12' => '12',
            '16' => '16',
            '18' => '18',
        ]]);

        // when updating a product allow components editable that are not null
        if (request()->segment(3) != 'create' && !is_null(request()->segment(3))) {
            $components = Product::select(config('components.all'))->where('id', '=',request()->segment(3))->first();
            if (isset($components)) {
                foreach ($components->toArray() as $component => $value) {
                    if (!is_null($value)) {
                        $this->crud->addField(['name'  => $component, 'label' => trans("games.components.$component"),'type' => 'checkbox']);
                    }
                }
            }
        }

        $this->crud->addField([
            'name' => 'components',
            'label' => 'Components',
            'type' => 'table_advanced',
            'entity_singular' => 'Component',
            'columns' => [
                'name' => ['type' => 'text', 'label' => 'Component Name'],
                'complete' => ['type' => 'boolean', 'label' => 'Counts Towards Completion']
            ],
        ]);

        $this->crud->addField(['name'  => 'cover_generator', 'default' => true, 'label' => 'Enable cover generator', 'type' => 'toggle', 'hint' => 'Add platform bar with logo on top of game cover.']);

        $this->crud->addField([
            'name' => 'platform_id',
            'label' => 'Platform',
            'type' => 'select2_from_array',
            'options' => $platformSelect,
            'allows_null' => false,
            'attributes' => ['required' => 'required']
        ]);
        $this->crud->addField(['label' => 'Also Available On', 'placeholder' => 'Search Games', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'altGroupRegionless', 'attribute' => 'name_and_platform', 'model' => 'App\Models\Product', 'pivot' => false, 'data_source' => url("api/game/search")], 'update');
        $this->crud->addField(['label' => 'Ratings Group', 'placeholder' => 'Search Games', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'ratingsGroup', 'attribute' => 'name_and_platform', 'model' => 'App\Models\Product', 'pivot' => false, 'data_source' => url("api/game/search")], 'update');

        $this->crud->addColumn(['name' => 'game_name', 'type' => 'model_function','function_name' => 'getNameAdmin',
        'searchLogic' => function ($query, $column, $searchTerm) {
              $query->orWhere('name', 'like', "%$searchTerm%")
                    ->orWhere('catalog_number', 'like', "%$searchTerm%")
                    ->orWhere('upc', 'like', "%$searchTerm%");
          }
        ]);
        $this->crud->addColumn(['name' => 'platform_id','type' => 'model_function','function_name' => 'getConsoleAdmin']);
        $this->crud->addColumn(['name' => 'esrb', 'label' => 'ESRB', 'type' => 'closure','function' => function ($entry) {
            if ($entry->esrb) {
                return '<img style="max-height:35px" src="'.env("S3_BUCKET_URL").'ratings/'.$entry->esrb.'.png'.'">';
            }
        }]);
        $this->crud->addColumn(['name' => 'pegi', 'label' => 'PEGI', 'type' => 'closure','function' => function ($entry) {
            if ($entry->pegi) {
                return '<img style="max-height:35px" src="'.env("S3_BUCKET_URL").'ratings/'.$entry->pegi.'.png'.'">';
            }
        }]);

        $this->crud->addColumn(['name' => 'ntsc_u', 'type' => 'date', 'label' => 'NTSC-U']);
        $this->crud->addColumn(['name' => 'ntsc_j', 'type' => 'date', 'label' => 'NTSC-J']);
        $this->crud->addColumn(['name' => 'pal', 'type' => 'date', 'label' => 'PAL']);
        $this->crud->addColumn(['name' => 'pa', 'type' => 'date', 'label' => 'playasia']);

        $this->crud->addColumn(['name' => 'active_listings', 'label' => 'Active Listings', 'type' => 'model_function','function_name' => 'getListingsAdmin']);


        $this->crud->addButtonFromView('top', 'add', 'create_game', 'beginning');

        $this->crud->addField(['label' => 'Genres', 'type' => 'select2_multiple', 'name' => 'genres', 'attribute' => 'name', 'model' => 'App\Models\Genre', 'pivot' => true]);
        $this->crud->addField(['label' => 'Franchises', 'placeholder' => 'Select Franchises', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'franchises', 'attribute' => 'name', 'model' => 'App\Models\Franchise', 'pivot' => true, 'data_source' => url("api/franchises/search")]);
        $this->crud->addField(['label' => 'Developers', 'placeholder' => 'Select Developers', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'developers', 'attribute' => 'name', 'model' => 'App\Models\Developer', 'pivot' => true, 'data_source' => url("api/developers/search")]);
        $this->crud->addField(['label' => 'Extra Images', 'type' => 'browse_multiple', 'name' => 'extra_images', 'upload' => true, 'crop' => true, 'mime_types' => ['image']]);
        $this->crud->addColumn(['label' => 'Extra Images', 'name' => 'extra_images']);


        // US Fields
        $this->crud->addField(['name' => 'cover_us', 'label' => 'US Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'US']);
        $this->crud->addField(['name' => 'cover_us_url', 'label' => 'US Cover Image URL', 'type' => 'url_image', 'tab' => 'US']);
        $this->crud->addField(['name' => 'name_us', 'label' => 'US Region Name', 'tab' => 'US', 'hint' => '*Only needs to be filled if region name of the game is different']);
        $this->crud->addField(['name' => 'upc_us', 'label' => 'US UPC', 'tab' => 'US']);
        $this->crud->addField(['name' => 'catalog_number_us', 'label' => 'US Catalog Number', 'tab' => 'US']);
        $this->crud->addField(['name' => 'ntsc_u', 'type' => 'date_clear', 'label' => 'US Release Date', 'tab' => 'US']);
        $this->crud->addField(['label' => 'US Publishers', 'placeholder' => 'Select Publishers', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'publishersUs', 'attribute' => 'name', 'model' => 'App\Models\Publisher', 'pivot' => true, 'data_source' => url("api/publishers/search"), 'tab' => 'US']);


        // JP Fields
        $this->crud->addField(['name' => 'cover_jp', 'label' => 'JP Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'JP']);
        $this->crud->addField(['name' => 'cover_jp_url', 'label' => 'JP Cover Image URL', 'type' => 'url_image', 'tab' => 'JP']);
        $this->crud->addField(['name' => 'name_jp', 'label' => 'JP Region Name', 'tab' => 'JP', 'hint' => '*Only needs to be filled if region name of the game is different']);
        $this->crud->addField(['name' => 'upc_jp', 'label' => 'JP UPC', 'tab' => 'JP']);
        $this->crud->addField(['name' => 'catalog_number_jp', 'label' => 'JP Catalog Number', 'tab' => 'JP']);
        $this->crud->addField(['name' => 'ntsc_j', 'type' => 'date_clear', 'label' => 'JP Release Date', 'tab' => 'JP']);
        $this->crud->addField(['label' => 'JP Publishers', 'placeholder' => 'Select Publishers', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'publishersJp', 'attribute' => 'name', 'model' => 'App\Models\Publisher', 'pivot' => true, 'data_source' => url("api/publishers/search"), 'tab' => 'JP']);

        // EU Fields
        $this->crud->addField(['name' => 'cover_eu', 'label' => 'EU Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'EU']);
        $this->crud->addField(['name' => 'cover_eu_url', 'label' => 'EU Cover Image URL', 'type' => 'url_image', 'tab' => 'EU']);
        $this->crud->addField(['name' => 'name_eu', 'label' => 'EU Region Name', 'tab' => 'EU', 'hint' => '*Only needs to be filled if region name of the game is different']);
        $this->crud->addField(['name' => 'upc_eu', 'label' => 'EU UPC', 'tab' => 'EU']);
        $this->crud->addField(['name' => 'catalog_number_eu', 'label' => 'EU Catalog Number', 'tab' => 'EU']);
        $this->crud->addField(['name' => 'pal', 'type' => 'date_clear', 'label' => 'EU Release Date', 'tab' => 'EU']);
        $this->crud->addField(['label' => 'EU Publishers', 'placeholder' => 'Select Publishers', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'publishersEu', 'attribute' => 'name', 'model' => 'App\Models\Publisher', 'pivot' => true, 'data_source' => url("api/publishers/search"), 'tab' => 'EU']);

        // PA Fields
        $this->crud->addField(['name' => 'cover_pa', 'label' => 'playasia Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true, 'tab' => 'playasia']);
        $this->crud->addField(['name' => 'cover_pa_url', 'label' => 'playasia Cover Image URL', 'type' => 'url_image', 'tab' => 'playasia']);
        $this->crud->addField(['name' => 'name_pa', 'label' => 'playasia Region Name', 'tab' => 'playasia', 'hint' => '*Only needs to be filled if region name of the game is different']);
        $this->crud->addField(['name' => 'upc_pa', 'label' => 'playasia UPC', 'tab' => 'playasia']);
        $this->crud->addField(['name' => 'catalog_number_pa', 'label' => 'playasia Catalog Number', 'tab' => 'playasia']);
        $this->crud->addField(['name' => 'pa', 'type' => 'date_clear', 'label' => 'playasia Release Date', 'tab' => 'playasia']);
        $this->crud->addField(['label' => 'playasia Publishers', 'placeholder' => 'Select Publishers', 'minimum_input_length' => 2, 'type' => 'select2_from_ajax_multiple', 'name' => 'publishersPa', 'attribute' => 'name', 'model' => 'App\Models\Publisher', 'pivot' => true, 'data_source' => url("api/publishers/search"), 'tab' => 'playasia']);


        // ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS
        // $this->crud->addColumn(); // add a single column, at the end of the stack
        // $this->crud->addColumns(); // add multiple columns, at the end of the stack
        // $this->crud->removeColumn('column_name'); // remove a column from the stack
        // $this->crud->removeColumns(['column_name_1', 'column_name_2']); // remove an array of columns from the stack
        // $this->crud->setColumnDetails('column_name', ['attribute' => 'value']); // adjusts the properties of the passed in column (by name)
        // $this->crud->setColumnsDetails(['column_1', 'column_2'], ['attribute' => 'value']);

        // ------ CRUD BUTTONS
        // possible positions: 'beginning' and 'end'; defaults to 'beginning' for the 'line' stack, 'end' for the others;
        // $this->crud->addButton($stack, $name, $type, $content, $position); // add a button; possible types are: view, model_function
        // $this->crud->addButtonFromModelFunction($stack, $name, $model_function_name, $position); // add a button whose HTML is returned by a method in the CRUD model
        // $this->crud->addButtonFromView($stack, $name, $view, $position); // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
        // $this->crud->removeButton($name);
        // $this->crud->removeButtonFromStack($name, $stack);

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        // $this->crud->enableDetailsRow();
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('details_row');
        // NOTE: you also need to do overwrite the showDetailsRow($id) method in your EntityCrudController to show whatever you'd like in the details row OR overwrite the views/backpack/crud/details_row.blade.php

        // ------ REVISIONS
        // You also need to use \Venturecraft\Revisionable\RevisionableTrait;
        // Please check out: https://laravel-backpack.readme.io/docs/crud#revisions
        // $this->crud->allowAccess('revisions');

        // ------ AJAX TABLE VIEW
        // Please note the drawbacks of this though:
        // - 1-n and n-n columns are not searchable
        // - date and datetime columns won't be sortable anymore
        // $this->crud->enableAjaxTable();


        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
        // $this->crud->enableExportButtons();

    // ------ ADVANCED QUERIES
    }

    public function store(StoreRequest $request)
    {
        $request->request->set('name', $request->get('true_name'));

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

        $response = parent::storeCrud($request);
        if ($this->data['entry']['id']) {
            // Find the product and sync publisher regions after it has saved
            $this->syncPublishers($this->data['entry']['id'], $request);
        }

        // update trophies that game's platform pertains to
        DB::beginTransaction();
        foreach (regionCodes() as $region) {
            $platform_id = $request->get('platform_id');
            if ($platform_id) {
                $count = Game::select('rating_game_id')->where('platform_id', $platform_id)->groupBy('rating_game_id')->whereNotNull($region)->get()->count();
                Trophy::where('platform_id', $platform_id)->where('region', $region)->where('threshold', '!=', 0)->orderBy('threshold', 'desc')->limit(1)->update(['threshold' =>  $count]);
            }
        }
        DB::commit();

        return $response;
    }

    public function update(UpdateRequest $request)
    {
        $request->request->set('name', $request->get('true_name'));
        // This workaround can be removed when Laravel >= 5.8 & Backpack >= 3.6
        if (!$request->has('extra_images')) {
            $request->request->set('extra_images', [0]);
        }
        // see: https://github.com/Laravel-Backpack/CRUD/issues/2397#issuecomment-577786222

        // A has many relationship on the same table has problems saving. Updating the relationship through a model function and unsetting the key on the request.
        Product::saveAltGroup($request->get('id'), $request->get('altGroupRegionless'));
        Product::saveRatingsGroup($request->get('id'), $request->get('ratingsGroup'));
        unset($request['altGroupRegionless']);
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

        $response = parent::updateCrud($request);

        // Find the product and sync publisher regions after it has saved
        $this->syncPublishers($request->get('id'), $request);


        // update trophies that game's platform pertains to
        DB::beginTransaction();
        foreach (regionCodes() as $region) {
            $platform_id = $request->get('platform_id');
            if ($platform_id) {
                $count = Game::select('rating_game_id')->where('platform_id', $platform_id)->groupBy('rating_game_id')->whereNotNull($region)->get()->count();
                $count = $count > 0 ? $count : 1;
                Trophy::where('platform_id', $platform_id)->where('type', 'gamer')->where('region', $region)->where('threshold', '!=', 0)->orderBy('threshold', 'desc')->limit(1)->update(['threshold' =>  $count]);
                Trophy::where('platform_id', $platform_id)->where('type', 'collector')->where('region', $region)->where('threshold', '!=', 0)->orderBy('threshold', 'desc')->limit(1)->update(['threshold' =>  $count]);
            }
        }
        DB::commit();

        return $response;
    }

    private function syncPublishers($id, $request) {
        $product = Product::where('id', $id)->first();
        $publishers = [];
        foreach ($request->get('publishersUs') ?: [] as $publisher) {
            $publishers[] = [':game_id' => $product->id, ':publisher_id' => $publisher, ':region' => 'us'];
        }
        foreach ($request->get('publishersJp') ?: [] as $publisher) {
            $publishers[] = [':game_id' => $product->id, ':publisher_id' => $publisher, ':region' => 'jp'];
        }
        foreach ($request->get('publishersEu') ?: [] as $publisher) {
            $publishers[] = [':game_id' => $product->id, ':publisher_id' => $publisher, ':region' => 'eu'];
        }
        foreach ($request->get('publishersPa') ?: [] as $publisher) {
            $publishers[] = [':game_id' => $product->id, ':publisher_id' => $publisher, ':region' => 'pa'];
        }
        // insert into pivot table, if there is already a duplicate do nothing
        foreach ($publishers as $publisher) {
            DB::statement('
                INSERT INTO game_publisher(game_id, publisher_id, region) 
                VALUES (:game_id, :publisher_id, :region)
                ON DUPLICATE KEY UPDATE
                id=id
            ', $publisher);
        }
    }
}
