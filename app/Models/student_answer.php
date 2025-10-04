<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student_answer extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'question_id',
        'answer_text',
        'chosen_option_ids',
    ];

    protected $casts = [
        'chosen_option_ids' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class,'question_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}

