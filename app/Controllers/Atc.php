<?php namespace App\Controllers;


use App\Models\AtcLog;
use App\Models\AtcLogHistory;
use App\Models\Facility;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Atc extends Controller
{
    public function insertAtcLog(){

        $ATC = new AtcLog();
        $ATCHISTORY = new AtcLogHistory();
        $FACILITY = new Facility();

        $TIME = Time::now('UTC');
        $TIME = $TIME->subHours('6');
        $sessionId = $ATC->getDistinctSessionid();




        foreach ($sessionId as $item) {

            $firstRow = $ATC->orderBy('timestamp', 'ASC')->find($item->sessionId);
            $lastRow = $ATC->orderBy('timestamp', 'DESC')->find($item->sessionId);


            //CPNTROLLA L'ULTIMA RIGA CHE ABBIA UN TIMESTAMP PIU' VECCHIO DI 3 ORE
            if ($lastRow->timestamp < $TIME->format('Y-m-d H:i:s')){

                $splitCallsign = explode('_',$lastRow->callsign);
                $position = $splitCallsign[0];
                $facility = end($splitCallsign);


                $facility = $FACILITY->where([
                    'icao' => $position,
                    'postazione' => $facility
                ])
                        ->find();

                if (!empty($facility)){

                    $facility = $facility[0];
                    $insert['sessionId'] = $lastRow->sessionId;
                    $insert['callsign'] = $lastRow->callsign;
                    $insert['clientName'] = $lastRow->clientName;
                    $insert['clientVersion'] = $lastRow->clientVersion;
                    $insert['rating'] = $lastRow->rating;
                    $insert['connTime'] = $this->timeConverter($lastRow->connTime);
                    $insert['connDate'] = $lastRow->connTime;
                    $insert['position'] = $lastRow->position;
                    $insert['vid'] = $lastRow->vid;
                    $insert['frequency'] = $lastRow->frequency;
                    $insert['timeOnline'] = $this->timeCalculator($lastRow->timestamp, $firstRow->timestamp);
                    $insert['facility'] = $facility->rating;
                    $insert['fir'] = $facility->fir;

                    $ATCHISTORY->insert($insert);

                    //CANCELLO I DATI
                    $ATC->delete($item->sessionId);

                }
            }
        }


    }

    private function timeCalculator($lastDate, $firstDate){
        $TIME1 = Time::createFromFormat('Y-m-d H:i:s', $firstDate);
        $TIME2 = Time::createFromFormat('Y-m-d H:i:s', $lastDate);

        if ($lastDate >= $firstDate){
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

}