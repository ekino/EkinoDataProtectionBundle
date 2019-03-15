<?php

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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DecryptLogType.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class DecryptLogType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * DecryptLogType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'required' => true,
                'label'    => $this->translator->trans('admin.logs.decrypt.content', [], 'EkinoDataProtectionBundle'),
                'attr'     => ['class' => 'form-control', 'rows' => 20],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Log::class,
        ]);
    }
}
