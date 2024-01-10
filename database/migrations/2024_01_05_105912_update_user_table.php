<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string("poster_path")->nullable();
            $table->unsignedBigInteger('role_of_user_id')->nullable();

            $table->foreign('role_of_user_id', 'user_of_role_fk')->references('id')->on('role_of_users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('user_of_role_fk');
            $table->dropColumn(['role_of_user_id']);

        });
    }
};
