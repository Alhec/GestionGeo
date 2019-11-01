<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\CustomResetPassword;
use Illuminate\Support\Facades\Password;

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
        'mobile','work_phone','email','active','with_disabilities','nationality','sex','level_instruction','organization_id'
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
        return [
            'user' =>self::getUserById($this['id'],$this['user_type'],$this['organization_id']),
        ];

        // TODO: Implement getJWTCustomClaims() method.

    }

    public function teacher()
    {
        return $this->hasOne('App\Teacher');
    }

    public function student()
    {
        return $this->hasOne('App\Student')
            ->with('degrees')
            ->with('guideTeacher');
    }

    public function administrator()
    {
        return $this->hasOne('App\Administrator','id','id');
    }

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }

    public static function getUsers($userType,$organizationId)
    {
        $users = self::where('user_type',$userType)
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('id','=',$organizationId);
            });
        if ($userType == 'A'){
            return $users->with('administrator')
                ->get();
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
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('id','=',$organizationId);
            });
        if ($userType == 'A'){
            return $user
                ->with('administrator')
                ->get();
        }elseif ($userType=='T'){
            return $user
                ->with('teacher')
                ->get();
        }elseif ($userType=='S'){
            return $user
                ->with('student')
                ->get();
        }else{
            return [];
        }
    }
    public static function existUserById($id,$userType,$organizationId)
    {
        return self::where('id',$id)
            ->where('user_type',$userType)
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('id','=',$organizationId);
            })->exists();
    }
    public static function existUserByIdentification($identification,$userType,$organizationId)
    {
        return self::where('identification',$identification)
            ->where('user_type',$userType)
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('id','=',$organizationId);
            })->exists();
    }

    public static function existUserByEmail($email,$userType,$organizationId)
    {
        return self::where('email',$email)
            ->where('user_type',$userType)
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })->exists();
    }

    public static function addUser($user)
    {
        return self::insertGetId($user->only('identification', 'password','user_type','first_name','second_name',
            'first_surname','second_surname','telephone','mobile','work_phone','email','level_instruction','active','with_work',
            'with_disabilities','sex','nationality','organization_id'));
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
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })->get();
    }

    public static function getUserByEmail($email,$userType,$organizationId)
    {
        return self::where('email',$email)
            ->where('user_type',$userType)
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

    public static function updateUserLikeArray($id,$user)
    {
        self::find($id)
            ->update($user);
    }

    public static function getUsersActive($userType,$organizationId)
    {
        $users = self::where('user_type',$userType)
            ->where('active',true)
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            });
        if ($userType == 'A'){
            return $users->with('administrator')
                ->get();
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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
