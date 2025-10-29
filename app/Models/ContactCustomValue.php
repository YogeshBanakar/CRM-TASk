<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCustomValue extends Model
{
    use HasFactory;

    protected $fillable = ['contact_id', 'custom_field_id', 'value'];

    /**
     * Get the custom field that this value belongs to.
     */
    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }

    /**
     * Get the contact that owns this custom value.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
