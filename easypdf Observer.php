<?php
class Overcart_BarcodeTesting_Model_Observer
{
	function easypdf_shipmentObserver($observer)
	{
		/*
		* for barcode...
		*/
		$event = $observer->getEvent()->getData('source');
		
		// Zend_debug::dump($event->getIncrementId());die; gives shipment incriment ID
		$tracking = $event->getData('tracking');

		$awbNumber = null;
		foreach ($tracking as $track) {
			$awbNumber = $track->getTrackNumber();
		}
		$event->setIncrementId($awbNumber);
		// Zend_debug::dump($event->getIncrementId());die; // now shipment incriment ID has been changed to AWB no.

		/*
		*for warehouse id...
		*/

		$order = $event->getData('order')->getData();
		$shipment_id = $event->getEntityId();

		$shipmentitems = Mage::getModel('sales/order_shipment')->load($shipment_id)->getAllItems();
		foreach ($shipmentitems as $item) 
		{
			$item_warehouse_ids[] = Mage::getModel('sales/order_item')->load($item->getOrderItemId())->getWarehouseId();
		}
		$customer_id = $order['data']->customer_id;

		foreach ($item_warehouse_ids as $each_warehouse_id) {
			$order['data']->setData('customer_id',$each_warehouse_id);
			break;//just using the warehouse assigned to the 1st item. As all are same
		}
		$customer_id = $order['data']->customer_id;
		return;
	}

	function easypdf_invoiceObserver($observer)
	{
		$event = $observer->getEvent()->getData('source');
		$order_id = $event->getOrderId();

		/*
		for warehouse id from items in the invoice...
		*/
		$order = $event->getData('order')->getData();	
		$order['data']->setData('customer_id',$event->getWarehouseId());
	}
}