<?php 

namespace App\Model;

class SearchData
{
    /** * @var int */
    public $page = 1;

    /** * @var string */
    public $metiers = '';

    /** * @var string */
    public $villes = '';

    /** * @var array */
    public $typeEmplois = [];

    /** * @var array */
    public $contrats = [];
}
