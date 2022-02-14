<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;
	
	public function features(){
		return $this->belongsToMany(Feature::class);
	} 
	
	 public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
	
	public function user()
	{
		return $this->belongsTo(User::class,'landlord_id','id');
		
	}
}
