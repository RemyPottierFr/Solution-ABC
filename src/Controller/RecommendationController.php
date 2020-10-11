<?php

namespace App\Controller;

use App\Entity\Recommendation;
use App\Form\RecommendationStep1Type;
use App\Form\RecommendationStep2Type;
use App\Form\RecommendationType;
use App\Form\StatusFormType;
use App\Repository\MemberRepository;
use App\Repository\PrestationRepository;
use App\Repository\RecommendationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recommendation")
 */
class RecommendationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/", name="recommendation_index", methods={"GET"})
     */
    public function index(RecommendationRepository $recommendationRepo): Response
    {
        return $this->render('recommendation/index.html.twig', [
            'recommendations' => $recommendationRepo->findAll(),
        ]);
    }

    /**
     *
     * @Route("/newstep1", name="recommendation_newstep1", methods={"GET","POST"})
     */
    public function newstep1(Request $request): Response
    {
        $recommendation = new Recommendation();
        $form = $this->createForm(RecommendationStep1Type::class, $recommendation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $owner = $this->getUser();
            $recommendation->setOwner($owner);
            $targetedMember = $recommendation->getTarget();
            $targMemPresId = [];
            $targetedMemberJobs = [];
            if (!empty($targetedMember)) {
                $targetedMemberJobs = $targetedMember->getJobs();
            }
            foreach ($targetedMemberJobs as $targetedMemberJob) {
                foreach ($targetedMemberJob->getPrestations() as $targMemberPresta) {
                    $targMemPresId[] = $targMemberPresta->getId();
                }
            }

            if (!empty($recommendation->getTarget())) {
                $request->getSession()->set('target_id', $recommendation->getTarget()->getId());
            }
            if (!empty($recommendation->getOwner())) {
                $request->getSession()->set('owner_id', $recommendation->getOwner()->getId());
            }

            $request->getSession()->set('prestation_ids', $targMemPresId);
            return $this->redirectToRoute('recommendation_newstep2');
        }

        return $this->render('recommendation/newstep1.html.twig', [
            'recommendation' => $recommendation,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/newstep2", name="recommendation_newstep2", methods={"GET","POST"})
     */
    public function newstep2(
        Request $request,
        MailerInterface $mailer,
        PrestationRepository $prestationRepository,
        MemberRepository $memberRepository
    ): Response
    {
        $ownerId = $request->getSession()->get('owner_id');
        $prestationIds = $request->getSession()->get('prestation_ids');

        $target = $memberRepository->findOneById($request->getSession()->get('target_id'));
        $owner = $memberRepository->findOneById($ownerId);
        $prestations = $prestationRepository->findBy([
            'id' => $prestationIds
        ]);

        $recommendation = new Recommendation();
        $form = $this->createForm(RecommendationStep2Type::class, $recommendation, [
            'prestations' => $prestations
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recommendation->setTarget($target);
            $recommendation->setOwner($owner);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recommendation);
            $entityManager->flush();


            $prestations = $recommendation->getPrestations();
            $clientName = $recommendation->getClientName();
            $infoClient = $recommendation->getInfoClient();
            $comment = $recommendation->getComment();

            $variable = [
                'target' => $target,
                'owner' => $owner,
                'prestations' => $prestations,
                'clientName' => $clientName,
                'infoClient' => $infoClient,
                'comment' => $comment
            ];

            $email = (new Email())
                ->from($owner->getRegisterEmail())
                ->to($target->getRegisterEmail())
                ->subject('Vous venez de recevoir une nouvelle recommandation')
                ->html($this->renderView('recommendation/mail.html.twig', $variable));

            $mailer->send($email);

            $request->getSession()->remove('prestation_ids');
            $request->getSession()->remove('target_id');
            $request->getSession()->remove('owner_id');
            return $this->redirectToRoute('recommendation_list');
        }
        return $this->render('recommendation/newstep2.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="recommendation_show", methods={"GET"})
     */
    public function show(Recommendation $recommendation): Response
    {
        return $this->render('recommendation/show.html.twig', [
            'recommendation' => $recommendation,
        ]);
    }

    /**
     *
     * @Route("/{id}/edit", name="recommendation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Recommendation $recommendation): Response
    {
        $form = $this->createForm(RecommendationType::class, $recommendation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recommendation_index');
        }

        return $this->render('recommendation/edit.html.twig', [
            'recommendation' => $recommendation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recommendation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Recommendation $recommendation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recommendation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recommendation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recommendation_index');
    }

    /**
     *
     * @Route("/{id}/status", name="recommendation_status", methods={"GET","POST"})
     */
    public function status(Request $request, Recommendation $recommendation): Response
    {
        $form = $this->createForm(StatusFormType::class, $recommendation);
        $form->handleRequest($request);

        $user = $this->getUser();

        $targetedMember = $recommendation->getTarget();

        if (!empty($user) && !empty($targetedMember) && $user->getId() !== $targetedMember->getId()) {
            return $this->redirectToRoute('default');
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('member_profile', ['id' => $user->getId()]);
        }

        return $this->render('recommendation/status.html.twig', [
            'recommendation' => $recommendation,
            'form' => $form->createView(),
        ]);
    }
}
