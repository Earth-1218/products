<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->boolean('name')->default(true);
            $table->boolean('price')->default(true);
            $table->boolean('sku')->default(true);
            $table->boolean('details')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preferences');
    }
}
