<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identification', 'password','user_type','first_name','second_name','first_surname','second_surname','telephone',
        'mobile','work_phone','email','active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email_verified_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [
            'user_type' =>$this->user_type,
        ];
    }

    public function teacher()
    {
        return $this->hasOne('App\Teacher');
    }

    public function student()
    {
        return $this->hasOne('App\Student');
    }

    public function organization()
    {
        return $this->hasOne('App\OrganizationUser');
    }

    public static function getUsers($userType,$organizationId)
    {
        $users = self::where('user_type',$userType)
            ->with('organization')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            });
        if ($userType == 'A'){
            return $users->get();
        }elseif ($userType=='T'){
            return $users->with('teacher')
                ->get();
        }elseif ($userType=='S'){
            return $users->with('student')
                ->get();
        }else{
            return [];
        }
    }

    public static function getUserById($id,$userType,$organizationId)
    {
        $user =self::where('id',$id)
            ->where('user_type',$userType)
            ->with('organization')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            });
        if ($userType == 'A'){
            return $user->get();
        }elseif ($userType=='T'){
            return $user->with('teacher')
                ->get();
        }elseif ($userType=='S'){
            return $user->with('student')
                ->get();
        }else{
            return [];
        }
    }

    public static function existUserByIdentification($identification,$userType,$organizationId)
    {
        return self::where('identification',$identification)
            ->where('user_type',$userType)
            ->with('organization')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })->exists();
    }

    public static function existUserByEmail($email,$userType,$organizationId)
    {
        return self::where('email',$email)
            ->where('user_type',$userType)
            ->with('organization')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })->exists();
    }

    public static function addUser($user)
    {
        self::create($user->all());
    }

    public static function findUser($identification,$userType)
    {
        return self::where('identification',$identification)
            ->where('user_type',$userType)
            ->get();
    }

    public static function deleteUser($id)
    {
        self::find($id)
            ->delete();
    }

    public static function getUserByIdentification($identification,$userType,$organizationId)
    {
        return self::where('identification',$identification)
            ->where('user_type',$userType)
            ->with('organization')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })->get();
    }

    public static function getUserByEmail($email,$userType,$organizationId)
    {
        return self::where('email',$email)
            ->where('user_type',$userType)
            ->with('organization')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })->get();
    }

    public static function updateUser($id,$user)
    {
        self::find($id)
            ->update($user->all());
    }
}
