<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Entity\Tricks;
use App\Form\ReplyType;
use App\Form\TricksType;
use App\Entity\Discussions;
use App\Form\DiscussionsType;
use App\Service\ImageManager;
use App\Service\TrickManager;
use App\Service\AuthenticationService;
use App\Service\DiscussionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{
    private $entityManager;
    private $authenticationService;
    private $trickManager;
    private $imageManager;

    public function __construct(
        AuthenticationService $authenticationService,
        TrickManager $trickManager,
        ImageManager $imageManager,
        EntityManagerInterface $entityManager
    ) {
        $this->authenticationService = $authenticationService;
        $this->trickManager = $trickManager;
        $this->imageManager = $imageManager;
        $this->entityManager = $entityManager;
    }

    #[Route('/tricks', name: 'tricks')]
    public function indexTricks(): Response
    {
        // Recupere tous les tricks
        $tricks = $this->entityManager->getRepository(Tricks::class)->findByCreationDate();

        // Recupere la premiere image de chaque trick
        $images = $this->imageManager->getFirstImagesByTricks($tricks);
        
        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks,
            'images' => $images
        ]);
    }

    #[Route('/trick/add', name: 'add_trick')]
    #[Route('/trick/edit/{slug}', name: 'edit_trick')]
    public function formTrick(Request $request, ?string $slug = null): Response
    {
        $edit = false;

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Modification
        if ($slug != null){
            $trick = $this->trickManager->findTrickBySlug($slug);
            if ($trick === null || !$this->authenticationService->isTrickOwnedByUser($trick, $this->getUser())) {
                return $this->redirectToRoute('tricks');
            }
            $edit = true;
        } 
        else{
            $trick = new Tricks;
        }

        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $trick = $edit == false ? $this->trickManager->createTrick($form) : $this->trickManager->updateTrick($form, $trick, $request);
         
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('tricks/edit.html.twig', [
            'form' => $form->createView(),
            'edit' => $edit,
            'images' => $trick->getImages()
        ]);
    }


    #[Route('/trick/{slug}', name: 'trick_show')]
    public function show(Request $request, $slug, DiscussionManager $discussionManager): Response
    {
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);
        
        // Si le trick n'existe pas, on renvoie vers la page des tricks
        if (!$trick) {
            return $this->redirectToRoute('tricks');
        }

        $discussion = new Discussions();
        $discussions = $this->entityManager->getRepository(Discussions::class)->getDiscussionJoinAuthor($trick->getId());

        $form = $this->createForm(DiscussionsType::class, $discussion);
        $form->handleRequest($request);

        $reply = new Reply();
        $replyForm = $this->createForm(ReplyType::class, $reply);
        $replyForm->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $discussion = $form->getData();
            $discussionManager->createDiscussion($discussion, $this->getUser(), $trick);
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('tricks/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'discussions' => $discussions,
            'replyForm' => $replyForm->createView()
        ]);
    }

    #[Route('/trick/delete/{id}', name: 'trick_delete')]
    public function delete($id): Response
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        //Supprimer un trick et ses images et videos
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['id' => $id]);
        // Si le trick n'existe pas, on renvoie vers la page des tricks
        if ($trick === null || !$this->authenticationService->isTrickOwnedByUser($trick, $this->getUser())) {
            return $this->redirectToRoute('tricks');
        }
        $this->trickManager->deleteTrick($trick);
        $this->addFlash('success', "Votre tricks a été supprimer avec succes");
        return $this->redirectToRoute('tricks');
    }

    #[Route('/trick/reply/{id}', name: 'reply')]
    public function reply(Request $request, $id, DiscussionManager $discussionManager): Response
    {
        $reply = new Reply();
        $replyForm = $this->createForm(ReplyType::class, $reply);
        $replyForm->handleRequest($request);

        if ($replyForm->isSubmitted() && $replyForm->isValid()) {

            // Renseignez la discussion parent ici avec id mais depuis le slug de tricks
            $discussion = $this->entityManager->getRepository(Discussions::class)->findOneBy(['id' => $id]);
            
            $discussionManager->createReply($replyForm->getData(), $this->getUser(), $discussion);
    
            // Rediriger vers la même page pour afficher la réponse nouvellement ajoutée
            return $this->redirectToRoute('trick_show', ['slug' => $discussion->getIdTricks()->getSlug()]);
        }
    }

}
