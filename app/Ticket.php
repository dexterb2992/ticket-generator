<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $guarded = ['id'];

    public function numbers()
    {
    	return $this->hasMany(TicketNumber::class);
    }
}
