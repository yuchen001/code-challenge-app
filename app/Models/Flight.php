<?php

namespace App\Models;

use Predis\Client;
use Redis\Graph;

class Flight
{
    public string $id;

    public string $from;

    public string $to;

    public static $redis;
    public static $graph;


    /**
     * @param string $id
     * @param string $from
     * @param string $to
     */
    public function __construct(string $id, string $from, string $to)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
    }


    /**
     * search flight the shortest paths for redis
     *
     * @param string $from
     * @param string $to
     * @return array
     */
    public static function shortestPaths(string $from, string $to): array
    {
        $query_result = static::$graph->query("
            MATCH (a1:Airport {iata: '$from'}), (a2:Airport {iata: '$to'})
            WITH a1, a2
            MATCH f=allShortestPaths((a1)-[:Flight*]->(a2))
            UNWIND relationships(f) as flight
            RETURN flight.id, flight.from, flight.to
        ");

        $result = [];
        while ($v = $query_result->fetch()) {
            $result[] = static::to_obj($v);
        }
        return $result;
    }

    /**
     * Converse query results to the instance.
     *
     * @param array|null $v
     * @return Flight
     */
    private static function to_obj(array|null $v): Flight
    {
        return new Flight($v['flight.id'], $v['flight.from'], $v['flight.to']);
    }
}

Flight::$redis = new Client('redis://127.0.0.1:6379/');
Flight::$graph = new Graph('flight-route', Flight::$redis);
