<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
  
        // Crear un usuario de prueba con información específica
         \App\Models\User::factory()->create([
             'name' => 'Jeremias Aquino',
             'email' => 'test@example.com',
             'password' => bcrypt('test123456'),
         ]);

         \App\Models\User::factory()->create([
            'name' => 'Juana Aquino',
            'email' => 'usuario@example.com',
            'password' => bcrypt('aquino123456'),
        ]);
    }
}
