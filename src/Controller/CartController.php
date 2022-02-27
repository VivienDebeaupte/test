<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Item;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Promotion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart", name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        // Je définis les marques avec leurs TVA, leurs frais de livraison
        // et le maximum d'articles, pour ces frais de livraison
        $farmitoo  = new Brand('Farmitoo', 1.20, 20, 3);
        $gallagher = new Brand('Gallagher', 1.05, 15);

        // Je définis des articles avec un titre, un prix et une marque (instanciées ci-dessus)
        $product1 = new Product('Cuve à gasoil', 250000, $farmitoo);
        $product2 = new Product('Nettoyant pour cuve', 5000, $farmitoo);
        $product3 = new Product('Piquet de clôture', 1000, $gallagher);

        // Je définis les promotions actuelles avec des conditions pour y être éligible
        // un montant minimum sur la commande (hors taxe)
        // une date de validité.
        //
        // Elle permet à l'utilisateur de bénéficier d'une réduction sur sa commande,
        // d'avoir potentiellement les frais de livraison offert.
        // On définit un nombre de fois que la promotion peux se cumuler en respectant les prérequis.
        $promotion1 = new Promotion(
            50000,
            8,
            false,
            new \DateTime('2022-03-28'),
            4
        );
        $promotion2 = new Promotion(
            75000,
            12,
            false,
            new \DateTime('2022-03-05'),
            2
        );
        $promotion3 = new Promotion(
            100000,
            22,
            true,
            null,
            1,
            2
        );
        $promotion4 = new Promotion(
            20000,
            2,
            true,
            new \DateTime('2022-02-02'),
            12,
            5
        );
        $promotion5 = new Promotion(
            15000,
            6,
            true,
            new \DateTime('2022-04-02'),
            8,
            20
        );

        // Je passe une commande avec
        // Cuve à gasoil x1
        // Nettoyant pour cuve x3
        // Piquet de clôture x5
        $item1 = new Item($product1,5);
        $item2 = new Item($product2, 4);
        $item3 = new Item($product3);

        $order = new Order([$item1  , $item2, $item3]);

        // J'utilise cette fonction qui me permet de savoir quelles sont les promotions
        // éligibles à la commande.
        $promotions = $this->chooseReductions($order, [$promotion1, $promotion2, $promotion3, $promotion4, $promotion5]);

        // J'utilise cette fonction pour calculer les frais de livraison "totaux" en fonction
        // des articles et de leurs marques (des conditions de frais de livraison définies sur celles-ci)
        $delivery = $this->calculateDeliveryCost($order);

        // J'effectue le rendu de ma page "panier" avec mon objet commande,
        // mon tableau des promotions éligibles et mon montant de frais de livraison
        return $this->render('cart/index.html.twig', [
            'order'      => $order,
            'delivery'   => $delivery,
            'promotions' => $promotions,
        ]);
    }

    /**
     * @Route("/promotion", name="promotion")
     */
    public function promotion(Request $request): Response
    {
        // Je récupère mes paramètres de mon objet Request
        // venant de ma requête HTTP via mon ajax côté client en méthode GET
        // (L'utilisateur clique sur une promotion)
        $parameters   = $request->query->all();
        $minAmount    = $parameters['minAmount'];
        $reduction    = $parameters['reduction'];
        $freeDelivery = $parameters['freeDelivery'];
        $date         = $parameters['date'];
        $productCount = $parameters['productCount'];
        $numberOfUse  = $parameters['numberOfUse'];
        $delivery     = $parameters['delivery'];
        $total_ht     = $parameters['total_ht'];
        $total_ttc    = $parameters['total_ttc'];

        // Je définis les frais de livraison à 0 si la promotion offre les frais de livraison gratuits
        if($freeDelivery == true)
            $delivery = 0;

        // Je calcule combien de fois la promotion peux être appliquée
        // et que ça ne dépasse pas le maximum de fois autorisée
        $numOfUse = floor($total_ht/$minAmount);
        if($numberOfUse < $numOfUse)
            $numOfUse = $numberOfUse;

        // Je calcule les totaux avec la promotion
        $total_ht  = $total_ht - $total_ht*($numOfUse*$reduction/100);
        $total_ttc = $total_ttc - $total_ttc*($numOfUse*$reduction/100);

        // Je retourne une réponse au format json qui contient
        // le rendu de mon tableau des nouveaux montants totaux calculés
        // et le total ttc qui sera utilisé pour le formulaire vers la page de paiement
        return new JsonResponse([
            'view' => $this->renderView('cart/includes/total_table.html.twig', [
                'delivery'  => $delivery,
                'total_ht'  => $total_ht,
                'total_ttc' => $total_ttc
            ]),
            'total_ttc' => $total_ttc
        ]);

    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment(Request $request): Response
    {
        $cost = $request->request->get('cost');

        // Je retourne le rendu de ma page avec le total ttc
        return $this->render('cart/payment.html.twig', [
            'cost'  => $cost,
        ]);
    }

    private function calculateDeliveryCost(Order $order): float
    {
        // Je définis un array pour regrouper toutes les informations nécessaires
        $deliveries = [];

        // Je boucle sur ma commande pour définir combien j'ai d'articles pour chaque marque
        foreach ($order->getItems() as $item) {

            // J'inclus pour chaque marque le coût des frais de livraison et le maximum d'articles pour ce coût
            $deliveries[$item->getProduct()->getBrand()->getName()]['cost'] =
                $item->getProduct()->getBrand()->getDeliveryCost();
            $deliveries[$item->getProduct()->getBrand()->getName()]['max_count'] =
                $item->getProduct()->getBrand()->getDeliveryMaxArticlesCount();
            if(!isset($deliveries[$item->getProduct()->getBrand()->getName()]['count']))
                $deliveries[$item->getProduct()->getBrand()->getName()]['count'] = 0;

            // J'additionne les quantités pour chaque marque
            $deliveries[$item->getProduct()->getBrand()->getName()]['count'] += $item->getQuantity();
        }

        $total_delivery = 0;

        // Je boucle sur mon array pour calculer le coût de livraison total
        // en fonction des paliers définis pour chaque marque
        foreach($deliveries as $delivery) {
            if($delivery['max_count'] === null) {
                $total_delivery += $delivery['cost'];
            } else {
                $total_delivery += ceil($delivery['count']/$delivery['max_count']) * $delivery['cost'];
            }
        }
        return $total_delivery;
    }

    private function chooseReductions(Order $order, array $promotions): array
    {
        $totalHt      = 0;
        $productCount = 0;

        // Je boucle sur ma commande pour définir son montant total hors taxes
        // et son nombre d'aticles
        foreach($order->getItems() as $item) {
            $product  = $item->getProduct();
            $totalHt += $product->getPrice() * $item->getQuantity();
            $productCount += $item->getQuantity();
        }

        // Je retire de mon array les promotions qui ne peuvent pas être appliquées à ma commande
        // en fonction de son montant, de son nombre d'articles et de la date d'aujourd'hui
        foreach ($promotions as $key => $promotion) {
            if(
                $totalHt < $promotion->getMinAmount() ||
                $productCount < $promotion->getProductCount() ||
                ($promotion->getDate() && new \DateTime('now') > $promotion->getDate())
            )
                unset($promotions[$key]);
        }

        // je retourne les promotions éligibles
        return $promotions;
    }


}
