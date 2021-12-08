<?php
namespace App\Http\Controllers\Admin;

use App\Models\Offer;
use App\Models\Listing;
use App\Models\User_Rating;
use App\Models\Game;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Payment;
use Charts;
use DB;

class DashboardController
{
    public function index()
    {
        DB::beginTransaction();
        $this->data['chart_listing'] = Charts::database(Listing::all(), 'area', 'chartjs')
            ->elementLabel("Listings")
            ->title("Listings last 7 days")
            ->colors(['#00c0ef'])
            ->responsive(false)
            ->height(300)
            ->width(0)
            ->lastByDay();

        $this->data['chart_offer'] = Charts::database(Offer::all(), 'area', 'chartjs')
            ->elementLabel("Offers")
            ->title("Offers last 7 days")
            ->colors(['#00a65a'])
            ->responsive(false)
            ->height(300)
            ->width(0)
            ->lastByDay();

        $this->data['chart_user'] = Charts::database(User::all(), 'area', 'chartjs')
            ->elementLabel("Users")
            ->title("User registrations last 7 days")
            ->colors(['#f39c12'])
            ->responsive(false)
            ->height(300)
            ->width(0)
            ->lastByDay();

        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
        $this->data['users'] = User::orderBy('created_at', 'desc')->limit(10)->get(); // get users
        $this->data['users_count'] = User::count(); // get users
        $this->data['listings'] = Listing::count(); // get listings
        $this->data['offers'] = Offer::count(); // get offers
        $this->data['games'] = Game::count(); // get games
        $this->data['transactions_fee_total'] = Transaction::where('type', 'fee')->sum('total'); // get transactions fee total
        $this->data['payments_count'] = Payment::where('status', '1')->count(); // get payments
        $this->data['payments_sum'] = Payment::where('status', '1')->sum('total'); // get payments
        $this->data['payments_sum_transaction_fee'] = Payment::where('status', '1')->sum('total'); // get payments
        DB::commit();

        // Install security check
        $this->data['security'] = substr(sprintf('%o', fileperms(base_path('.env'))), -4) >= '0755' || substr(sprintf('%o', fileperms(base_path('config/app.php'))), -4) >= '0755';

        // Check for new version

        $check_version = array('ip' => @$_SERVER['SERVER_ADDR'], 'hostname' => @$_SERVER['SERVER_ADDR'] ,'domain' => $_SERVER['HTTP_HOST'], 'email' => auth()->user()->email);

        $this->data['version_response'] = self::checkVersion($check_version, 'https://www.wiledia.com/gameport/version');

        return view('backpack::dashboard', $this->data);
    }


    /**
     * checkVersion()
     *
     * @param mixed $_p
     * @param mixed $remote_url
     * @return
     */
    public function checkVersion($_p, $remote_url)
    {
    	$remote_url = trim($remote_url);

    	$is_https = (substr($remote_url, 0, 5) == 'https');

    	$fields_string = http_build_query($_p);

    	if(function_exists('curl_init')) {

    		$ch = curl_init();

    		curl_setopt($ch, CURLOPT_URL, $remote_url);

    		if($is_https && extension_loaded('openssl')) {
    			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    		}

    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    		curl_setopt($ch, CURLOPT_HEADER, false);

    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    		$response = curl_exec($ch);

            return $response;

    		curl_close($ch);

    	} else {

    		$context_options = array (
    			'http' => array (
    				'method' => 'POST',
    				'header' => "Content-type: application/x-www-form-urlencoded\r\n".
    							"Content-Length: ".strlen($fields_string)."\r\n",
    				'content' => $fields_string
    			 )
    		 );


            try {

                $context = stream_context_create($context_options);
                $fp = fopen($remote_url, 'r', false, $context);

         		$response = @stream_get_contents($fp);

            } catch(\Exception $e) {
                return false;
            }

    	}
    	return $response;
    }
}
