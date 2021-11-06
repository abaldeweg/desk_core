<?php

namespace App\Controller;

use App\Entity\Wiki;
use App\Form\WikiType;
use Baldeweg\Bundle\ApiBundle\AbstractApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/wiki")
 */
class WikiController extends AbstractApiController
{
    private $fields = ['id', 'title', 'body'];

    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->setResponse()->collection(
            $this->fields,
            $this->getDoctrine()->getRepository(Wiki::class)->findByUser(
                $this->getUser(),
                ['createdAt' => 'DESC']
                )
        );
    }

    /**
     * @Route("/{wiki}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and wiki.getUser() == user")
     */
    public function show(Wiki $wiki): JsonResponse
    {
        return $this->setResponse()->single($this->fields, $wiki);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $wiki = new Wiki();
        $form = $this->createForm(WikiType::class, $wiki);

        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wiki);
            $em->flush();

            return $this->setResponse()->single($this->fields, $wiki);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Route("/{wiki}", methods={"PUT"})
     * @Security("is_granted('ROLE_USER') and wiki.getUser() == user")
     */
    public function edit(Request $request, Wiki $wiki): JsonResponse
    {
        $form = $this->createForm(WikiType::class, $wiki);

        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->setResponse()->single($this->fields, $wiki);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Route("/{wiki}", methods={"DELETE"})
     * @Security("is_granted('ROLE_USER') and wiki.getUser() == user")
     */
    public function delete(Wiki $wiki): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($wiki);
        $em->flush();

        return $this->setResponse()->deleted();
    }
}
