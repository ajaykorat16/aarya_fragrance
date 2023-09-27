<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                "attr" => [
                    "class" => 'btn btn-dark'
                ],
                'label' => "search"
            ]);
    }
}

