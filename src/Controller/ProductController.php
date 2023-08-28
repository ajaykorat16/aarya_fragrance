<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {}

    #[Route('/list', name: 'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $images = $form->get('images')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

                foreach ($images as $image) {
                    $productImage = $image['image'][0];
                    $originalName = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalName);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                    $imageEntity = new Image();
                    $imageEntity->setImageName($newFilename);

                    try {
                        $productImage->move($this->getParameter('image_directory'), $newFilename);
                    } catch (FileException $exception) {
                        return new Response('something went wrong with upload file...!!', $exception);
                    }
                    $product->addImage($imageEntity);
                    $entityManager->persist($product);
                }
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        foreach ($product->getImages() as $productPath) {
            $imageDirectory = $this->parameterBag->get('image_directory');
            $imagePath = $imageDirectory . '/' . $productPath->getImageName();
            $productPath->setImage(new UploadedFile($imagePath, $productPath->getImageName()));
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $productImage = $image['image'][0];
                $originalName = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalName);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                $imageEntity = new Image();
                $imageEntity->setImageName($newFilename);

                try {
                    $productImage->move($this->getParameter('image_directory'), $newFilename);
                } catch (FileException $exception) {
                    return new Response('something went wrong with upload file...!!', $exception);
                }

                $product->addImage($imageEntity);
            }
                $entityManager->flush();
                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($product);
            $entityManager->flush();

        return $this->redirectToRoute('app_product_index');
    }
}
