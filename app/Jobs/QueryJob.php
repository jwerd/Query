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
    public function __construct($address, $port, $secondaryport)
    {
        $this->address       = $address;
        $this->port          = $port;
        $this->secondaryport = $secondaryport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $query = new SourceQuery();

        try {
            $query->Connect($this->address, $this->port, $this->timeout, $this->engine);
            if($info = $query->GetInfo()) {
                echo "Updating ".$info['HostName'].PHP_EOL;
                $server = new Server;
                $server->current_player_count = $info['Players'];
                $server->max_player_count     = $info['MaxPlayers'];
                $server->last_checked         = time();
                $server->save();
            }
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }

        $query->Disconnect();
    }
}
