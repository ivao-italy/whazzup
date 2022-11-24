<?php namespace App\Models;


use CodeIgniter\Model;

class Facility extends Model
{

    protected $table = 'facility';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [ 'id', 'icao', 'postazione', 'rating', 'fir'];
}