<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueStatus;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Form\IssueType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/", name="dte_bts_issue")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('view', 'Dte\\BtsBundle\\Entity\\Issue')")
     *
     * @return array
     */
    public function indexAction()
    {
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
     * @Route("/", name="dte_bts_issue_create")
     * @Method("POST")
     * @Template("DteBtsBundle:Issue:new.html.twig")
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\Issue')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $issue = new Issue();
        $form = $this->createCreateForm($issue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            return $this->redirect($this->generateUrl('dte_bts_issue_show', array('id' => $issue->getId())));
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
            'action'       => $this->generateUrl('dte_bts_issue_create'),
            'method'       => 'POST',
            'form_context' => IssueType::CREATE_CONTEXT,
            'isSubtask'    => $isSubtask,
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.create'));

        return $form;
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/new", name="dte_bts_issue_new")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\Issue')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $issue  = new Issue();

        $status = $em
            ->getRepository('DteBtsBundle:IssueStatus')
            ->findOneBy(array('label' => IssueStatus::OPEN_STATUS_LABEL));

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
     * @Route("/{id}", name="dte_bts_issue_show")
     * @Method("GET")
     * @Template()
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     * @Security("is_granted('view', issue)")
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function showAction(Issue $issue)
    {
        return array(
            'entity'       => $issue,
            'types'        => IssueTaskType::getItems(),
        );
    }

    /**
     * Displays a form to edit an existing Issue entity.
     *
     * @Route("/{id}/edit", name="dte_bts_issue_edit")
     * @Method("GET")
     * @Template()
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     * @Security("is_granted('edit', issue)")
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function editAction(Issue $issue)
    {
        $editForm = $this->createEditForm($issue);

        return array(
            'entity'    => $issue,
            'edit_form' => $editForm->createView(),
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
        $form = $this->get('form.factory')->create('dte_btsbundle_issue', $issue, array(
            'action'       => $this->generateUrl('dte_bts_issue_update', array('id' => $issue->getId())),
            'method'       => 'PUT',
            'form_context' => IssueType::EDIT_CONTEXT,
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.update'));

        return $form;
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/{id}", name="dte_bts_issue_update", requirements={"id": "\d+"})
     * @Method("PUT")
     * @Template("DteBtsBundle:Issue:edit.html.twig")
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     * @Security("is_granted('edit', issue)")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     *
     * @return array
     */
    public function updateAction(Request $request, Issue $issue)
    {
        $em       = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($issue);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('dte_bts_issue_edit', array('id' => $issue->getId())));
        }

        return array(
            'entity'      => $issue,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Change a issue's status
     *
     * @Route("/{id}/{status}", name="dte_bts_issue_change_status", requirements={"status": "\d+"})
     * @Method("GET")
     * @ParamConverter("issue", class="DteBtsBundle:Issue", options={"id" = "id"})
     * @ParamConverter("status", class="DteBtsBundle:IssueStatus", options={"id" = "status"})
     * @Security("is_granted('edit', issue)")
     *
     * @param Issue $issue
     * @param IssueStatus $status
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeStatusAction(Issue $issue, IssueStatus $status)
    {
        $em = $this->getDoctrine()->getManager();
        $issue->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('dte_bts_issue_show', array('id' => $issue->getId())));
    }

    /**
     * Get issue collaborators
     *
     * @Route("/{id}/collaborators/", name="dte_bts_issue_collaborators")
     * @Method("GET")
     * @Template("DteBtsBundle:Issue:collaborators.html.twig")
     * @ParamConverter("issue", class="DteBtsBundle:Issue")
     * @Security("is_granted('view', issue)")
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function getCollaboratorsAction(Issue $issue)
    {
        return array(
            'entity' => $issue,
        );
    }
}
