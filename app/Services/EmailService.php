<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 18/09/19
 * Time: 12:29 PM
 */

namespace App\Services;

use App\Http\Controllers\Controller;
use App\User;
use App\Organization;
use Illuminate\Support\Facades\Mail;

class EmailService extends Controller
{
    public static function createUserICT($id,$userType,$organizationId)
    {
        $user= User::getUserById($id,$userType,$organizationId);
        if (is_numeric($user)&&$user==0){
            return 0;
        }
        $user=$user[0];
        $organization=Organization::getOrganizationById($organizationId);
        if (is_numeric($organization)&&$organization==0){
            return 0;
        }
        $organization=$organization[0];
        $data['name']=$user['first_name'];
        $data['organization']=$organizationId;
        switch ($user['user_type']){
            case 'S':
                $data['profile']='Estudiante';
                break;
            case 'T':
                $data['profile']='Profesor';
                break;
            case 'A':
                $data['profile']='Administrador';
                break;
            default:
                break;
        }
        $data['web']=$organization['website'];
        Mail::send('email.Geoquimica.emailCreateUser',$data,function ($message) use ($user){
            $message->to($user['email'], $user['first_name'])
                ->subject('Usuario creado exitosamente');
        });
        if (Mail::failures()) {
            return 0;
        }else{
            return 1;
        }
    }

    public static function userCreate($id,$organizationId,$userType)
    {
        switch ($organizationId){
            case 'ICT':
                return self::createUserICT($id,$userType,$organizationId);
                break;
            default:
                return 1;
        }
    }

}
