<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Company;
use AppBundle\Form\CompanyType;

/**
 * Company controller.
 */
class CompanyController extends Controller
{

    /**
     * Lists all Company entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $companies = $em->getRepository('AppBundle:Company')->findAll();

        return $this->render(
                'company/index.html.twig', array(
                'companies' => $companies,
                )
        );
    }

    /**
     * Creates a new Company entity.
     */
    public function newAction(Request $request)
    {
        $company = new Company();
        $form = $this->createForm('AppBundle\Form\CompanyType', $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_show', array('id' => $company->getId()));
        }

        return $this->render(
                'company/new.html.twig', array(
                'company' => $company,
                'form' => $form->createView(),
                )
        );
    }

    /**
     * Finds and displays a Company entity.
     */
    public function showAction(Request $request, Company $company)
    {
        //$this->denyAccessUnlessGranted('view', $company);
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                    case 'name':
                        $company->setName($v);
                        $update = true;
                        break;
                    case 'description':
                        $company->setDescription($v);
                        $update = true;
                        break;
                    case 'phone':
                        $company->setPhone($v);
                        $update = true;
                        break;
                    case 'address1':
                        $company->setAddressLine1($v);
                        $update = true;
                        break;
                    case 'address2':
                        $company->setAddressLine2($v);
                        $update = true;
                        break;
                    case 'city':
                        $company->setCity($v);
                        $update = true;
                        break;
                    case 'state':
                        $company->setState($v);
                        $update = true;
                        break;
                    case 'zip':
                        $company->setZip($v);
                        $update = true;
                        break;
                    case 'country':
                        $company->setCountry($v);
                        $update = true;
                        break;
                    case 'website':
                        $company->setWebsite($v);
                        $update = true;
                        break;       
                    case 'foundation_year':
                        //TODO:: Timestamp?
                        $company->setFoundationYear($v);
                        $update = true;
                        break;                         
                    case 'workspace_id':
                        $company->setWorkspaceId($v);
                        $update = true;
                        break;
                    default:
                        break;
                }
            }
            if ($update) {
                $company->setUpdatedAt(time());
                $em->persist($company);
                $em->flush();
            }
        }
        return new JsonResponse($company->toArray());
    }

    /**
     * Displays a form to edit an existing Company entity.
     */
    public function editAction(Request $request, Company $company)
    {
        $deleteForm = $this->createDeleteForm($company);
        $editForm = $this->createForm('AppBundle\Form\CompanyType', $company);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_edit', array('id' => $company->getId()));
        }

        return $this->render(
                'company/edit.html.twig', array(
                'company' => $company,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                )
        );
    }

    /**
     * Deletes a Company entity.
     */
    public function deleteAction(Request $request, Company $company)
    {
        $form = $this->createDeleteForm($company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($company);
            $em->flush();
        }

        return $this->redirectToRoute('company_index');
    }

    /**
     * Creates a form to delete a Company entity.
     *
     * @param Company $company The Company entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Company $company)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('company_delete', array('id' => $company->getId())))
                ->setMethod('DELETE')
                ->getForm();
    }
}
