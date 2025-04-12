<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    /* WHY:use HasFactoryと記述することでPostFactoryを使用してシーダーを実行できる。
     *     これがないとPostFactoryを使用してシーダーを実行するときにエラーが発生する。
     */
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
