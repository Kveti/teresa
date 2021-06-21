<?php

namespace App\Controller;

use App\Entity\Logs;
use App\Entity\Month;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use ZipArchive;
use App\Service\DirCrowler;

class ReportsController extends AbstractController
{
    /**
     * @Route("/reports/{project_name}", name="app_reports")
     */
    public function reports(Security $security, String $project_name, Request $request): Response
    {
        $base = $this->getParameter('base_url');
        $path = $this->getParameter('project_path');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $projekty = array_diff(scandir($path), array('..', '.', 'Downloads'));

        $logy = array_diff(scandir($path . "/" . $project_name . "/logy/"), array('..', '.', 'merge'));
        $mena_mesiacov = array("01" => "Január", "02" => "Február", "03" => "Marec", "04" => "Apríl", "05" => "Máj", "06" => "Jún", "07" => "Júl", "08" => "August", "09" => "September", "10" => "Október", "11" => "November", "12" => "December");
        $cisla_mesiacov = array("Január" => "01", "Február" => "02", "Marec" => "03", "Apríl" => "04", "Máj" => "05", "Jún" => "06", "Júl" => "07", "August" => "O8", "September" => "09", "Október" => "10", "November" => "11", "December" => "12");

        $logs = new Logs();
        $mesiace = array();
        $idx = 0;
        foreach(array_reverse($logy) as $log)
        {
            $mesiace[$idx] = new Month();
            $mesiace[$idx]->setMonth(substr($log, 0, 4) . " " . $mena_mesiacov[substr($log, 5)]);
            $idx++;
        }
        $logs->setMonths($mesiace);

        $months = array();
        foreach($logs->getMonths() as $month)
        {
            $months[$month->getMonth()] = $month->getMonth();
        }

        //$form = $this->createForm(LogsFormType::class, $logs);
        $logs_form = $this->createFormBuilder($logs)
            ->add('months', ChoiceType::class, [
                'label' => '',
                'expanded' => true,
                'multiple' => true,
                'choices' => $months
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Merdžnuť'
            ])
            ->getForm();

        $logs_form->handleRequest($request);
        $monthsToMerge = array();
        if ($logs_form->isSubmitted() && $logs_form->isValid())
        {
            foreach($logs->getMonths() as $month)
            {
                $words = explode(" ", $month);
                $monthsToMerge[] = $words[0] . "_" . $cisla_mesiacov[$words[1]];
            }

            $zip_name = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . $project_name . '_reporty.zip';
            if(file_exists($zip_name)) {
                unlink ($zip_name);
            }
            $zip = new ZipArchive;
            $zip->open($zip_name, ZipArchive::CREATE);
            foreach($monthsToMerge as $oneMonthToMerge)
            {
                $filesForZip = DirCrowler::scanuj_dir($path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "logy" . DIRECTORY_SEPARATOR . $oneMonthToMerge);
                foreach($filesForZip as $file)
                {
                    $zip->addFile($path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "logy" . DIRECTORY_SEPARATOR . $oneMonthToMerge . DIRECTORY_SEPARATOR . $file, $oneMonthToMerge . DIRECTORY_SEPARATOR . $file);
                }
            }
            $zip->close();
            $response = new Response(file_get_contents($zip_name));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-disposition', 'filename="' . $project_name . '_reporty.zip"');
            $response->headers->set('Content-length', filesize($zip_name));
            return $response;
        }

        return $this->render('reports/reports.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'project' => $project_name,
            'base' => $base,
            'logs_form' => $logs_form->createView(),
            'fms' => sizeof($months) - 1,
        ]);
    }

    /**
     * @Route("/reports/{project_name}/{month}", name="app_repoorts_month")
     */
    public function reports_month(Security $security, String $project_name, String $month, Request $request): Response
    {
        $subdir = $request->query->get('subdir');
        $path = $this->getParameter('project_path');
        $logy = $request->query->get('log2');
        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $projekty = array_diff(scandir($path), array('..', '.', 'Downloads'));
        $vopchacik = "";
        if ($subdir == "logy")
        {
            $vopchacik = '<p>nejaky vopchacik</p><br />';
        }
        $path = array();
        if (isset($subdir))
        {
            $directories = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . $subdir), array('..', '.'));
            $content = array("con" => array(), "path" => array());
            foreach ($directories as $directory)
            {
                if(strpos($directory, "html"))
                {
                    $path[] = $base . "/project/" . $project_name . "/" . $subdir . "/" . $directory;
                } else {
                    $path[] = $base . "/project/" . $project_name . "?subdir=" . $subdir . "/" . $directory;
                }
            }
        } else {
            $directories = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project_name ), array('..', '.'));
            foreach ($directories as $directory)
            {
                if(strpos($directory, "html"))
                {
                    $path[] = $base . "/project/" . $project_name . "/" . $subdir . "/" . $directory;
                } else {
                    $path[] = $base . "/project/" . $project_name . "?subdir=" . $directory;
                }
            }
        }
        return $this->render('project/project.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'project' => $project_name,
            'base' => $base,
            'path' => $path,
            'vopchacik' => $vopchacik,
            'log2' => $logy,
        ]);
    }
}
