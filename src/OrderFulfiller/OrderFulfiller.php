<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderFulfiller;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusMiintoPlugin\Exception\ConstraintViolationException;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Provider\AddressProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\CustomerProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\OrderItemsProviderInterface;
use Setono\SyliusMiintoPlugin\Repository\PaymentMethodMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingTypeMappingRepositoryInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\SMException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class OrderFulfiller implements OrderFulfillerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var FactoryInterface */
    private $orderFactory;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    /** @var OrderItemsProviderInterface */
    private $orderItemsProvider;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var AddressProviderInterface */
    private $billingAddressProvider;

    /** @var AddressProviderInterface */
    private $shippingAddressProvider;

    /** @var PaymentMethodMappingRepositoryInterface */
    private $paymentMethodMappingRepository;

    /** @var ShippingTypeMappingRepositoryInterface */
    private $shippingTypeMappingRepository;

    /** @var ValidatorInterface */
    private $validator;

    /** @var array */
    private $orderValidationGroups;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider,
        OrderItemsProviderInterface $orderItemsProvider,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressProviderInterface $billingAddressProvider,
        AddressProviderInterface $shippingAddressProvider,
        PaymentMethodMappingRepositoryInterface $paymentMethodMappingRepository,
        ShippingTypeMappingRepositoryInterface $shippingTypeMappingRepository,
        ValidatorInterface $validator,
        array $orderValidationGroups
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderProcessor = $orderProcessor;
        $this->customerProvider = $customerProvider;
        $this->orderItemsProvider = $orderItemsProvider;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->billingAddressProvider = $billingAddressProvider;
        $this->shippingAddressProvider = $shippingAddressProvider;
        $this->paymentMethodMappingRepository = $paymentMethodMappingRepository;
        $this->shippingTypeMappingRepository = $shippingTypeMappingRepository;
        $this->validator = $validator;
        $this->orderValidationGroups = $orderValidationGroups;
    }

    /**
     * @throws SMException
     * @throws StringsException
     */
    public function fulfill(OrderInterface $order): void
    {
        $data = $order->getData();

        $shop = $this->getShop($order);
        $channel = $this->getChannel($shop);
        $localeCode = $this->getLocaleCode($shop);
        $paymentMethod = $this->getPaymentMethod($shop);
        $shippingType = $this->getShippingType($order);
        $shippingMethod = $this->getShippingMethod($shop, $shippingType);

        /** @var SyliusOrderInterface $syliusOrder */
        $syliusOrder = $this->orderFactory->createNew();

        $orderStateMachine = $this->stateMachineFactory->get($syliusOrder, OrderCheckoutTransitions::GRAPH);

        $syliusOrder->setChannel($channel);
        $syliusOrder->setLocaleCode($localeCode);
        $syliusOrder->setCurrencyCode($data['currency']);

        $customer = $this->customerProvider->provide($order);
        $syliusOrder->setCustomer($customer);

        // add order items
        $orderItems = $this->orderItemsProvider->provide($order);
        if (count($orderItems->getFulfillablePositionIds()) === 0) {
            return;
        }
        foreach ($orderItems->getOrderItems() as $orderItem) {
            $syliusOrder->addItem($orderItem);
        }

        $this->orderProcessor->process($syliusOrder);

        // add addresses
        $billingAddress = $this->billingAddressProvider->provide($order);
        $syliusOrder->setBillingAddress($billingAddress);

        $shippingAddress = $this->shippingAddressProvider->provide($order);
        $syliusOrder->setShippingAddress($shippingAddress);

        $orderStateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

        // add shipment
        foreach ($syliusOrder->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }
        $orderStateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        // add payment
        foreach ($syliusOrder->getPayments() as $payment) {
            $payment->setMethod($paymentMethod);
        }
        $orderStateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

        // complete order
        $orderStateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        // mark order as paid
        $orderPaymentStateMachine = $this->stateMachineFactory->get($syliusOrder, OrderPaymentTransitions::GRAPH);
        $orderPaymentStateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY);

        $violations = $this->validator->validate($syliusOrder, null, $this->orderValidationGroups);
        if ($violations->count() > 0) {
            throw new ConstraintViolationException($violations);
        }

        $order->setOrder($syliusOrder);

        $this->orderRepository->add($syliusOrder);
    }

    /**
     * @throws StringsException
     */
    private function getShippingType(OrderInterface $order): string
    {
        $data = $order->getData();

        if (!isset($data['shippingInformation']['deliveryAddress']['type'])) {
            throw new InvalidArgumentException(sprintf('No shipping type set on order %s', $order->getId()));
        }

        return $data['shippingInformation']['deliveryAddress']['type'];
    }

    /**
     * @throws StringsException
     */
    private function getShop(OrderInterface $order): ShopInterface
    {
        $shop = $order->getShop();
        if (null === $shop) {
            throw new InvalidArgumentException(sprintf('No shop set on order %s', $order->getId()));
        }

        return $shop;
    }

    /**
     * @throws StringsException
     */
    private function getChannel(ShopInterface $shop): ChannelInterface
    {
        $channel = $shop->getChannel();
        if (null === $channel) {
            throw new InvalidArgumentException(sprintf('No channel set on shop %s', $shop->getId()));
        }

        return $channel;
    }

    /**
     * @throws StringsException
     */
    private function getLocaleCode(ShopInterface $shop): string
    {
        $locale = $shop->getLocale();
        if (null === $locale) {
            throw new InvalidArgumentException(sprintf('No locale set on shop %s', $shop->getId()));
        }

        $code = $locale->getCode();
        if (null === $code) {
            throw new InvalidArgumentException(sprintf('No code set on locale %s', $locale->getId()));
        }

        return $code;
    }

    /**
     * @throws StringsException
     */
    private function getPaymentMethod(ShopInterface $shop): PaymentMethodInterface
    {
        $mapping = $this->paymentMethodMappingRepository->findOneValid($shop);
        if (null === $mapping) {
            throw new InvalidArgumentException(sprintf('No payment method mapping set for the shop with id %s', $shop->getId()));
        }

        $paymentMethod = $mapping->getPaymentMethod();
        if (null === $paymentMethod) {
            throw new InvalidArgumentException(sprintf('No payment method mapping set on the mapping %s', $mapping->getId()));
        }

        return $paymentMethod;
    }

    /**
     * @throws StringsException
     */
    private function getShippingMethod(ShopInterface $shop, string $shippingType): ShippingMethodInterface
    {
        $mapping = $this->shippingTypeMappingRepository->findOneValid($shop, $shippingType);

        if ($mapping === null) {
            throw new InvalidArgumentException(sprintf('No payment method mapping set for the shop with id %s', $shop->getId()));
        }

        $shippingMethod = $mapping->getShippingMethod();
        if (null === $shippingMethod) {
            throw new InvalidArgumentException(sprintf('No shipping method mapping set on the mapping %s', $mapping->getId()));
        }

        return $shippingMethod;
    }
}
