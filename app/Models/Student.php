<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
     use HasFactory;

    protected $fillable = [
        'exam_id',
        'candidate_name',
        'candidate_email',
        'candidate_contact',
        'candidate_city',
        'otp',
        'otp_expires_at',
    ];

    // Relationship: Student belongs to an Exam
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
