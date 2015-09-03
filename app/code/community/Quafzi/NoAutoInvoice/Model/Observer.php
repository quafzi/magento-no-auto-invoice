<?php
/**
 * @category   Sales
 * @package    Quafzi_NoAutoInvoice
 * @license    MIT
 * @author     Thomas Birke <tbirke@netextreme.de>
 */
class Quafzi_NoAutoInvoice_Model_Observer
{
    public function sales_order_shipment_save_before($event)
    {
        $this->_validateCountry($event->getShipment()->getShippingAddress()->getCountryId());
    }

    public function sales_order_invoice_save_before($event)
    {
        $this->_validateCountry($event->getInvoice()->getShippingAddress()->getCountryId());
    }

    protected function _validateCountry($countryId)
    {
        if (false === $this->_getHelper()->isInvoicingAllowed($countryId)) {
            $this->_setValidationFailure($this->_getHelper()->__('Auto-invoicing is disabled for %s.', $countryId));
        }
    }

    /**
     * Clear previous success messages and add error message.
     * This needs to be done as in CE 1.6.2.0 a success message
     * is added even before the shipment is actually saved.
     * As session messages are already translated, there is no
     * reliable way to remove only the "shipment has been created"
     * message. Therefore all previous success messages get
     * deleted here.
     *
     * @param string $errorMessage
     * @throws Mage_Core_Exception
     */
    protected function _setValidationFailure($errorMessage)
    {
        /* remove success messages */
        /* @var $messageCollection Mage_Core_Model_Message_Collection */
        $messageCollection = Mage::getSingleton('adminhtml/session')->getMessages();
        $messages = $messageCollection->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        /* @var $message Mage_Core_Model_Message_Abstract */
        foreach ($messages as $message) {
            $messageCollection->deleteMessageByIdentifier($message->getIdentifier());
        }

        // avoid further handling by DHL extension
        Mage::unregister('current_shipment');

        Mage::throwException($errorMessage);
    }

    /**
     * get this extension's helper
     *
     * @return Quafzi_NoAutoInvoice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('quafzi_noautoinvoice');
    }
}
