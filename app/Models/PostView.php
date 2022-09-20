<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'posts_views';
    protected $fillable = [
        'user_id',
        'post_id',
        'ip',
        'date'
    ];
    public $incrementing = true;
    public $timestamps = false;
}
