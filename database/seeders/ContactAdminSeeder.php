<?php

namespace Database\Seeders;

use App\Models\ContactAdmin;
use Illuminate\Database\Seeder;

class ContactAdminSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            ['name' => 'Phone', 'value' => '+201000000000', 'is_active' => true],
            ['name' => 'Email', 'value' => 'info@metwlogistic.test', 'is_active' => true],
            ['name' => 'WhatsApp', 'value' => '+201000000000', 'is_active' => true],
            ['name' => 'Facebook', 'value' => 'https://facebook.com/metwlogistic', 'is_active' => true],
            ['name' => 'Instagram', 'value' => 'https://instagram.com/metwlogistic', 'is_active' => true],
            ['name' => 'Address', 'value' => 'Cairo, Egypt', 'is_active' => true],
        ];

        foreach ($contacts as $contact) {
            ContactAdmin::updateOrCreate(
                ['name' => $contact['name']],
                $contact
            );
        }
    }
}
