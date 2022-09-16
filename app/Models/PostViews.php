<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostViews extends Model
{
    use HasFactory;

    protected $primaryKey = 'post_id';
    protected $table = 'posts_views';
    public $incrementing = false;
    public $timestamps = false;
}
