<?php 

namespace App\Service;

use App\Form\SearchEmploiType;
use Symfony\Component\Form\FormFactoryInterface;

class SearchFormProvider
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getSearchForm()
    {
        return $this->formFactory->create(SearchEmploiType::class)->createView();
    }
}
