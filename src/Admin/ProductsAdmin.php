<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\AttributeValue;
use App\Entity\Product;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * Class ProductsAdmin
 *
 * @package App\Admin
 */
class ProductsAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Product ? $object->getTitle() : 'Product';
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', TextType::class)
                   ->add('price', IntegerType::class)
                   ->add('description', TextType::class)
                   ->add('category', EntityType::class, array(
                       'class'        => Category::class,
                       'choice_label' => 'name',
                   ))
                   ->add('attributeValues', EntityType::class, array(
                       'class'        => AttributeValue::class,
                       'label'        => 'Attributes',
                       'choice_label' => 'title',
                       'group_by'     => 'attribute',
                       'multiple'     => true,
                   ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
                       ->add('description');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title')
                   ->addIdentifier('category.name')
                   ->addIdentifier('price')
                   ->addIdentifier('description');
    }
}
