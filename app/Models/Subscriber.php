<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = ['name', 'email', 'email_list_id'];

    public function emailList()
    {
        return $this->belongsTo(EmailList::class);
    }
}


