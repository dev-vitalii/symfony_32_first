<?php

namespace AppBundle\Form;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true, // render check-boxes
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Manager' => 'ROLE_MANAGER',
                ],
            ])
//            ->add('studiedGenuses', EntityType::class, [
//                'class' => Genus::class,
//                'multiple' => true,
//                'expanded' => true,
//                'choice_label' => 'name',
//                'by_reference' => false, //by reference needed for call adder and remover from non-owning side of many-to-man relation
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_edit_user_form_type';
    }
}
