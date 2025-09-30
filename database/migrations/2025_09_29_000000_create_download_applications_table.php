<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_year');
            $table->string('application_month');
            $table->string('name_en');
            $table->string('name_si');
            $table->string('name_ta');
            $table->string('file_path_en')->nullable();
            $table->string('file_path_si')->nullable();
            $table->string('file_path_ta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_applications');
    }
};


