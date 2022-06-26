<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Predis\Client;
use Redis\Graph;
use Redis\Graph\Node;
use Redis\Graph\Edge;

class LoadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loaddata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load airports and flights data from the remote service.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        [$airports, $flights] = static::read_json();

        /**
         *  save airports
         */
        $redis = new Client('redis://127.0.0.1:6379/');
        $graph = new Graph('flight-route', $redis);

        foreach ($airports as &$value){
            $graph->addNode(new Node("$value[iata]:Airport", $value));
        }


        foreach ($flights as &$value){

            // find from_node and to_node
            $from = null;
            $to = null;
            foreach ($graph->nodes as &$node){
                if (!$from && $node->alias == $value['from']){
                    $from = $node;
                }

                if (!$to && $node->alias == $value['to']){
                    $to = $node;
                }

                if ($from && $to){
                    break;
                }
            }

            // create relation for from node and to node
            if ($from != null and $to != null){
                $edge = new Edge($from, $to, 'Flight', $value);
                $graph->addEdge($edge);
            }
        }

        $result = $graph->commit();
        $result->prettyPrint();
        return 0;
    }


    private static function read_json(): array
    {

        $cwd = getcwd();
        $air_file = "$cwd/airports.json";
        $flight_file = "$cwd/flights.json";

        $airports = json_decode(file_get_contents($air_file), true);
        $flights = json_decode(file_get_contents($flight_file), true);
        return [$airports, $flights];
    }
}
