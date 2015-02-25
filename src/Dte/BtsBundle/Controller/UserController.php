<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        $entities = $em->getRepository('DteBtsBundle:User')->findAll();

        return array(
            'entities' => $entities,
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

        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $plainPassword = $entity->getPassword();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);

            $password = $encoder->encodePassword($plainPassword, $entity->getSalt());
            $entity->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param \Dte\BtsBundle\Entity\User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
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

        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show", requirements={
     *     "id": "\d+"
     * }))
     * @Method("GET")
     * @Template()
     *
     * @param mixed $id
     *
     * @return array
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.user.error.not_found'));
        }

        $openedIssues = $em->getRepository('DteBtsBundle:Issue')->findOpenedIssuesAssignedToUser($entity);

        return array(
            'entity'       => $entity,
            'openedIssues' => $openedIssues,
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit", requirements={
     *     "id": "\d+"
     * }))
     * @Method("GET")
     * @Template()
     *
     * @param mixed $id
     *
     * @return array
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.user.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param \Dte\BtsBundle\Entity\User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action'       => $this->generateUrl('user_update', array('id' => $entity->getId())),
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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $id
     *
     * @return mixed
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.user.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $oldPassword = $entity->getPassword();
        $oldRoles    = $entity->getRoles();

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $isProfileContext = ($editForm->get('is_profile')->getData() == 1);

        if ($editForm->isValid()) {
            $plainPassword = $entity->getPassword();

            if (!empty($plainPassword)) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);

                $password = $encoder->encodePassword($plainPassword, $entity->getSalt());
                $entity->setPassword($password);
            } else {
                $entity->setPassword($oldPassword);
            }

            if ($isProfileContext) {
                $entity->addRoles($oldRoles);
            }

            $em->flush();

            if ($isProfileContext) {
                $url = $this->generateUrl('user_profile_edit');
            } else {
                $url = $this->generateUrl('user_edit', array('id' => $id));
            }

            return $this->redirect($url);
        }

        return array(
            'entity'       => $entity,
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
        $entity = $this->get('security.context')->getToken()->getUser();

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.user.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('profile', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createProfileForm($entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User profile.
    *
    * @param \Dte\BtsBundle\Entity\User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createProfileForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action'      => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method'      => 'PUT',
            'form_context' => 'profile',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
}
