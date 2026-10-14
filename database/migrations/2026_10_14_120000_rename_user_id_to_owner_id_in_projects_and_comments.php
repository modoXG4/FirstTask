<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('projects', 'user_id') && ! Schema::hasColumn('projects', 'owner_id')) {
            DB::statement('ALTER TABLE `projects` CHANGE `user_id` `owner_id` BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('comments', 'user_id') && ! Schema::hasColumn('comments', 'owner_id')) {
            DB::statement('ALTER TABLE `comments` CHANGE `user_id` `owner_id` BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'owner_id') && ! Schema::hasColumn('projects', 'user_id')) {
            DB::statement('ALTER TABLE `projects` CHANGE `owner_id` `user_id` BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('comments', 'owner_id') && ! Schema::hasColumn('comments', 'user_id')) {
            DB::statement('ALTER TABLE `comments` CHANGE `owner_id` `user_id` BIGINT UNSIGNED NOT NULL');
        }
    }
};
