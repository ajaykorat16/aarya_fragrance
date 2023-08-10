<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class admincontroller extends AbstractController
{

    #[Route('/deshboard', name: 'app_deshboard')]
    public function homepage() : Response
    {
        $tracks = [
            ['song' => 'Gangsta\'s Paradise', 'Artist' =>  'Coolio '],
            ['song' =>'Waterfalls' , 'Artist' => 'TLC'],
            ['song' => 'Creep' , 'Artist' =>  'Radiohead'],
            ['song' =>'Kiss from a rose', 'Artist' =>  ' seal'],
            ['song' =>'On Bended knee', 'Artist' =>  ' Boys II Men'],
            ['song' =>'Fantasy' , 'Artist' =>  ' Mariah carey'],
        ];
        return $this->render('admincontroller.html.twig',[
            'title' => 'PB and Jems',
            'tracks' => $tracks,

        ]);
    }
}
