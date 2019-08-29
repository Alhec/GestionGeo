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
        OrganizationUser::create([
            'user_id'=>2,
            'organization_id'=>'C',
        ]);
        OrganizationUser::create([
            'user_id'=>3,
            'organization_id'=>'G',
        ]);
        OrganizationUser::create([
            'user_id'=>4,
            'organization_id'=>'C',
        ]);
        OrganizationUser::create([
            'user_id'=>5,
            'organization_id'=>'G',
        ]);
        OrganizationUser::create([
            'user_id'=>6,
            'organization_id'=>'C',
        ]);
    }
}
