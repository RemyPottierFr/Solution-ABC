<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Member;
use App\Entity\Recommendation;
use App\Entity\Prestation;
use App\Entity\Job;
use App\Repository\RecommendationRepository;
use App\Repository\MemberRepository;
use Knp\Component\Pager\PaginatorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        $members = $this->getDoctrine()
        ->getRepository(Member::class)
        ->findAll();

        $recommendations = $this->getDoctrine()
        ->getRepository(Recommendation::class)
        ->findAll();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'members' => $members,
            'recommendations' => $recommendations
        ]);
    }

    /**
    * @Route("member/list", name="member_list")
    */
    public function memberList()
    {
        $members = $this->getDoctrine()
            ->getRepository(Member::class)
            ->findAll();
        return $this->render('home/member.html.twig', [
            'members' => $members
        ]);
    }

    /**
    * @Route("/recommendation/list", name="recommendation_list")
    */
    public function recommendationList(PaginatorInterface $paginator, Request $request)
    {
        $recommendations = $this->getDoctrine()
            ->getRepository(Recommendation::class)
            ->findAll();
        $recommendations = array_reverse($recommendations);
        $recommendations = $paginator->paginate(
            $recommendations, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('home/recommendation.html.twig', [
            'recommendations' => $recommendations
        ]);
    }

    /**
    * @Route("/profile/{id}", name="member_profile", methods={"GET"})
    */
    public function profile($id)
    {
        $member = $this->getDoctrine()
            ->getRepository(Member::class)
            ->findOneById($id);

        if (!$member) {
            throw $this->createNotFoundException('The member does not exist');
        }

        $jobs = $member->getjobs();
        $prestationsOfAllJobs = [];
        foreach ($jobs as $job) {
            $prestationsOfAllJobs[] = $job->getPrestations();
        }
        $variables = [
            'member' => $member,
            'jobs' => $jobs,
            'prestationsOfAllJobs' => $prestationsOfAllJobs,
            'user' => $this->getUser(),
        ];
        $ownedRecommendations = $member->getRecommendations();

        $targRecommendations = $this->getDoctrine()
            ->getRepository(Recommendation::class)
            ->findBy(['target' => $member], array('id' => 'DESC'));

        $ownedRecommendations = $this->getDoctrine()
            ->getRepository(Recommendation::class)
            ->findBy(['owner' => $member], array('id' => 'DESC'));

        $variables['ownedRecommendations'] = $ownedRecommendations;
        $variables['targRecommendations'] = $targRecommendations;
        
        $nbPrestationsOwn = count($ownedRecommendations);
        $nbPrestationsTar = count($targRecommendations);

        $variables['nbPrestationsOwn'] = $nbPrestationsOwn;
        $variables['nbPrestationsTar'] = $nbPrestationsTar;

        return $this->render('member/profile.html.twig', $variables);
    }
}
