<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dados extends Model
{
    use HasFactory;

    protected $table = 'dados';
    protected $fillable = ['name', 'password', 'file_path'];

    public function rules()
    {
        return [
            'name' => ['required', 'unique:dados','max:30'],
            'password' => ['max:50'],
            'file' => ['file'],
        ];
    }
}
