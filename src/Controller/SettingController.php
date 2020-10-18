<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;

#use Symfony\Component\Form\Extension\Core\Type\FileType;
#use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;

/**
 * Setting controller.
 *
 */
class SettingController extends AbstractController
{
    /**
     * @Route("/settings", methods={"GET"}, name="settings_index")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $logo = $em->getRepository('App\Entity\Setting')
            ->findOneBy(['name' => 'logo']);

        $form = $this->getForm();
        $form->get('logo')
            ->setData($logo ? $logo->getValue() : '');

        return $this->render('setting/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/settings", methods={"POST"}, name="settings_save")
     */
    public function saveAction(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           if ($logo = $em->getRepository('App\Entity\Setting')
               ->findOneBy(['name' => 'logo'])
           ) {
               $em->persist($logo->setValue($form->get('logo')->getData()));
               $em->flush();
           }
       }

        return $this->redirect($this->generateUrl('settings_index'));
    }

    private function getForm(): Form
    {
        return $this->createFormBuilder()
           ->add('logo', TextType::class, [
               //'data' => $logo ? $logo->getValue() : ''
           ])
           // ->add('logo', ResourceAutocompleteChoiceType::class, [
           //     'choice_name' => 'name',
           //     'choice_value' => 'id',
           //     'resource' => 'app.file'
           // ])
           ->add('save', SubmitType::class, ['label' => 'Save'])
           ->getForm();
    }
}
