<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project/{project_name}", name="app_project")
     */
    public function project(Security $security, String $project_name, Request $request): Response
    {
        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $projekty = array_diff(scandir(__DIR__ . "/../../results/"), array('..', '.'));
        $path = array();

        $directories = array_diff(scandir(__DIR__ . "/../../results/" . $project_name ), array('..', '.'));
        foreach ($directories as $directory)
        {
            if($directory == "logy")
            {
                $path[] = $base . "/logs/" . $project_name;
            } else if($directory == "stats") {
                $path[] = $base . "/stats/" . $project_name;
            } else {
                $path[] = $base . "/view/" . $project_name . "/" . $directory;
            }
        }

        return $this->render('project/project.html.twig', [
            'username' => $name,
            'directories' => $directories,
            'projects' => $projekty,
            'project' => $project_name,
            'base' => $base,
            'path' => $path,
        ]);
    }
    /**
     * @Route("/view/{project_name}/{dir}", name="app_view", requirements={"dir"="scenáre|poznámky"})
     */
    public function view(Security $security, Request $request, String $project_name, String $dir): Response
    {

        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        //$path = __DIR__ . "/../../results/" . $subdir;
        //$file = readfile($path);
        $projekty = array_diff(scandir(__DIR__ . "/../../results/"), array('..', '.'));
        $files = array_diff(scandir(__DIR__ . "/../../results/" . $project_name . "/" . $dir), array('..', '.'));
        return $this->render('project/view.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'files' => $files,
            'base' => $base,
            'project' => $project_name,
            'dir' => $dir,
        ]);
        //$path = __DIR__ . "/../../results/portalvs/logy/2020Februar/log-20200913-022802.html";
        //$file = readfile($path);
        //return new Response($file);
    }
    /**
     * @Route("/view/{project_name}/{dir}/{file}", name="app_view_file", requirements={"dir"="scenáre|poznámky"})
     */
    public function view_file(Security $security, Request $request, String $project_name, String $dir, String $file): Response
    {
        //$base = "http://localhost/reporty5W2/public/index.php";
        //$user = $security->getUser();
        //$name = $user->getName() . " " . $user->getSurname();
        ////$path = __DIR__ . "/../../results/" . $subdir;
        ////$file = readfile($path);
        //$projekty = array_diff(scandir(__DIR__ . "/../../results/"), array('..', '.'));
        //$files = array_diff(scandir(__DIR__ . "/../../results/" . $project_name . "/" . $dir), array('..', '.'));
        //return $this->render('project/view.html.twig', [
        //    'username' => $name,
        //    'projects' => $projekty,
        //    'files' => $files,
        //    'base' => $base,
        //    'project' => $project_name,
        //]);
        $path = __DIR__ . "/../../results/" . $project_name . "/" . $dir . "/" . $file;
        $file = readfile($path);
        return new Response($file);
    }
}
