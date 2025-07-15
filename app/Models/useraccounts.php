<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class useraccounts extends Model
{
    //
    use HasFactory;

    protected $table = 'useraccounts'; // Specify the table name if it differs from the model name

    protected $fillable = [
        'username',
        'password',
        'email',
    ];
}
