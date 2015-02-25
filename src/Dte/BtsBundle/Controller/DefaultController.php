<?php

namespace Dte\BtsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="dte_bts_homepage")
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $activities   = $em->getRepository('DteBtsBundle:Activity')->findActivitiesByUser($user);
        $openedIssues = $em->getRepository('DteBtsBundle:Issue')->findOpenedIssuesByCollaborator($user);

        return $this->render('DteBtsBundle:Default:index.html.twig', array(
            'activities'   => $activities,
            'openedIssues' => $openedIssues,
        ));
    }

    /**
     * @Route("/login", name="dte_bts_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $err = SecurityContextInterface::AUTHENTICATION_ERROR;

        if ($request->attributes->has($err)) {
            $error = $request->attributes->get($err);
        } elseif (null !== $request->getSession() && $request->getSession()->has($err)) {
            $error = $request->getSession()->get($err);
            $request->getSession()->remove($err);
        } else {
            $error = null;
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContextInterface::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="dte_bts_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="dte_bts_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}
