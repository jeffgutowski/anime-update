<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PlatformsTableSeeder::class);
        $this->call(DigitalsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionRolesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(GenresTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
