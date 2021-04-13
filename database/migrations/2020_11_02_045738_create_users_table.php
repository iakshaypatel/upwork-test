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
            $table->foreignId('invited_by')->nullable();
            $table->string('name',100);
            $table->string('user_name',100)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image',100)->default(DEFAULT_IMG_NAME);
            $table->unsignedTinyInteger('user_role')->default(2)->comment('1=Admin, 2=User');
            $table->dateTime('registered_at');
            $table->string('api_token',255)->nullable();
            $table->unsignedTinyInteger('device_type')->nullable()->comment('1=Android,2=IOS');
            $table->string('device_token',1000)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
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
