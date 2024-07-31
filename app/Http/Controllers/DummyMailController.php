<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\DummyMail;
class DummyMailController extends Controller
{
    //
    public function __construct(){

    }

    public function index(){
        $mailData = array(
            'subject' => 'This is mail subject',
            'body' => 'This is mail body'
        );
        Mail::to('pawankayatm@gmail.com')->send(new DummyMail($mailData));
    }


}
