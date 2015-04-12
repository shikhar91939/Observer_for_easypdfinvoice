<?php
class Overcart_BarcodeTesting_Model_Observer
{
	function printShipment($observer)// function called before pdf is downloaded. This is the pdf created when we press the Print button on the Shipment page .
	{
		// $event = $observer;//->getEvent()->getData();
		// Zend_debug::dump($event);
		// die;
	}

	function easypdf_shipmentObserver($observer)
	{
		/*
		* for barcode...
		*/
		$event = $observer->getEvent()->getData('source');
		// Zend_debug::dump($event);die;

		
		$inc_id = $event->getIncrementId();
		$tracking = $event->getData('tracking');

		$awbNumber = null;
		foreach ($tracking as $track) {
			$awbNumber = $track->getTrackNumber();
		}
		$event->setIncrementId($awbNumber);

		/*
		*for warehouse id...
		*/

		$order = $event->getData('order')->getData();
		$shipment_id = $event->getEntityId();
		// Zend_debug::dump( $shipment_id);die;
		$shipmentitems = Mage::getModel('sales/order_shipment')->load($shipment_id)->getAllItems();
		foreach ($shipmentitems as $item) 
		{
			$item_warehouse_ids[] = Mage::getModel('sales/order_item')->load($item->getOrderItemId())->getWarehouseId();
		}
		// Zend_debug::dump($item_warehouse_ids);echo '<hr>';//die;


		// $order_data  = $order->getData();
		$customer_id = $order['data']->customer_id;
		// Zend_debug::dump($customer_id);echo '<hr>';//die;
		

		foreach ($item_warehouse_ids as $each_warehouse_id) {
			$order['data']->setData('customer_id',$each_warehouse_id);
			// $event->setCustomerId($each_warehouse_id);//discarded method as wrong version of cust. id
			break;//just using the warehouse assigned to the 1st item. As all are same
		}
		// Zend_debug::dump($event);die;
		$customer_id = $order['data']->customer_id;
		// Zend_debug::dump($customer_id);echo '<hr>';die;

		return;
	}

	function easypdf_invoiceObserver($observer)
	{
		$event = $observer->getEvent()->getData('source');
		$order_id = $event->getOrderId();

		/*
		for warehouse id from items in the invoice...
		*/

		// Zend_debug::dump($event->getWarehouseId());die;		
		$order = $event->getData('order')->getData();	
		// Zend_debug::dump($order['data']->customer_id);//echo '<hr>';//die;
		$order['data']->setData('customer_id',$event->getWarehouseId());//$event->getWarehouseId()
		// Zend_debug::dump( $order['data']->customer_id);//echo '<hr>';die;


		/*
		for checking the attribute show_imei_in_invoice
		*/
		
		$order_items = Mage::getModel('sales/order')->load($order_id)->getAllItems();

		foreach ($order_items as $current_item) // IMPORTANT: this loops over all the items in the order (not invice) 
		{
			Zend_debug::dump($current_item->getSku());
		}die;


		
	}
}