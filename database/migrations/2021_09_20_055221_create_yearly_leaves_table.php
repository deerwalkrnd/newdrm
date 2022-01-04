<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYearlyLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yearly_leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('version')->default('0');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->integer('days');
            $table->enum('status',['active','disabled'])->default('active');
            $table->integer('year')->default(date('Y'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yearly_leaves');
    }
}
