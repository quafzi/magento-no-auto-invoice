<?php
/**
 * @category   Sales
 * @package    Quafzi_NoAutoInvoice
 * @license    MIT
 * @author     Thomas Birke <tbirke@netextreme.de>
 */
class Quafzi_NoAutoInvoice_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * if user wants to create an invoice manually
     *
     * @return bool
     */
    public function isManualInvoiceCreation()
    {
        $allowedControllerNames = ['sales_order_invoice', 'sales_order_creditmemo'];
        return (Mage::app()->getRequest()
            && 'save' === Mage::app()->getRequest()->getActionName()
            && in_array(Mage::app()->getRequest()->getControllerName(), $allowedControllerNames)
        );
    }

    /**
     * if auto-invoicing is allowed for given country
     *
     * @param int $countryId Country ID (e.g. "DE")
     * @return bool
     */
    public function isInvoicingAllowed ($countryId)
    {
        $allowedCountryIds = explode(',', Mage::getStoreConfig('sales/invoice/autocreation_countries'));
        return $this->isManualInvoiceCreation() || in_array($countryId, $allowedCountryIds);
    }
}
