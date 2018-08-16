<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * Class ProductType
 * @package App\Form
 */
class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', Type\TextType::class)
                ->add('description', Type\TextType::class)
                ->add('price', Type\IntegerType::class)
                ->add('save', Type\SubmitType::class, array('label' => 'Save'));
    }
}
