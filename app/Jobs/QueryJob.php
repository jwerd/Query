<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use xPaw\SourceQuery\SourceQuery;

class QueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minute timeout

    protected $servers;
    protected $server;
    protected $server_id;
    protected $sq_timeout = 1;
    protected $sq_engine  = SourceQuery::SOURCE;
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
        \DB::beginTransaction();
        foreach($this->servers as $server) {
            //dd($server);
            try {
                $query->Connect($server->address, $server->gameport, $this->sq_timeout, $this->sq_engine);
                if($info = $query->GetInfo()) {
//                    echo "Updating ".$info['HostName'].PHP_EOL;

                    \DB::table('tbl_server')->where('id', $server->id)->update([
                        'current_player_count' => $info['Players'],
                        'max_player_count'     => $info['MaxPlayers'],
                        'last_checked'         => time(),
                    ]);
                }
            }
            catch(\Exception $e)
            {
                echo $e->getMessage();
            }
            $query->Disconnect();
        }
        \DB::commit();
    }
}
