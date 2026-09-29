<?php

namespace Database\Seeders;

use App\Domain\Contact\Models\ContactSubmission;
use Illuminate\Database\Seeder;

class ContactSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        ContactSubmission::query()->firstOrCreate(
            [
                'email' => 'demo.contact@example.com',
                'subject' => 'Pertanyaan demo dari seeder',
            ],
            [
                'name' => 'Demo Pengunjung',
                'phone' => '+62 812-0000-0000',
                'message' => 'Ini pesan contoh untuk panel admin.',
                'locale' => 'id',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder',
                'status' => ContactSubmission::STATUS_NEW,
            ],
        );
    }
}
