<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateTransactionsTable extends Migration
    {
        /* SAM: 202010809: Accept key, value */

        protected $fillable = ['key', 'value'];

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('transactions', function (Blueprint $table)
            {
                $table->id();
                /* <START> SAM: 20210809: Add key, value fields */
                $table->string('key');
                $table->string('value');
                /* <END> SAM: 20210809: Add key, value fields */
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
            Schema::dropIfExists('transactions');
        }

    }
