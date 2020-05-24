<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
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
        'identification','first_name','second_name','first_surname','second_surname','telephone', 'mobile','work_phone',
        'email','password','user_type','level_instruction','active','with_disabilities','sex','nationality',
        'organization_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email_verified_at','organization_id','user_type'
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
            'user' =>self::getUserById($this['id'],$this['user_type'],$this['organization_id'])[0],
        ];
    }

    public function teacher()
    {
        return $this->hasOne('App\Teacher','id','id');
    }

    public function student()
    {
        return $this->hasMany('App\Student')
            ->with('degrees')
            ->with('guideTeacher')
            ->with('equivalence')
            ->with('schoolProgram');
    }

    public function administrator()
    {
        return $this->hasOne('App\Administrator','id','id');
    }

    public static function getUsers($userType,$organizationId)
    {
        try{
            $users = self::where('user_type',$userType)
                ->where('organization_id',$organizationId);
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
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getUserById($id,$userType,$organizationId)
    {
        try{
            $user =self::where('id',$id)
                ->where('user_type',$userType)
                ->where('organization_id',$organizationId);
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
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existUserById($id,$userType,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->where('user_type',$userType)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existUserByIdentification($identification,$userType,$organizationId)
    {
        try{
            return self::where('identification',$identification)
                ->where('user_type',$userType)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existUserByEmail($email,$userType,$organizationId)
    {
        try{
            return self::where('email',$email)
                ->where('user_type',$userType)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addUser($user)
    {
        try{
            return self::insertGetId($user->only('identification','first_name','second_name','first_surname',
                'second_surname','telephone','mobile','work_phone','email', 'password','user_type','level_instruction',
                'active','with_work', 'with_disabilities','sex','nationality','organization_id'));
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteUser($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getUserByIdentification($identification,$userType,$organizationId)
    {
        try{
            return self::where('identification',$identification)
                ->where('user_type',$userType)
                ->where('organization_id',$organizationId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getUserByEmail($email,$userType,$organizationId)
    {
        try{
            return self::where('email',$email)
                ->where('user_type',$userType)
                ->where('organization_id',$organizationId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateUser($id,$user)
    {
        try{
            self::find($id)
                ->update($user->all());
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateUserLikeArray($id,$user)
    {
        try{
            self::find($id)
                ->update($user);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }

    }

    public static function getUsersActive($userType,$organizationId)
    {
        try{
            $users = self::where('user_type',$userType)
                ->where('active',true)
                ->where('organization_id',$organizationId);
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
        }catch (\Exception $e){
            return 0;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
