<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Service\DirCrowler;
use ZipArchive;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project/{project_name}", name="app_project")
     */
    public function project(Security $security, String $project_name, Request $request, \Doctrine\DBAL\Connection $connection): Response
    {
        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $path = $this->getParameter('project_path');
        $projekty = array_diff(scandir($path), array('..', '.'));
        $directories = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project_name ), array('..', '.'));
        $directories = array_values($directories);
        $path = array();

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
        $log_s = $stmt->fetchAllAssociative(); // ✅  ❌
        $log_0 = "";
        $log_1 = "";
        $log_2 = "";
        if (isset($log_s[2]))
        {
            if ($log_s[2]["P"] > 0)
            {
                $log_2 = $log_s[2]["F"] == 0 ? "✔" : "❌";
            }
        }
        if (isset($log_s[1]))
        {
            if ($log_s[1]["P"] > 0)
            {
                $log_1 = $log_s[1]["F"] == 0 ? "✔" : "❌";
            }
        }
        if (isset($log_s[0]))
        {
            if ($log_s[0]["P"] > 0)
            {
                $log_0 = $log_s[0]["F"] == 0 ? "✔" : "❌";
            }
        }
        $logs_status = "logy " . $log_0 . "  " . $log_1 . "  " . $log_2;
         


        foreach ($directories as $directory)
        {
            if($directory == "logy")
            {
                $path[] = $base . "/logs/" . $project_name;
            } else if($directory == "stats") {
                $path[] = $base . "/stats/" . $project_name;
            } else if($directory == "automaticke" || $directory == "automatické") {
                $path[] = $base . "/src/" . $project_name;
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
            'logs_status' => $logs_status,
        ]);
    }
    /**
     * @Route("/view/{project_name}/{dir}", name="app_view", requirements={"dir"="scenáre|poznámky"})
     */
    public function view(Security $security, Request $request, String $project_name, String $dir): Response
    {

        $base = $this->getParameter('base_url');
        $path = $this->getParameter('project_path');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $projekty = array_diff(scandir($path), array('..', '.'));
        $files = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . $dir), array('..', '.'));
        return $this->render('project/view.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'files' => $files,
            'base' => $base,
            'project' => $project_name,
            'dir' => $dir,
        ]);
    }
    /**
     * @Route("/view/{project_name}/{dir}/{file}", name="app_view_file", requirements={"dir"="scenáre|poznámky"})
     */
    public function view_file(Security $security, Request $request, String $project_name, String $dir, String $file): Response
    {
        $path = $this->getParameter('project_path');
        $final_path = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file;
        $file = readfile($final_path);
        return new Response($file);
    }
    /**
     * @Route("/src/{project_name}", name="app_src")
     */
    public function src_project(Security $security, Request $request, String $project_name): Response
    {
        $path = $this->getParameter('project_path');
        $project_path = $path . DIRECTORY_SEPARATOR . $project_name;
        $dirs = array_diff(scandir($project_path), array('..', '.'));

        //$key = array_search("automatické", $dirs); 
        //$in = in_array("automatické", $dirs);
        if (in_array("automaticke", $dirs))
        {
            $src_path = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "automaticke";
        }
        else if(in_array("automatické", $dirs))
        {
            $src_path = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "automatické";
        }
        //foreach ($dirs as $dir)
        //{
        //    if($dir=="automatické" || $dir=="automaticke")
        //    {
        //        $src_path = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . $dir;
        //        break;
        //    }
        //}
        $files = DirCrowler::scanuj_dir($src_path);
        $zip_name = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . $project_name . '_src.zip';
        if(file_exists($zip_name)) {
            unlink ($zip_name);
        }
        $zip = new ZipArchive;
        $zip->open($zip_name, ZipArchive::CREATE);
        foreach($files as $file)
        {
            $zip->addFile($src_path . DIRECTORY_SEPARATOR . $file, $file);
        }
        $zip->close();
        $response = new Response(readfile($zip_name));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-disposition', 'filename="' . $project_name . '_src.zip"');
        $response->headers->set('Content-length', filesize($zip_name));
        return $response;
    }
}