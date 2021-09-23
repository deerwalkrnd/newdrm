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
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('leave_type')->constrained('leave_types');
            $table->enum('paid_unpaid',['paid','unpaid']);
            $table->integer('days');
            $table->integer('year'); //might change later
            $table->enum('status',['active','disabled']);
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
