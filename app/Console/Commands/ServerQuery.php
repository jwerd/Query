<?php

namespace App\Console\Commands;

use App\Jobs\QueryJob;
use Illuminate\Console\Command;

class ServerQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:query';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queries all servers within our server model';
    protected $downcount_thresholdhold = 10000;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::table('tbl_server')->where('downcount', '<', $this->downcount_thresholdhold)->orderBy('current_player_count', 'DESC')->chunk(100, function ($servers) {
            dispatch(new QueryJob($servers));
            exit;
            foreach ($servers as $server) {
                //dispatch(new QueryJob($server->id, $server->address, $server->realgameport, $server->gameport));
            }
        });
    }
}
