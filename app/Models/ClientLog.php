<?php namespace App\Models;

use CodeIgniter\Model;

class ClientLog extends Model
{
    protected $table = 'clientLog';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['id', 'callsign', 'vid', 'timestamp', 'connType', 'lat', 'lon', 'altitude', 'gs', 'FIid', 'FPacft', 'FPdepAD', 'FPdestAD', 'connTime', 'time', 'rating', 'heading', 'onGround', 'clientName', 'clientVersion'];

}