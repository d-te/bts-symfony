<?php

namespace Dte\BtsBundle\Controller;

use Dte\BtsBundle\Entity\Project;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('view', 'Dte\\BtsBundle\\Entity\\Project')")
     *
     * @return  array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('DteBtsBundle:Project')->findAll();

        return array(
            'entities' => $projects,
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("DteBtsBundle:Project:new.html.twig")
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\Project')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createCreateForm($project);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
        }

        return array(
            'entity' => $project,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param \Dte\BtsBundle\Entity\Project $project The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $project)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_project', $project, array(
            'action' => $this->generateUrl('project_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.create'));

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('create', 'Dte\\BtsBundle\\Entity\\Project')")
     *
     * @return array
     */
    public function newAction()
    {
        $project = new Project();
        $form   = $this->createCreateForm($project);

        return array(
            'entity' => $project,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}", name="project_show")
     * @Method("GET")
     * @Template()
     * @ParamConverter("project", class="DteBtsBundle:Project")
     * @Security("is_granted('view', project)")
     *
     * @param Project $project
     *
     * @return array
     */
    public function showAction(Project $project)
    {
        $em = $this->getDoctrine()->getManager();

        $activities = $em->getRepository('DteBtsBundle:Activity')->findActivitiesByProject($project);

        return array(
            'entity'     => $project,
            'activities' => $activities,
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Method("GET")
     * @Template()
     * @ParamConverter("project", class="DteBtsBundle:Project")
     * @Security("is_granted('edit', project)")
     *
     * @param Project $project
     *
     * @return array
     */
    public function editAction(Project $project)
    {
        $editForm = $this->createEditForm($project);

        return array(
            'entity'      => $project,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Project entity.
    *
    * @param \Dte\BtsBundle\Entity\Project $project The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Project $project)
    {
        $form = $this->get('form.factory')->create('dte_btsbundle_project', $project, array(
            'action' => $this->generateUrl('project_update', array('id' => $project->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'bts.default.action.update'));

        return $form;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}", name="project_update")
     * @Method("PUT")
     * @Template("DteBtsBundle:Project:edit.html.twig")
     * @ParamConverter("project", class="DteBtsBundle:Project")
     * @Security("is_granted('edit', project)")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Project $project
     *
     * @return array
     */
    public function updateAction(Request $request, Project $project)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($project);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('project_edit', array('id' => $project->getId())));
        }

        return array(
            'entity'      => $project,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Get project's members
     *
     * @Route("/{id}/members", name="project_members_api")
     * @Method("GET")
     * @ParamConverter("project", class="DteBtsBundle:Project")
     * @Security("is_granted('view', project)")
     *
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMembersAction(Project $project = null)
    {
        if ($project) {
            $members = array();

            foreach ($project->getMembers() as $user) {
                $members[] = array('id' => $user->getId(), 'label' => $user->getFullname());
            }

            return new JsonResponse($members);
        } else {
            return new JsonResponse(
                array('error' => $this->get('translator')->trans('bts.page.project.error.not_found')),
                404
            );
        }
    }

    /**
     * Get project's stories
     *
     * @Route("/{id}/stories", name="project_stories_api")
     * @Method("GET")
     * @ParamConverter("project", class="DteBtsBundle:Project")
     * @Security("is_granted('view', project)")
     *
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getStoriesAction(Project $project = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($project) {
            $issues = $em->getRepository('DteBtsBundle:Issue')->findStoriesByProject($project);

            $stories = array();

            foreach ($issues as $issue) {
                $stories[] = array('id' => $issue->getId(), 'label' => $issue->getSelectLabel());
            }

            return new JsonResponse($stories);
        } else {
            return new JsonResponse(
                array('error' => $this->get('translator')->trans('bts.page.project.error.not_found')),
                404
            );
        }
    }
}
