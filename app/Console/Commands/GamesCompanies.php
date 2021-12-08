<?php

namespace App\Console\Commands;

use App\Services\IgdbService;
use File;
use Illuminate\Console\Command;
use Storage;

class GamesCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Companies into json file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Companies Start');
        ini_set('memory_limit', '-1');
        $offset = 0;
        $all = false;
        $old_created_at = null;
        if (!File::exists(storage_path('app/public/json/companies/companies.json'))) {
            File::put(storage_path('app/public/json/companies/companies.json'), null);
        }
        while (!$all) {
            $companies = json_decode(File::get(storage_path('app/public/json/companies/companies.json')), true);
            $companies = !is_null($companies) ? $companies : [];
            if (isset($companies)) {
                $created_at = end($companies)['created_at'];
                if ($old_created_at === $created_at && !is_null($created_at)) {
                    app('log')->debug('duplicate created at ', [$old_created_at]);
                    $this->info('duplicate created at '.$old_created_at);
                    $created_at += 1;
                }
            }
            while ($offset <= 150) {
                if (empty($companies)) {
                    $import = IgdbService::searchCompanies(null, ['id', 'created_at', 'name', 'logo', 'url', 'published.id', 'published.name', 'developed.id', 'developed.name'], 50, $offset, 'created_at');
                } else {
                    $import = IgdbService::searchCompanies(null, ['id', 'created_at', 'name', 'logo', 'url', 'published.id', 'published.name', 'developed.id', 'developed.name'], 50, null, 'created_at', ['created_at' => ['gte' => $created_at]]);
                }
                $companies = array_merge($companies, (array) $import);
                $offset += 50;
                if (count($import) < 50) {
                    $this->info('Final Import Page Count: '.count($import));
                    $all = true;
                    break;
                }
            }
            $offset = 0;
            $old_created_at = $created_at;
            Storage::disk('public')->put('json/companies/companies.json', json_encode($companies, JSON_PRETTY_PRINT));
            $this->info('Created At: '.$created_at);
        }
        $this->info('Companies Finished');
    }
}
