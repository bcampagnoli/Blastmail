<?php

namespace App\Models;

use App\Observers\CampaignMailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts()
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function emailList()
    {
        return $this->belongsTo(emailList::class);
    }

    public function mails(){
        return $this->hasMany(CampaignMail::class);
    }
}
