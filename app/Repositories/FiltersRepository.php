<?php
namespace App\Repositories;

use App\Models\AccessoriesHardwareCompanies as Company;

class FiltersRepository {

    public $filterCount = 0;
    public $developers = [];
    public $publishers = [];
    public $conditionsArray = [];
    public $companies = [];
    public $typesArray = [];
    public $direction = "asc";
    public $genresIncluded = [];
    public $genresExcluded = [];
    public $platformsArray = [];
    public $order = 'name_order';
    public $pageErrorRedirection;
    public $filtersList;
    public $groupBy = 'games.id';
    public $region = null;

    public function __construct()
    {
        if ($this->region == null) {
            $this->region = session('region.code');
        }
        $this->filtersTable = [
            // Age Ratings
            'a' => function($model, $value, $request) {
                $ageRatingsArray = explode(',', $value);
                $model->where(function ($query) use ($ageRatingsArray) {
                    if (session('region.code') == "ntsc_u") {
                        $query->whereIn('esrb', $ageRatingsArray);
                        if (in_array("unrated", $ageRatingsArray)) {
                            $query->orWhereNull('esrb');
                        }
                    } elseif (session('region.code') == "pal") {
                        $query->whereIn('pegi', $ageRatingsArray);
                        if (in_array("unrated", $ageRatingsArray)) {
                            $query->orWhereNull('pegi');
                        }
                    }
                });
                $this->filterCount += count($ageRatingsArray);
            },
            // Company/Manufacturer
            'c' => function($model, $value, $request) {
                $companyArray = explode(',', $value);
                $this->companies = Company::whereIn('id', $companyArray)->get();
                foreach ($this->companies as $company) {
                    $companyNames[] = $company->name;
                }
                $model->whereIn('company', $companyNames);
                $this->filterCount += count($companyArray);
            },
            // Conditions
            'co' => function($model, $value, $request) {
                $conditionsArray = explode(',', $value);
                $model->where(function ($query) use ($conditionsArray) {
                    $query->whereIn('condition', $conditionsArray);
                });
                $this->conditionsArray = $conditionsArray;
                $this->filterCount += count($this->conditionsArray);
            },
            // Developers
            'd' => function($model, $value, $request) {
                $developersArray = explode(',', $value);
                $model->whereHas('developers', function ($q) use ($developersArray) {
                    $q->whereIn('developers.id', $developersArray);
                });
                $this->developers = \App\Models\Developer::whereIn('id', $developersArray)->get();
                $this->filterCount += count($developersArray);
            },
            // Genres
            'g' => function($model, $value, $request) {
                $genres = $value;
                $genresIncluded = [];
                $genresExcluded = [];
                $genresArray = explode(',', $genres);
                foreach ($genresArray as $genre) {
                    if ($genre < 0) {
                        $genresExcluded[] = abs($genre);
                    } else {
                        $genresIncluded[] = $genre;
                    }
                }

                if ($genresIncluded && request()->get('ge') === "i") { /*inclusive*/
                    foreach ($genresIncluded as $genreId) {
                        $model->whereHas('genres', function ($q) use ($genreId) {
                            $q->where('genres.id', $genreId);
                        });
                    }
                } elseif ($genresIncluded) { /*exclusive*/
                    $model->whereHas('genres', function ($q) use ($genresIncluded) {
                        $q->whereIn('genres.id', $genresIncluded);
                    });
                }
                if ($genresExcluded) {
                    $model->whereDoesntHave('genres', function ($q) use ($genresExcluded) {
                        $q->whereIn('genres.id', $genresExcluded);
                    });
                }
                $this->genresIncluded = $genresIncluded;
                $this->genresExcluded = $genresExcluded;
                $this->filterCount += count($genresArray);
            },
            // Platforms
            'p' => function($model, $value, $request) {
                $platformsArray = explode(',', $value);
                $model->where(function ($query) use ($platformsArray) {
                    $query->whereIn('platform_id', $platformsArray);
                    foreach ($platformsArray as $platform) {
                        $query->orWhere('other_platforms', 'like', '%"'.$platform.'"%');
                    }
                });
                $this->platformsArray = $platformsArray;
                $this->filterCount += count($platformsArray);
            },
            // Price
            'pr' => function($model, $value, $request) {
                // Custom Range for Rating Filter
                $min = substr($value, 0, strpos($value, "-"));
                $max = substr($value, strpos($value, "-") + 1);
                $model->where(function($query) use ($min, $max) {
                    if (is_numeric($min)) {
                        $query->where('price', '>=', $min * 100); // price decimal is not stored and is stored as int
                    }
                    if (is_numeric($max)) {
                        $query->where('price', '<=', $max * 100); // price decimal is not stored and is stored as int
                    }
                });
                $this->filterCount ++;
            },
            // publishers
            'pu' => function($model, $value, $request) {
                $publishersArray = explode(',', $value);
                $model->whereHas('publishers', function ($q) use ($publishersArray) {
                    $q->whereIn('publishers.id', $publishersArray)
                        ->where('region', '=', session('region.abbr'));
                });
                $this->publishers = \App\Models\Publisher::whereIn('id', $publishersArray)->get();
                $this->filterCount += count($publishersArray);
            },
            // Search Terms
            'q' => function($model, $value, $request) {
                $model->selectRaw("MATCH (games.name, games.name_us, games.name_jp, games.name_eu) AGAINST (?) AS `rank`", [$value])
                    ->whereRaw("MATCH (games.name, games.name_us, games.name_jp, games.name_eu) AGAINST (?)", [$value]);
                $this->filterCount ++;
            },
            // Ratings
            'r' => function($model, $value, $request) {
                if (is_numeric($value)) {
                    $model->where('average_rating', ">=", request()->get('r'));
                    $this->filterCount ++;
                } else if (isset($request['rc'])) {
                    // Custom Range for Rating Filter
                    $min = substr($request['rc'], 0, strpos($request['rc'], "-"));
                    $max = substr($request['rc'], strpos($request['rc'], "-") + 1);
                    $model->whereBetween('average_rating', [$min, $max]);
                    $this->filterCount ++;
                }
            },
            // Ratings Custom
            'rc' => function($model, $value, $request) {
                // fulfilled in Ratings Filter
            },
            'rs' => function($model, $value, $request) {
                if ($value == "my") {
                    // fulfilled in Ratings Filter
                    $model->leftJoin('game_rating', function ($join) {
                        $join->on('game_rating.game_id', '=', 'games.id')
                            ->where(function ($query) {
                                $query->where('game_rating.user_id', '=', auth()->id());
                            });
                    });
                    $model->addSelect(['game_rating.rating', 'game_rating.difficulty', 'game_rating.duration']);
                }
            },
            // Order By
            'o' => function($model, $value, $request) {
                $this->order = $value;
                if (substr($value, 0, 1) === "-") {
                    $this->order = str_replace("-", "", $value);
                    $this->direction = "desc";
                } else {
                    $this->direction = "asc";
                }
                if (strpos($value, 'release') !== false && $this->region == "all") {
                    $this->order = "release_date";
                } else if (strpos($value, 'release') !== false) {
                    $this->order = session('region.code');
                }

                if ($this->order == 'average_duration') {
                    $model->where('average_duration', '!=', 'null');
                }
            },
            // Types
            't' => function($model, $value, $request) {
                $typesArray = explode(',', $value);
                $model->whereIn('type', $typesArray);
                $this->filterCount += count($typesArray);
            },
            'ys' => function($model, $value, $request) {
                $model->where(session('region.code'), ">=", $value."-01-01");
                $this->filterCount += 1;
            },
            'ye' => function($model, $value, $request) {
                $model->where(session('region.code'), "<=", $value."-12-31");
                $this->filterCount += 1;
            }
        ];
        $this->filtersList = array_keys($this->filtersTable);
    }

