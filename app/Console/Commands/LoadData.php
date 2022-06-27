<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
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


    private const flag_key = 'save_flag';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $graph = new Graph('flight-route', Redis::connection()->client());

        if (static::check_if_exist_data())
            return 0;

        [$airports, $flights] = static::read_json();

        /**
         *  save airports
         */
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

        # set the flag for mark the data has been stored.
        Redis::set(static::flag_key, true);

        echo 'load end.';

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

    private static function check_if_exist_data()
    {
        $flag = Redis::get(static::flag_key);
        if ($flag){
            echo "the data was save.";
        }

        return $flag;
    }
}
