<?php
// src/Mana/ClientBundle/Form/Type/DobAgeType.php
// the custom field to convert age to date of birth
namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mana\ClientBundle\Form\DataTransformer\AgeToDOB;

class DobAgeType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'required' => false,
            'invalid_message' => "DOB or age not valid",
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new AgeToDOB();

        $builder->addModelTransformer($transformer);
    }

    public function getParent() {
        return 'text';
    }

    public function getName() {
        return 'dob_age';
    }

}