<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name', 'email', 'phone', 'gender',
        'profile_image', 'additional_file', 'status'
    ];

    public function customValues()
    {
        return $this->hasMany(ContactCustomValue::class);
    }

    public function mergedInto()
    {
        return $this->hasMany(MergedContact::class, 'merged_contact_id');
    }

    public function mergedFrom()
    {
        return $this->hasMany(MergedContact::class, 'master_contact_id');
    }
}
