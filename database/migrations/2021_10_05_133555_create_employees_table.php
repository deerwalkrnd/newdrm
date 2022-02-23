<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('version')->default(0);
            // personal details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('employee_id');
            $table->date('date_of_birth');
            $table->string('marital_status');
            $table->string('gender');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('grand_father')->nullable();
            $table->string('mobile');
            $table->string('alternative_mobile')->nullable();
            $table->string('home_phone')->nullable();
            // image missing ->nullable()
            $table->string('image_name')->nullable();
            $table->string('alter_email');
            $table->string('cv_file_name')->nullable();
            $table->string('country');
            $table->string('nationality')->nullable();
            $table->string('profile')->nullable();
            $table->string('blood_group')->nullable();

            //permanent address
            // $table->foreign('permanent_address')->references('id')->on('provinces'); //foreign id
            // $table->foreignId('permanent_district')->constrained('districts'); //foreign id
            $table->foreignId('permanent_address')->constrained('provinces');
            $table->foreignId('permanent_district')->constrained('districts');
            $table->string('permanent_municipality');
            $table->string('permanent_ward_no');
            $table->string('permanent_tole');
            $table->enum('temp_add_same_as_per_add',[0,1]);
            
            //temporary address
            $table->foreignId('temporary_address')->nullable()->constrained('provinces');
            $table->foreignId('temporary_district')->nullable()->constrained('districts');;
            $table->string('temporary_municipality')->nullable();
            $table->string('temporary_ward_no')->nullable();
            $table->string('temporary_tole')->nullable();

            //official detail
            // $table->string('domain_user_name');
            //password

            $table->date('join_date');
            $table->date('intern_trainee_ship_date')->nullable();
            $table->foreignId('service_type')->constrained('service_types');
            // $table->foreignId('service_type')->constrained('service_types')->onDelete('restrict');
            //service change date missing
            $table->foreignId('manager_id')->nullable()->constrained('employees');// manager missing->nullable()
            //manager_change_date //not needed
            $table->foreignId('designation_id')->constrained('designations'); //foreign
            $table->date('designation_change_date')->nullable();
            //tier level missing
            //tier missing
            // $table->string('tier_change_date');
            
            // $table->foreignId('squad_id')->nullable(); //foreignId
            $table->foreignId('organization_id')->constrained('organizations'); //foreign id
            $table->foreignId('unit_id')->constrained('units'); //foreign
            //unit change date missing

            // $table->foreignId('role')->constrained('roles');
            $table->string('email');
            $table->foreignId('shift_id')->constrained('shifts');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('contract_status', ['active','terminated'])->default('active');
            $table->string('remarks')->nullable();


            //emergency contact seperate model
            
            $table->foreignId('department_id')->constrained('departments'); //foreign
            // $table->string('filename');
            // $table->date('propmotion_date');
            // $table->string('status');
            // $table->foreignId('supervisor_id'); //foreign
            // $table->date('updated_join_date');
            // $table->integer('volunteer_days');
            // $table->string('work_phone');
            // $table->integer('probation_days');
            // $table->string('grade_reward');
            // $table->string('account_number');
            // $table->string('cit');
            // $table->string('insurance_premium_amount');
            // $table->string('is_doc');
            // $table->string('council_number');
            // $table->string('is_coordinator');
            // $table->string('is_patient');
            // $table->string('to_date');
            // $table->string('skype_id');
            $table->date('terminated_date')->nullable();
            $table->string('department_change_date')->nullable();
            $table->string('manager_change_date')->nullable();
            // $table->string('from_time');
            // $table->string('to_time');
            // $table->foreignId('tier_employee_id'); //foreign_id
            // $table->string('fullname');
            // $table->string('show_information');

            // $table->string('first_login');
            // $table->string('emp_id');
            // $table->string('document_date');
            // $table->foreignId('provincep_id'); //foreignId
            // $table->foreignId('provincet_id'); //foreignId            
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
        Schema::dropIfExists('employees');
    }
}
