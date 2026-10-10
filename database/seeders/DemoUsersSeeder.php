<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo accounts may only be seeded in local or testing environments.');
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@vesperia.test'],
            [
                'name' => 'Vesperia Demo Admin',
                'password' => Hash::make('AdminDemo!2026'),
            ]
        );
        $admin->forceFill(['role' => 'admin'])->save();

        $user = User::updateOrCreate(
            ['email' => 'user@vesperia.test'],
            [
                'name' => 'Vesperia Demo User',
                'password' => Hash::make('UserDemo!2026'),
            ]
        );
        $user->forceFill(['role' => 'user'])->save();

        $this->command?->info('Demo accounts created for local development.');
    }
}
