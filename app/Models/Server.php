<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    public $table = 'tbl_server'; // existing table
    public $timestamps = false;
    protected $autoincrement = true;
    public $fillable = [
        'id',
        'name',
        'current_player_count',
        'max_player_count',
        'version',
        'address',
        'gameport',
        'realgameport'
    ];
}
