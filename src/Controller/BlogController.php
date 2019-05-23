<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Entity\Comment;
use App\Repository\ArticleLikeRepository;
use App\Entity\ArticleLike;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CommentRepository;
use App\Entity\User;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function blog(ArticleRepository $repo)
    {
        $articles = $repo->findALL();
        return $this->render('blog/articles.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles

        ]);
    }

    /**
     * @Route("/blog/new",name="blog_create")
     * @Route("/blog/{id}/edit",name="blog_editArticle")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        if (!$article) {
            $article = new Article();
        }
        // le formulaire est crée à l'aide de la console
        $form = $this->createForm(ArticleType::class, $article);

        //Analyser la requete http
        $form->handleRequest($request);
        // si le formulaire est submit
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                //définr la date de création de l'article
                $article->setCreatedAt(new \DateTime());
                $article->setUser($user);
            }
            if ($article->getImage()) {
                //--------------------traitement de l'image----------------
                //chemin vers le dossier images
                $path = $this->getParameter('kernel.project_dir') . '/public/imagesArticles';
                $image = $article->getImage();
                //obtenir le file soumit par l'utilisateur
                $file = $image->getFileImage();
                //renommer l'image 
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $image->setName($fileName);
                //déplacer l'image dans le repertoir src/Images
                $file->move($path, $fileName);
            }

            //persister et envoyer l'article vers DB
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        return $this->render('blog/formArticle.html.twig', [
            'formArticle' => $form->createView(),
            //si j'ai un id article je suis en edition d'article
            'edition' => $article->getId() !== null
            //on utilise createView pour afficher mon formulaire
        ]);
    }



    /**
     * @Route("/blog/{id}",name="blog_show")
     */
    public function show(CommentRepository $commentRepo, Article $article, Request $request, ObjectManager $manager)
    {
        //$user = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $comment->setCreatedAt(new \DateTime())
        //         ->setUser($user)
        //         ->setArticle($article);
        //     $manager->persist($comment);
        //     $manager->flush();
        // }
        $comments = $commentRepo->orderCommentByDate($article);

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
            'comments' => $comments
        ]);
    }
    // -------------------traitement commentaire------------------
    /**
     * Undocumented function
     * @Route("/blog/{id}/comment", name ="comment_article")
     * @param Article $article
     * @param Request $request
     * @param ObjectManager $manager
     * @return void
     */
    public function comment(Article $article, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setUser($user)
                ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();
        }
        return $this->json();
    }
    /**
     * liker ou desliker un article
     * @Route("/blog/{id}/like",name="article_like")
     * @param Article $article
     * @param ObjectManager $manager
     * @param ArticleLikeRepository $likeRepository
     * @return Response
     */
    public function like(Article $article, ObjectManager $manager, ArticleLikeRepository $likeRepository): Response
    {
        //pour obtenir le user .
        $user = $this->getUser();
        //si pas de user
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => "vous devez être connecté"
            ], 403);
        }
        //----------------------------si user à déjà aimé cet article et veut desliker------------------------------------
        if ($article->isLikedByUser($user)) {
            //trouver le like.
            $like = $likeRepository->findOneBy([
                'article' => $article,
                'user' => $user
            ]);
            $manager->remove($like);
            $manager->flush();
            //retourner de json avec le bon nombre de likes de l'article desliker
            return $this->json([
                'code' => 200,
                'message' => "le like à bien été supprimer",
                'likes' => $likeRepository->count(['article' => $article])
            ], 200);
        }
        //---------------------si user n'a pas encore aimé cet article---------------------------------------------------------------
        $like = new ArticleLike();
        $like->setArticle($article)
            ->setUser($user);
        $manager->persist($like);
        $manager->flush();
        // retourner le json avec le bon nombre de likes
        return $this->json([
            'code' => 200,
            'message' => "le like à bien été ajouter",
            'likes' => $likeRepository->count(['article' => $article])
        ], 200);
    }
    /**
     * Undocumented function
     * @Route("/blog/{id}/supprimer",name="article_supprimer", methods={"delete"})
     * @param Article $article
     * @param ArticleRepository $articleRepo
     * @param ObjectManager $manager
     * @param ArticleLikeRepository $likeRepo
     * @param CommentRepository $commentRepo
     * @return Response
     */
    public function supprimerArticle(Article $article, ArticleRepository $articleRepo, ObjectManager $manager, ArticleLikeRepository $likeRepo, CommentRepository $commentRepo): Response
    {
        $likes = $likeRepo->findBy(['article' => $article]);
        foreach ($likes as $like) {
            $manager->remove($like);
        }
        $comments = $commentRepo->findBy(['article' => $article]);
        foreach ($comments as $comment) {
            $manager->remove($comment);
        }
        $manager->flush();
        $articleASupprimer = $articleRepo->find($article);
        $manager->remove($articleASupprimer);
        $manager->flush();
        return new Response();
    }
    /**
     * @Route("/blog/{id}/supprimer",name ="supprimer_comment")
     * @param Comment $comment
     * @param CommentRepository $commentRepo
     * @param ObjectManager $manager
     * @return void
     */
    public function supprimerComment(Comment $comment,  CommentRepository $commentRepo, ObjectManager $manager)
    {
        $user = $this->getUser();
        //pas de user
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => "vous devez être connecté"
            ], 403);
        }
        //trouver le comment
        $commentASupprimer = $commentRepo->find($comment);
        // remove comment DB
        $manager->remove($commentASupprimer);
        $manager->flush();
        return $this->json([
            'code' => 200,
            'message' => "le commentaire à bien été supprimer",
        ], 200);
    }
}
