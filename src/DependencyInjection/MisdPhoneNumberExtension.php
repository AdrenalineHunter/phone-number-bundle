<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Bundle extension.
 */
class MisdPhoneNumberExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('jms_serializer.xml');

        if (interface_exists('Symfony\Component\Templating\Helper\HelperInterface')) {
            $loader->load('templating.xml');
        }
        if (class_exists('Symfony\Bundle\TwigBundle\TwigBundle')) {
            $loader->load('twig.xml');
        }
        if (interface_exists('Symfony\Component\Form\FormTypeInterface')) {
            $loader->load('form.xml');
        }
        if (interface_exists('Symfony\Component\Serializer\Normalizer\NormalizerInterface')) {
            $loader->load('serializer.xml');
        }

        $this->setFactory($container->getDefinition('libphonenumber\PhoneNumberUtil'));
        $this->setFactory($container->getDefinition('libphonenumber\geocoding\PhoneNumberOfflineGeocoder'));
        $this->setFactory($container->getDefinition('libphonenumber\ShortNumberInfo'));
        $this->setFactory($container->getDefinition('libphonenumber\PhoneNumberToCarrierMapper'));
        $this->setFactory($container->getDefinition('libphonenumber\PhoneNumberToTimeZonesMapper'));
    }
    /**
     * Set Factory of FactoryClass & FactoryMethod based on Symfony version.
     *
     * to be removed when dependency on Symfony DependencyInjection is bumped to 2.6 and
     * services inlined in services.xml
     *
     * @param $def
     */
    private function setFactory(Definition $def)
    {
        if (method_exists($def, 'setFactory')) {
            // to be inlined in services.xml when dependency on Symfony DependencyInjection is bumped to 2.6
            $def->setFactory(array($def->getClass(), 'getInstance'));
        } else {
            // to be removed when dependency on Symfony DependencyInjection is bumped to 2.6
            $def->setFactoryClass($def->getClass());
            $def->setFactoryMethod('getInstance');
        }
    }
}

