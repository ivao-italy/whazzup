<?php namespace App\Models;

use CodeIgniter\Model;

class WhazzupInfo extends Model
{
    protected $table = 'whazzupInfo';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['timestamp' , 'code'];


}