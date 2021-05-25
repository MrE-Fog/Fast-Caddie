<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Employe;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CategorieType;
use App\Form\EmployeType;
use App\Repository\ArticlesRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\EmployeRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN", message="Vous devez être un administrateur pour accéder à cette partie du site")
 */
class BackendController extends AbstractController
{
    // ------------- PAGE D'ACCUEIL ------------
    /**
     * @Route("/", name="backoffice")
     */
    public function backoffice() : ?Response
    {
        return $this->render('backend/home_backend.html.twig');
    }

    // ----------- USER ------------
    /**
     * @Route("/gestion_users", name="gestion_users")
     */
    public function gestion_users(UserRepository $userRepository) : ?Response {
        $users = $userRepository->findBy([],["username" => "ASC"]);

        return $this->render("backend/utilisateurs.html.twig", [
            'utilisateurs' => $users
        ]);
    }

    /**
     * @Route("/deleteuser/{id}", name="delete_user")
     */
    public function delete_user(User $user, EntityManagerInterface $manager) : ?Response
    {
        $manager->remove($user);
        $manager->flush();
        $nom = $user->getUsername();
        $this->addFlash('success',"L'utilisateur $nom a bien été supprimé");
        return $this->redirectToRoute('gestion_users');
    }

    // ------------------ EMPLOYE ------------------------

    /**
     * @Route("/add_employe",name="add_employe")
     */
    public function add_employe(Request $request, EntityManagerInterface $manager) {

        $employe= new Employe();

        $formEmploye = $this->createForm(EmployeType::class, $employe, array('ajouter' => true));
        $formEmploye->handleRequest($request);

        if ($formEmploye->isSubmitted() && $formEmploye->isValid()):

            $photoFile = $formEmploye->get('photo')->getData();

            if ($photoFile):

                $nomPhoto = $photoFile->getClientOriginalName();
                $nomPhoto = date("YmdHis")."-".uniqid()."-".$nomPhoto;

                try {
                    $photoFile->move(
                        $this->getParameter('images_directory'),
                        $nomPhoto
                    );
                } catch (FileException $e){

                    $this->redirectToRoute('add_employe',[
                        'erreur'=> $e
                    ]);
                }

                $employe->setPhoto($nomPhoto);

            endif;

            $date = new DateTime("now");
            $date->format("d/m/Y");
            $employe->setDateInscription($date);

            $manager->persist($employe);
            $manager->flush();

            $nom = $employe->getPseudo();
            $this->addFlash("success", "L'employé $nom à bien été ajouté");

            return $this->redirectToRoute("gestion_employe");

        endif;

        return $this->render("backend/employe/add_employe.html.twig", [
            'formEmploye'=>$formEmploye->createView()
        ]);
    }

    /**
     * @Route("/edit_employe/{id}", name="edit_employe")
     */
    public function edit_employe(Employe $employe, EntityManagerInterface $manager, Request $request) {

        $formEmploye = $this->createForm(EmployeType::class, $employe, array('modifier' => true));
        $formEmploye->handleRequest($request);

        if($formEmploye->isSubmitted() && $formEmploye->isValid())
        {

            $photoFile = $formEmploye->get('photoFile')->getData();

            if($photoFile)
            {
                $nomPhoto = date("YmdHis") . "-" . uniqid() . "-" . $photoFile->getClientOriginalName();

                try
                {
                    $photoFile->move(
                        $this->getParameter('images_directory'),
                        $nomPhoto
                    );
                } catch(FileException $e) {}

                if(!empty($employe->getPhoto()))
                {
                    unlink($this->getParameter('images_directory') .'/'. $employe->getPhoto());
                }

                $employe->setPhoto($nomPhoto);

            }

            $manager->persist($employe);
            $manager->flush();

            return $this->redirectToRoute("gestion_employe");
        }

        return $this->render("backend/employe/edit_employe.html.twig" , [
            "formEmploye" => $formEmploye->createView(),
            "employe" => $employe
        ]);
    }

    /**
     * @Route("/gestion_employe",name="gestion_employe")
     */
    public function gestion_employe(EmployeRepository $employeRepository) {

        $employes = $employeRepository->findBy([],["nom" => "ASC"]);

        return $this->render("backend/employe/gestion_employe.html.twig", [
            'employes' => $employes
        ]);
    }

