<?php namespace App\Models;


use CodeIgniter\Model;

class ClientLog extends Model
{

    protected $table = 'clientLog';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [ 'id', 'callsign', 'vid', 'timestamp', 'connType', 'lat', 'lon', 'altitude', 'gs', 'FPid', 'FPacft', 'FPdepAD', 'FPdestAD', 'FPaltAD', 'FProute', 'FPremarks', 'FPspeed', 'FPfl', 'FPfrule', 'FPflightType', 'FPeet', 'FPendurance', 'FPdepTime', 'FPpob', 'FPequip', 'connTime', 'time', 'rating', 'heading', 'onGround', 'clientName', 'clientVersion'];


        public function getDistinctFPid($where){
            $query = $this->db->table($this->table)->where($where);
            return $query->distinct('FPid')->select('FPid, connTime')->get()->getResult('object');
        }
}