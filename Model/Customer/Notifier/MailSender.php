<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Notifier;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Model\Notifier\AbstractMailSender;

final class MailSender extends AbstractMailSender implements SenderInterface
{
    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        View $customerViewHelper,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        array $configPaths
    ) {
        $this->customerViewHelper = $customerViewHelper;
        $this->storeManager = $storeManager;
        parent::__construct($transportBuilder, $scopeConfig, $configPaths);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function send(CustomerInterface $customer): void
    {
        $storeId = $customer->getStoreId() === null ? null : (int) $customer->getStoreId();
        $vars = [
            'customer' => $customer,
            'store' => $this->storeManager->getStore($customer->getStoreId()),
        ];

        $this->sendMail($customer->getEmail(), $this->customerViewHelper->getCustomerName($customer), $storeId, $vars);
    }
}
