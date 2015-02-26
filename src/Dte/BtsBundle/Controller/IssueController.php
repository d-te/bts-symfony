<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Form\CommentType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     *
     * @return array
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $issues = $em->getRepository('DteBtsBundle:Issue')->findAll();

        return array(
            'entities' => $issues,
            'types'    => IssueTaskType::getItems(),
        );
    }

    /**
     * Creates a new Issue entity.
     *
     * @Route("/", name="issue_create")
     * @Method("POST")
     * @Template("DteBtsBundle:Issue:new.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $issue = new Issue();
        $form = $this->createCreateForm($issue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
        }

        return array(
            'entity' => $issue,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Issue entity.
     *
     * @param \Dte\BtsBundle\Entity\Issue $issue The entity
     * @param boolean $isSubtask creation of subtask from story
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Issue $issue, $isSubtask = false)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_issue', $issue, array(
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
     * @param \Dte\BtsBundle\Entity\Comment $comment The entity
     * @param \Dte\BtsBundle\Entity\Issue $issue parent story
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCommentForm(Comment $comment, Issue $issue)
    {
        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('issue_comment_create', array('issueId' => $issue->getId())),
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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function newAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $issue  = new Issue();

        $status = $em->getRepository('DteBtsBundle:IssueStatus')->findOneBy(array('label' => 'Open'));

        $issue->setStatus($status);

        $isSubtask = false;

        if (intval($request->get('story')) > 0) {
            $story = $em->getRepository('DteBtsBundle:Issue')->find(intval($request->get('story')));

            if ($story && $story->getType() === IssueTaskType::STORY_TYPE) {
                $issue->setParent($story);
                $issue->setProject($story->getProject());
                $issue->setType(IssueTaskType::SUBTASK_TYPE);

                $isSubtask = true;
            }
        }

        $form   = $this->createCreateForm($issue, $isSubtask);

        return array(
            'entity' => $issue,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/{id}", name="issue_show")
     * @Method("GET")
     * @Template()
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function showAction(Issue $issue)
    {
        if (false === $this->get('security.context')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $commentForm = $this->createCommentForm(new Comment(), $issue);

        return array(
            'entity'       => $issue,
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
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function editAction(Issue $issue)
    {
        if (false === $this->get('security.context')->isGranted('edit', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($issue);

        return array(
            'entity'      => $issue,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Issue entity.
    *
    * @param \Dte\BtsBundle\Entity\Issue $issue The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Issue $issue)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create('dte_btsbundle_issue', $issue, array(
            'action'       => $this->generateUrl('issue_update', array('id' => $issue->getId())),
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
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     *
     * @return array
     */
    public function updateAction(Request $request, Issue $issue)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.context')->isGranted('edit', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($issue);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('issue_edit', array('id' => $issue->getId())));
        }

        return array(
            'entity'      => $issue,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Change a issue's status
     *
     * @Route("/{id}/{status}", name="issue_change_status", requirements={"status": "\d+"})
     * @Method("GET")
     * @ParamConverter("issue", class="DteBtsBundle:Issue", options={"id" = "id"})
     * @ParamConverter("status", class="DteBtsBundle:IssueStatus", options={"id" = "status"})
     *
     * @param Issue $issue
     * @param IssueStatus $status
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeStatusAction(Issue $issue, $status)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.context')->isGranted('edit', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $issue->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
    }

    /**
     * Get issue collaborators
     *
     * @Route("/{id}/collaborators/", name="issue_collaborators")
     * @Method("GET")
     * @Template("DteBtsBundle:Issue:collaborators.html.twig")
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function getCollaboratorsAction(Issue $issue)
    {
        if (false === $this->get('security.context')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        return array(
            'entity' => $issue,
        );
    }
}
