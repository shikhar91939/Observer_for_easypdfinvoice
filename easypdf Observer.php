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


		// Zend_debug::dump($event->getCustomerId());echo '<hr>';//die;
		

		foreach ($item_warehouse_ids as $key => $each_warehouse_id) {
			$event->setCustomerId($each_warehouse_id);
			break;//just using the warehouse assigned to the 1st item. As all are same
		}
		Zend_debug::dump($event->getCustomerId());die;

		return;
	}

	function easypdf_invoiceObserver($observer)
	{
		$event = $observer->getEvent()->getData('source');
		/*
		*for warehouse id... from oder. This is not being used now. now we are  geting warehouse from the items
		*/
		
		/*$order = $event->getData('order')->getData();
		$warehouse_id = $order['data']->warehouse_id;

		Zend_debug::dump( $warehouse_id);die;

		$event = $observer->getEvent()->getData('source');

		// Zend_debug::dump($event);
		// $warehouse_id = $event->getData('warehouse_id');echo $warehouse_id.'asq';die;
		$customer_note = $event->getData('customer_note');
		// echo $customer_note;die;
		// Zend_debug::dump($event);die;

		echo "$warehouse_id<hr/>";
		if(!is_null($warehouse_id))
		{
			$warehouse_name = Mage::getModel('awa_inventory/warehouse')->load($warehouse_id)->getData('completeaddress');
		}

		// Zend_debug::dump($warehouse_name);
		// die;*/



		/*
		*for warehouse id from items in the invoice
		*/

		$order = $event->getData('order')->getData();
		$shipment_id = $event->getEntityId();
		// Zend_debug::dump( $shipment_id);die;
		$shipmentitems = Mage::getModel('sales/order_shipment')->load($shipment_id)->getAllItems();
		Zend_debug::dump( $shipmentitems);die;
		foreach ($shipmentitems as $item) 
		{
			$item_warehouse_ids[] = Mage::getModel('sales/order_item')->load($item->getOrderItemId())->getWarehouseId();
		}
		Zend_debug::dump($item_warehouse_ids);die;
	}
}