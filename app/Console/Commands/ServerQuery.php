<?php

namespace App\Console\Commands;

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
        \DB::table('tbl_server')->orderBy('id')->chunk(100, function ($servers) {
            dd($servers);
            foreach ($servers as $server) {

            }
        });
    }
}