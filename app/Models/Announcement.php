<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'target_audience',
        'target_courses',
        'target_student_groups',
        'priority',
        'status',
        'posted_by',
        'published_at',
        'attachment_path',
        'attachment_name',
        'attachment_type',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'target_courses' => 'array',
            'target_student_groups' => 'array',
        ];
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'announcement_course');
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function getAttachmentUrl(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }
}
