<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['title' => 'auth_profile_edit'],
            ['title' => 'user_management_access'],
            ['title' => 'permission_create'],
            ['title' => 'permission_edit'],
            ['title' => 'permission_show'],
            ['title' => 'permission_delete'],
            ['title' => 'permission_access'],
            ['title' => 'role_create'],
            ['title' => 'role_edit'],
            ['title' => 'role_show'],
            ['title' => 'role_delete'],
            ['title' => 'role_access'],
            ['title' => 'user_create'],
            ['title' => 'user_edit'],
            ['title' => 'user_show'],
            ['title' => 'user_delete'],
            ['title' => 'user_access'],
            ['title' => 'config_list_access'],
            ['title' => 'user_type_create'],
            ['title' => 'user_type_edit'],
            ['title' => 'user_type_show'],
            ['title' => 'user_type_delete'],
            ['title' => 'user_type_access'],
            ['title' => 'merchant_create'],
            ['title' => 'merchant_edit'],
            ['title' => 'merchant_show'],
            ['title' => 'merchant_delete'],
            ['title' => 'merchant_access'],
            ['title' => 'message_create'],
            ['title' => 'message_edit'],
            ['title' => 'message_show'],
            ['title' => 'message_delete'],
            ['title' => 'message_access'],
            ['title' => 'message_template_create'],
            ['title' => 'message_template_edit'],
            ['title' => 'message_template_show'],
            ['title' => 'message_template_delete'],
            ['title' => 'message_template_access'],
            ['title' => 'message_template_fields_create'],
            ['title' => 'message_template_fields_edit'],
            ['title' => 'message_template_fields_show'],
            ['title' => 'message_template_fields_delete'],
            ['title' => 'message_template_fields_access'],
            ['title' => 'main_dashboard_access'],
            ['title' => 'price_plan_create'],
            ['title' => 'price_plan_edit'],
            ['title' => 'price_plan_show'],
            ['title' => 'price_plan_delete'],
            ['title' => 'price_plan_access'],
            ['title' => 'price_plan_type_create'],
            ['title' => 'price_plan_type_edit'],
            ['title' => 'price_plan_type_show'],
            ['title' => 'price_plan_type_delete'],
            ['title' => 'price_plan_type_access'],
            ['title' => 'subscription_create'],
            ['title' => 'subscription_edit'],
            ['title' => 'subscription_show'],
            ['title' => 'subscription_delete'],
            ['title' => 'subscription_access']
        ];


        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate($permission, $permission);
        }
    }
}
