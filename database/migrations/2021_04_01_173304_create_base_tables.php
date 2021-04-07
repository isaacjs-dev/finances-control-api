<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseTables extends Migration
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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->string('phone');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('users_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_wallet');
            $table->boolean('proprietary')->nullable(); //Sim ou Não
            $table->string('permissions', 16)->nullable(); // (0 - Prenião negada, usar o deleted_at), 1 - Ver, 2 - eitar, 3 controle
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('inflows', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wallet');
            $table->integer('id_category'); // Salario, Emprestimo...
            $table->float('value');
            $table->string('description');
            $table->integer('expected'); //Era privisto
            $table->integer('frequency'); //é recorente
            $table->timestamp('date');
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('outflows', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wallet');
            $table->integer('id_category'); //Alimentação, luz, agua...
            $table->float('value');
            $table->string('description');
            $table->integer('expected');
            $table->string('frequency');
            $table->string('id_type_pay'); //avista, cartão avista, cartão parcelado...
            $table->string('id_card')->nullable(); //1-visa...
            $table->string('link')->nullable(); // suar quando for pacelado
            $table->timestamp('date');
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('monthly_closing', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->string('id_wallet');
            $table->boolean('closed'); //1 - Sim || 2 - Não
            $table->string('balance_expected')->unique();
            $table->string('balance_reality')->unique();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('history_movement', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wallet');
            $table->integer('type_source'); //1 - interno / 2 - exteno
            $table->integer('tipo_movement'); //1 - entrada / 2 - saida
            $table->string('comments');
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('description');  // Salario, Emprestimo...
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('types_pay', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->boolean('installment'); //sim / não (parcelado)
            $table->boolean('card'); //sim / não
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->integer('id_wallet');
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('users_wallets');
        Schema::dropIfExists('inflows');
        Schema::dropIfExists('outflows');
        Schema::dropIfExists('monthly_closing');
        Schema::dropIfExists('history_movement');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('types_pay');
        Schema::dropIfExists('cards');
    }
}
