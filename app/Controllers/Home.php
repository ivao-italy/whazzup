<?php

namespace App\Controllers;


use App\Models\ClientLog;
use App\Models\WhazzupInfo;
use CodeIgniter\Config\Services;

class Home extends BaseController
{

    public function cron(){
        $this->reiterateDownload();
        //$this->insertPilotLogInDB();
    }

    /**
     * Metti inloop il download del PilotSummary finchè non ottiene un succes o raggiunge il limite di 50 per non sovra caricare
     * il server
     */
    private function reiterateDownload(){


        $CURL = Services::curlrequest();
        $WHAZZUPINFO = new WhazzupInfo();


        $a = 0;
        do {
            $CURL->setHeader("Api-Key", "MV8kLc2labiJ0cr3bUMXFPrm9MTuw4uC");
            $CURL->setHeader("accept", "application/json");
            $response = $CURL->request('GET', 'https://api.ivao.aero/v2/tracker/now/pilots', ['http_errors' => false]);


            $code = $response->getStatusCode();

            if ($code < 400){
                $body = $response->getJSON();

                file_put_contents(WRITEPATH . 'pilotLog/pilotLog', json_decode($body));
                $insert['code'] = $code;

            }
            else{
                $insert['code'] = $code;
                //$insert['message'] = $response->getBody();
            }

            $a++;
            $insert['message'] = $a;
        }while($code > 400 && $a < 50);
        $WHAZZUPINFO->insert($insert);

        if ($a == 50){
            $log = fopen(WRITEPATH . 'logs/download.log', 'a+');
            fwrite($log, time() . " [ERROR] Reached 50 download without success \n" );
            fclose($log);
        }
        else{
            $log = fopen(WRITEPATH . 'logs/download.log', 'a+');
            fwrite($log, time() . " [INFO] File downloaded Successfully \n" );
            fclose($log);
        }




        $lastrow = $WHAZZUPINFO->orderBy('timestamp', 'DESC')->first();

        if ($lastrow->code <400){

            $CLIENTLOG = new ClientLog();
            $file = json_decode(file_get_contents(WRITEPATH . 'pilotLog/pilotLog'));

            foreach ($file as $item){

                $insert['vid'] = $item->userId;
                $insert['callsign'] = $item->callsign;
                $insert['connTime'] = $item->createdAt;

                $insert['connType'] = $item->connectionType ;

                $lastTrack = $item->lastTrack;

                $insert['altitude'] = $lastTrack->altitude ?? NULL;
                $insert['gs'] = $lastTrack->groundSpeed ?? NULL;
                $insert['heading'] = $lastTrack->heading ?? NULL;
                $insert['lat'] = $lastTrack->latitude ?? NULL;
                $insert['lon'] = $lastTrack->longitude ?? NULL;
                $insert['onGround'] = $lastTrack->onGround ?? NULL;
                $insert['timestamp'] = $lastTrack->timestamp ?? NULL;
                $insert['time'] = $lastTrack->time ?? NULL;

                $flightPlan = $item->flightPlan;

                //Ignoro le tracce che non hanno presentato un piano di volo
                if (!empty($flightPlan)){
                    $insert['FPdepAD'] = $flightPlan->arrivalId;
                    $insert['FPdestAD'] = $flightPlan->departureId;
                    $insert['FPacft'] = $flightPlan->aircraftId;

                    $softwareType = $item->softwareType;

                    $insert['clientName'] = $softwareType->name;

                    $user = $item->user;
                    $rating = $user->rating;

                    $insert['rating'] = $rating->pilotRatingId;

                    $CLIENTLOG->insert($insert);
                }
            }
            $log = fopen(WRITEPATH . 'logs/download.log', 'a+');
            fwrite($log, time() . " [INFO] Rows inserted Correctly \n" );
            fclose($log);
        }
        else{
            $log = fopen(WRITEPATH . 'logs/download.log', 'a+');
            fwrite($log, time() . " [ERROR] Rows not inserted. Code >400 \n" );
            fclose($log);
        }
    }
/*
    public function insertPilotLogInDB(){

        $WHAZZUPINFO = new WhazzupInfo();

        $lastrow = $WHAZZUPINFO->orderBy('timestamp', 'DESC')->first();

        if ($lastrow->code <400){

            $CLIENTLOG = new ClientLog();
            $file = json_decode(file_get_contents(WRITEPATH . 'pilotLog/pilotLog'));

            foreach ($file as $item){

                $insert['vid'] = $item->userId;
                $insert['callsign'] = $item->callsign;
                $insert['connTime'] = $item->createdAt;

                $insert['connType'] = $item->connectionType ;

                $lastTrack = $item->lastTrack;

                $insert['altitude'] = $lastTrack->altitude ?? NULL;
                $insert['gs'] = $lastTrack->groundSpeed ?? NULL;
                $insert['heading'] = $lastTrack->heading ?? NULL;
                $insert['lat'] = $lastTrack->latitude ?? NULL;
                $insert['lon'] = $lastTrack->longitude ?? NULL;
                $insert['onGround'] = $lastTrack->onGround ?? NULL;
                $insert['timestamp'] = $lastTrack->timestamp ?? NULL;
                $insert['time'] = $lastTrack->time ?? NULL;

                $flightPlan = $item->flightPlan;

                //Ignoro le tracce che non hanno presentato un piano di volo
                if (!empty($flightPlan)){
                    $insert['FPdepAD'] = $flightPlan->arrivalId;
                    $insert['FPdestAD'] = $flightPlan->departureId;
                    $insert['FPacft'] = $flightPlan->aircraftId;

                    $softwareType = $item->softwareType;

                    $insert['clientName'] = $softwareType->name;

                    $user = $item->user;
                    $rating = $user->rating;

                    $insert['rating'] = $rating->pilotRatingId;

                    $CLIENTLOG->insert($insert);
                }
            }
            $log = fopen(WRITEPATH . 'logs/download.log', 'a+');
            fwrite($log, time() . " [INFO] Rows inserted Correctly \n" );
            fclose($log);
        }
        else{
            $log = fopen(WRITEPATH . 'logs/download.log', 'a+');
            fwrite($log, time() . " [ERROR] Rows not inserted. Code >400 \n" );
            fclose($log);
        }
    }
*/
}
