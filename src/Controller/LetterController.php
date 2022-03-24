<?php

namespace App\Controller;

use App\Entity\Letter;
use App\Form\LetterType;
use Baldeweg\Bundle\ApiBundle\AbstractApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/letter')]
class LetterController extends AbstractApiController
{
    private $fields = ['id', 'title', 'meta', 'content'];

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(ManagerRegistry $registry): JsonResponse
    {
        return $this->setResponse()->collection(
            $this->fields,
            $registry->getRepository(Letter::class)->findByUser(
                $this->getUser(),
                ['createdAt' => 'DESC']
            )
        );
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/download/{letter}', methods: ['GET'])]
    public function download(Letter $letter): BinaryFileResponse
    {
        $file = __DIR__.'/../../data/'.$letter->getTitle().'.pdf';

        return new BinaryFileResponse($file);
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
    public function new(Request $request, ManagerRegistry $registry): JsonResponse
    {
        $letter = new Letter();
        $form = $this->createForm(LetterType::class, $letter);
        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $registry->getManager();
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
    public function edit(Letter $letter, Request $request, ManagerRegistry $registry): JsonResponse
    {
        $form = $this->createForm(LetterType::class, $letter);
        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $registry->getManager();
            $em->flush();

            return $this->setResponse()->single($this->fields, $letter);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{letter}', methods: ['DELETE'])]
    public function delete(Letter $letter, ManagerRegistry $registry): JsonResponse
    {
        $em = $registry->getManager();
        $em->remove($letter);
        $em->flush();

        return $this->setResponse()->deleted();
    }
}
