<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === UserRole::Superadmin;
    }

    public function isJury(): bool
    {
        return $this->role === UserRole::Jury;
    }

    /** @return HasMany<FestivalJury, $this> */
    public function festivalJuries(): HasMany
    {
        return $this->hasMany(FestivalJury::class);
    }

    /** Juri yang mengikuti sebuah festival tertentu. */
    public function juriesInFestival(Festival $festival): HasMany
    {
        return $this->hasMany(FestivalJury::class)
            ->where('festival_id', $festival->id);
    }

    /** Subkategori yang dipilih juri ini pada sebuah festival. */
    public function participationsInFestival(Festival $festival): HasManyThrough
    {
        return $this->hasManyThrough(
            JurySubcategoryParticipation::class,
            FestivalJury::class,
            'user_id',
            'festival_jury_id',
        )->where('jury_subcategory_participations.festival_id', $festival->id);
    }

    /** @return HasMany<InnovationWinner, $this> */
    public function appointedInnovationWinners(): HasMany
    {
        return $this->hasMany(InnovationWinner::class, 'appointed_by');
    }

    /** @return HasMany<InstitutionWinner, $this> */
    public function appointedInstitutionWinners(): HasMany
    {
        return $this->hasMany(InstitutionWinner::class, 'appointed_by');
    }

    /** @return HasMany<Festival, $this> */
    public function publishedFestivals(): HasMany
    {
        return $this->hasMany(Festival::class, 'results_published_by');
    }

    /** @return HasMany<AuditLog, $this> */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }
}
