<?php


namespace App\Controller;


use App\Entity\Product;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontEndProductController extends AbstractController
{
    #[Route('/', name: 'app_product_display')]
    public function display(ProductRepository $productRepository,ImageRepository $imageRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $imageRepository->findAll(),
            $request->get('page', 1),
            $this->getParameter('page_limit')
        );

        return $this->render('product/display.html.twig',[
            'products' =>$productRepository->findAll(),
            'pagination' => $pagination,
            'images' =>$imageRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}/detail', name: 'app_product_detail', methods: ['GET'])]
    public function detail(Product $product,ImageRepository $image): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'image' => $image->findAll(),
        ]);
    }
}