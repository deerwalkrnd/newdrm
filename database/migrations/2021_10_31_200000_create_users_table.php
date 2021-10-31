<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('version')->default('0');
            $table->enum('account_expired',['1','0'])->default('0');
            $table->enum('account_locked',['1','0'])->default('0');
            $table->foreignId('organization_id')->constrained('organizations')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->unique();
            $table->foreignId('role_id')->constrained('roles')->unique();
            $table->enum('enable',['1','0'])->default('1');
            $table->string('password');
            $table->enum('password_expired',['1','0'])->default('0');
            $table->string('username')->unique();
            $table->timestamps();
            // $table->foreignId('guest_id')->constrained('guest')->nullable();
            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
