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
        // Refer to the Office model for info
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->string('name');
            $table->integer('price')->unsigned();
            $table->integer('office_count')->unsigned();
            $table->integer('table_count')->unsigned();
            $table->integer('area_size')->unsigned();
            
            $table->index('name');
            $table->index('price');
            $table->index('office_count');
            $table->index('table_count');
            $table->index('area_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
