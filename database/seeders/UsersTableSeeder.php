<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

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
        	'name' => 'admin', 
        	'user_name' => 'admin', 
        	'email' => 'admin@gmail.com', 
        	'password' => bcrypt('123456'), 
        	'user_role' => 1, 
        	'registered_at' => \Carbon\Carbon::now()->toDateTimeString(),
        	'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        	'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);
    }
}
