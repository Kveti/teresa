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
        $user = $security->getUser();
        $name = $user->getName() . " " . $user->getSurname();
        $stats = array();
        $projekty = array_diff(scandir(__DIR__ . "/../../results/"), array('..', '.'));
        $mesiace = array_diff(scandir(__DIR__ . "\\..\\..\\results\\" . $project . "\\logy"), array(".", "..", "merge"));
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
            $dni = scandir(__DIR__ . "\\..\\..\\results\\" . $project . "\\logy\\" . $mesiac);
            foreach ($dni as $zaznam)
            {
                if (strpos($zaznam, "output") !== 0)
                {
                    $dni = array_diff($dni, array($zaznam));
                }
            }
            foreach($dni as $den)
            {
                $xmlko = simplexml_load_file("C:\\Users\\matov\\Documents\\web\\reporty5W2\\results\\portalvs\\logy\\" . $mesiac . "\\" . $den);
                $status = $this->xml2array($xmlko)["suite"]["kw"]["status"]["@attributes"]["status"];
                if ($status == "PASS"){
                    $pass++;
                }
                $vsetci++;
            }
            $meno_mesiaca = $mena_mesiacov[ substr($mesiac, 5)];
            $stats[substr($mesiac, 0, 4)][$meno_mesiaca]["pass"] = $pass;
            $pass_all += $pass;
            $stats[substr($mesiac, 0, 4)][$meno_mesiaca]["all"] = $vsetci;
            $all_all += $vsetci;
            $posledny_mesiac = array(substr($mesiac, 0, 4), $mena_mesiacov[substr($mesiac, 5)], $pass, $vsetci);
        }
        //$logy = scandir(__DIR__ . "\\..\\..\\results\\" . $project . "\\logy\\2020Februar");
        //foreach ($logy as $zaznam)
        //{
        //    if (strpos($zaznam, "log") !== 0)
        //    {
        //        $logy = array_diff($logy, array($zaznam));
        //    }
        //}
        //$xml = array();
        //$xmlko = simplexml_load_file("C:\\Users\\matov\\Documents\\web\\reporty5W2\\results\\portalvs\\logy\\2020Februar\output.xml");
        //$xml[] = $this->xml2array($xmlko)["suite"]["kw"]["status"]["@attributes"]["status"];
        //echo $xml_arr["suite"]["kw"]["status"]["@attributes"]["status"];
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
    /**
     * function xml2array
     *
     * This function is part of the PHP manual.
     *
     * The PHP manual text and comments are covered by the Creative Commons
     * Attribution 3.0 License, copyright (c) the PHP Documentation Group
     *
     * author  k dot antczak at livedata dot pl
     * date    2011-04-22 06:08 UTC
     * link    http://www.php.net/manual/en/ref.simplexml.php#103617
     * license http://www.php.net/license/index.php#doc-lic
     * license http://creativecommons.org/licenses/by/3.0/
     * license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
     */
    private function xml2array ( $xmlObject, $out = array () ): array
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }
}
