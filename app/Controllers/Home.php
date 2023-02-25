<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
		//echo 'asce re';die();
        return view('home/home');
    }

    public function vdHowItWorks()
    {
        $FaqClass=new \FaqClass();
        $data['video_help'] = $FaqClass->videoSerialize(11, 'video_helps'); //rakesh
        $data['video_help_serial'] = 11;

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('howWorkVd',$data);
    }
}
