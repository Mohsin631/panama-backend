<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['plan_name' => 'Day Pass'],
            [
                'description' => 'Access for 1 day.',
                'price' => 20.00,
                'currency' => 'USD',
                'validity_value' => 1,
                'validity_unit' => 'day',
                'is_active' => true
            ]
        );

        Plan::updateOrCreate(
            ['plan_name' => 'Monthly'],
            [
                'description' => 'Access for 1 month.',
                'price' => 50.00,
                'currency' => 'USD',
                'validity_value' => 1,
                'validity_unit' => 'month',
                'is_active' => true
            ]
        );

        Plan::updateOrCreate(
            ['plan_name' => 'Yearly'],
            [
                'description' => 'Access for 1 year.',
                'price' => 299.00,
                'currency' => 'USD',
                'validity_value' => 1,
                'validity_unit' => 'year',
                'is_active' => true
            ]
        );
    }
}
