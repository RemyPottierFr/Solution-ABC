<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\JobRepository;
use App\Repository\PrestationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/prestation")
 */
class PrestationController extends AbstractController
{
    /**
     * @Route("/", name="prestation_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param PrestationRepository $prestationRepository
     * @return Response
     */
    public function index(PrestationRepository $prestationRepository): Response
    {
        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="prestation_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $jobs = $user->getJobs();

        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation, [
            'jobs' => $jobs->toArray()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($prestation);
            $entityManager->flush();

            if ($request->get('id')) {
                return $this->redirectToRoute($request->get('fallback'), [
                    'id' => $request->get('id')
                ]);
            } elseif ($request->get('fallback')) {
                return $this->redirectToRoute($request->get('fallback'));
            } else {
                return $this->redirectToRoute('default');
            }
        }

        return $this->render('prestation/new.html.twig', [
            'prestation' => $prestation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="prestation_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param Prestation $prestation
     * @return Response
     */
    public function show(Prestation $prestation): Response
    {
        return $this->render('prestation/show.html.twig', [
            'prestation' => $prestation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="prestation_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Prestation $prestation
     * @param JobRepository $jobRepository
     * @return Response
     */
    public function edit(Request $request, Prestation $prestation, JobRepository $jobRepository): Response
    {
        $form = $this->createForm(PrestationType::class, $prestation, ['jobs' => $jobRepository->findAll()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('prestation_index');
        }

        return $this->render('prestation/edit.html.twig', [
            'prestation' => $prestation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="prestation_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param Prestation $prestation
     * @return Response
     */
    public function delete(Prestation $prestation): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($prestation);
        $entityManager->flush();

        return $this->redirectToRoute('prestation_index');
    }
}
