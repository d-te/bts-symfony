<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Comment controller.
 *
 * @Route("/issue/{issueId}/comment", requirements={"issueId": "\d+"})
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="issue_comment")
     * @Method("GET")
     * @Template()
     * @ParamConverter("issue", class="DteBtsBundle:Issue", options={"id" = "issueId"})
     * @Security("is_granted('view', 'Dte\\BtsBundle\\Entity\\Comment')")
     *
     * @param Issue $issue
     *
     * @return  array
     */
    public function indexAction(Issue $issue)
    {
        $comments = $issue->getComments();

        $forms = array(
            'edit'   => array(),
            'delete' => array(),
        );

        foreach ($comments as $comment) {
            $editForm   = $this->createEditForm($comment, $issue)->createView();
            $deleteForm = $this->createDeleteForm($comment, $issue)->createView();

            $forms['edit'][$comment->getId()]   = $editForm;
            $forms['delete'][$comment->getId()] = $deleteForm;
        }

        return array(
            'entities' => $comments,
            'forms'    => $forms,
        );
    }

    /**
     * Displays a form to create a new Comment entity.
     *
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\Comment')")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     *
     * @return array
     */
    public function newAction(Issue $issue)
    {
        $comment = new Comment();

        $form   = $this->createCreateForm($comment, $issue);

        return array(
            'entity' => $comment,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/", name="issue_comment_create")
     * @Method("POST")
     * @ParamConverter("issue", class="DteBtsBundle:Issue", options={"id" = "issueId"})
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\Comment')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request, Issue $issue)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $comment = new Comment();

        $form = $this->createCreateForm($comment, $issue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setIssue($issue);
            $comment->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return new JsonResponse(array('saved'));
        }

        return new JsonResponse($this->getFormErrors($form), 400);
    }

    /**
     * Creates a form to create a Comment entity.
     *
     * @param \Dte\BtsBundle\Entity\Comment $comment The entity
     * @param \Dte\BtsBundle\Entity\Issue $issue Issue
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comment $comment, Issue $issue)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_comment', $comment, array(
            'action' => $this->generateUrl('issue_comment_create', array('issueId' => $issue->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.page.issue.action.comment'));

        return $form;
    }

    /**
    * Creates a form to edit a Comment entity.
    *
    * @param \Dte\BtsBundle\Entity\Comment $comment The entity
    * @param \Dte\BtsBundle\Entity\Issue $issue Issue
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comment $comment, Issue $issue)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_comment', $comment, array(
            'action' => $this->generateUrl('issue_comment_create', array('issueId' => $issue->getId())),
            'method' => 'PUT',
        ));

        $form->add('buttons', 'form_actions', [
            'buttons' => [
                'save'   => ['type' => 'submit', 'options' => ['label' => 'bts.default.action.update']],
                'cancel' => ['type' => 'button', 'options' => ['label' => 'bts.default.action.cancel']],
            ]
        ]);

        return $form;
    }

    /**
     * Edits an existing Comment entity.
     *
     * @Route("/{id}", name="issue_comment_update", requirements={"id": "\d+"})
     * @Method("PUT")
     * @ParamConverter("comment", class="DteBtsBundle:Comment")
     * @Security("is_granted('edit', comment)")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     * @param Comment $comment
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request, Issue $issue, Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($comment, $issue);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return new JsonResponse(array('saved'));
        }

        return new JsonResponse($this->getFormErrors($form), 400);
    }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/{id}", name="issue_comment_delete")
     * @Method("DELETE")
     * @ParamConverter("comment", class="DteBtsBundle:Comment")
     * @Security("is_granted('delete', comment)")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     * @param Comment $comment
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request, Issue $issue, Comment $comment)
    {
        $form = $this->createDeleteForm($comment, $issue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();

            return new JsonResponse(array('deleted'));
        }

        return new JsonResponse($this->getFormErrors($form), 400);
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param Comment $comment
     * @param Issue $issue
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comment $comment, Issue $issue)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'issue_comment_delete',
                    array(
                        'id' => $comment->getId(),
                        'issueId' => $issue->getId()
                    )
                )
            )
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'bts.default.action.delete'))
            ->getForm()
        ;
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param \Symfony\Component\Form\Form $form
     *
     * @return array
     */
    private function getFormErrors(Form $form)
    {
        $errors = array();

        foreach ($form->all() as $item) {
            $errors[$item->getName()] = array();

            foreach ($item->getErrors() as $error) {
                $errors[$item->getName()] = $error->getMessage();
            }
        }

        return $errors;
    }
}
