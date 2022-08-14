<?php

namespace App\Model;

class Files extends \Eloquent
{
    protected $table = 'file';
    protected $softDelete = true;
}
