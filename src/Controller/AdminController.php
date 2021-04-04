<?php

namespace App\Controller;

use App\Entity\Category;
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
        // Generate title
        for ($i = 0; $i < 5; $i++)
        {
            $user = New User();

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
        }


        // Category
        // Generate name
        for ($i = 0; $i < 3; $i++)
        {
            $category = new Category();

            $name = $this->faker->word();
            $category->setName($name);

            // Generate Date
            $date = $this->faker->dateTime;
            $category->setCreatedAt($date);

            $entityManager->persist($category);
        }

        // TRICKS
        for ($i = 0; $i < 25; $i++) {

            $trick = new Trick();

            // Generate Title
            $name = $this->faker->word();
            $trick->setName($name);

            // Generate Introduction
            $intro = $this->faker->realText(25, 2);
            $trick->setDescription($intro);

            // Generate image
            $trick->setImage('https://via.placeholder.com/150');

            // Link to User
            $nbRandomUser = random_int(1, 5);
            $user = $entityManager->getRepository(User::class)
                ->findOneBy([ 'id' => $nbRandomUser]);
            $trick->setUsers($user);

            // Link to Category
            $nbRandomCategory = random_int(1, 3);
            $category = $entityManager->getRepository(Category::class)
                ->findOneBy([ 'id' => $nbRandomCategory]);
            $trick->setCategory($category);

            // Generate Date
            $date = $this->faker->dateTime();
            $trick->setCreatedAt($date);
            $trick->setUpdatedAt($date);
            $entityManager->persist($trick);
        }

        $entityManager->flush();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
