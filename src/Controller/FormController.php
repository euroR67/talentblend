<?php
namespace App\Controller;

use App\Form\SearchEmploiType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormController extends AbstractController
{
    public function searchForm()
    {
        $form = $this->createForm(SearchEmploiType::class);

        return $this->render('form/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
?>