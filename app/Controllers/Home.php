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
            //$CURL->setHeader("Api-Key", "MV8kLc2labiJ0cr3bUMXFPrm9MTuw4uC");
            $CURL->setHeader("accept", "application/json");
            $response = $CURL->request('GET', 'https://api.ivao.aero/v2/tracker/whazzup', ['http_errors' => false]);


            $code = $response->getStatusCode();

            if ($code < 400){
                $body = json_decode($response->getBody());

                $pilots = $body->clients->pilots;
                $atcs = $body->clients->atcs;

                file_put_contents(WRITEPATH . 'pilotLog/pilotLog', json_encode($pilots));
                file_put_contents(WRITEPATH . 'pilotLog/atctLog', json_encode($atcs));
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
                $insert['connType'] = 'PILOT' ;
                $insert['clientName'] = $item->softwareTypeId;
                $insert['clientVersion'] = $item->softwareVersion;
                $insert['vid'] = $item->userId;
                $insert['rating'] = $item->rating;

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
                    $insert['FPdestAD'] = $flightPlan->arrivalId;
                    $insert['FPid'] = $flightPlan->id;
                    $insert['FPdepAD'] = $flightPlan->departureId;
                    $insert['FPacft'] = $flightPlan->aircraftId;
                    $insert['FPaltAD'] = $flightPlan->alternativeId;
                    $insert['FProute'] = $flightPlan->route;
                    $insert['FPremarks'] = $flightPlan->remarks;
                    $insert['FPspeed'] = $flightPlan->speed;
                    $insert['FPfl'] = $flightPlan->level;
                    $insert['FPfrule'] = $flightPlan->flightRules;
                    $insert['FPflightType'] = $flightPlan->flightType;
                    $insert['FPeet'] = $flightPlan->eet;
                    $insert['FPendurance'] = $flightPlan->endurance;
                    $insert['FPdepTime'] = $flightPlan->departureTime;
                    $insert['FPpob'] = $flightPlan->peopleOnBoard;
                    $insert['FPequip'] = $flightPlan->aircraftEquipments;

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

    public function test(){

        $CURL = Services::curlrequest();

        //$CURL->setHeader("Api-Key", "MV8kLc2labiJ0cr3bUMXFPrm9MTuw4uC");
        $CURL->setHeader("accept", "application/json");
        $response = $CURL->request('GET', 'https://api.ivao.aero/v2/tracker/whazzup', ['http_errors' => false]);


        $code = $response->getStatusCode();


            $body = json_decode($response->getBody());


            echo "<pre>";
            echo(json_encode($body->clients->pilots));
            echo "</pre>";



    }

}
