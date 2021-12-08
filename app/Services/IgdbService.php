<?php

namespace App\Services;

use GuzzleHttp\Client;

class IgdbService
{
    /**
     * @var string
     */
    protected $igdbKey;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    const VALID_RESOURCES = [
        'games' => 'games',
        'characters' => 'characters',
        'companies' => 'companies',
        'game_engines' => 'game_engines',
        'game_modes' => 'game_modes',
        'keywords' => 'keywords',
        'people' => 'people',
        'platforms' => 'platforms',
        'pulses' => 'pulses',
        'themes' => 'themes',
        'collections' => 'collections',
        'player_perspectives' => 'player_perspectives',
        'reviews' => 'reviews',
        'franchises' => 'franchises',
        'genres' => 'genres',
        'release_dates' => 'release_dates'
    ];


    /**
     * Get game information by ID
     *
     * @param integer $gameId
     * @param array $fields
     * @return \StdClass
     * @throws \Exception
     */
    public static function getGame($gameId, $fields = ['*'])
    {
        $apiUrl = self::getEndpoint('games');
        $apiUrl .= $gameId;

        $params = array(
            'fields' => implode(',', $fields)
        );

        $apiData = self::apiGet($apiUrl, $params);

        return self::decodeSingle($apiData);
    }

    /**
     * Search games by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     * @return \StdClass
     * @throws \Exception
     */
    public static function searchGames($search = null, $fields = ['*'], $limit = 10, $offset = 0, $order = null, $filter = [])
    {
        $apiUrl = self::getEndpoint('games');
        $params = array_filter([
            'fields' => implode(',', $fields),
            'limit' => $limit,
            'offset' => $offset,
            'order' => $order,
            'search' => $search,
            'filter' => $filter,
        ]);
        $apiData = self::apiGet($apiUrl, $params);
        return self::decodeMultiple($apiData);
    }

    /**
     * Search games by name
     *
     * @param string $search
     * @param array $fields
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     * @return \StdClass
     * @throws \Exception
     */
    public static function searchCompanies($search = null, $fields = ['*'], $limit = 10, $offset = 0, $order = null, $filter = [])
    {
        $apiUrl = self::getEndpoint('companies');
        $params = array_filter([
            'fields' => implode(',', $fields),
            'limit' => $limit,
            'offset' => $offset,
            'order' => $order,
            'search' => $search,
            'filter' => $filter,
        ]);
        $apiData = self::apiGet($apiUrl, $params);
        return self::decodeMultiple($apiData);
    }

    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */
    /**
     * @param $name
     * @return mixed
     */
    private static function getEndpoint($name)
    {
        return rtrim(app('config')->get('services.igdb.url'), '/').'/'.self::VALID_RESOURCES[$name].'/';
    }

    /**
     * Decode the response from IGDB, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param  string $apiData the api response from IGDB
     * @throws \Exception
     * @return \StdClass  an IGDB resource object
     */
    private static function decodeSingle(&$apiData)
    {
        $resObj = json_decode($apiData);

        if (isset($resObj->status)) {
            $msg = "Error " . $resObj->status . " " . $resObj->message;
            throw new \Exception($msg);
        }

        if (!is_array($resObj) || count($resObj) == 0) {
            return false;
        }

        return $resObj[0];
    }

    /**
     * Decode the response from IGDB, extract the multiple resource object.
     *
     * @param  string $apiData the api response from IGDB
     * @throws \Exception
     * @return \StdClass  an IGDB resource object
     */
    private static function decodeMultiple(&$apiData)
    {
        $resObj = json_decode($apiData);

        if (isset($resObj->status)) {
            $msg = "Error " . $resObj->status . " " . $resObj->message;
            throw new \Exception($msg);
        } else {
            if (!is_array($resObj)) {
                return false;
            } else {
                return $resObj;
            }
        }
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    private static function apiGet($url, $params)
    {
        $url = $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params);
        try {
            $client = new Client();
            $response = $client->request('GET', $url, [
                'headers' => [
                    'user-key' => app('config')->get('services.igdb.key'),
                    'Accept' => 'application/json'
                ]
            ]);
        } catch (RequestException $exception) {
            if ($response = $exception->getResponse()) {
                throw new \Exception($exception);
            }
            throw new \Exception($exception);
        } catch (Exception $exception) {
            throw new \Exception($exception);
        }

        return $response->getBody();
    }
}
