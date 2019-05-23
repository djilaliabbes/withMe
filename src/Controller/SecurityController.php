<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\InscriptionType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     * @Route("inscription/{id}",name="modifier_profile")
     */
    public function inscription(User $user = null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        if (!$user) {
            //obtenir une instance de User (vide)
            $user = new User();
        }

        //relier l'instance $user avec notre formulaire pour le remplir
        $form = $this->createForm(InscriptionType::class, $user);
        // analyser la requête HTTP
        $form->handleRequest($request);
        //Vérifier si le formulaire submit est valide 
        if ($form->isSubmitted() && $form->isValid()) {
            // hasher le mot de passe entrer par l'utilisateur
            $hash = $encoder->encodePassword($user, $user->getPassword());
            //remplacer le password entrer en claire par son hash avant de l'envoyer à la DB
            $user->setPassword($hash);
            //--------------------traitement de l'image----------------
            //chemin vers le dossier images
            $path = $this->getParameter('kernel.project_dir') . '/public/images';
            $image = $user->getImage();
            //obtenir le file soumit par l'utilisateur
            $file = $image->getFileImage();
            //renommer l'image 
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $image->setName($fileName);
            //déplacer l'image dans le repertoir src/Images
            $file->move($path, $fileName);
            //persister et enregistrer en DB
            $manager->persist($user);
            $manager->flush();
            //une fois inscription terminé, redirection vers la page login
            return $this->redirectToRoute('login');
        }
        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/connexion",name="login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }
    /**
     * @Route("/deconnexion" , name="logout")
     */
    public function logout()
    { }
}
