<?php

namespace App\Form;

use App\Entity\AttributeValue;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                ->add('category', EntityType::class, array(
                    'class'        => Category::class,
                    'choice_label' => 'name',
                ))
                ->add('attributeValues', EntityType::class, array(
                    'class'        => AttributeValue::class,
                    'choice_label' => 'title',
                    'multiple'     => true,
                    'expanded'     => true
                ))
                ->add('brochure', Type\FileType::class, array('label' => 'Brochure (image file)'))
                ->add('save', Type\SubmitType::class, array('label' => 'Save'));
    }
}
