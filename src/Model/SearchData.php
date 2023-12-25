<?php 

namespace App\Model;

class SearchData
{
    /** * @var int */
    public $page = 1;

    /** * @var string */
    public $poste = '';

    /** * @var string */
    public $ville = '';

    /** * @var array */
    public $typeEmplois = [];

    /** * @var array */
    public $contrats = [];
}
