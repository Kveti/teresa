<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


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
}
