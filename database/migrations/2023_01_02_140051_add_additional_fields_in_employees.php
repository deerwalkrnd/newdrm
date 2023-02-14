<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsInEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('spouse_name')->nullable()->after('marital_status');
            $table->integer('pan_number')->nullable()->after('manager_change_date');
            $table->string('cit_number')->nullable()->after('pan_number');
            $table->bigInteger('ssf_id')->nullable()->after('cit_number');
            $table->string('nibl_account_number')->nullable()->after('ssf_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
}
