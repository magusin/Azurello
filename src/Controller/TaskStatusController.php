<?php

namespace App\Controller;

use App\Entity\TaskStatus;
use App\Repository\TaskStatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

class TaskStatusController extends AbstractController
{
    #[Route('/taskstatus', name: 'app_task_status')]
    public function index(TaskStatusRepository $taskStatusRepository, Request $request): JsonResponse
    {
        $taskStatus = $taskStatusRepository->findAll();

        return $this->json($taskStatusRepository->findAll());
    }





    // #[Route('/authors', name: 'app_author', methods:["HEAD", "GET"])]
    // public function inzdex(SerializerInterface $serializer, AuthorRepository $authorRepository, Request $request): JsonResponse
    // {
    //     $authors = $authorRepository->findAll();

    //     // -- Pseudo serialisation
    //     // -- 
    //     foreach ($authors as $author_key => $author)
    //     {
    //         $books = $author->getBooks();

    //         foreach ($books as $book_key => $book)
    //         {
    //             $books[$book_key] = [
    //                 'id' => $book->getId(),
    //                 'title' => $book->getTitle(),
    //                 'href' => $this->urlGenerator->generate('app_book_show', ['id' => $book->getId()]),

    //             ];
    //         }

    //         $authors[$author_key] = [
    //             'id' => $author->getId(),
    //             'firstname' => $author->getFirstname(),
    //             'lastname' => $author->getLastname(),
    //             'books' => $books,
    //             'href' => $this->urlGenerator->generate('app_author_show', ['id' => $author->getId()]),
    //         ];
    //     }
    //     // -- 
    //     // -- Fin Pseudo serialisation

    //     return $this->json($this->response($request, $authors, "authors"));
    // }
}
