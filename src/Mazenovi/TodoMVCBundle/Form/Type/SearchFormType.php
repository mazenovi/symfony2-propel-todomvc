<?php
namespace Mazenovi\TodoMVCBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\NavbarFormInterface;

class SearchFormType extends AbstractType implements NavbarFormInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttribute('render_fieldset', false)
            ->setAttribute('label_render', false)
            ->setAttribute('show_legend', false)
            ->add('search', 'text', array(
                'widget_control_group' => false,
                'widget_controls' => false,
                'attr' => array(
                    'placeholder' => "search",
                    'class' => "input-medium search-query"
                )
            ))
        ;
    }
    
    public function getName()
    {
        return 'mopa_bootstrap_example_search';
    }
    
    /**
    * To implement NavbarFormTypeInterface
    */
    public function getRoute()
    {
        return "mazenovi_todomvc_api_index"; # return here the name of the route the form should point to
    }
}

