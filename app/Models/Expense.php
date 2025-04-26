<?php

namespace App\Models;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Expense extends Model
{
    use HasApiTokens;

    protected $fillable=['expenseName', 'expensePrice', 'budget_id', 'date'];

    public function budget(){
        return $this->belongsTo(Budget::class);
    }
}
