<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event")
     * @param EventRepository $event
     * @return Response
     */
    public function index(EventRepository $event)
    {
        return $this->render('event/index.html.twig', [
            'events' => $event->findAll()
        ]);
    }

    /**
     * @Route("/add", name="event_add")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $flyerFile = $form->get('flyer')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($flyerFile) {
                $newFilename = uniqid().'.'.$flyerFile->guessExtension();

                // Move the file to the directory where brochures are stored
                    $flyerFile->move(
                        $this->getParameter('flyers_directory'),
                        $newFilename
                    );

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setFlyer($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event');
        }

        return $this->render('event/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
