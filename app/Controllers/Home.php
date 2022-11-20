<?php

namespace App\Controllers;


use App\Models\ClientLog;
use App\Models\WhazzupInfo;
use CodeIgniter\Config\Services;
use CodeIgniter\Files\File;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function test(){


        $CURL = Services::curlrequest();
        $WHAZZUPINFO = new WhazzupInfo();



       $CURL->setHeader("Api-Key", "MV8kLc2labiJ0cr3bUMXFPrm9MTuw4uC");
        $CURL->setHeader("accept", "application/json");
        $response = $CURL->request('GET', 'https://api.ivao.aero/v2/tracker/now/pilots', ['http_errors' => false]);

        //$this->response->setHeader('Content-Type', 'application/json');
        $code = $response->getStatusCode();

       if ($code < 400){
            $body = $response->getJSON();

            file_put_contents(WRITEPATH . 'pilotLog/pilotLog', json_decode($body));
            $insert['code'] = $code;

        }
        else{
            $insert['code'] = $code;
        }

        $WHAZZUPINFO->insert($insert);

    }

    public function test1(){

        $CLIENTLOG = new ClientLog();
        $file = json_decode(file_get_contents(WRITEPATH . 'pilotLog/pilotLog'));
        //$this->response->setHeader('Content-Type', 'application/json');

        //print_r( $file );
        $a = 0;
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
                /*echo $a;
                echo $insert['time'];
                echo " <br>";*/
                //printf($insert['time']);

            }

        }
    }
}
