<?php

namespace AppBundle\Form;

use AppBundle\Entity\GenusScientist;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenusScientistEmbeddedForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->andWhere('user.isScientist = :isScientist')
                        ->setParameter('isScientist', true);
                }
            ])
            ->add('yearsStudied')
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                array($this, 'onPostSetData')
            )
        ;
        //$builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GenusScientist::class
        ]);
    }

    public function onPostSetData(FormEvent $event)
    {
        if ($event->getData() && $event->getData()->getId()) {
            $form = $event->getForm();
            unset($form['user']);
        }
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_genus_scientist_embedded_form';
    }

    /**
     * This fixes a validation issue with the Collection. Suppose
     * the following situation:
     *
     * A) Edit a Genus
     * B) Add 2 new scientists - don't submit & leave all fields blank
     * C) Delete the FIRST scientist
     * D) Submit the form
     *
     * The one new scientist has a validation error, because
     * the yearsStudied field was left blank. But, this error
     * shows at the *top* of the form, not attached to the form.
     * The reason is that, on submit, addGenusScientist() is
     * called, and the new scientist is added to the next available
     * index (so, if the Genus previously had 2 scientists, the
     * new GenusScientist is added to the "2" index). However,
     * in the HTML before the form was submitted, the index used
     * in the name attribute of the fields for the new scientist
     * was *3*: 0 & 1 were used for the existing scientists and 2 was
     * used for the first genus scientist form that you added
     * (and then later deleted). This mis-match confuses the validator,
     * which thinks there is an error on genusScientists[2].yearsStudied,
     * and fails to map that to the genusScientists[3].yearsStudied
     * field.
     *
     * Phew! It's a big pain :). Below, we fix it! On submit,
     * we simply re-index the submitted data before it's bound
     * to the form. The submitted genusScientists data, which
     * previously had index 0, 1 and 3, will now have indexes
     * 0, 1 and 2. And these indexes will match the indexes
     * that they have on the Genus.genusScientists property.
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        dump($data);die();
        $data['genusScientists'] = array_values($data['genusScientists']);
        $event->setData($data);
    }
}
