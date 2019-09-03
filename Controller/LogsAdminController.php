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

namespace Ekino\DataProtectionBundle\Controller;

use Ekino\DataProtectionBundle\Encryptor\EncryptorInterface;
use Ekino\DataProtectionBundle\Exception\EncryptionException;
use Ekino\DataProtectionBundle\Form\Type\LogType;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogsAdminController.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class LogsAdminController extends Controller
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * LogsAdminController constructor.
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function decryptEncryptAction(Request $request): Response
    {
        $form    = $this->createForm(LogType::class);
        $results = [];

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log     = $form->getData();
            $content = $log->getContent();
            try {
                $results = $log->isDecryptAction() ? $this->getDecryptedResults($content) : $this->getEncryptedResult($content);
            } catch (EncryptionException $e) {
                $message = $log->isDecryptAction() ? 'admin.logs.decrypt.error' : 'admin.logs.encrypt.error';

                $this->addFlash('error', $this->trans($message, [], 'EkinoDataProtectionBundle'));
            }
        }

        return $this->renderWithExtraParams('@EkinoDataProtection/LogsAdmin/decrypt.html.twig', [
            'action'  => 'decrypt',
            'results' => $results,
            'form'    => $form->createView(),
        ], null);
    }

    /**
     * @param string $content
     *
     * @return array
     */
    private function getDecryptedResults(string $content): array
    {
        $results = [];
        preg_match_all('#"private_(.*?)":"(.[^"]*)"#m', $content, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            foreach ($matches as $index => $match) {
                $results[sprintf('%s_%d', $match[1], $index + 1)] = json_decode($this->encryptor->decrypt($match[2]), true);
            }
        } else {
            $results['result'] = json_decode($this->encryptor->decrypt($content), true);
        }

        return $results;
    }

    /**
     * @param string $content
     *
     * @return array
     */
    private function getEncryptedResult(string $content): array
    {
        return ['result' => $this->encryptor->encrypt(json_encode($content))];
    }
}
