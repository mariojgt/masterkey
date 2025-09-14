
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('masterkey_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nonce', 64)->unique();
            $table->string('code', 6);
            $table->boolean('used')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('masterkey_verifications');
    }
};
