<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        // USER
        for ($i = 0; $i < 5; $i++) {
            $user = new User();

            // Genrate Name
            $name = $this->faker->name;
            $user->setUsername($name);

            // Generate Email
            $email = $this->faker->email;
            $user->setEmail($email);

            // Generate Password
            $password = $this->faker->password;
            $user->setPassword($password);

            // Generate Date
            $date = $this->faker->dateTime;
            $user->setCreatedAt($date);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        // CATEGORY
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();

            // Generate name
            $name = $this->faker->word();
            $category->setName($name);

            // Generate Date
            $date = $this->faker->dateTime;
            $category->setCreatedAt($date);

            $entityManager->persist($category);
            $entityManager->flush();
        }

        // IMAGES
        for ($i = 0; $i < 20; $i++)
        {
            $image = new Image();

            // Generate Name
            $name = $this->faker->word();
            $image->setName($name);

            // Generate Caption
            $caption = $this->faker->realText(25, 2);
            $image->setCaption($caption);

            // Generate Path
            $image->setPath('https://via.placeholder.com/150');

            // Generate Date
            $date = $this->faker->dateTime();
            $image->setCreatedAt($date);

            $entityManager->persist($image);
        }
        $entityManager->flush();

        // TRICKS
        for ($i = 0; $i < 5; $i++) {

            $trick = new Trick();

            // Generate Title
            $name = $this->faker->word();
            $trick->setName($name);

            // Generate Introduction
            $intro = $this->faker->realText(25, 2);
            $trick->setDescription($intro);

            // Link to Image
            $nbRandomAttachedItem = random_int(3, 8);
            for ($i = 0; $i < $nbRandomAttachedItem; $i++)
            {
                $idRandomImage = random_int(1, 20);
                $image = $entityManager->getRepository(Image::class)
                    ->findOneBy(['id' => $idRandomImage]);
                $trick->addImage($image);
            }

            // Link to User
            $nbRandomUser = random_int(1, 5);
            $user = $entityManager->getRepository(User::class)
                ->findOneBy(['id' => $nbRandomUser]);
            $trick->setUser($user);

            // Link to Category
            $nbRandomCategory = random_int(1, 3);
            $category = $entityManager->getRepository(Category::class)
                ->findOneBy(['id' => $nbRandomCategory]);
            $trick->setCategory($category);

            // Generate Date
            $date = $this->faker->dateTime();
            $trick->setCreatedAt($date);
            $trick->setUpdatedAt($date);

            $entityManager->persist($trick);
        }
            $entityManager->flush();

        // COMMENT
        for ($i = 0; $i < 5; $i++)
        {
            $comment = new Comment();

            //Generate content
            $content = $this->faker->realText(25, 2);
            $comment->setContent($content);

            // Generate Date
            $date = $this->faker->dateTime();
            $comment->setCreatedAt($date);

            // Link to User
            $user = $entityManager->getRepository(User::class)
                ->findOneBy(['id' => 16]);
            $trick->setUser($user);

            $entityManager->persist($comment);
        }
        $entityManager->flush();


        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
