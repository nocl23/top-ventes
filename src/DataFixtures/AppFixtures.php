<?php

namespace App\DataFixtures;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $categories = ["Ordinateurs","Livres","Decoration","Meubles"];
        $categoriesObj = array();

        for($i=0; $i < sizeof($categories) ; $i++){
            $category = new Category();
            $category->setName($categories[$i]);
            $categoriesObj[$i] = $category;
            $manager->persist($category);
        }
        $manager->flush();
        

        $productsOrdinateurs = ["APPLE","Intel","DELL","ACER","ASUS","HP"];
        $productsLivres = ["Apprendre le HTML","Asterix et Cleopatre","Harry Potter"];
        $productsDecoration = ["Lampe","Cadre","Pele Mele","Tapis","Plaid","Figurine"];
        $productsMeubles = ["Canape","Fauteuil","Table","Meuble TV","Bureau","Chaise","Lit","Etagere"];

        $products = array();

        $allProducts = [$productsOrdinateurs,$productsLivres,$productsDecoration,$productsMeubles];

        for($i = 0;$i<sizeof($allProducts);$i++){
            
			for($j = 0; $j< sizeof($allProducts[$i]); $j++){
                $product = new Product();
                $product->setName($allProducts[$i][$j]);
                $product->setPrice(rand(0,100));
                $product->setCategory($categoriesObj[$i]);
                $products[] = $product;
                $manager->persist($product);
            }
            
        };
        $productsSize = sizeof($products);
        $manager->flush();

        for ($i = 0; $i < 30; $i++) {
            $order = new Orders();
            $order->setName("Commande - " . ($i + 1));
            $manager->persist($order);
            $orderList[] = $order;
        }
        $manager->flush();

        for ($i = 0;$i< sizeof($orderList); $i++){

            $OrderItemsNumber = rand(1,10);

            for($j = 0 ; $j < $OrderItemsNumber ; $j++){
                $orderItem = new OrderItems();
                $orderItem->setProduct($products[rand(0,$productsSize-1)]);
                $orderItem->setQuantity(rand(1, 6));
                $orderItem->setOrders($orderList[$i]);
                $manager->persist($orderItem);
            }

        }
        $manager->flush();

    }
        
}
