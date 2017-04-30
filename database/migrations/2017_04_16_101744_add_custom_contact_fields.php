<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomContactFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->string('custom_contact_label1')->nullable();
            $table->string('custom_contact_label2')->nullable();
        });

        Schema::table('contacts', function ($table) {
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
        });

        Schema::table('payment_methods', function ($table) {
            $table->unsignedInteger('account_gateway_token_id')->nullable()->change();
            $table->dropForeign('payment_methods_account_gateway_token_id_foreign');
        });

        Schema::table('payment_methods', function ($table) {
            $table->foreign('account_gateway_token_id')->references('id')->on('account_gateway_tokens')->onDelete('cascade');
        });

        Schema::table('payments', function ($table) {
            $table->dropForeign('payments_payment_method_id_foreign');
        });

        Schema::table('payments', function ($table) {
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });

        Schema::table('expenses', function($table) {
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->boolean('invoice_documents')->default(true);
        });

        // remove duplicate annual frequency
        if (DB::table('frequencies')->count() == 9) {
            DB::statement('update invoices set frequency_id = 8 where is_recurring = 1 and frequency_id = 9');
            DB::statement('update accounts set reset_counter_frequency_id = 8 where reset_counter_frequency_id = 9');
            DB::statement('update frequencies set name = "Annually" where id = 8');
            DB::statement('delete from frequencies where id = 9');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('custom_contact_label1');
            $table->dropColumn('custom_contact_label2');
        });

        Schema::table('contacts', function ($table) {
            $table->dropColumn('custom_value1');
            $table->dropColumn('custom_value2');
        });

        Schema::table('expenses', function($table) {
            $table->dropColumn('payment_type_id');
            $table->dropColumn('payment_date');
            $table->dropColumn('transaction_reference');
            $table->dropColumn('invoice_documents');
        });
    }
}