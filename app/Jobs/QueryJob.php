<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use xPaw\SourceQuery\SourceQuery;
use App\Models\Server;

class QueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $servers;
    protected $server_id;
    protected $timeout = 1;
    protected $engine  = SourceQuery::SOURCE;
    protected $address;
    protected $port;
    protected $secondaryport;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($servers)
    {
        $this->servers = $servers;
//        $this->server_id     = $id;
//        $this->address       = $address;
//        $this->port          = $port;
//        $this->secondaryport = $secondaryport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $query = new SourceQuery();
        $updates = [];
        foreach($this->servers as $server) {
            //dd($server);
            try {
                $query->Connect($server->address, $server->gameport, $this->timeout, $this->engine);
                if($info = $query->GetInfo()) {
                    echo "Updating ".$info['HostName'].PHP_EOL;
                    $updates[] = [
                        'id'                   => $server->id,
                        'current_player_count' => $info['Players'],
                        'max_player_count'     => $info['MaxPlayers'],
                        'last_checked'         => time(),
                    ];
                }
            }
            catch(\Exception $e)
            {
                echo $e->getMessage();
            }
            $query->Disconnect();
        }
        dd($updates);
    }
}
