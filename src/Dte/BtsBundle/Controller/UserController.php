<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/", name="dte_bts_user")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('view', 'Dte\\BtsBundle\\Entity\\User')")
     *
     * @return  array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('DteBtsBundle:User')->findAll();

        return array(
            'entities' => $users,
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="dte_bts_user_create")
     * @Method("POST")
     * @Template("DteBtsBundle:User:new.html.twig")
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\User')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
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

            return $this->redirect($this->generateUrl('dte_bts_user_show', array('id' => $user->getId())));
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
            'action'       => $this->generateUrl('dte_bts_user_create'),
            'method'       => 'POST',
            'form_context' => UserType::CREATE_CONTEXT,
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="dte_bts_user_new")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\User')")
     *
     * @return array
     */
    public function newAction()
    {
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
     * @Route("/{id}", name="dte_bts_user_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     * @ParamConverter("user", class="DteBtsBundle:User")
     * @Security("is_granted('view', user)")
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
     * @Route("/{id}/edit", name="dte_bts_user_edit", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     * @ParamConverter("user", class="DteBtsBundle:User")
     * @Security("is_granted('edit', user)")
     *
     * @param User $user
     *
     * @return array
     */
    public function editAction(User $user)
    {
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
            'action'       => $this->generateUrl('dte_bts_user_update', array('id' => $user->getId())),
            'method'       => 'PUT',
            'form_context' => UserType::EDIT_CONTEXT,
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="dte_bts_user_update")
     * @Method("PUT")
     * @Template("DteBtsBundle:User:edit.html.twig")
     * @ParamConverter("user", class="DteBtsBundle:User")
     * @Security("is_granted('edit', user)")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param User $user
     *
     * @return mixed
     */
    public function updateAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

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
                $url = $this->generateUrl('dte_bts_user_profile_edit');
            } else {
                $url = $this->generateUrl('dte_bts_user_edit', array('id' => $user->getId()));
            }

            return $this->redirect($url);
        }

        return array(
            'entity'       => $user,
            'edit_form'    => $editForm->createView(),
            'form_context' => ($isProfileContext) ? UserType::PROFILE_CONTEXT : UserType::EDIT_CONTEXT,
        );
    }

    /**
     * User Profile.
     *
     * @Route("/profile", name="dte_bts_user_profile")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('profile', 'Dte\\BtsBundle\\Entity\\User')")
     *
     * @return  array
     */
    public function profileAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        return array(
            'entity' => $user
        );
    }

    /**
     * User Profile edit action.
     *
     * @Route("/profile/edit", name="dte_bts_user_profile_edit")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('profile', 'Dte\\BtsBundle\\Entity\\User')")
     *
     * @return  array
     */
    public function profileEditAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

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
            'action'       => $this->generateUrl('dte_bts_user_update', array('id' => $user->getId())),
            'method'       => 'PUT',
            'form_context' => UserType::PROFILE_CONTEXT,
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
}
