<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="inscription")
     * @Route("/modifprofil/{id}", name="modifprofil")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, User $user=null)
    {
        if (!$user){
            $user=new User();
            $mode=true;
        }else{
            $mode=false;
        }



        $form=$this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):
            $hash=$encoder->encodePassword($user, $user->getPassword());

        $user->setPassword($hash);

        $manager->persist($user);
        $manager->flush();
        if ($mode==true){
        $this->addFlash('success', "Félicitation, vous êtes bien inscrit");
            return $this->redirectToRoute('login');
        }else{
            $this->addFlash('success', "Vos modifications ont bien été enregistré");
            return $this->redirectToRoute('profil');
        }


            endif;

        return $this->render('security/inscription.html.twig',[
            'form'=>$form->createView(),
            'mode'=>$mode
        ]);

    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {
        $lastuser=$authenticationUtils->getLastUsername();

        return $this->render('security/connexion.html.twig',[
            'lastuser'=>$lastuser
        ]);

    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {

    }

}
