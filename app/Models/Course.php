<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'base_price',
        'academic_year',
        'term',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function registeredStudents()
    {
        return $this->belongsToMany(Student::class, 'course_registrations')
            ->withPivot('academic_year', 'term', 'registration_date', 'status', 'notes')
            ->withTimestamps();
    }
}
