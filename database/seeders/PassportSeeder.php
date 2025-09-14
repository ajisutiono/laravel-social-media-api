<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientRepository = app(ClientRepository::class);

        if (!DB::table('oauth_clients')->where('name', 'Personal Access Client')->exists()) {
            $clientRepository->createPersonalAccessGrantClient(
                'Personal Access Client',
                'users'
            );

            $clientRepository->createPasswordGrantClient(
                '',
                'Password Grant Client',
                'http://localhost',
                'users'
            );
        }
    }
}
