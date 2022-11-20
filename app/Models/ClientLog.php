<?php namespace App\Models;

use CodeIgniter\Model;

class ClientLog extends Model
{
    protected $table = 'clientLog';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['id', 'callsign', 'vid', 'timestamp', 'connType', 'lat', 'lon', 'altitude', 'gs', 'FPacft', 'FPspeed', 'FPdepAD', 'FPfl', 'FPdestAD', 'facility', 'FPfrule', 'FPdepTime', 'EETHours', 'EETMins', 'enduranceHours', 'enduranceMins', 'altnAD', 'otherInfo', 'FProute', 'atis', 'connTime', 'time', 'rating', 'FPtypeOfFlight', 'FPpob', 'heading', 'onGround', 'clientName', 'clientVersion'];

}