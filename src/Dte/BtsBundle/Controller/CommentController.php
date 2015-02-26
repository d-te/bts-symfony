<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Form\CommentType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     *
     * @param Issue $issue
     *
     * @return  array
     */
    public function indexAction(Issue $issue)
    {
        if (false === $this->get('security.context')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

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
     * Creates a new Comment entity.
     *
     * @Route("/", name="issue_comment_create")
     * @Method("POST")
     * @ParamConverter("issue", class="DteBtsBundle:Issue", options={"id" = "issueId"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Issue $issue
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request, Issue $issue)
    {
        if (false === $this->get('security.context')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

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
        }

        return new JsonResponse(array('saved'));
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
        $form = $this->createForm(new CommentType(), $comment, array(
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
        $form = $this->createForm(new CommentType(), $comment, array(
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

        if (false === $this->get('security.context')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if (false === $this->get('security.context')->isGranted('edit', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($comment, $issue);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
        }

        return new JsonResponse(array('saved'));
    }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/{id}", name="issue_comment_delete")
     * @Method("DELETE")
     * @ParamConverter("comment", class="DteBtsBundle:Comment")
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
            if (false === $this->get('security.context')->isGranted('view', $issue)) {
                throw new AccessDeniedException('Unauthorised access!');
            }

            if (false === $this->get('security.context')->isGranted('delete', $comment)) {
                throw new AccessDeniedException('Unauthorised access!');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return new JsonResponse(array('deleted'));
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
}
