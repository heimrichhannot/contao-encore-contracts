<?php

namespace HeimrichHannot\EncoreContracts;

use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait PageAssetsTrait
{
    use AddPageEntrypointTrait;

    /** @var ContainerInterface $container */
    protected $container;

    public static function getSubscribedServices(): array
    {
        $services = \method_exists(\get_parent_class(self::class) ?: '', __FUNCTION__)
            ? parent::getSubscribedServices()
            : [];

        if (\class_exists(FrontendAsset::class)) {
            $services[] = '?'.FrontendAsset::class;
        }

        return $services;
    }

    #[Required]
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $ret = null;
        if (\method_exists(\get_parent_class(self::class) ?: '', __FUNCTION__)) {
            $ret = parent::setContainer($container);
        }

        if ($ret instanceof ContainerInterface) {
            return $ret;
        }

        return $this->container = null;
    }

    /**
     * @return FrontendAsset|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getFrontendAsset(): mixed
    {
        if (!\class_exists(FrontendAsset::class)) {
            return null;
        }

        if (!isset($this->container)) {
            return null;
        }

        if (!$this->container->has(FrontendAsset::class)) {
            return null;
        }

        $service = $this->container->get(FrontendAsset::class);

        if (!$service instanceof FrontendAsset) {
            return null;
        }

        return $service;
    }
}