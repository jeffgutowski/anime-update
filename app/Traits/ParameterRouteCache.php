<?php
namespace App\Traits;

trait ParameterRouteCache
{
    public $cachekey;
    public function __construct()
    {
        $path = request()->getPathInfo();
        $queryParams = request()->query();

        // Sort and rebuild the query parameters so that no matter what order the parameters are, it will be from the same cache
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullPath = "{$path}?{$queryString}";

        // cache it by region and hash the full path to have a shortened key
        $this->cachekey = ':region:'.session()->get('region.code').':route_cache:'.sha1($fullPath);
    }

    private function paginationLinks($modelResults)
    {
        if (request()->has('frag')) {
            return $modelResults->fragment(request('frag'))->appends($_GET)->links();
        } else {
            return $modelResults->appends($_GET)->links();
        }
    }
}

