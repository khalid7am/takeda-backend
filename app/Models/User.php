<?php

namespace App\Models;

use App\Types\RoleType;
use App\Traits\HasUuid;
use Illuminate\Support\Str;
use App\Helpers\AppHelpers;
use App\Services\RankingService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasUuid;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_lecturer' => 'boolean',
    ];

    public function author()
    {
        return $this->hasOne(Author::class);
    }

    public function getIsAuthorVerifiedAttribute()
    {
        if ($this->is_author_or_admin) {
            if (!is_null($this->author)) {
                if (!is_null($this->author->alkalmazott_tag) &&
                    !is_null($this->author->reszvenytulajdon) &&
                    !is_null($this->author->eloadoi_dij) &&
                    !is_null($this->author->testuleti_reszvetel) &&
                    !is_null($this->author->konzultacios_szerzodes) &&
                    !is_null($this->author->tovabbkepzesi_hozzajarulas)) {
                    
                    return true;
                }
            }
        }

        return false;
    }

    public function getUserPerformancePoints($date = null)
    {
        // INIT
        $point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD
        $point = (new UserService)->getUserPerformancePoints($this, $date);

        return $point;
    }

    public function getOverallPerformancePointsAttribute()
    {
        $point = (new UserService)->getUserPerformancePointsOverallAndCurrentMonth($this);

        return $point;
    }

    public function getUserPerformancePointsAttribute()
    {
        // INIT
        $point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD
        $point = (new UserService)->getUserPerformancePoints($this);

        return $point;
    }

    public function getAuthorPerformancePointsAttribute()
    {
        // INIT
        $point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD
        $point = (new UserService)->getAuthorPerformancePoints($this);

        return $point;
    }

    public function getPerformancePointsAttribute()
    {
        // INIT
        $point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD

        if ($this->is_user) {
            $point = (new UserService)->getUserPerformancePoints($this);
        // COMMENT OUT THE NEXT ELSEIF LINE
        // IN CASE ADMINS CANT BE PRESENTED ON LEADERBOARD
        } elseif ($this->is_author_or_admin) {
            // IN CASE ADMINS CANT BE PRESENTED ON LEADERBOARD
            // COMMENT OUT THE NEXT ELSEIF LINE
            //} elseif ($this->is_author) {
            $point = (new UserService)->getAuthorPerformancePoints($this);
        }

        return $point;
    }

    public function getRankTitleAttribute()
    {
        $title = $this->static_title. ' ' . $this->dynamic_position_name;

        return $title;
    }

    public function getRankIconsAttribute()
    {
        $man = true;


        if ($this->is_user) {
            $dynamicRank = (new RankingService)->getUserDynamicRankingValues($this);

            if ($man) {
                $rank = $dynamicRank['unique'];
            } else {
                $rank = isset($dynamicRank['unique_woman'])
                ? $dynamicRank['unique_woman']
                : $dynamicRank['unique'];
            }
        } elseif ($this->is_author_or_admin) {
            $rank = (new RankingService)->getAuthorDynamicRankingValues($this)['unique'];
        }

        $color = (new RankingService)->getStaticRankingValues($this)['unique'];


        if (!Str::contains($rank, '_100')) {
            $url = (new RankingService)->getIconByRank($color, $rank);
        } else {
            $url = [];
        }

        return $url;
    }


    public function getStaticTitleAttribute()
    {
        return (new RankingService)->getStaticRankingValues($this)['title'];
    }

    public function getStaticColorAttribute()
    {
        return (new RankingService)->getStaticRankingValues($this)['color'];
    }

    public function getUserCurrentPositionPercentageAttribute()
    {
        return (new RankingService)->getNormalUserRanking($this->id);
    }

    public function getAuthorCurrentPositionPercentageAttribute()
    {
        return (new RankingService)->getAuthorUserRanking($this->id);
    }

    public function getCurrentPositionPercentageAttribute()
    {
        $percentage = 0;
        if ($this->is_user) {
            $percentage = (new RankingService)->getNormalUserRanking($this->id);
        } elseif ($this->is_author_or_admin) {
            $percentage = (new RankingService)->getAuthorUserRanking($this->id);
        }

        return $percentage;
    }

    public function getCurrentPositionAttribute()
    {
        $position = 0;
        if ($this->is_user) {
            $position = (new RankingService)->getNormalUserPosition($this->id)['place'];
        } elseif ($this->is_author_or_admin) {
            $position = (new RankingService)->getAuthorUserPosition($this->id)['place'];
        }

        return $position;
    }

    public function getOvertookedUsersAttribute()
    {
        $overtTookedusersCount = 0;
        if ($this->is_user) {
            $overtTookedusersCount = (new RankingService)->getNormalUserPosition($this->id)['overtTookedUsers'];
        } elseif ($this->is_author_or_admin) {
            $overtTookedusersCount = (new RankingService)->getAuthorUserPosition($this->id)['overtTookedUsers'];
        }

        return $overtTookedusersCount;
    }

    public function getDynamicPositionNameAttribute()
    {
        if ($this->is_user) {
            $dynamicRank = (new RankingService)->getUserDynamicRankingValues($this);
        } elseif ($this->is_author_or_admin) {
            $dynamicRank = (new RankingService)->getAuthorDynamicRankingValues($this);
        }

        // TODO
        // SET MAN/WOMAN VARIABLE
        $man = true;

        if ($man) {
            $name = $dynamicRank['name'];
        } else {
            $name = isset($dynamicRank['name_woman'])
            ? $dynamicRank['name_woman']
            : $dynamicRank['name'];
        }

        return $name;
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id', 'id');
    }

    public function answeredArticles()
    {
        return $this->hasMany(ArticleUserAnswered::class, 'user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answers::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function userRoleChangeRequests()
    {
        return $this->hasMany(UserRoleChangeRequest::class, 'user_id', 'id');
    }

    public function preferences()
    {
        return $this->belongsToMany(Preference::class, UserPreference::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'user_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(ArticleView::class, 'user_id', 'id');
    }

    public function downloads()
    {
        return $this->hasMany(ArticleDownload::class, 'user_id', 'id');
    }

    public function authorArticleViews()
    {
        return $this->hasManyThrough(ArticleView::class, Article::class, 'author_id', 'article_id');
    }

    public function authorArticleDownloads()
    {
        return $this->hasManyThrough(ArticleDownload::class, Article::class, 'author_id', 'article_id');
    }

    public function authorArticleReviews()
    {
        return $this->hasManyThrough(Review::class, Article::class, 'author_id', 'article_id');
    }

    public function passwordResets()
    {
        return $this->hasMany(PasswordReset::class, 'email', 'id');
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }

    public function scopeNotAccepted($query)
    {
        return $query->whereNull('accepted_at');
    }

    public function scopeRejected($query)
    {
        return $query->whereNotNull('rejected_at');
    }

    public function scopeNotRejected($query)
    {
        return $query->whereNull('rejected_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('rejected_at')->whereNull('accepted_at');
    }

    public function scopeNotPending($query)
    {
        return $query->where(function ($query) {
            $query->whereNotNull('rejected_at')->orWhereNotNull('accepted_at');
        });
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($query) use ($searchTerm) {
            return $query->where('name', 'LIKE', "%$searchTerm%")
                         ->orWhere('email', 'LIKE', "%$searchTerm%")
                         ->orWhere('institution', 'LIKE', "%$searchTerm%");
        });
    }

    public function scopeAuthors($query)
    {
        $roleId = \DB::table('roles')->select('id')->where('code', RoleType::EDITOR)->first()->id;

        return $query->where('role_id', $roleId);
    }

    public function scopeNormals($query)
    {
        $roleId = \DB::table('roles')->select('id')->where('code', RoleType::USER)->first()->id;

        return $query->where('role_id', $roleId);
    }

    public function scopeSuperadmins($query)
    {
        $roleId = \DB::table('roles')->select('id')->where('code', RoleType::SUPERADMIN)->first()->id;

        return $query->where('role_id', $roleId);
    }

    public function scopeEditors($query)
    {
        $roleIds = \DB::table('roles')->whereIn('code', [RoleType::SUPERADMIN, RoleType::ADMIN, RoleType::EDITOR])->pluck('id');

        return $query->whereIn('role_id', $roleIds);
    }

    public function scopeLecturers($query)
    {
        return $query->where('is_lecturer', true);
    }

    public function scopeAdmins($query)
    {
        $roleIds = \DB::table('roles')->where('code', RoleType::SUPERADMIN)->OrWhere('code', RoleType::ADMIN)->pluck('id');

        return $query->whereIn('role_id', $roleIds);
    }

    public function scopeNotAdmins($query)
    {
        $roleIds = \DB::table('roles')->where('code', RoleType::USER)->OrWhere('code', RoleType::EDITOR)->pluck('id');

        return $query->whereIn('role_id', $roleIds);
    }

    public function isActive()
    {
        return ! empty($this->accepted_at);
    }

    public function getPasswordResetToken()
    {
        if ($this->passwordResets()->valid()->exists()) {
            return $this->passwordResets()->valid()->first()->token;
        } else {
            return PasswordReset::create([
                'email' => $this->email,
                'token' => Str::random(16),
            ])->token;
        }
    }

    public function accept()
    {
        $this->update([
            'accepted_at' => now(),
            'rejected_at' => null,
            'accepter_id' => auth()->id(),
            'rejecter_id' => null,
        ]);
    }

    public function reject()
    {
        $this->update([
            'accepted_at' => null,
            'rejected_at' => now(),
            'accepter_id' => null,
            'rejecter_id' => auth()->id(),
        ]);
    }

    public function pend()
    {
        $this->update([
            'accepted_at' => null,
            'rejected_at' => null,
            'accepter_id' => null,
            'rejecter_id' => null,
        ]);
    }

    public function getStatusText()
    {
        if (! empty($this->deleted_at)) {
            return 'deleted';
        } elseif (! empty($this->accepted_at)) {
            return 'accepted';
        } elseif (! empty($this->rejected_at)) {
            return 'rejected';
        } else {
            return 'pending';
        }
    }

    public function hasPermissionFor($roleCode)
    {
        $roleMatrix = AppHelpers::getRoleMatrix();

        return in_array($this->role->code, $roleMatrix[$roleCode]);
    }

    public function isAdmin()
    {
        return in_array($this->role->code, [RoleType::SUPERADMIN, RoleType::ADMIN]);
    }

    public function getIsSuperadminAttribute()
    {
        return $this->role->code == RoleType::SUPERADMIN;
    }

    public function getIsAdminAttribute()
    {
        return $this->role()->whereIn('code', [RoleType::SUPERADMIN, RoleType::ADMIN])->exists();
    }

    public function getIsUserAttribute()
    {
        return $this->role()->where('code', RoleType::USER)->exists();
    }

    public function getIsAuthorAttribute()
    {
        return $this->role()->where('code', RoleType::EDITOR)->exists();
    }

    public function getIsAuthorOrAdminAttribute()
    {
        return $this->role()->whereIn('code', [RoleType::SUPERADMIN, RoleType::ADMIN, RoleType::EDITOR])->exists();
    }

    public function loggedIn()
    {
        $this->sessions()->create([
            'login_at' => now(),
        ]);
    }

    public function loggedOut()
    {
        $session = $this->sessions->last();
        if ($session) {
            $session->update([
                'logout_at' => now(),
            ]);
        }
    }

    public function adminAcceptedUsers()
    {
        return $this->whereNotNull('accepted_at')->where('accepter_id', $this->id);
    }

    public function adminRejectedUsers()
    {
        return $this->whereNotNull('rejected_at')->where('rejecter_id', $this->id);
    }

    public function adminDeletedComments()
    {
        return $this->hasMany(Comment::class, 'deleter_id', 'id')->withTrashed();
    }

    public function adminPublishedArticles()
    {
        return $this->hasMany(Article::class, 'publisher_id', 'id');
    }

    public function adminRejectedArticles()
    {
        return $this->hasMany(Article::class, 'deleter_id', 'id')->withTrashed();
    }

    public function getIsOnlineAttribute()
    {
        $session = $this->sessions()->latest('login_at')->first();
        if ($session) {
            return $session->is_online();
        }

        return false;
    }
}
