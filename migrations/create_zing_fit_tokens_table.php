<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZingFitTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zing_fit_tokens', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('oauth_type');
            $table->string('access_token');
            $table->string('token_type');
            $table->string('refresh_token')->nullable();
            $table->string('scope');
            $table->boolean('active')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zing_fit_tokens');
    }
}
