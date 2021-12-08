<?php
namespace App\Http\Controllers\Admin;

use App\Models\Platform;
use Backpack\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PlatformRequest as StoreRequest;
use App\Http\Requests\PlatformRequest as UpdateRequest;
use App\Models\Trophy;
use App\Models\Game;
use DB;

class PlatformCrudController extends CrudController
{
    public function setUp()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("App\Models\Platform");
        $this->crud->setRoute("admin/platform");
        $this->crud->setEntityNameStrings('platform', 'platforms');
        $this->crud->enableAjaxTable();
        $this->crud->orderBy('name', 'asc');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        //$this->crud->setFromDb();



        // ------ CRUD FIELDS
        $this->crud->addField(['name'  => 'name', 'attributes' => ['required' => 'required']]);
        $this->crud->addField(['name'  => 'acronym']);

        $this->crud->addField(['name' => 'cover_image', 'label' => 'Cover Image', 'type' => 'image', 'upload' => true, 'crop' => true]);
        $this->crud->addField(['name'  => 'description', 'type' => 'summernote']);
        $this->crud->addField(['name'  => 'color', 'label' => 'Color', 'type' => 'color_picker', 'attributes' => ['required' => 'required']]);
        $this->crud->addField(['name' => 'text_color', 'label' => 'Text Color', 'type' => 'color_picker']);
        $this->crud->addField(['name'  => 'cover_position', 'label' => 'Cover position', 'type' => 'enum']);
        $this->crud->addField(['name'  => 'ntsc_u', 'label' => 'NTSC-U', 'type' => 'checkbox']);
        $this->crud->addField(['name'  => 'ntsc_j', 'label' => 'NTSC-J', 'type' => 'checkbox']);
        $this->crud->addField(['name'  => 'pal', 'label' => 'PAL', 'type' => 'checkbox']);

        // components columns
        $this->crud->addField(['name'  => 'box', 'label' => 'Box', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'case', 'label' => 'Case', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'manual', 'label' => 'Manual', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'disc', 'label' => 'Disc', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'case_art', 'label' => 'Case Art', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'cartridge', 'label' => 'Cartridge', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'cartridge_holder', 'label' => 'Cartridge Holder Insert', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'clamshell', 'label' => 'Cartridge Clamshell', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'box_or_case', 'label' => 'Box / Case', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'art_or_holder', 'label' => 'Case Art / Inner Cartridge Holder', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'case_sticker', 'label' => 'Case Sticker', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'styrofoam', 'label' => 'Styrofoam', 'type' => 'checkbox', 'tab' => 'Components']);
        $this->crud->addField(['name'  => 'insert', 'label' => 'Registration Card / Advertisement Inserts', 'type' => 'checkbox', 'tab' => 'Components']);


        // ------ CRUD COLUMNS
        $this->crud->addColumn(['name' => 'name', 'label' => 'Name', 'type' => 'model_function', 'function_name' => 'getCover']);
        $this->crud->addColumn(['name' => 'id', 'label' => 'Games', 'type' => 'model_function','function_name' => 'getGamesAdmin' ]);
        $this->crud->addColumn(['name'  => 'cover_position', 'label' => 'Cover position', 'type' => 'enum']);
        $this->crud->addColumn(['name'  => 'ntsc_u', 'label' => 'NTSC-U', 'type' => 'boolean']);
        $this->crud->addColumn(['name'  => 'ntsc_j', 'label' => 'NTSC-J', 'type' => 'boolean']);
        $this->crud->addColumn(['name'  => 'pal', 'label' => 'PAL', 'type' => 'boolean']);

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
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    public function store(StoreRequest $request)
    {
        $return = parent::storeCrud();

        // Create trophies for the new platform
        $platform = Platform::where('name',  $request->get('name'))->where('name', '!=', 'Multi-Platform')->first();
        if ($platform) {
            DB::beginTransaction();
            foreach (regionCodes() as $region) {
                Trophy::create(['name' => $platform->name.' Gamer', 'threshold' => 0, 'type' => 'gamer', 'platform_id' => $platform->id, 'region' => $region]);
                Trophy::create(['name' => $platform->name.' Gamer Completist', 'threshold' => 1, 'type' => 'gamer', 'platform_id' => $platform->id, 'region' => $region]);
                Trophy::create(['name' => $platform->name.' Collector', 'threshold' => 0, 'type' => 'collector', 'platform_id' => $platform->id, 'region' => $region]);
                Trophy::create(['name' => $platform->name.' Collector Completist', 'threshold' => 1, 'type' => 'collector', 'platform_id' => $platform->id, 'region' => $region]);
            }
            DB::commit();
        }

        return $return;
    }

    public function update(UpdateRequest $request)
    {
        $platform = Platform::where('id',  $request->get('id'))->where('name', '!=', 'Multi-Platform')->first();

        $return = parent::updateCrud();
        // Update Trophies to reflect platform changes
        if ($platform) {
            DB::beginTransaction();
            if ($request->get('name') != $platform->name) {
                $trophies = Trophy::where('platform_id', $platform->id)->get();
                foreach ($trophies as $trophy) {
                    $trophy->name = str_replace($platform->name, $request->get('name'), $trophy->name);
                    $trophy->save();
                }
            }
            foreach (regionCodes() as $region) {
                $count = Game::select('rating_game_id')->where('platform_id', $platform->id)->groupBy('rating_game_id')->whereNotNull($region)->get()->count();
                $count = $count > 0 ? $count : 1;
                Trophy::where('platform_id', $platform->id)->where('type', 'gamer')->orderBy('threshold', 'desc')->limit(1)->where('region', $region)->update(['threshold' => $count]);
                Trophy::where('platform_id', $platform->id)->where('type', 'collector')->orderBy('threshold', 'desc')->limit(1)->where('region', $region)->update(['threshold' => $count]);
            }
            DB::commit();
        }
        return $return;
    }
}
