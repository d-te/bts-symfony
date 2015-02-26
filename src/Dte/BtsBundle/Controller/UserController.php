<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     *
     * @return  array
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('DteBtsBundle:User')->findAll();

        return array(
            'entities' => $users,
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("DteBtsBundle:User:new.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $plainPassword = $user->getPassword();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }

        return array(
            'entity' => $user,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param \Dte\BtsBundle\Entity\User $user The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $user)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_user', $user, array(
            'action'       => $this->generateUrl('user_create'),
            'method'       => 'POST',
            'form_context' => 'create',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function newAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $user = new User();
        $form   = $this->createCreateForm($user);

        return array(
            'entity' => $user,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     * @ParamConverter("user", class="DteBtsBundle:User")
     *
     * @param User $user
     *
     * @return array
     */
    public function showAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $openedIssues = $em->getRepository('DteBtsBundle:Issue')->findOpenedIssuesAssignedToUser($user);

        return array(
            'entity'       => $user,
            'openedIssues' => $openedIssues,
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     * @ParamConverter("user", class="DteBtsBundle:User")
     *
     * @param User $user
     *
     * @return array
     */
    public function editAction(User $user)
    {
        if (false === $this->get('security.context')->isGranted('edit', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($user);

        return array(
            'entity'    => $user,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param \Dte\BtsBundle\Entity\User $user The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $user)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_user', $user, array(
            'action'       => $this->generateUrl('user_update', array('id' => $user->getId())),
            'method'       => 'PUT',
            'form_context' => 'edit',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     * @Method("PUT")
     * @Template("DteBtsBundle:User:edit.html.twig")
     * @ParamConverter("user", class="DteBtsBundle:User")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param User $user
     *
     * @return mixed
     */
    public function updateAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.context')->isGranted('edit', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $oldPassword = $user->getPassword();
        $oldRoles    = $user->getRoles();

        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);

        $isProfileContext = ($editForm->get('is_profile')->getData() == 1);

        if ($editForm->isValid()) {
            $plainPassword = $user->getPassword();

            if (!empty($plainPassword)) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);

                $password = $encoder->encodePassword($plainPassword, $user->getSalt());
                $user->setPassword($password);
            } else {
                $user->setPassword($oldPassword);
            }

            if ($isProfileContext) {
                $user->addRoles($oldRoles);
            }

            $em->flush();

            if ($isProfileContext) {
                $url = $this->generateUrl('user_profile_edit');
            } else {
                $url = $this->generateUrl('user_edit', array('id' => $user->getId()));
            }

            return $this->redirect($url);
        }

        return array(
            'entity'       => $user,
            'edit_form'    => $editForm->createView(),
            'form_context' => ($isProfileContext) ? 'profile' : 'edit',
        );
    }

    /**
     * User Profile.
     *
     * @Route("/profile", name="user_profile")
     * @Method("GET")
     * @Template()
     *
     * @return  array
     */
    public function profileAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (false === $this->get('security.context')->isGranted('profile', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        return array(
            'entity'      => $user
        );
    }

    /**
     * User Profile edit action.
     *
     * @Route("/profile/edit", name="user_profile_edit")
     * @Method("GET")
     * @Template()
     *
     * @return  array
     */
    public function profileEditAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.user.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('profile', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createProfileForm($user);

        return array(
            'entity'    => $user,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User profile.
    *
    * @param \Dte\BtsBundle\Entity\User $user The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createProfileForm(User $user)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_user', $user, array(
            'action'      => $this->generateUrl('user_update', array('id' => $user->getId())),
            'method'      => 'PUT',
            'form_context' => 'profile',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
}
