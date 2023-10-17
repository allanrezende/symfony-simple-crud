<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;

class BookController extends AbstractController
{
    #[Route('/books', name: 'books_list', methods: ['GET'])]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        return $this->json([
            'data' => $bookRepository->findAll()
        ]);
    }

    #[Route('/books/{book}', name: 'books_single', methods: ['GET'])]
    public function single(int $book, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);
        if (!$book) throw $this->createNotFoundException();

        return $this->json([
            'data' => $book
        ]);
    }

    #[Route('/books', name: 'books_create', methods: ['POST'])]
    public function create(Request $request, BookRepository $bookRepository): JsonResponse
    {
        if ($request->headers->get('Content-Type') == 'application/json') {
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $bookRepository->add($book, true);

        return $this->json([
            'message' => 'Book created successfully',
            'data' => $book
        ], 201);
    }

    #[Route('/books/{book}', name: 'books_update', methods: ['PUT', 'PATCH'])]
    public function update(int $book, Request $request, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);
        if (!$book) throw $this->createNotFoundException();

        if ($request->headers->get('Content-Type') == 'application/json') {
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $bookRepository->update();

        return $this->json([
            'message' => 'Book updated successfully',
            'data' => $book
        ]);
    }

    #[Route('/books/{book}', name: 'books_delete', methods: ['DELETE'])]
    public function delete(int $book, Request $request, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);
        if (!$book) throw $this->createNotFoundException();

        $bookRepository->delete($book, true);

        return $this->json([
            'message' => 'Book deleted successfully'
        ]);
    }
}
