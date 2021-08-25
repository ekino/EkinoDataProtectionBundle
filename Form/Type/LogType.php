<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Form\Type;

use Ekino\DataProtectionBundle\Form\DataClass\Log;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LogType.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class LogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'required' => true,
                'label'    => 'admin.logs.content',
                'attr'     => ['class' => 'form-control', 'rows' => 20],
            ])
            ->add('action', ChoiceType::class, [
                'required' => true,
                'label'    => 'admin.logs.action',
                'choices'  => [
                    'admin.logs.decrypt' => Log::ACTION_DECRYPT,
                    'admin.logs.encrypt' => Log::ACTION_ENCRYPT,
                ],
            ])

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Log::class,
            'translation_domain' => 'EkinoDataProtectionBundle',
        ]);
    }
}
