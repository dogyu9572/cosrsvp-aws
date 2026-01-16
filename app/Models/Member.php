<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'login_id',
        'password',
        'name',
        'gender',
        'email',
        'phone_kr',
        'phone_local',
        'birth_date',
        'occupation',
        'major',
        'affiliation',
        'department',
        'position',
        'passport_number',
        'passport_expiry',
        'alien_registration_number',
        'alien_registration_expiry',
        'project_term_id',
        'course_id',
        'operating_institution_id',
        'project_period_id',
        'country_id',
        'hotel_name',
        'hotel_address',
        'hotel_address_detail',
        'training_period',
        'visa_type',
        'cultural_experience',
        'account_info',
        'insurance_status',
        'clothing_size',
        'dietary_restrictions',
        'special_requests',
        'departure_location',
        'arrival_location',
        'entry_date',
        'exit_date',
        'entry_flight',
        'exit_flight',
        'ticket_file',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'passport_expiry' => 'date',
        'alien_registration_expiry' => 'date',
        'entry_date' => 'date',
        'exit_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * 비밀번호를 해시화하여 저장
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * 프로젝트 기수와의 관계
     */
    public function projectTerm(): BelongsTo
    {
        return $this->belongsTo(ProjectTerm::class);
    }

    /**
     * 과정과의 관계
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * 운영기관과의 관계
     */
    public function operatingInstitution(): BelongsTo
    {
        return $this->belongsTo(OperatingInstitution::class);
    }

    /**
     * 프로젝트기간과의 관계
     */
    public function projectPeriod(): BelongsTo
    {
        return $this->belongsTo(ProjectPeriod::class);
    }

    /**
     * 국가와의 관계
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * 회원비고와의 관계
     */
    public function memberNotes(): HasMany
    {
        return $this->hasMany(MemberNote::class);
    }

    /**
     * 제출서류와의 관계
     */
    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class);
    }

    /**
     * 수정로그와의 관계
     */
    public function modificationLogs(): HasMany
    {
        return $this->hasMany(MemberModificationLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * 알림과의 관계
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * 주소록과의 관계 (Many-to-Many)
     */
    public function mailAddressBooks(): BelongsToMany
    {
        return $this->belongsToMany(MailAddressBook::class, 'mail_address_book_members', 'member_id', 'address_book_id')
            ->withTimestamps();
    }

    /**
     * 주소록과의 관계 (Many-to-Many)
     */
    public function addressBooks(): BelongsToMany
    {
        return $this->belongsToMany(MailAddressBook::class, 'mail_address_book_members', 'member_id', 'address_book_id')
            ->withTimestamps();
    }

    /**
     * 프로젝트 기수로 필터링
     */
    public function scopeByProjectTerm($query, $projectTermId)
    {
        return $query->where('project_term_id', $projectTermId);
    }

    /**
     * 과정으로 필터링
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * 국가로 필터링
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * 활성화된 회원만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
