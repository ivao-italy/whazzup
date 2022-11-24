<?php namespace App\Models;


use CodeIgniter\Model;

class AtcLog extends Model
{

    protected $table = 'atcLog';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [ 'id', 'sessionId', 'callsign', 'clientName', 'clientVersion', 'rating', 'connTime', 'timestamp', 'position', 'vid', 'frequency' ];

        public function getDistinctSessionid($where){
            $query = $this->db->table($this->table)->where($where);
            return $query->distinct('sessionId')->select('sessionId')->get()->getResult('object');
        }
}