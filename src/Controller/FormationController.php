<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\User;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpClient\HttpClient;

#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository,CacheInterface $cache): Response
    {
     $user = $this->getUser();

         $formation = $cache->get('formation_details',function(ItemInterface $item) use($formationRepository)
        {
            $item->expiresAfter(20);
            return $formationRepository->findByUser($this->user);
        });

        return $this->render('formation/index.html.twig', [
                'formations' => $formationRepository->findByUser($user)
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormationRepository $formationRepository): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        $client = HttpClient::create();

        if ($form->isSubmitted() && $form->isValid()) {
            $formation->setUser($this->getUser());
            $formationRepository->save($formation, true);

            $this->addFlash(
                'success',
                'La formation a bien été créée'
            );
            
            $response = $client->request('POST', "https://webhook.site/367a3baf-71ff-4e03-834c-6617b1a38f5b" , [
                'json' => [
                    'titre' => $formation->getName(),
                    'Pseudo de l\'auteur '=> $this->getUser()->getPseudo(),
                    'Date de création' => $formation->getCreatedAt(),
                    'Date de dernière mise à jour' => $formation->getUpdatedAt(),
                    ]
              ]);
            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, FormationRepository $formationRepository, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->save($formation, true);
            $formation->setUpdatedAt(new \DateTimeImmutable('now'));

            $em->persist($formation);
            $em->flush();
            $this->addFlash(
                'success',
                'La formation a bien été éditée'
            );
            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
