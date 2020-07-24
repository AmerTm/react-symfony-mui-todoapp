<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/tag", name="api_tag")
 */
class TagController extends AbstractController
{
    /**
     * @var TagRepository
     */
    private $tagRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(TagRepository $tagRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        $this->tagRepository = $tagRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @Route("/read", name="api_tag_read", methods={"GET"})
     */
    public function read()
    {
        $oldTags = $this->tagRepository->findAll();
        $newTags = [];

        foreach ($oldTags as $tag) {
            $newTags[] = $tag->normalize();
        }
        return $this->json($newTags);
    }

    /**
     * @Route("/create", name="api_tag_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $content = json_decode($request->getContent());

        $tag = new Tag();
        $tag->setName($content->name);

        $tagNameErrors = $this->validator->validate($tag);

        $errors = [];
        if (sizeof($tagNameErrors) > 0) {

//            enable this to generate an array of all errors instead of just one
//            foreach ($tagNameErrors as $tagNameError) {
//                $errors[] = $tagNameError->getMessage();
//            }

            $errors[] = $tagNameErrors[0]->getMessage();

            return $this->json(
                ['message' => ['text' => $errors, 'level' => 'error']]
            );
        }

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $this->json([
            'tag' => $tag->normalize(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="api_tag_update", methods={"PUT"})
     * @param Request $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function update(Request $request, Tag $tag)
    {
        $content = json_decode($request->getContent());
        $tag->setName($content->name);

        $this->validator->validate($tag);

        $validatorErrors = $this->validator->validate($tag);

        $errors = [];
        if (sizeof($validatorErrors) > 0) {

//            enable this to generate an array of all errors instead of just one
//            foreach ($validatorErrors as $error) {
//                $errors[] = $error->getMessage();
//            }

            $errors[] = $validatorErrors[0]->getMessage();

            return $this->json(
                ['message' => ['text' => $errors, 'level' => 'error']]
            );
        } else {
            $this->entityManager->flush();

            return $this->json([
                ['message' => ['text' => 'Tag has been updated!', 'level' => 'success']],
            ]);
        }
    }

    /**
     * @Route("/delete/{id}", name="api_tag_delete", methods={"DELETE"})
     * @param Tag $tag
     * @return JsonResponse
     */
    public function delete(Tag $tag)
    {
        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        $form = $this->createFormBuilder();

        return $this->json([]);
    }
}
