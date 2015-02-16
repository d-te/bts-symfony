<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Form\CommentType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Comment controller.
 *
 * @Route("/issue/{issue_id}/comment", requirements={
 *     "issue_id": "\d+"
 * }))
 */
class CommentController extends Controller
{

    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="issue_comment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($issue_id)
    {
        $em = $this->getDoctrine()->getManager();

        $issue = $em->getRepository('DteBtsBundle:Issue')->find($issue_id);

        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $entities = $issue->getComments();

        $forms = array(
            'edit'   => array(),
            'delete' => array(),
        );

        foreach ($entities as $entity) {
            $forms['edit'][$entity->getId()]   = $this->createEditForm($entity, $issue)->createView();
            $forms['delete'][$entity->getId()] = $this->createDeleteForm($entity->getId(), $issue->getId())->createView();
        }

        return array(
            'entities'   => $entities,
            'forms' => $forms,
        );
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/", name="issue_comment_create")
     * @Method("POST")
     */
    public function createAction(Request $request, $issue_id)
    {
        $em = $this->getDoctrine()->getManager();

        $issue = $em->getRepository('DteBtsBundle:Issue')->find($issue_id);

        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new Comment();

        $form = $this->createCreateForm($entity, $issue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIssue($issue);
            $entity->setUser($user);

            $em->persist($entity);
            $em->flush();
        }

        return new JsonResponse(array('saved'));
    }

    /**
     * Creates a form to create a Comment entity.
     *
     * @param Comment $entity The entity
     * @param Issue $entity Issue
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comment $entity, Issue $issue)
    {
        $form = $this->createForm(new CommentType(), $entity, array(
            'action' => $this->generateUrl('issue_comment_create', array('issue_id' => $issue->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Comment'));

        return $form;
    }

    /**
    * Creates a form to edit a Comment entity.
    *
    * @param Comment $entity The entity
    * @param Issue $entity Issue
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comment $entity, Issue $issue)
    {
        $form = $this->createForm(new CommentType(), $entity, array(
            'action' => $this->generateUrl('issue_comment_create', array('issue_id' => $issue->getId())),
            'method' => 'PUT',
        ));

        $form->add('buttons', 'form_actions', [
            'buttons' => [
                'save'   => ['type' => 'submit', 'options' => ['label' => 'Update']],
                'cancel' => ['type' => 'button', 'options' => ['label' => 'Cancel']],
            ]
        ]);

        return $form;
    }

    /**
     * Edits an existing Comment entity.
     *
     * @Route("/{id}", name="issue_comment_update", requirements={
     *     "id": "\d+"
     * }))
     * @Method("PUT")
     */
    public function updateAction(Request $request, $issue_id, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $issue = $em->getRepository('DteBtsBundle:Issue')->find($issue_id);

        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $entity = $em->getRepository('DteBtsBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $editForm = $this->createEditForm($entity, $issue);
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
     */
    public function deleteAction(Request $request, $issue_id, $id)
    {
        $form = $this->createDeleteForm($id, $issue_id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $issue = $em->getRepository('DteBtsBundle:Issue')->find($issue_id);

            if (!$issue) {
                throw $this->createNotFoundException('Unable to find Issue entity.');
            }

            $entity = $em->getRepository('DteBtsBundle:Comment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return new JsonResponse(array('deleted'));
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $issue_id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('issue_comment_delete', array('id' => $id, 'issue_id' => $issue_id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
