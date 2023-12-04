<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\UtilisateurRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AccountDetailsType;
use App\Form\ForgetPasswordType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ManagerRegistry;

class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): RedirectResponse
    {
        // Redirigez l'utilisateur vers la page de connexion après la déconnexion
        return new RedirectResponse($this->generateUrl('app_login'));
    }

    #[Route(path: '/account', name: 'account')]
    public function accountDetails( UtilisateurRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager):Response{
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $status = null;

        $user = $userRepository->loadUserByIdentifier( $this->getUser()->getUserIdentifier() )   ;

        $form = $this->createForm(AccountDetailsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if( strlen( $form->get('plainPassword')->getData() ) >0 ) {
                $userRepository->upgradePassword(
                    $user,
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }else{
                $entityManager->persist($user);
                $entityManager->flush();
            }
            $status = "updated";
        }else{
            $entityManager->refresh($user);
        }
        return $this->render('utilisateur/account.html.twig', [ 'status'=>$status, 'accountForm'=>$form->createView()]);
    }

    #[Route(path: '/forgot', name: 'forgot')]
public function forgotPassword(
    Request $request,
    UtilisateurRepository $userRepository,
    MailerInterface $mailer,
    TokenGeneratorInterface $tokenGenerator,
    ManagerRegistry $doctrine
) {
    $form = $this->createForm(ForgetPasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $donnees = $form->getData();
        $user = $userRepository->findOneBy(['email' => $donnees['email']]);

        if (!$user) {
            $this->addFlash('danger', 'Cette adresse n\'existe pas');
            return $this->redirectToRoute("forgot");
        }

        $token = $tokenGenerator->generateToken();

        try {
            $user->setResetToken($token);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'Une erreur est survenue : ' . $exception->getMessage());
            return $this->redirectToRoute("app_login");
        }

        $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // Reste du code pour l'envoi de l'e-mail
        // ...

        $this->addFlash('message', 'E-mail de réinitialisation du mot de passe envoyé.');
        return $this->redirectToRoute("app_login");
    }

    return $this->render("security/forgotPassword.html.twig", ['form' => $form->createView()]);
}

    #[Route(path: '/resetpassword/{token}', name: 'app_reset_password')]
    public function resetpassword(Request $request,string $token, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['reset_token'=>$token]);

        if($user == null ) {
            $this->addFlash('danger','TOKEN INCONNU');
            return $this->redirectToRoute("app_login");

        }

        if($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('password')));
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message','Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");

        }
        else {
            return $this->render("security/resetPassword.html.twig",['token'=>$token]);

        }
    }

    
}

