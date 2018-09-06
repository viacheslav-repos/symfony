<?php

namespace App\Admin;

use App\Entity\Attribute;
use App\Form\AttributeValueType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AttributesAdmin
 *
 * @package App\Admin
 */
class AttributesAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Attribute ? $object->getTitle() : 'Attribute';
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', TextType::class)
                   ->add('attributeValues', CollectionType::class, array(
                       'entry_type'   => AttributeValueType::class,
                       'label'        => 'Values',
                       'by_reference' => false,
                       'allow_add'    => true,
                       'allow_delete' => true,
                   ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title');
    }
}
