<?php

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
use Ekino\DataProtectionBundle\Form\Type\DecryptLogType;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * {@inheritdoc}
     */
    public function decryptAction(Request $request)
    {
        $form    = $this->createForm(DecryptLogType::class);
        $results = [];

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->getData()->getContent();
            preg_match_all('#"private_(.*?)":"(.[^"]*)"#m', $content, $matches, PREG_SET_ORDER);

            if (!empty($matches)) {
                foreach ($matches as $index => $match) {
                    $results[sprintf('%s_%d', $match[1], $index + 1)] = json_decode($this->encryptor->decrypt($match[2]), true);
                }
            } else {
                $results['result'] = json_decode($this->encryptor->decrypt($content), true);
            }
        }

        return $this->renderWithExtraParams('@EkinoDataProtection/LogsAdmin/decrypt.html.twig', [
            'action'  => 'decrypt',
            'results' => $results,
            'form'    => $form->createView(),
        ], null);
    }
}
