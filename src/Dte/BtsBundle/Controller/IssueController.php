<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Form\CommentType;
use Dte\BtsBundle\Form\IssueType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Issue controller.
 *
 * @Route("/issue")
 */
class IssueController extends Controller
{

    /**
     * Lists all Issue entities.
     *
     * @Route("/", name="issue")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DteBtsBundle:Issue')->findAll();

        return array(
            'entities' => $entities,
            'types'    => IssueTaskType::getItems(),
        );
    }
    /**
     * Creates a new Issue entity.
     *
     * @Route("/", name="issue_create")
     * @Method("POST")
     * @Template("DteBtsBundle:Issue:new.html.twig")
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Issue entity.
     *
     * @param Issue $entity The entity
     * @param boolean $isSubtask creation of subtask from story
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Issue $entity, $isSubtask = false)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_issue', $entity, array(
            'action'       => $this->generateUrl('issue_create'),
            'method'       => 'POST',
            'form_context' => 'create',
            'isSubtask'    => $isSubtask,
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.create'));

        return $form;
    }

    /**
     * Creates a form to create/edit Comment.
     *
     * @param Issue $entity The entity
     * @param Issue $story parent story
     * @param Issue $project project
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCommentForm(Comment $entity, Issue $issue)
    {
        $form = $this->createForm(new CommentType(), $entity, array(
            'action' => $this->generateUrl('issue_comment_create', array('issue_id' => $issue->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.page.issue.action.comment'));

        return $form;
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/new", name="issue_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $entity  = new Issue();

        $status = $em->getRepository('DteBtsBundle:IssueStatus')->findOneBy(array('label' => 'Open'));

        $entity->setStatus($status);

        $isSubtask = false;

        if (intval($request->get('story')) > 0) {
            $story = $em->getRepository('DteBtsBundle:Issue')->find(intval($request->get('story')));

            if ($story && $story->getType() === IssueTaskType::STORY_TYPE) {
                $entity->setParent($story);
                $entity->setProject($story->getProject());
                $entity->setType(IssueTaskType::SUBTASK_TYPE);

                $isSubtask = true;
            }
        }

        $form   = $this->createCreateForm($entity, $isSubtask);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/{id}", name="issue_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.issue.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('view', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $commentForm = $this->createCommentForm(new Comment(), $entity);

        return array(
            'entity'       => $entity,
            'comment_form' => $commentForm->createView(),
            'types'        => IssueTaskType::getItems(),
        );
    }

    /**
     * Displays a form to edit an existing Issue entity.
     *
     * @Route("/{id}/edit", name="issue_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.issue.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Issue entity.
    *
    * @param Issue $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Issue $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create('dte_btsbundle_issue', $entity, array(
            'action'       => $this->generateUrl('issue_update', array('id' => $entity->getId())),
            'method'       => 'PUT',
            'form_context' => 'edit',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.update'));

        return $form;
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/{id}", name="issue_update", requirements={
     *     "id": "\d+"
     * }))
     * @Method("PUT")
     * @Template("DteBtsBundle:Issue:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.issue.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('issue_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Change a issue's status
     *
     * @Route("/{id}/{status}", name="issue_change_status", requirements={"status": "1|2|3"})
     * @Method("GET")
     */
    public function changeStatusAction(Request $request, $id, $status)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DteBtsBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('bts.page.issue.error.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $status = $em->getRepository('DteBtsBundle:IssueStatus')->find($status);

        if (!$status) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('bts.page.issue.error.not_found_status')
            );
        }

        $entity->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('id' => $id)));
    }
}
