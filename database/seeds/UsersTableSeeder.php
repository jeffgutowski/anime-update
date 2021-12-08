<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'kelly',
                'email' => 'kellyreef@gmail.com',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'status' => 1,
                'password' => '$2y$10$19cA.6vZC6yukduE8ZoKluv8YxytQd17ZQQ8THzoD9m9GlEsCHIVG',
                'confirmed' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'josh',
                'email' => 'josh@example.com',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'status' => 1,
                'password' => '$2y$10$JOVL72ogm8WGxuczoAV0TegW1.0IiAxJGn4cXBAf3z8V1vF5GXE4y',
                'confirmed' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'danny',
                'email' => 'danny@example.com',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'status' => 1,
                'password' => '$2y$10$FDxHbTWocBbZ/PMJPkYMTun1t5SXL/Yzj5HHqmHvNWNX3bY9.Lsw2',
                'confirmed' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'jake',
                'email' => 'jake@example.com',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'status' => 1,
                'password' => '$2y$10$m0VegCQZZGDLrEqVs.iJhuf681Oa.zMvXbi4nrvZn9XqotQ8wqjvu',
                'confirmed' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        DB::beginTransaction();
        $users = User::all();
        foreach ($users as $user) {
            DB::table('role_users')->insert([
                'role_id' => 1,
                'user_id' => $user->id,
            ]);
        }
        DB::commit();
    }
}
