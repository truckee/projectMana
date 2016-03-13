<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\Utilities\DisabledOptions.php

namespace Mana\ClientBundle\Utilities;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

/**
 * Description of DisabledOptions:
 * Returns parameters for FormBuilder->add() to either
 *  display enabled options, with default the current value
 *  or display options as a read only field
 *
 * @author George Brooks
 */
class DisabledOptions
{

    private $enabled;
    private $formEntity;
    private $formMethod;
    private $fieldClassName;
    private $fieldEntity;
    private $fieldName;
    private $fieldMethod;

    /**
     *
     * @param object $formEntity
     * @param string $formMethod
     * @param string $fieldClassName
     * @param string $fieldName
     * @param string $fieldMethod
     */
    public function __construct($formEntity, $formMethod, $fieldClassName, $fieldName, $fieldMethod)
    {
        $this->formEntity = $formEntity;
        $this->formMethod = $formMethod;
        $this->fieldClassName = $fieldClassName;
        $this->fieldEntity = $formEntity->$formMethod();
        $this->fieldName = $fieldName;
        $this->fieldMethod = $fieldMethod;
        $fieldEntity = $formEntity->$formMethod();
        $this->enabled = (!empty($fieldEntity)) ? $fieldEntity->getEnabled() : true;
    }

    public function type()
    {
        return ($this->enabled) ? EntityType::class : 'text';
    }

    public function fieldArray()
    {
        $fieldEntity = $this->fieldEntity;
        $fieldName = $this->fieldName;
        $class = get_class($fieldEntity);
        $len = strrpos($class, '\\', -1);
        $fieldEntityName = substr($class, $len + 1);
        if ($this->enabled) {
            return array(
                'class' => 'ManaClientBundle:' . $fieldEntityName,
                'property' => $fieldName,
                'empty_value' => '',
                'query_builder' => function(EntityRepository $er) use ($fieldName) {
                    return $er->createQueryBuilder('f')
                                    ->orderBy('f.' . $fieldName, 'ASC')
                                    ->where("f.enabled=1");
                },
            );
        }
        else {
            return array(
                'class' => 'ManaClientBundle:' . $fieldEntityName,
                'property' => $fieldName,
                'attr' => array('disabled' => true),
                'empty_value' => '',
                'query_builder' => function(EntityRepository $er) use ($fieldName) {
            return $er->createQueryBuilder('f')
                            ->orderBy('f.' . $fieldName, 'ASC')
//                            ->where("f.enabled=1")
            ;
        },
            );
//            $fieldMethod = $this->fieldMethod;
//            return array(
//                'data' => $fieldEntity->$fieldMethod(),
//                'attr' => array('readonl' => 'readonly')
//            );
        }
    }

}
