<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Api\ApiManager;

class ApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Api Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling requests to the API.
    |
    */


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('api');

        $this->apiManager = new ApiManager();
    }

    public function availableEndpoints()
    {
        $endPoints = $this->apiManager->getEndpoints();

        if (is_null($endPoints)) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }

        return response()->json($endPoints, 200);
    }

    public function processOrder()
    {
        $order = request('order');

        if (is_null($order)) {

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);            

        }

        $processedOrder = $this->apiManager->processOrder($order);


        return response()->json($processedOrder['body'], $processedOrder['status']);
    }
}
