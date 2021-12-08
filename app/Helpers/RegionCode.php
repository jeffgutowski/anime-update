<?php
function regionCode(String $input, String $separator = '_', String $case = 'lower', String $conversion = 'country_to_code') : ?String
{
    if ($conversion !== 'country_to_code' && $conversion !== 'code_to_country') {
        throw new \Exception('Invalid conversion parameter');
    }
    if ($case !== 'lower' && $case !== 'upper') {
        throw new \Exception('$case has incorrect String');
    }
    if ($conversion === 'country_to_code') {
        $regionAbbr = strtoupper($input);
        $regionCode = null;
        switch ($regionAbbr) {
            case 'US':
                $regionCode = 'ntsc'.$separator.'u';
                break;
            case 'JP':
                $regionCode = 'ntsc'.$separator.'j';
                break;
            case 'EU':
                $regionCode = 'pal';
                break;
        }
        if (is_null($regionCode)) {
            throw new \Exception('$regionAbbr has incorrect String');
        }

        if ($case === 'upper') {
            $regionCode = strtoupper($regionCode);
        }
        return $regionCode;
    } elseif($conversion === 'code_to_country') {
        $region = null;
        switch (strtolower($input)) {
            case 'ntsc'.$separator.'u':
                $region = 'us';
                break;
            case 'ntsc'.$separator.'j':
                $region = 'jp';
                break;
            case 'pal':
                $region = 'eu';
                break;
            case 'pa':
                $region = 'pa';
                break;
            default:
                throw new \Exception("Invalid Region Code");
                break;
        }
        if ($case === 'upper') {
            $region = strtoupper($region);
        }
        return $region;
    }
    throw new \Exception("regionCode Error");
}

function regions() : Array
{
    return [
        'us',
        'jp',
        'eu',
        'pa',
    ];
}

function regionCodes() : Array
{
    return [
        'ntsc_u',
        'ntsc_j',
        'pal',
        'pa',
    ];
}
