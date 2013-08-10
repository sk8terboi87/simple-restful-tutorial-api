<?php
namespace Model;

use Purekid\Mongodm\Model;

class Blog extends Model
{
    static $collection = "blogs";

    public static $config = 'default';

    protected static $attrs = array(
        'title' => array(
            'type' => 'string'
        ),
        'description' => array(
            'type' => 'string'
        )
    );

}