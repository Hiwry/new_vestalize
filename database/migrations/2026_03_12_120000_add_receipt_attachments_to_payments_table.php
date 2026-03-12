<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'receipt_attachments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->json('receipt_attachments')->nullable()->after('receipt_attachment');
            });
        }

        $payments = DB::table('payments')
            ->select('id', 'receipt_attachment', 'receipt_attachments')
            ->whereNotNull('receipt_attachment')
            ->get();

        foreach ($payments as $payment) {
            if (!empty($payment->receipt_attachments)) {
                continue;
            }

            DB::table('payments')
                ->where('id', $payment->id)
                ->update([
                    'receipt_attachments' => json_encode([[
                        'path' => $payment->receipt_attachment,
                        'name' => basename($payment->receipt_attachment),
                        'uploaded_at' => null,
                    ]]),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'receipt_attachments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('receipt_attachments');
            });
        }
    }
};
