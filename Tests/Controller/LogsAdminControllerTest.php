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

namespace Ekino\DataProtectionBundle\Tests\Controller;

use Ekino\DataProtectionBundle\Controller\LogsAdminController;
use Ekino\DataProtectionBundle\Encryptor\EncryptorInterface;
use Ekino\DataProtectionBundle\Exception\EncryptionException;
use Ekino\DataProtectionBundle\Form\DataClass\Log;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogsAdminControllerTest.
 *
 * @author William JEHANNE <william.jehanne@ekino.com>
 */
class LogsAdminControllerTest extends TestCase
{
    /**
     * @var LogsAdminController|MockObject
     */
    private $controller;

    /**
     * @var EncryptorInterface|MockObject
     */
    private $encryptor;

    /**
     * Initialize test LogsAdminControllerTest.
     */
    protected function setUp(): void
    {
        $this->encryptor  = $this->createMock(EncryptorInterface::class);
        $this->controller = $this->getMockBuilder(LogsAdminController::class)
            ->setConstructorArgs([$this->encryptor])
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['addFlash', 'createForm', 'renderWithExtraParams', 'trans'])
            ->getMock();
    }

    /**
     * Test decryptEncryptAction method of LogsAdminController.
     */
    public function testDecryptEncryptAction(): void
    {
        $form = $this->createMock(Form::class);
        $log  = $this->createMock(Log::class);
        $log->expects($this->once())->method('getContent')->willReturn('foo');

        $response = $this->createMock(Response::class);
        $this->controller->expects($this->never())->method('addFlash');
        $this->controller->expects($this->once())->method('renderWithExtraParams')->willReturn($response);

        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($log);

        $this->controller->expects($this->once())->method('createForm')->willReturn($form);

        $request = $this->createMock(Request::class);

        $this->controller->decryptEncryptAction($request);
    }

    /**
     * Test decryptEncryptAction method of LogsAdminController not ok.
     */
    public function testDecryptEncryptActionNok(): void
    {
        $this->encryptor->expects($this->any())->method('encrypt')->willThrowException(new EncryptionException());

        $form = $this->createMock(Form::class);
        $log  = $this->createMock(Log::class);
        $log->expects($this->once())->method('getContent')->willReturn('foo');
        $log->expects($this->any())->method('isDecryptAction')->willReturn(false);

        $response = $this->createMock(Response::class);
        $this->controller->expects($this->once())->method('addFlash')->with('error');
        $this->controller->expects($this->once())->method('renderWithExtraParams')->willReturn($response);

        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($log);

        $this->controller->expects($this->once())->method('createForm')->willReturn($form);

        $request = $this->createMock(Request::class);

        $this->controller->expects($this->once())->method('trans')->with('admin.logs.encrypt.error', [], 'EkinoDataProtectionBundle');

        $this->controller->decryptEncryptAction($request);
    }
}
