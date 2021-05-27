<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats/{project}", name="stats")
     */
    public function stats(String $project, Security $security): Response
    {
        $base = $this->getParameter('base_url');
        $path = $this->getParameter('project_path');

        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();

        $stats = array();
        $projekty = array_diff(scandir($path), array('..', '.'));
        $mesiace = array_diff(scandir($path . DIRECTORY_SEPARATOR . $project . DIRECTORY_SEPARATOR . "logy"), array(".", "..", "merge"));

        $mena_mesiacov = array("01" => "Január", "02" => "Február", "03" => "Marec", "04" => "Apríl", "05" => "Máj", "06" => "Jún", "07" => "Júl", "08" => "August", "09" => "September", "10" => "Október", "11" => "November", "12" => "December");
        $cisla_mesiacov = array("Január" => "01", "Február" => "02", "Marec" => "03", "Apríl" => "04", "Máj" => "05", "Jún" => "06", "Júl" => "07", "August" => "O8", "September" => "09", "Október" => "10", "November" => "11", "December" => "12");

        // prejdem mesiace v projekte
        $dnicky = array();
        $pass_all = 0;
        $all_all = 0;
        $posledny_mesiac = array();
        foreach ($mesiace as $mesiac)
        {
            $pass = 0;
            $vsetci = 0;
            // nacitam zoznam dni
            $dni = scandir($path . DIRECTORY_SEPARATOR . $project . DIRECTORY_SEPARATOR . "logy" . DIRECTORY_SEPARATOR . $mesiac);
            foreach($dni as $den)
            {
                if (strpos($den, "output") === 0)
                {
                    $xml_path = $path . DIRECTORY_SEPARATOR . $project . DIRECTORY_SEPARATOR . "logy" . DIRECTORY_SEPARATOR . $mesiac . DIRECTORY_SEPARATOR . $den;
                    $vsetci += $this->pocet_testov($xml_path);
                    $pass += $this->pocet_uspesnych($xml_path);
                }
            }
            $meno_mesiaca = $mena_mesiacov[ substr($mesiac, 5)];
            $stats[substr($mesiac, 0, 4)][$meno_mesiaca]["pass"] = $pass;
            $pass_all += $pass;
            $stats[substr($mesiac, 0, 4)][$meno_mesiaca]["all"] = $vsetci;
            $all_all += $vsetci;
            $posledny_mesiac = array(substr($mesiac, 0, 4), $mena_mesiacov[substr($mesiac, 5)], $pass, $vsetci);
        }

        return $this->render('stats/stats.html.twig', [
            'username' => $name,
            'project' => $project,
            'projects' => $projekty,
            'base' => $base,
            'posledny_mesiac' => $posledny_mesiac,
            'stats' => $stats,
            'pass_dokopy' => $pass_all,
            'testy_dokopy' => $all_all,
        ]);
    }


    private function pocet_uspesnych(String $xml_path): int
    {
        $xmlko = simplexml_load_file($xml_path);
        $json = json_encode($xmlko);
        $pole = json_decode($json,TRUE);
        $pocet_testov = count($pole["suite"]["test"]);
        $pass = 0;
        for($i=0; $i<$pocet_testov; $i++)
        {
            if (gettype($pole["suite"]["test"][$i]["status"])!="string")
            {
                if ($pole["suite"]["test"][$i]["status"]["@attributes"]["status"]=="PASS")
                {
                    $pass++;
                }
            }
        }
        return $pass;
    }

    private function pocet_testov(String $xml_path): int
    {
        $xmlko = simplexml_load_file($xml_path);
        $json = json_encode($xmlko);
        $pole = json_decode($json,TRUE);
        $pocet_testov = count($pole["suite"]["test"]);
        return $pocet_testov;
    }

}
