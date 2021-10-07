<?php

namespace App\Controller;

use App\Entity\Letter;
use App\Form\LetterType;
use Baldeweg\Bundle\ApiBundle\AbstractApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route(path: '/api/letter')]
class LetterController extends AbstractApiController
{
    private $fields = ['id', 'title', 'meta', 'content'];

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->setResponse()->collection(
            $this->fields,
            $this->getDoctrine()->getRepository(Letter::class)->findByUser($this->getUser())
        );
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/download/{letter}', methods: ['GET'])]
    public function download(Letter $letter): BinaryFileResponse
    {
        $file = __DIR__ . '/../../data/' . $letter->getTitle() . '.pdf';
        $response = new BinaryFileResponse($file);

        return $response;
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{letter}', methods: ['GET'])]
    public function show(Letter $letter): JsonResponse
    {
        return $this->setResponse()->single($this->fields, $letter);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $letter = new Letter();
        $form = $this->createForm(LetterType::class, $letter);
        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($letter);
            $em->flush();

            return $this->setResponse()->single($this->fields, $letter);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{letter}', methods: ['PUT'])]
    public function edit(Request $request, Letter $letter): JsonResponse
    {
        $form = $this->createForm(LetterType::class, $letter);
        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->setResponse()->single($this->fields, $letter);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{letter}', methods: ['DELETE'])]
    public function delete(Letter $letter): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($letter);
        $em->flush();

        return $this->setResponse()->deleted();
    }
}
