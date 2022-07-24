<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUsers extends Migration
{
    public function up()
    {
        Schema::table('users',function($table){
            $table->dropColumn('name');
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('address');
            $table->dropColumn('telephone');
        });
    }

    public function down()
    {
        Schema::table('users',function($table){
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('address');
            $table->string('telephone');
        });
    }
}
