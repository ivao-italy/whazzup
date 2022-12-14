<?php namespace App\Controllers;

use App\Models\ClientLog;
use App\Models\PilotLogHistory;
use CodeIgniter\I18n\Time;

class Pilot extends BaseController
{

    /**
     * La funzione processa i dati nel CLientLog e inserisce i dati nel pilotLog e cancella i dati nel clientLog.
     * Gira ogni notte alle 3:00
     * @return void
     * @throws \ReflectionException
     */
    public function insertPilotLog(){

        $PILOTS = new ClientLog();
        $PILOTLOG = new PilotLogHistory();


        $TIME = Time::now('UTC');
        $pastDate = $TIME->addHours('-24')->format('Y-m-d H:i:s');
        $FIip  = $PILOTS->getDistinctFPid(['connTime< ' => $pastDate]);



        foreach ($FIip as $singleConnection) {

            $firstRowOnGround = $PILOTS
                ->where([
                    'FPid'=> $singleConnection->FPid,
                    'onGround' => 1])
                ->orderBy('timestamp', 'ASC')
                    ->first();
            if (!empty($firstRowOnGround)){
                $firstRowOnAir    = $PILOTS
                    ->where([
                        'FPid'=> $singleConnection->FPid,
                        'onGround' => 0,
                        'timestamp>' => $firstRowOnGround->timestamp])
                    ->orderBy('timestamp', 'ASC')
                    ->first();
            }
            if (!empty($firstRowOnAir)){
                $lastRowOnAir     = $PILOTS
                    ->where([
                        'FPid'=> $singleConnection->FPid,
                        'onGround' => 0,
                        'timestamp>' => $firstRowOnAir->timestamp])
                    ->orderBy('timestamp', 'DESC')
                    ->first();
            }
            if (!empty($lastRowOnAir)){
                $lastRowOnGround  = $PILOTS
                    ->where([
                        'FPid'=> $singleConnection->FPid,
                        'onGround' => 1,
                        'timestamp>' => $lastRowOnAir->timestamp])
                    ->orderBy('timestamp', 'DESC')
                    ->first();

            }
            if (!empty($firstRowOnGround) && !empty($firstRowOnAir) && !empty($lastRowOnAir) && !empty($lastRowOnGround) ){



                $totalOnGround = $this->timeCalculator($firstRowOnGround->timestamp, $lastRowOnGround->timestamp);
                $totalOnAir = $this->timeCalculator($firstRowOnAir->timestamp, $lastRowOnAir->timestamp);

                //Controllo che onAir sia minore di onGround.
                if ($totalOnGround > $totalOnAir){


                    $insert['callsign'] = $firstRowOnGround->callsign;
                    $insert['FPid'] = $firstRowOnGround->FPid;
                    $insert['vid'] = $firstRowOnGround->vid;
                    $insert['FPacft'] = $firstRowOnGround->FPacft;
                    $insert['FPdepAD'] = $firstRowOnGround->FPdepAD;
                    $insert['FPdestAD'] = $firstRowOnGround->FPdestAD;
                    $insert['FProute'] = $firstRowOnGround->FProute;
                    $insert['FPspeed'] = $firstRowOnGround->FPspeed;
                    $insert['FPfrule'] = $firstRowOnGround->FPfrule;
                    $insert['FPfl'] = $firstRowOnGround->FPfl;
                    $insert['FPdepTime'] = $firstRowOnGround->FPdepTime;
                    $insert['EETHours'] = $this->secondConverter($firstRowOnGround->FPeet, 'H');
                    $insert['EETMins'] = $this->secondConverter($firstRowOnGround->FPeet, 'i');
                    $insert['enduranceHours'] = $this->secondConverter($firstRowOnGround->FPendurance, 'H');;
                    $insert['enduranceMins'] = $this->secondConverter($firstRowOnGround->FPendurance, 'i');;
                    $insert['altnAD'] = $firstRowOnGround->FPaltAD;
                    $insert['otherInfo'] = $firstRowOnGround->FPremarks;
                    $insert['FPtypeOfFlight'] = $firstRowOnGround->FPflightType;
                    $insert['FPpob'] = $firstRowOnGround->FPpob;
                    $insert['rating'] = $firstRowOnGround->rating;

                    $insert['connTime'] = $this->timeConverter($firstRowOnGround->connTime);
                    $insert['airborneTime'] =  $this->timeConverter($firstRowOnAir->timestamp);
                    $insert['timeOnline'] = $totalOnGround + 120;
                    $insert['timeOnAir'] = $totalOnAir + 120;

                    $PILOTLOG->insert($insert);

                    //Cancello tutti i record con lo stesso ID
                    $PILOTS->where(['FPid' => $firstRowOnGround->FPid])->delete();
                }
            }

        }
        //CANCELLO TUTTI I LOG VECCHI DI PASTDATE
        $PILOTS->where(['connTime<' => $pastDate])->delete();
    }

    private function timeCalculator($firstDate, $lastDate){
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

    private function timeConverter($date, $formatInput = 'Y-m-d H:i:s', $formatOutput= 'YmdHis'){
        $TIME = Time::createFromFormat($formatInput, $date);

        return $TIME->format($formatOutput);
    }

    private function secondConverter($seconds, $formatOutput = 'H:i', $additionalTime = 0){
        return gmdate($formatOutput, $seconds + $additionalTime);
    }


}