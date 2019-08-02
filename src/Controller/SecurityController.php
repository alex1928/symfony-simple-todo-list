<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{


    /**
     * @Route("/", name="app_locale_detect")$
     *
     * @return Response
     */
    public function index(): Response
    {

        $clientLocale = strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]);
        $avaliableLocales = explode('|', $this->getParameter('app.locales'));

        if(in_array($clientLocale, $avaliableLocales))
            return $this->redirect($this->generateUrl('app_login', ['_locale'=>$clientLocale]));
        else
            return $this->redirect($this->generateUrl('app_login', ['_locale'=>'en']));
    }


    /**
     * @Route("/{_locale}/", name="app_login", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {

            return $this->redirectToRoute('task');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error]
        );
    }


    /**
     * @Route("/{_locale}/logout", name="app_logout", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
