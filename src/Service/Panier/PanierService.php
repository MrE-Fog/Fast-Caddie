<?php


namespace App\Service\Panier;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{

    public $session;
    public $articleRepository;

    public function __construct(SessionInterface $session, ArticlesRepository  $articleRepository)
    {
        $this->session=$session;
        $this->articleRepository=$articleRepository;
    }



    public function add(int $id)
    {

        /**
         * déclaration en session d'un panier qui charge les produit par id
         * et quantifie le nombre de fois que le même produit a été ajouté
         * via l'ajout de l'id
         */
        $panier= $this->session->get('panier', []);
        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id]=1;
        }
        $this->session->set('panier', $panier);
    }

    public function remove(int $id)
    {

        /**
         * decharge les produit du panier par id
         * et requantifie le nombre de fois que le même produit a été retiré
         * via la décrémentation de l'id
         */

        $panier= $this->session->get('panier', []);
        if(!empty($panier[$id] )&& $panier[$id]>1){
            $panier[$id]--;

        }else{
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    public function delete(int $id)
    {

        /**
         *Vide totalement la ligne du produit appelé via son id
         */
        $panier=$this->session->get('panier', []);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);

    }


    public function getFullPanier() : array
    {

        /**
         * boucle permettant de synthétiser l'intégralité
         * des ajout effectués sur le panier et la quantité de chaques ajouts
         */

        $panier = $this->session->get('panier', []);

        $panierDetail=[];
        foreach ($panier as $id => $quantite){
            if ($quantite < 1){ $quantite = 0;}
            $panierDetail[]=[
                'article'=>$this->articleRepository->find($id),
                'quantite'=>$quantite
            ];
        }

        return $panierDetail;
    }




    public function getTotal() : float
    {
        $total=0;
        /**
         * fonction permettant d'avoir le montant total
         * du panier au fur et à mesure des ajouts retraits ou suppression.
         * Même montant qui va être setter lors de l'envoie de la commande
         */

        foreach ($this->getFullPanier() as $item){

            $total += $item['article']->getPrix() * $item['quantite'];
        }


        return $total;
    }
}
