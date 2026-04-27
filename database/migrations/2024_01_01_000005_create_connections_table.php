<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->string('ip_address')->nullable();
            $table->string('pppoe_username')->unique()->nullable();
            $table->string('pppoe_password')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('onu_serial')->nullable();
            $table->boolean('online')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('connections'); }
};
