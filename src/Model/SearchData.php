<?php 

namespace App\Model;

class SearchData
{
    /** * @var int */
    public $page = 1;

    /** * @var string */
    public $metier = '';

    /** * @var string */
    public $ville = '';

    /** * @var array */
    public $typesEmploi = [];

    /** * @var array */
    public $contrats = [];

    /** * @var array */
    public $niveau = [];
}
