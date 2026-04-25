<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabReportItem extends Model
{
    protected $fillable = [
        'lab_report_id', 'lab_test_id', 'result', 'is_abnormal', 'notes',
    ];

    public function report()
    {
        return $this->belongsTo(LabReport::class, 'lab_report_id');
    }

    public function test()
    {
        return $this->belongsTo(LabTest::class, 'lab_test_id');
    }
}
