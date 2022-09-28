<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */


declare(strict_types=1);

namespace spec\BitBag\SyliusMultiVendorMarketplacePlugin\Operator;

use BitBag\SyliusMultiVendorMarketplacePlugin\Entity\ProductInterface;
use BitBag\SyliusMultiVendorMarketplacePlugin\Entity\ProductListing\ProductDraftInterface;
use BitBag\SyliusMultiVendorMarketplacePlugin\Entity\ProductListing\ProductDraftTaxonInterface;
use BitBag\SyliusMultiVendorMarketplacePlugin\Operator\ProductDraftTaxonsOperator;
use BitBag\SyliusMultiVendorMarketplacePlugin\Operator\ProductDraftTaxonsOperatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductDraftTaxonsOperatorSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        FactoryInterface $productTaxonFactory
    ): void {
        $this->beConstructedWith(
            $entityManager,
            $productTaxonFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductDraftTaxonsOperator::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ProductDraftTaxonsOperatorInterface::class);
    }

    public function it_copies_taxons_from_draft_to_product(
        ProductDraftInterface $productDraft,
        ProductInterface $product,
        TaxonInterface $taxon,
        ProductDraftTaxonInterface $productDraftTaxon,
        ProductTaxonInterface $productTaxon,
        FactoryInterface $productTaxonFactory
    )
    {
        $productDraft->getMainTaxon()->willReturn($taxon);
        $productTaxonFactory->createNew()->willReturn($productTaxon);
        $productDraftTaxon->getTaxon()->willReturn($taxon);
        $productDraftTaxon->getProductDraft()->willReturn($productDraft);
        $productDraftTaxons = new ArrayCollection([$productDraftTaxon->getWrappedObject()]);
        $productDraft->getProductDraftTaxons()->willReturn($productDraftTaxons);
        $productTaxon->getProduct()->willReturn(null);
        $productTaxon->getTaxon()->willReturn(null);
        $productTaxons = new ArrayCollection([$productTaxon->getWrappedObject()]);
        $product->getProductTaxons()->willReturn($productTaxons);

        $this->copyTaxonsToProduct($productDraft, $product);

        $productTaxon->setProduct($product)->shouldHaveBeenCalled();
        $productTaxon->setTaxon($taxon)->shouldHaveBeenCalled();
        $product->addProductTaxon($productTaxon)->shouldHaveBeenCalled();
        $product->setMainTaxon($taxon)->shouldHaveBeenCalled();
    }
}