    public function filter($model, $request, $type, $paginateName = "page")
    {
        $nameRegion = 'name_'.session('region.abbr');
        // Set name to be the region name so it can sort correctly if it is not null
        $regionNaming = "IF(`$nameRegion` IS NOT NULL, `games`.`$nameRegion`, `games`.`name`)";
        // Order by names without "The" at the beginning
        $model->addSelect(\DB::raw("
            CASE WHEN $regionNaming LIKE 'The %' THEN TRIM(SUBSTR($regionNaming FROM 4)) ELSE $regionNaming END AS name_order, 
            $regionNaming AS name
        "));
        foreach ($request as $param => $value) {
            $this->filtersTable[$param]($model, $value, $request);
        }
        if ($this->region != 'all' && isset($this->region)) {
            $model->whereNotNull($this->region);
        }
        if ($this->order == 'name') {
            $this->order = 'name_order';
        }
        if ($this->order == "price") {
            $model->orderByRaw("`price` + `delivery_price` {$this->direction}");
        } else {
            $model->orderBy($this->order, $this->direction);
        }
        // Sort by Name for tie breaks of equal sorting
        $model->orderBy('name_order', $this->direction);

        // group by a games id to only show game once for any joins that happen
        if ($this->groupBy) {
            $model->groupBy($this->groupBy);
        }

        $paginator = $model->paginate('36', ['*'], $paginateName);
        if ($paginator->currentPage() > $paginator->lastPage()) {
            $this->pageErrorRedirection = $paginator->getUrlRange(0,0)[0];
        }
        return $paginator;
    }
}
