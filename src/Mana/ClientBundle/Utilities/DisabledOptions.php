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
    private $fieldClassName;
    private $fieldName;
    private $fieldLabel;

    public function __construct($fieldClassName, $fieldName, $fieldlLabel, $enabled)
    {
        $this->fieldClassName = $fieldClassName;
        $this->fieldName = $fieldName;
        $this->fieldLabel = $fieldlLabel;
        $this->enabled = $enabled;
    }

    public function fieldArray()
    {
        $fieldName = $this->fieldName;
        if ($this->enabled) {
            return array(
                'class' => 'ManaClientBundle:' . $this->fieldClassName,
                'property' => $fieldName,
                'label' => $this->fieldLabel,
                'label_attr' => ['style' => 'font-weight:bold;'],
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
                'class' => 'ManaClientBundle:' . $this->fieldClassName,
                'property' => $fieldName,
                'attr' => ['style' => 'display:none;'],
                'empty_value' => '',
                'query_builder' => function(EntityRepository $er) use ($fieldName) {
            return $er->createQueryBuilder('f')
                            ->orderBy('f.' . $fieldName, 'ASC')
            ;
        },
            );
        }
    }

}
