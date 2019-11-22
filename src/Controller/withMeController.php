<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Invitation;
use App\Entity\Ami;
use App\Repository\AmiRepository;
use App\Repository\InvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UsersSearch;
use App\Form\UsersSearchType;
use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Image;
use App\Repository\PostRepository;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\User;
use App\Repository\MessageRepository;

class withMeController extends AbstractController
{

    /**
     * @Route("/",name="home")
     * @param UserRepository $userRepo
     * @param Post $post
     * @param Request $request 
     * @param ObjectManager $manager
     * @param PostRepository $postRepo
     * @return void
     */
    public function home(MessageRepository $messageRepository, Post $post = null, Request $request, ObjectManager $manager, PostRepository $postRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute("blog");
            exit;
        }
        //le nombre de messages non lu par Ami du user connecté
        $msgNonLu = $messageRepository->countMsgNonLu($user);
        //les posts publier par le user connecté
        $postsUser = $postRepo->findPostUserByDate($user);
        //les posts publier par les amis du user connecté 
        $postsAmis = $postRepo->findPostAmisByDate($user);
        if (!$post) {
            $post = new Post();
        }
        //--------------------------- le formulaire Post ------------------------------------------
        $formPost = $this->createForm(PostType::class, $post);
        //Analyser la requete http
        $formPost->handleRequest($request);
        // si le formulaire est submit
        if ($formPost->isSubmitted() && $formPost->isValid()) {
            if (!$post->getId()) {
                //définr la date de création de l'article
                $post->setCreatedAt(new \DateTime());
                $post->setUser($user);
            }

            if ($request->files->get('post')['my_files']) {
                //--------------------traitement de l'image----------------
                $images = $request->files->get('post')['my_files'];
                //chemin vers le dossier images
                $path = $this->getParameter('kernel.project_dir') . '/public/imagesPosts';
                foreach ($images as $image) {
                    //obtenir le file soumit par l'utilisateur
                    $fileName = md5(uniqid()) . '.' . $image->guessExtension();
                    //déplacer l'image dans le repertoir src/Images
                    $image->move($path, $fileName);
                    $img = new Image();
                    $img->setPost($post)
                        ->setName($fileName);
                    $manager->persist($img);
                    $post->addImage($img);
                }
            }
            $manager->persist($post);
            $manager->flush();
        }
        //--------------------------- Fin du formulaire Poste------------------------------------------
        //--------------------------- debut form message------------------------------------------
        return $this->render('withMeTemplates/home.html.twig', [
            'formPost' => $formPost->createView(),
            'postsUser' => $postsUser,
            'postsAmis' => $postsAmis,
            'msgNonLu' => $msgNonLu,
            //si j'ai un id article je suis en edition d'article
            'edition' => $post->getId() !== null
        ]);
    }
    /**
     * Undocumented function
     * @Route("/messages/t/{id}",name="discussion")
     * @Route("/messages/t/",name="derniere_discussion")
     */
    function discussion($id = null, AmiRepository $amiRepo, MessageRepository $messageRepository, UserRepository $userRepo, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $error = false;
        // je vérifie si j'ai un user connecté
        if (!$user) {
            $this->redirectToRoute('login');
            exit;
        }
        //si id en get = idUser
        if ($id === $user->getId()) $error = true;
        // si pas id en get 
        if (!$id) {
            //je cherche le dernier msg envoyé
            $dernierMsg = $messageRepository->findDernierMsg($user);
            //si j'ai un msg
            $userTo = new User();
            if($dernierMsg){
                 //j'obtiens le userTo de dernier msg envoyé
                 $userTo = $dernierMsg[0]->getUserTo();
            }
           
        } else {
            // je vérifie si id en get correspond à un user 
            $userTo = $userRepo->findOneBy(['id' => $id]);
            // je vérifie si id en get fait partie des amis de user connecté
            $ami = $amiRepo->findOneBy([
                'user' => $user,
                'ami' => $userTo
            ]);
            if (!$ami) $error = true;
        }
        // si id en get = id user connecté ou $ami est null

        //j'obtien les  messages entre user connecté et son ami si $userTo est bien son ami
        $userTo ? $messages  = $messageRepository->findMessages($user, $userTo) : $messages = [] && $error = true;
        $msgNonLu = $messageRepository->countMsgNonLu($user);
        foreach ($msgNonLu as $msg) {
            if ($msg['userFrom'] === $id) {
                //
                $messageRepository->updateReadAt($id, $user->getId());
                $msg['count'] = 0;
            }
        }
        //vider le formulaire 

        // formulaire envoi message
        $message = new Message();
        $formMessage = $this->createForm(MessageType::class, $message);
        if ($userTo) {
            $formMessage->handleRequest($request);
            if ($formMessage->isSubmitted() && $formMessage->isValid()) {
                $message->setCreatedAt(new \DateTime())
                    ->setUserFrom($user)
                    ->setUserTo($userTo);
                $manager->persist($message);
                $manager->flush();
                //vider le formulaire
                $message = new Message();
                $formMessage = $this->createForm(MessageType::class, $message);
            }
        }
        return $this->render('withMeTemplates/discussion.html.twig', [
            'messages' => $messages,
            'formMessage' => $formMessage->createView(),
            'userTo' => $userTo,
            'error' => $error,
            'msgNonLu' => $msgNonLu
        ]);
    }
    /**
     * Undocumented function
     * @Route("/allUsers",name="allUsers")
     * @param PaginationInterface $paginator
     * @param UserRepository $userRepo
     * @param Request $request
     * @return Response
     */
    function AfficherUser(UserRepository $userRepo, Request $request): Response
    {
        $user = $this->getUser();
        $recherche = new UsersSearch();
        $form = $this->createForm(UsersSearchType::class, $recherche);
        $form->handleRequest($request);

        if ($recherche->getAge() || $recherche->getSexe()) {
            $users = $userRepo->findAllUsers($recherche, $user);
        } else {
            $users = $userRepo->findAll();
        }

        return $this->render('withMeTemplates/rechercheUsers.html.twig', [
            'users' => $users,
            'formSearch' => $form->createView()
        ]);
    }
    /**
     * envoi invitation d'ajout à la liste d'amis
     * @Route("/allUsers/recherche/{id}/inviter", name="inviter_user")
     * @param UserRepository $userRepo
     * @param ObjectManager $manager
     * @return Response
     */
    public function inviter($id, UserRepository $userRepo, ObjectManager $manager): Response
    {

        //user qui invite
        $userQuiInvite = $this->getUser();
        //user invité
        $userInviter = $userRepo->findOneBy([
            'id' => $id
        ]);
        $invitation = new Invitation();
        $invitation->setUserQuiInvite($userQuiInvite)
            ->setUserInvite($userInviter);
        $manager->persist($invitation);
        $manager->flush();
        return new Response();
    }
    /**
     * supprimer une invitation
     * @Route("/home/invitation/{id}/remove",name="supprimer_invitation")
     * @param Invitation $invitation
     * @param InvitationRepository $invitRepo
     * @param ObjectManager $manager
     * @return Response|null
     */
    public function removeInvitation(Invitation $invitation, InvitationRepository $invitRepo, ObjectManager $manager): ?Response
    {
        $user = $this->getUser();
        if ($user) {
            $invitASupprimer = $invitRepo->find($invitation);
            $manager->remove($invitASupprimer);
            $manager->flush();
            return new Response();
        }
    }
    /**
     * Undocumented function
     * @Route("/home/invitation/{id}/accepter",name="accepter_invitation")
     * @param InvitationRepository $invitRepo
     * @param Invitation $invitation
     * @param ObjectManager $manager
     * @return Response
     */
    public function accepterInvitation(InvitationRepository $invitRepo, Invitation $invitation, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $ami1 = new Ami();
            $ami2 = new Ami();
            $ami1->setUser($invitation->getUserQuiInvite())
                ->setAmi($invitation->getUserInvite());
            $manager->persist($ami1);
            $ami2->setUser($invitation->getUserInvite())
                ->setAmi($invitation->getUserQuiInvite());
            $manager->persist($ami2);
            $invitASupprimer = $invitRepo->find($invitation);
            $manager->remove($invitASupprimer);
            $manager->flush();
            return new Response();
        }
    }
    /**
     * @Route("/home/test")
     *
     * @return Response
     */
    public function test():Response
    {
        $user = $this->getUser();
        var_dump($user);
      //return $this->json($user);
    }
    /**
     * Undocumented function
     * @Route("/home/ami/{id}/remove",name="supprimer_ami")
     * @param Ami $ami
     * @param AmiRepository $amiRepo
     * @param ObjectManager $manager
     * @return Response
     */
    public function removeAmi(Ami $ami, AmiRepository $amiRepo, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $amiASupprimer = $amiRepo->find($ami);
            $manager->remove($amiASupprimer);
            $manager->flush();
            return new Response();
        }
    }
}
