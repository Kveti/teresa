<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core;
use Symfony\Component\Form\Extension\Core\Type;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function admin(\Doctrine\DBAL\Connection $connection): Response
    {
        $project_name = "rvs";
        $sql = '
            SELECT CAST(test_datetime AS DATE) date, sum(cases_failed) F, sum(cases_passed) P 
            FROM results 
            WHERE test_name LIKE :project 
            AND (date(test_datetime) = curdate()
            OR date(test_datetime) = SUBDATE(curdate(), INTERVAL 1 DAY)
            OR date(test_datetime) = SUBDATE(curdate(), INTERVAL 2 DAY))
            GROUP BY CAST(test_datetime AS DATE);
            ';
        $stmt = $connection->prepare($sql);
        $stmt->execute(['project' => $project_name . "%"]);
        // returns an array of arrays (i.e. a raw data set)
        $pole = $stmt->fetchAllAssociative(); // ✅

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'pole' => $pole,
        ]);
    }
    /**
     * @Route("/admin/users", name="admin_users")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function admin_users(Core\Security $security, \Doctrine\DBAL\Connection $connection): Response
    {
        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $path = $this->getParameter('project_path');
        $projekty = array_diff(scandir($path), array('..', '.'));
        //$project_name = "rvs";
        $sql = '
            select id, email, name, surname, skupina
            from user;
            ';
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $table = $stmt->fetchAllAssociative(); // ✅

        return $this->render('admin/users.html.twig', [
            'controller_name' => 'Users',
            'table' => $table,
            'base' => $base,
        ]);
    }
    /**
     * @Route("/admin/user/{id}", name="admin_user")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function admin_user(int $id, \Doctrine\DBAL\Connection $connection, Request $request): Response
    {
        //$project_name = "rvs";
        //$sql = '
        //    select id, email, roles, password, name, surname, skupina
        //    from user
        //    where id = :id;
        //    ';
        //$stmt = $connection->prepare($sql);
        //$stmt->execute(['id' => $id]);
        //$table = $stmt->fetchAllAssociative(); // ✅
        $data = ["jeden", "dva"];
        $defaultData = ['email' => 'jeden@dva.tri', 'meno' => "jeden", 'priezvisko' => 'habla blabla'];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', Type\EmailType::class)
            ->add('meno', Type\TextType::class)
            ->add('priezvisko', Type\TextType::class)
            ->add('heslo', Type\TextType::class)
            ->add('skupina', Type\TextType::class)
            ->add('send', Type\SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            //return new Response(print_r($data, true));
        }

       
        return $this->render('admin/user.html.twig', [
            'controller_name' => 'User',
            'user_form' => $form->createView(),
            'data' => $data,
        ]);
    }
    /**
     * @Route("/admin/delete/user/{id}", name="admin_delete_user")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function admin_delete_user(int $id, \Doctrine\DBAL\Connection $connection): Response
    {
        $project_name = "rvs";
        $sql = '
            delete from user
            where id = :id;
            ';
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        //$table = $stmt->fetchAllAssociative(); // ✅
        $table = [["jeden", "dva"]];
        return new Response("Zmazane");
    }
}
