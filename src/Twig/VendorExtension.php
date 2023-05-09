<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\OpenMarketplace\Twig;

use BitBag\OpenMarketplace\Entity\VendorProfileUpdate;
use BitBag\OpenMarketplace\Provider\VendorProviderInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\CompositeLocaleContext;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VendorExtension extends AbstractExtension
{
    private VendorProviderInterface $vendorProvider;

    private ObjectManager $manager;

    private CompositeLocaleContext $localeContext;

    private ChannelRepositoryInterface $channelRepository;

    private TaxonRepositoryInterface $taxonRepository;

    private CachedPerRequestChannelContext $channelContext;

    public function __construct(
        VendorProviderInterface $vendorProvider,
        ObjectManager $manager,
        CompositeLocaleContext $localeContext,
        ChannelRepositoryInterface $channelRepository,
        TaxonRepository $taxonRepository,
        CachedPerRequestChannelContext $channelContext
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->manager = $manager;
        $this->localeContext = $localeContext;
        $this->channelRepository = $channelRepository;
        $this->taxonRepository = $taxonRepository;
        $this->channelContext = $channelContext;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_pending_vendor_profile_update', [$this, 'isPendingVendorProfileUpdate']),
            new TwigFunction('current_locale', [$this, 'currentLocale']),
            new TwigFunction('get_channel', [$this, 'getChannel']),
            new TwigFunction('get_channel_main_taxon', [$this, 'getChannelMainTaxon']),
        ];
    }

    public function isPendingVendorProfileUpdate(): bool
    {
        $vendor = $this->vendorProvider->provideCurrentVendor();
        $pendingUpdate = $this->manager->getRepository(VendorProfileUpdate::class)
            ->findOneBy(['vendor' => $vendor]);

        if (null === $pendingUpdate) {
            return true;
        }

        return false;
    }

    public function currentLocale(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    public function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        return $channel;
    }

    public function getChannelMainTaxon(): TaxonInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
//        dd($channel);
        return $channel->getMenuTaxon();
    }
}
