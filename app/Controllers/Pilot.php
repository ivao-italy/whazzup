<?php namespace App\Controllers;

use App\Models\ClientLog;

class Pilot extends BaseController
{
    public function execute(){

        $PILOTS = new ClientLog();


        $FIip  = $PILOTS->select();
    }

}