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
use Ekino\DataProtectionBundle\Form\Type\DecryptLogType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DecryptLogTypeTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class DecryptLogTypeTest extends TestCase
{
    /**
     * @var DecryptLogType
     */
    private $formType;

    /**
     * @var TranslatorInterface|MockObject
     */
    private $translator;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->formType   = new DecryptLogType($this->translator);
    }

    /**
     * Test configureOptions method.
     */
    public function testConfigureOptions(): void
    {
        $optionResolver = $this->createMock(OptionsResolver::class);
        $optionResolver->expects($this->once())->method('setDefaults')->with(['data_class' => Log::class,]);

        $this->formType->configureOptions($optionResolver);
    }

    /**
     * Test buildForm method.
     */
    public function testBuildForm(): void
    {
        $this->translator->expects($this->once())
            ->method('trans')
            ->with('admin.logs.decrypt.content', [], 'EkinoDataProtectionBundle')
            ->willReturn('foo');
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->once())->method('add')->with('content', TextareaType::class, [
            'required' => true,
            'label'    => 'foo',
            'attr'     => ['class' => 'form-control', 'rows' => 20],
        ]);

        $this->formType->buildForm($formBuilder, []);
    }
}
