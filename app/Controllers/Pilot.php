<?php namespace App\Controllers;

use App\Models\ClientLog;
use CodeIgniter\I18n\Time;

class Pilot extends BaseController
{
    public function execute(){

        $PILOTS = new ClientLog();


        $TIME = Time::now('UTC');
        $pastDate = $TIME->addHours('-24')->format('Y-m-d H:i:s');
        $FIip  = $PILOTS->getDistinctFPid(['connTime< ' => $pastDate]);


        //var_dump($FIip);

        foreach ($FIip as $singleConnection) {

            $firstRowOnGround = $PILOTS->where(['FPid'=> $singleConnection->FPid, 'onGround' => 1])->orderBy('timestamp', 'ASC')->first();
            $firstRowOnAir    = $PILOTS->where(['FPid'=> $singleConnection->FPid, 'onGround' => 0])->orderBy('timestamp', 'ASC')->first();
            $lastRowOnAir     = $PILOTS->where(['FPid'=> $singleConnection->FPid, 'onGround' => 0])->orderBy('timestamp', 'DESC')->first();
            $lastRowOnGround  = $PILOTS->where(['FPid'=> $singleConnection->FPid, 'onGround' => 1])->orderBy('timestamp', 'DESC')->first();

            if (!empty($firstRowOnGround) && !empty($firstRowOnAir) && !empty($lastRowOnAir) && !empty($lastRowOnGround) ){

                //Seleziono per ogni FPid soltanto  voli che risultano con prima e ultima riga con onground 1
               // foreach ($singleConnection as $item) {

                    $totalOnGround = $this->timeCalculator($firstRowOnGround->timestamp, $lastRowOnGround->timestamp);
                    $totalOnAir = $this->timeCalculator($firstRowOnAir->timestamp, $lastRowOnAir->timestamp);
                    echo $singleConnection->FPid . ' ' .  ' ' . $firstRowOnGround->onGround . ' ' . $totalOnGround . ' <strong>' . $totalOnAir . '</strong><br>';

               // }

            }

        }


    }

    private function timeCalculator($firstDate, $lastDate, $output = 's'){
        $TIME1 = Time::createFromFormat('Y-m-d H:i:s', $firstDate);
        $TIME2 = Time::createFromFormat('Y-m-d H:i:s', $lastDate);

        if ($firstDate >= $lastDate){
            $total = $TIME2->getTimestamp() - $TIME1->getTimestamp();
            return $total;
        }
        else{
            $total = $TIME2->getTimestamp() - $TIME1->getTimestamp();
            return $total;
        }
    }


}