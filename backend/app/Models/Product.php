<?php
//  a model represents a table in your database and acts as the "M" in MVC (Model–View–Controller). It helps you interact with your database using object-oriented code instead of raw SQL.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'image', 
    ];
} 