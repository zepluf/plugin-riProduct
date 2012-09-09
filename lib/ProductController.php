<?php

namespace plugins\riProduct;

use Symfony\Component\HttpFoundation\Request;

use plugins\riSimplex\Controller;
use plugins\riPlugin\Plugin;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller{
	
	public function ajaxFindByNameAction(Request $request) {
		$products = Plugin::get('riProduct.Products')->findByName($request->get('term'));
		$data = array();
		foreach ($products as $product){
			$data[] = array('id' => $product->getProductsId(), 'label' => $product->getDescription()->getProductsName());
		}
		return new Response(json_encode(
        	$data
        ));   
	}
}