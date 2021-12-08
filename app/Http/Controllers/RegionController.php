<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class RegionController extends Controller
{
    public function ntsc_u() {
        session()->put('region.code', 'ntsc_u');
        session()->put('region.abbr', 'us');
        $parse_url = parse_url(url()->previous());
        if (isset($parse_url['query'])) {
            $params = [];
            parse_str($parse_url['query'], $params);
            if (isset($params['page'])) {
                $params['page'] = 1;
                $path = $parse_url['path']."?".http_build_query($params);
                return redirect()->to($path);
            }
        }
        return redirect()->back();
    }

    public function pal() {
        session()->put('region.code', 'pal');
        session()->put('region.abbr', 'eu');
        $parse_url = parse_url(url()->previous());
        if (isset($parse_url['query'])) {
            $params = [];
            parse_str($parse_url['query'], $params);
            if (isset($params['page'])) {
                $params['page'] = 1;
                $path = $parse_url['path']."?".http_build_query($params);
                return redirect()->to($path);
            }
        }
        return redirect()->back();
    }

    public function ntsc_j() {
        session()->put('region.code', 'ntsc_j');
        session()->put('region.abbr', 'jp');
        $parse_url = parse_url(url()->previous());
        if (isset($parse_url['query'])) {
            $params = [];
            parse_str($parse_url['query'], $params);
            if (isset($params['page'])) {
                $params['page'] = 1;
                $path = $parse_url['path']."?".http_build_query($params);
                return redirect()->to($path);
            }
        }
        return redirect()->back();
    }

    public function pa() {
        session()->put('region.code', 'pa');
        session()->put('region.abbr', 'pa');
        $parse_url = parse_url(url()->previous());
        if (isset($parse_url['query'])) {
            $params = [];
            parse_str($parse_url['query'], $params);
            if (isset($params['page'])) {
                $params['page'] = 1;
                $path = $parse_url['path']."?".http_build_query($params);
                return redirect()->to($path);
            }
        }
        return redirect()->back();
    }
}
