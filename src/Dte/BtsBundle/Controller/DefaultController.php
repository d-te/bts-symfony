<?php

namespace Dte\BtsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $request->getSession() && $request->getSession()->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->getSession()->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $request->getSession()->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
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
