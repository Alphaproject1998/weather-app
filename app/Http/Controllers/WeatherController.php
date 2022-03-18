<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

// This Controller handles requests to do with the weather API.

class WeatherController extends Controller
{
    // Create a new WeatherController instance and require user to be authenticated with JWT.
    public function __construct() {
        //$this->middleware('auth:api');
    }
    
    // Get City from request and execute cURL session to use api.
    public function city($city){
        $curl = curl_init();

        // Get API Key
        $ApiKey = env("WEATHER_KEY");

        // Get filter if any
        $filter = Request()->filter ?? null;

        // Construct URL String via str_replace
        $url = str_replace(["ApiKey","City"],[$ApiKey,$city],"https://api.weatherapi.com/v1/current.json?key=ApiKey&q=City&aqi=yes");

        // Create array of options for the cURL session
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        // Execute cURL session and store the response in $response
        $response = curl_exec($curl);

        // If the operation failed, store the error message in $error
        $error = curl_error($curl);

        // Close cURL session
        curl_close($curl);
        
        // Save the response in a new variable and unserialize it to navigate to current.
        $data = json_decode($response,true)["current"];

        // If there was an error, return it for fixing. Otherwise, check for a filter and either filter result or return full result.
        if ($error) {
            return response()->json(['error' => $error]);
        } else {
            if ($filter) {
                return $data[$filter] ?? response()->json(['error' => 'filter not found in data'], 422);
            }
            else {
                return $data;
            }
        }
    }
}
