<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalSickDaysToCarryOverLeavesTable extends Migration
{
    public function up()
    {
        Schema::table('carry_over_leaves', function (Blueprint $table) {
            $table->float('personal_days')->default(0)->after('days');
            $table->float('sick_days')->default(0)->after('personal_days');
        });
    }

    public function down()
    {
        Schema::table('carry_over_leaves', function (Blueprint $table) {
            $table->dropColumn(['personal_days', 'sick_days']);
        });
    }
}
