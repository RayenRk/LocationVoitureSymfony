<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureFormType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class VoitureController extends AbstractController
{
    #[Route('/voiture', name: 'voiture')]
    public function createProduct(EntityManagerInterface $em, VoitureRepository $vr): Response
    {
        $voitures=$vr->findAll();
        return $this->render('voiture/listVoiture.html.twig', [
            "listeVoiture" => $voitures
            ]);
    }

    #[Route('/addVoiture', name: 'addvoiture')]
    public function addVoiture(Request $request, EntityManagerInterface $em): Response{

        $voiture = new Voiture();
        $form = $this->createForm(VoitureFormType::class, $voiture);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()){
            $em->persist($voiture);
            $em->flush();
            return $this->redirectToRoute('voiture');
        }

        return $this->render('voiture/addVoiture.html.twig', [
        'formVoiture'=>$form->createView()
        ]);
    }

    #[Route('/voiture/{id}', name: 'voitureDelete')]
    public function deleteVoiture(EntityManagerInterface $em,
                                  VoitureRepository $vr, $id): Response
    {
        $voiture = $vr->find($id);
        if ($voiture !== null) {
            $em->remove($voiture);
            $em->flush();

        }else{
            throw new NotFoundHttpException("La voiture d'id ".$id."n'existe pas");
        }
        return $this->redirectToRoute('voiture');
    }

    #[Route('/updateVoiture/{id}', name: 'voitureUpdate')]
    public function updateVoiture(Request $request, EntityManagerInterface $em,
                                        VoitureRepository $vr, $id): Response
    {
        $voiture = $vr->find($id);
        $editform = $this->createForm(VoitureFormType::class, $voiture);

        $editform->handleRequest($request);

        if ($editform->isSubmitted() and $editform->isValid()){
            $em->persist($voiture);
            $em->flush();
            return $this->redirectToRoute('voiture');
        }

        return $this->render('voiture/updateVoiture.html.twig', [
            'editFormVoiture'=>$editform->createView()
        ]);
    }
    #[Route('/searchVoiture', name:'voitureSearch')]
    public function searchVoiture(Request $request, EntityManagerInterface $em): Response
    {
        $voiture = null;

        if($request->isMethod('POST')){
            $serie = $request->request->get("input_serie");
            $query = $em->createQuery(
                "SELECT v FROM App\Entity\Voiture v 
                    WHERE v.serie LIKE '".$serie."'");

            $voiture = $query->getResult();
        }
        return $this->render("voiture/rechercheVoiture.html.twig",
        ["voitures"=>$voiture]);
    }
}
