<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // create -> сохтан
        // table -> обновить
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("phone_number")->nullable();
            $table->string("password")->nullable();
            $table->string("email");
            $table->dateTime("created_at")->null1able();
            $table->dateTime("updated_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};
