<?php

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Tests\Form\Type;

use Ekino\DataProtectionBundle\Form\DataClass\Log;
use Ekino\DataProtectionBundle\Form\Type\LogType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LogTypeTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class LogTypeTest extends TestCase
{
    /**
     * @var LogType
     */
    private $formType;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->formType = new LogType();
    }

    /**
     * Test configureOptions method.
     */
    public function testConfigureOptions(): void
    {
        $optionResolver = $this->createMock(OptionsResolver::class);
        $optionResolver->expects($this->once())->method('setDefaults')->with([
            'data_class'         => Log::class,
            'translation_domain' => 'EkinoDataProtectionBundle',
        ]);

        $this->formType->configureOptions($optionResolver);
    }

    /**
     * Test buildForm method.
     */
    public function testBuildForm(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->exactly(2))->method('add')->withConsecutive(
            [
                'content', TextareaType::class, [
                    'required' => true,
                    'label'    => 'admin.logs.content',
                    'attr'     => ['class' => 'form-control', 'rows' => 20],
                ]
            ],
            [
                'action', ChoiceType::class, [
                    'required' => true,
                    'label'    => 'admin.logs.action',
                    'choices'  => [
                        'admin.logs.decrypt' => Log::ACTION_DECRYPT,
                        'admin.logs.encrypt' => Log::ACTION_ENCRYPT,
                    ],
                ]
            ]
        )->willReturnSelf();

        $this->formType->buildForm($formBuilder, []);
    }
}
