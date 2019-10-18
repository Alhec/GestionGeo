<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 18/09/19
 * Time: 12:29 PM
 */

namespace App\Services;

use App\Http\Controllers\Controller;
//use Mail;
use App\User;
use App\Organization;
use Illuminate\Support\Facades\Mail;

class EmailService extends Controller
{
    public static function userCreate($id,$organizationId,$userType)
    {
        if ($organizationId == 'G'){
             $user= User::getUserById($id,$userType,$organizationId)[0];
             $organization=Organization::getOrganization($organizationId)[0];
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

            try{
                Mail::send('email.Geoquimica.emailTest',$data,function ($message) use ($user){
                    $message->to($user['email'], $user['first_name'])
                        ->subject('Usuario creado exitosamente');
                    //$message->from('noyala96@gmail.com','Creacion de usuario');
                });
            }
            catch(Exception $e){
                //Never reached
            }
            if (Mail::failures()) {
                return 0;
            }else{
                return 1;
            }
        }
    }
}
