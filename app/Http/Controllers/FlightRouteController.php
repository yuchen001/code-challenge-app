<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlightRouteController extends Controller
{
    /**
     * api for search flight route the shortest route.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {

        $from = $request->query('from', '');
        $to = $request->query('to', '');

        $result = Flight::shortestPaths($from, $to);
        return response()->json($result);
    }
}