    /**
     * @Route("/delete_employe/{id}" ,name="delete_employe")
     */
    public function delete_employe(EntityManagerInterface $manager, Employe $employe) {
        $manager->remove($employe);
        $manager->flush();

        $nom = $employe->getPseudo();
        $this->addFlash("success", "L'employé $nom à bien été supprimé");
        return $this->redirectToRoute("gestion_employe");
    }

    // ---------------- ARTICLES ---------------------
    /**
     * @Route("/add_article", name="add_article")
     */
    public function add(Request $request, EntityManagerInterface $manager)
    {

        $article= new Articles();

        $form=$this->createForm(ArticleType::class, $article, array('ajouter' => true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):
            // Recup l'image du form
            $imageFile=$form->get('image')->getData();

            if ($imageFile):
                $nomImg = $imageFile->getClientOriginalName();
                $nomImage=date("YmdHis")."-".uniqid()."-".$nomImg;
                // Applique un nom unique à l'image

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $nomImage
                    );
                } catch (FileException $e){
                    $this->redirectToRoute('add_article',[
                        'erreur'=> $e
                    ]);
                }

                $article->setImage($nomImage);

            endif;

            $manager->persist($article);
            $manager->flush();

            $nom = $article->getNom();
            $this->addFlash("success", "L'article $nom a bien été ajouté");

            return $this->redirectToRoute("gestion_articles");

        endif;


