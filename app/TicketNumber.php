<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketNumber extends Model
{
	protected $dates = ['created_at', 'updated_at'];
    protected $guarded = ['id'];

    public function ticket()
    {
    	return $this->belongsTo(Ticket::class);
    }
}
