<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('masterkey_sessions', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('tokenable_type')->nullable()->after('status');
            $table->unsignedBigInteger('tokenable_id')->nullable()->after('tokenable_type');

            // Add index for polymorphic relationship
            $table->index(['tokenable_type', 'tokenable_id']);
        });

        // Migrate existing data
        if (Schema::hasColumn('masterkey_sessions', 'user_id')) {
            DB::table('masterkey_sessions')
                ->whereNotNull('user_id')
                ->update([
                    'tokenable_type' => '\\App\\Models\\User',
                    'tokenable_id' => DB::raw('user_id')
                ]);
        }

        Schema::table('masterkey_sessions', function (Blueprint $table) {
            // Remove old user_id column after data migration
            if (Schema::hasColumn('masterkey_sessions', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('masterkey_sessions', function (Blueprint $table) {
            // Add back user_id column
            $table->bigInteger('user_id')->unsigned()->nullable()->after('status');
            $table->index('user_id');
        });

        // Migrate data back
        DB::table('masterkey_sessions')
            ->where('tokenable_type', '\\App\\Models\\User')
            ->update(['user_id' => DB::raw('tokenable_id')]);

        Schema::table('masterkey_sessions', function (Blueprint $table) {
            // Remove polymorphic columns
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
            $table->dropColumn(['tokenable_type', 'tokenable_id']);
        });
    }
};
