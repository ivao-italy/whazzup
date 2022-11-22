<?php

namespace App\Models;

use CodeIgniter\Model;

class PilotLogHistory extends Model
{

    protected $table = 'pilotLogHistory';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [ 'id', 'FPid', 'callsign', 'vid', 'FPacft', 'FPdepAD', 'FPdestAD', 'FProute', 'FPspeed', 'FPfrule', 'FPfl', 'FPdepTime', 'EETHours', 'EETMins', 'enduranceHours', 'enduranceMins', 'altnAD', 'otherInfo', 'FPtypeOfFlight', 'FPpob', 'rating', 'connTime', 'airborneTime', 'timeOnline', 'timeOnAir' ];


}