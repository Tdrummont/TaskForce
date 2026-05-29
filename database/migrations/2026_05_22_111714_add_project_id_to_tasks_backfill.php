<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'project_id')) {
            return;
        }

        $defaultProjectName = 'Default';

        $projectId = DB::table('projects')->where('name', $defaultProjectName)->value('id');

        if (!$projectId) {
            $projectId = DB::table('projects')->insertGetId([
                'name' => $defaultProjectName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('tasks')->whereNull('project_id')->update(['project_id' => $projectId]);
    }

    public function down(): void
    {
        // não reverte backfill por segurança
    }
};

