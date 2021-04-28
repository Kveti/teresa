<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard(Security $security): Response
    {
        //return $this->render('dashboard/index.html.twig', [
        //    'controller_name' => 'DashboardController',
        //]);
        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        //$arr = array("test 1", "test 2", "tetst 3");
        //$logy = array();
        //$projekty = scandir(FILE . "results")
        $projekty = array_diff(scandir(__DIR__ . "/../../results/"), array('..', '.'));
        //$scanned_directory = array_diff($directories, array('..', '.'));
        //$name = implode ( ", " , $directories );
        return $this->render('dashboard/dashboard.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'base' => $base,
        ]);
    }
}
