<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\ArticleLike;
use App\Entity\User;
use App\Entity\Image;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        //instance de faker
        $faker = \Faker\Factory::create('fr_FR');

        //tab users
        $users = [];
        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setUsername($faker->name)
                ->setAge(mt_rand(18, 45))
                ->setSexe('M')
                ->setTel($faker->e164PhoneNumber)
                ->setPassword($this->encoder->encodePassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
            $manager->flush();
        }
        //créer les catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            //sentence() et paragraph() sont des fonctions de Faker\Factory
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);
            /////////////////////////////////////////////////////////////////////////////
            //-------------Créer les Articles pour chaque Catégories--------
            //-------------mt_rand() méthode php qui permet de choisirun nombre au hazard ici 4 ou 6
            /////////////////////////////////////////////////////////////////////////////////
            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article();
                //join() pour joindre à mes paragraphes des balises p
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);
                $manager->persist($article);

                //------Créer des Commentaires pour nos articles
                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();
                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
                    //
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setUser($users[mt_rand(0, count($users) - 1)])
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                        ->setArticle($article);
                    $manager->persist($comment);
                }
                for ($l = 0; $l < mt_rand(0, 15); $l++) {
                    $like = new ArticleLike();
                    $like->setArticle($article)
                        ->setUser($users[mt_rand(0, count($users) - 1)]);
                    $manager->persist($like);
                }
            }
        }
        $manager->flush();
    }
}
