<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('tech_id')->nullable();
            $table->text('equipo');
            $table->text('marca')->nullable();
            $table->longText('accesorios')->nullable();
            $table->longText('falla');
            $table->longText('notas')->nullable();
            $table->float('monto_estimado', 8, 2, true);
            $table->date('date_repair')->nullable();
            $table->date('date_delivery')->nullable();
            $table->boolean('delivery')->default(0);
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
        Schema::dropIfExists('services');
    }
};
