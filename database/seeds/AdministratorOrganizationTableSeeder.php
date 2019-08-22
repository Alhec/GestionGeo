<?php

use Illuminate\Database\Seeder;
use App\AdministratorOrganization;

class AdministratorOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdministratorOrganization::query()->truncate();
        AdministratorOrganization::create([
            'administrator_id'=>1,
            'organization_id'=>'G',
        ]);
    }
}
