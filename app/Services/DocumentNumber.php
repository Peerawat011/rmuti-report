<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocumentNumber
{
    /**
     * คำนำหน้าเลขที่เอกสาร แยกตามประเภทฟอร์ม
     * (เพิ่มฟอร์มใหม่ในอนาคต → เพิ่มบรรทัดที่นี่)
     */
    private const PREFIXES = [
        'training' => 'พบ.',   // แบบรายงานการพัฒนาบุคลากร
    ];

    /**
     * ออกเลขที่เอกสารถัดไป — รันต่อปี พ.ศ. แยกตามประเภท
     * เช่น "พบ. 0012/2569" (ใช้ transaction + lock กันเลขซ้ำ)
     */
    public static function next(string $formType = 'training'): string
    {
        $year = now()->year + 543;

        return DB::transaction(function () use ($formType, $year) {
            $counter = DB::table('document_counters')
                ->where('form_type', $formType)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($counter) {
                $number = $counter->last_number + 1;
                DB::table('document_counters')
                    ->where('id', $counter->id)
                    ->update(['last_number' => $number, 'updated_at' => now()]);
            } else {
                $number = 1;
                DB::table('document_counters')->insert([
                    'form_type'   => $formType,
                    'year'        => $year,
                    'last_number' => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            $prefix = self::PREFIXES[$formType] ?? 'อบ.';

            return sprintf('%s %04d/%d', $prefix, $number, $year);
        });
    }
}
