<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class admincontroller extends AbstractController // extend this controller will give us sortcut methods.
{
    //almost use attribute to create route syntax: #[]-- way to configuration to code.
    //when we use attribute we must have a co-responding use statement for at the top of the file.
    #[Route('/deshboard', name: 'app_deshboard')] // inside the route / will be the url to our page.
        // this route defines the url in points to this controller bcz this written above to the controller.
        // '/' or no slash is the same for a homepage.
    public function homepage() : Response // bcz our controller always return response obj,you can add return type to the function .. its optional.
    {
        //the only thing that symfony cares about is that your controller returns a response object
        // return new Response('Title : PB and Jems');
        //we want the one of from httpFoundation . this is symfony library and this will gives us classes like
        // request , response and session
        //in render, first is name of template. second arg is an array of any variable that you want to pass into template
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
