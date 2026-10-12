<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('companies', function (Blueprint $t) {
            $t->boolean('is_approved')->default(true)->after('description');
        });
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('company_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) { $t->dropConstrainedForeignId('company_id'); });
        Schema::table('companies', fn (Blueprint $t) => $t->dropColumn('is_approved'));
    }
};
