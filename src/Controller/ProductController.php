<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\CustomSearchType;
use App\Form\ProductType;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/product')]
class ProductController extends AbstractController
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {}

    #[Route('/list', name: 'app_product_index')]
    public function index(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CustomSearchType::class, null, ['label' => false]);
        $form->handleRequest($request);

        $search = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
        }
        $pagination = $paginator->paginate(
            $productRepository->findByProduct($search),
            $request->get('page', 1),
            $this->getParameter('page_limit')
        );
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'pagination' => $pagination,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $productImage = $image->getImage();
                $originalName = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalName);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();
                $imageEntity = new Image();
                $imageEntity->setImageName($newFilename);

                try {
                    $productImage->move($this->getParameter('image_directory'), $newFilename);
                } catch (\FileException) {
                    return new Response('something went wrong with upload file...!!');
                }

                $product->addImage($imageEntity);
            }

            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product,ImageRepository $imageRepository): Response
    {
        return $this->render('product/show.html.twig', [
            'image' =>$imageRepository->findAll(),
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        foreach ($product->getImages() as $productImage) {
            $imageDirectory = $this->parameterBag->get('image_directory');
            $imagePath = $imageDirectory . '/' . $productImage->getImageName();
            $productImage->setImage(new UploadedFile($imagePath, $productImage->getImageName()));
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($product->getImages()->count() > 0) {
                foreach ($product->getImages() as $fetchImage) {
                    $product->removeImage($fetchImage);
                }
            }

            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                if ($productImage = $image->getImage()) {
                    $originalName = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalName);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();
                    $imageEntity = new Image();
                    $imageEntity->setImageName($newFilename);

                    try {
                        $productImage->move($this->getParameter('image_directory'), $newFilename);
                    } catch (\FileException) {
                        return new Response('something went wrong with upload file...!!');
                    }
                    $product->addImage($imageEntity);
                } else {
                    $product->addImage($image);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        foreach ($product->getImages() as $fetchImage) {
               unlink('uploads/'. $fetchImage->getImageName());
        }
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_product_index');
    }
}
