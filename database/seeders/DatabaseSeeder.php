<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Contact;
use App\Models\ContactCustomValue;
use App\Models\CustomField;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Custom Fields
        $birthday = CustomField::create(['name' => 'Birthday', 'type' => 'date']);
        $company = CustomField::create(['name' => 'Company Name', 'type' => 'text']);
        $address = CustomField::create(['name' => 'Address', 'type' => 'text']);
        // Contact 1
        $john = Contact::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'gender' => 'male',
            'profile_image' => 'contacts/images/john.jpg',
            'additional_file' => 'contacts/documents/resume.pdf'
        ]);

        ContactCustomValue::create([
            'contact_id' => $john->id,
            'custom_field_id' => $birthday->id,
            'value' => '1990-05-15'
        ]);

        ContactCustomValue::create([
            'contact_id' => $john->id,
            'custom_field_id' => $company->id,
            'value' => 'ABC Corp'
        ]);

        // Contact 2 (to merge)
        $jane = Contact::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '9999999999',
            'gender' => 'female'
        ]);

        ContactCustomValue::create([
            'contact_id' => $jane->id,
            'custom_field_id' => $birthday->id,
            'value' => '1988-08-20'
        ]);

        ContactCustomValue::create([
            'contact_id' => $jane->id,
            'custom_field_id' => $address->id,
            'value' => '123 Main St, NY'
        ]);
    }
}
