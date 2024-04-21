<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "bootstrap.php";
require "Helper.php";

use Illuminate\Support\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
// use Illuminate\Support\Facades\Request;


class LoginController
{
    protected $request;
    public function __construct()
    {
        $this->request = \Illuminate\Http\Request::capture();
    }
    public function validate(){
        $request = $this->request;

        $verify = $this->verifyRequest($request);
        if(!$verify)
        {
            throw new Exception("Please fill in user ID and password !!", 400);
            return;
        }
        
        $query = UserTbl::select('userid','userpass','usersecure','usergroup','username',
                    'useremail','useremail1','useremail2')
                    ->where('UserId', $request->userid)
                    ->where('userPass', $request->password);
        $validating = $query->count();
        
        // $query = Helper::getEloquentSqlWithBindings($validating);
        // throw new Exception($query, 400);
        // return;
        if(!$validating){
            throw new Exception("User ID or Password is wrong !!", 400);
            // return [
            //     'success' => false,
            //     'message' => "User ID or Password is wrong !!"
            // ];
        }
        
        // try {
            //code...
            $data = $query->first();
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return [
        //         'success' => false,
        //         'message' => $th
        //     ];
        //     // throw new Exception("User ID or Password is wrong !!", 400);
        // }
        

        // throw new Exception($data, 400);
        // return;
        $this->createSession($data);
        $this->logging($data);
         return [
                'success' => true,
                'message' => "Login Successful"
         ];
    }

    public function createSession($data)
    {
        $_SESSION['usr'] = $data->userid;
        $_SESSION['usrsecure'] = $data->usersecure;
        $_SESSION['usrgroup'] = $data->usergroup;
        $_SESSION['usrname'] = $data->username;
        $_SESSION['usrmail'] = $data->useremail;
    }

    public function verifyRequest($request)
    {
        $verify = false;
        if(@$request->userid == '')
        {
            return false;
        }
        if(@$request->password == '')
        {
            return false;
        }
        if(isset($request->userid) && isset($request->password))
        {
            return true;
        }
        else{
            return false;
        }
                
    }

    public function logging($data)
    {

        $waktu = gmdate("Y-m-d H:i:s");
        $namaserver = $_SERVER['SERVER_NAME'];
        $namaclient = $_SERVER['REMOTE_ADDR'];

        $log = new Log;
        $log->userid = $data->userid;
        $log->waktu = $waktu;
        $log->ipserver = $namaserver;
        $log->ipclient = $namaclient;
        
        try {
            //code...
            $log->save();   
            return true;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 400);
            return false;
        }

    }
}
