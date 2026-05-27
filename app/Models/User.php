<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<InventoryMovement, $this>
     */
    public function inventoryMovementsCreated(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<CashRegister, $this>
     */
    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    /**
     * @return HasMany<Sale, $this>
     */
    public function salesCreated(): HasMany
    {
        return $this->hasMany(Sale::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<Sale, $this>
     */
    public function salesCanceled(): HasMany
    {
        return $this->hasMany(Sale::class, 'canceled_by_user_id');
    }

    /**
     * @return HasMany<CashMovement, $this>
     */
    public function cashMovementsCreated(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendancesRecorded(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by_user_id');
    }

    /**
     * @return HasMany<Evaluation, $this>
     */
    public function evaluationsCreated(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<GradeRecord, $this>
     */
    public function gradeRecordsRecorded(): HasMany
    {
        return $this->hasMany(GradeRecord::class, 'recorded_by_user_id');
    }

    /**
     * @return HasMany<TeacherAssignment, $this>
     */
    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    /**
     * @return HasOne<Student, $this>
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * @return HasOne<UserNotificationPreference, $this>
     */
    public function notificationPreference(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }
}
