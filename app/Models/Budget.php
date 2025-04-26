<?php

namespace App\Models;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Budget extends Model
{
    use HasApiTokens;
    protected $fillable=['name', 'amount', 'icon', 'duration'];

    public function expense(){
        return $this->hasMany(Expense::class);
    }
}
