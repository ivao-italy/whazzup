<?php namespace App\Models;


use CodeIgniter\Model;

class AtcLogHistory extends Model
{

    protected $table = 'atcLogHistory';
    protected $primaryKey = 'sessionId';
    protected $returnType = 'object';
    protected $allowedFields = [ 'id', 'sessionId', 'callsign', 'vid', 'position', 'facility', 'fir', 'connTime', 'connDate', 'timeOnline', 'clientName', 'clientVersion', 'frequency', 'rating'];
}