        return $this->render('backend/article/add_article.html.twig',[
            'formArticle'=>$form->createView()
        ]);
    }

    /**
     * @Route("/article_modif/{id}" , name="article_modif")
     */
    public function modifier_article(Articles $article, Request $request, EntityManagerInterface $manager )
    {
        $form = $this->createForm(ArticleType::class, $article, array('modifier' => true));
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $imageFile = $form->get('imageFile')->getData();

            if($imageFile)
            {
                $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();

                try
                {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $nomImage
                    );
                } catch(FileException $e) {
                    $this->redirectToRoute('article_modif', [
                        'id' => $article->getId(),
                        'erreur'=> $e
                    ]);
                }

                if(!empty($article->getImage() ))
                {
                    unlink($this->getParameter('images_directory') .'/'. $article->getImage());
                }
                $article->setImage($nomImage);

            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute("gestion_articles");

        }

        return $this->render("backend/article/article_modifier.html.twig" , [
            "formArticle" => $form->createView(),
            "article" => $article
        ]);
    }

    /**
     * @Route("/gestion_articles", name="gestion_articles")
     * @Route("/filter_articles", name="filter_articles")
     */
    public function gestionArticles(ArticlesRepository $repository, Request $request, CategorieRepository $categorieRepository)
    {
        // Initialise les vars nécessaires à la recherche
        $introuvable = false;
        $introuvables = false;

        $articlesChoisi = null;
        $rechercheArticle = null;

        // Effectue les test pour la bar de recherche
        if($request->query->get('nomArticle')) {
            $rechercheArticle = $repository->search($request->query->get('nomArticle'));

            if(empty($rechercheArticle)) {
                $introuvable = true;
            }
        }
        // Effectue les test pour le trie avec la catégorie, le prix et le type
        else if($request->query->get('type') && empty($request->query->get('prixInf'))) {
            $articlesChoisi = $repository->findBy(["categorie" => $request->query->get('categorie'), "type" => $request->query->get('type')], ["prix" => "ASC"]);
            if(empty($articlesChoisi)) {
                $introuvables = true;
            }
        }
        else if($request->query->get('type') && $request->query->get('prixInf')) {
            $articlesChoisi = $repository->findByPrixTypeCategorie($request->query->get('prixInf'),$request->query->get('type'),$request->query->get('categorie'));
            if(empty($articlesChoisi)) {
                $introuvables = true;
            }
        }
        // Effectue les test pour le trie avec la catégorie et le prix
        else if($request->query->get('categorie') && empty($request->query->get('prixInf'))) {
            $articlesChoisi = $repository->findBy(['categorie' => $request->query->get('categorie')], ["prix" => "ASC"]);
            if(empty($articlesChoisi)) {
                $introuvables = true;
            }
        }
        else if($request->query->get('categorie') && $request->query->get('prixInf')) {
            $articlesChoisi = $repository->findByPrixCategorie($request->query->get('prixInf'), $request->query->get('categorie'));
            if(empty($articlesChoisi)) {
                $introuvables = true;
            }
        }
        else if($request->query->get('prixInf') && empty($request->query->get('categorie'))) {
            $articlesChoisi = $repository->findByPrix($request->query->get('prixInf'));
            if(empty($articlesChoisi)) {
                $introuvables = true;
            }
        }
        // Si il n'as rien trouvé alors initialise les vars à null
        else {
            $rechercheArticle = null;
            $articlesChoisi = null;
        }

        // Renvoi les articles et catégorie trier par nom
        $articles= $repository->findBy([], ['nom' => 'ASC']);
        $categories = $categorieRepository->findBy([], ['nom' => 'ASC']);

        return $this->render('backend/article/gestion_articles.html.twig',[
            'articles'=> $articles,
            'introuvable' => $introuvable,
            'rechercheArticle' => $rechercheArticle,
            'articlesChoisi' => $articlesChoisi,
            'introuvables' => $introuvables,
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/show_article/{id}", name="show_article")
     */
    public function showArticle(Articles $article)
    {
        return $this->render("backend/article/show_article.html.twig", [
            'article' => $article
        ]);
    }

    /**
     * @Route("/delete_article/{id}", name="delete_article")
     */
    public function deleteArticle(Articles $articles, EntityManagerInterface $manager)
    {
        $manager->remove($articles);
        $manager->flush();

        $nom = $articles->getNom();
        $this->addFlash('success', "L'article $nom a bien été supprimé");
        return $this->redirectToRoute('gestion_articles');
    }

    // ------------------- LISTE DES COMMANDES ---------------------------
    /**
     * @Route("/gestion_commandes", name="gestion_commandes")
     */
    public function gestion_commandes(CommandeRepository $commandeRepository) {

        $commandes = $commandeRepository->findAll();

        return $this->render("backend/commandes.html.twig", [
            'commandes' => $commandes
        ]);
    }

    /**
     * @Route("/commandes_user/{id}", name="commandes_user")
     */
    public function commandesUser(User $user, CommandeRepository $commandeRepository) {
        $commandes = $commandeRepository->findBy(['user' => $user->getId()]);

        if(empty($commandes)) {
            $this->addFlash("echec", "Cette utilisateurs n'as encore jamais commandé !");
        }

        return $this->render("backend/commandes_user.html.twig", [
            'commandes' => $commandes,
            'user_email' => $user->getEmail()
        ]);
    }

    /**
     * @Route("/delete_commande/{id}", name="delete_commande")
     */
    public function delete_commande(Commande $commande, EntityManagerInterface $manager) {
        $manager->remove($commande);
        $manager->flush();

        $id = $commande->getId();
        $this->addFlash("success", "Commande n°$id supprimé");
        return $this->redirectToRoute("gestion_commandes");
    }

    // -------------- CATEGORIES ----------------------------------
    /**
     * @Route("/categories", name="categories")
     * @Route("/edit_categorie/{id}", name="edit_categorie")
     * @Route("/filter_categories", name="filter_categories")
     */
    public function addCategorie(CategorieRepository $repo, Request $request, EntityManagerInterface $manager, Categorie $categorie=null) {

        // Recherche de catégorie
        $fail = false;
        if($request->query->get('nomCategorie')) {
            $categorieChoisi = $repo->findBy(['nom' => $request->query->get('nomCategorie')]);

            if(empty($categorieChoisi)) {
                $fail = true;
            }
        }
        else {
            $categorieChoisi = null;
        }

        // Test si ajout ou modif de catégorie
        $ajout = false;
        if(!$categorie) {
            $categorie = new Categorie();
            $ajout = true;
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($categorie);
            $manager->flush();

            $nom = $categorie->getNom();
            $this->addFlash("success", "Catégorie $nom ajouté");

            return $this->redirectToRoute("categories");
        }

        $categoriesTrier = $repo->findBy([],["nom" => "ASC"]);

        return $this->render("backend/categories.html.twig", [
            'formCategorie' => $form->createView(),
            'ajout' => $ajout,
            'categories' => $categoriesTrier,
            'categorieChoisi' => $categorieChoisi,
            'fail' => $fail
        ]);

    }

    /**
     * @Route("/delete_categorie/{id}" , name="delete_categorie")
     */
    public function deleteCategorie(Categorie $categorie, EntityManagerInterface $manager) {
        $manager->remove($categorie);
        $manager->flush();

        $nom = $categorie->getNom();
        $this->addFlash("success", "Catégorie $nom supprimé");
        return $this->redirectToRoute('categories');
    }


}
