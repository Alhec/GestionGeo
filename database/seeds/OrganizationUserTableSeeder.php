<?php

use Illuminate\Database\Seeder;
use App\OrganizationUser;

class OrganizationUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrganizationUser::query()->truncate();
        OrganizationUser::create([
            'user_id'=>1,
            'organization_id'=>'G',
        ]);
    }
}
