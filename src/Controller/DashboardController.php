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
        $base = $this->getParameter('base_url');
        $path = $this->getParameter('project_path');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $projekty = array_diff(scandir($path), array('..', '.', 'Downloads', 'global_assets'));
        return $this->render('dashboard/dashboard.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'base' => $base,
        ]);
    }
}
