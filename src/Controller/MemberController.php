<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/member")
 */
class MemberController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/", name="member_index", methods={"GET"})
     * @param MemberRepository $memberRepository
     * @return Response
     */
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/new", name="member_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $member->setRoles(['ROLE_MEMBER']);
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('member_index');
        }

        return $this->render('member/new.html.twig', [
            'member' => $member,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="member_show", methods={"GET"})
     * @param Member $member
     * @return Response
     */
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }

    /**
     * @IsGranted("ROLE_MEMBER")
     * @Route("/{id}/edit", name="member_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Member $member
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @return Response
     */
    public function edit(
        Request $request,
        Member $member,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles()) && $this->getUser()->getId() !== $member->getId()) {
            return $this->redirectToRoute('default');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $member->setPassword(
                $passwordEncoder->encodePassword(
                    $member,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('member_profile', array('id' => $member->getId()));
        }

        return $this->render('member/edit.html.twig', [
            'member' => $member,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="member_delete", methods={"DELETE"})
     * @param Request $request
     * @param Member $member
     * @return Response
     */
    public function delete(Request $request, Member $member): Response
    {
        if ($this->isCsrfTokenValid('delete' . $member->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($member);
            $entityManager->flush();
        }

        return $this->redirectToRoute('member_index');
    }
}
