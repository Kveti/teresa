<?php

namespace App\Controller;

use App\Entity\Logs;
use App\Entity\Month;
use App\Form\LogsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class LogsController extends AbstractController
{

    /**
     * @Route("/logs/{project_name}", name="app_logs")
     */
    public function logs(Security $security, String $project_name, Request $request): Response
    {
        $base = $this->getParameter('base_url');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $path = $this->getParameter('project_path');

        $projekty = array_diff(scandir($path), array('..', '.', 'Downloads'));

        $logy = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "logy"), array('..', '.', 'merge'));
        $mena_mesiacov = array("01" => "Január", "02" => "Február", "03" => "Marec", "04" => "Apríl", "05" => "Máj", "06" => "Jún", "07" => "Júl", "08" => "August", "09" => "September", "10" => "Október", "11" => "November", "12" => "December");
        $cisla_mesiacov = array("Január" => "01", "Február" => "02", "Marec" => "03", "Apríl" => "04", "Máj" => "05", "Jún" => "06", "Júl" => "07", "August" => "O8", "September" => "09", "Október" => "10", "November" => "11", "December" => "12");

        $mesiace = array();
        foreach(array_reverse($logy) as $log)
        {
            $mesiace[$log] = substr($log, 0, 4) . " " . $mena_mesiacov[substr($log, 5)];
        }

        //$em = $this->getDoctrine()->getManager();
        //$query = $em->createQuery("Select * from results");


        return $this->render('logs/logs.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'project' => $project_name,
            'base' => $base,
            'mesiace' => $mesiace,
        ]);
    }

    /**
     * @Route("/logs/{project_name}/{month}", name="app_logs_month")
     */
    public function logs_month(Security $security, String $project_name, String $month, Request $request): Response
    {
        $base = $this->getParameter('base_url');
        $path = $this->getParameter('project_path');
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $projekty = array_diff(scandir($path), array('..', '.', 'Downloads'));
        $files = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "logy" . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . "webapp"), array('..', '.'));

        $logs = array();
        foreach ($files as $file)
        {
            if (strpos($file, "log")===0)
            {
                $logs[] = $file;
            }
        }

        return $this->render('logs/month.html.twig', [
            'username' => $name,
            'projects' => $projekty,
            'files' => $logs,
            'base' => $base,
            'project' => $project_name,
            'mesiac' => $month,
        ]);
    }

    /**
     * @Route("/logs/{project_name}/{month}/view/{file}", name="app_logs_view")
     */
    public function logs_view(Security $security, String $project_name, String $month, String $file, Request $request): Response
    {
        $path = $this->getParameter('project_path');
        $final_path = $path . DIRECTORY_SEPARATOR . $project_name . DIRECTORY_SEPARATOR . "logy" . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $file;
        $file = readfile($final_path);
        return new Response($file);
    }

}
