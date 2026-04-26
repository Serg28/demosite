<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CMS auth tables: roles, role_users, activations, persistences, reminders, throttle.
 * user_id uses unsignedBigInteger to match Laravel users.id (bigint unsigned).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->string('slug', 50)->unique();
                $table->string('name');
                $table->text('permissions');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('role_users')) {
            Schema::create('role_users', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->unsignedBigInteger('user_id');
                $table->unsignedInteger('role_id');
                $table->timestamps();
                $table->primary(['user_id', 'role_id']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('activations')) {
            Schema::create('activations', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->unsignedBigInteger('user_id');
                $table->string('code');
                $table->tinyInteger('completed')->default(0);
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('persistences')) {
            Schema::create('persistences', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->unsignedBigInteger('user_id');
                $table->string('code');
                $table->timestamps();
                $table->index('user_id');
                $table->index('code');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('reminders')) {
            Schema::create('reminders', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->unsignedBigInteger('user_id');
                $table->string('code');
                $table->tinyInteger('completed')->default(0);
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('throttle')) {
            Schema::create('throttle', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('type');
                $table->string('ip', 50);
                $table->timestamps();
                $table->index('user_id');
                $table->index(['type', 'ip', 'created_at']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('throttle');
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('persistences');
        Schema::dropIfExists('activations');
        Schema::dropIfExists('role_users');
        Schema::dropIfExists('roles');
    }
};
