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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('poster_path')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            // $table->integer('category_id');
            $table->boolean('favorite')->default(false);

            $table->timestamps();

            $table->foreign('category_id', 'product_category_fk')->references('id')->on('categories')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
