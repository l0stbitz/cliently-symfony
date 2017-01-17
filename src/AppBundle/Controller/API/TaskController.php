<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;

/**
 * Task controller.
 */
class TaskController extends Controller
{

    /**
     * Lists all Task entities.
     */
    public function indexAction()
    {
        
    }

    /**
     * Finds and displays a Task entity.
     */
    public function showAction(Request $request, Task $task)
    {
        $this->denyAccessUnlessGranted('view', $task);
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                case 'type':
                    $task->setType($v);
                    $update = true;
                    break;
                case 'is_completed':
                    $task->setIsCompleted($v?1:0);
                    $update = true;
                    break;    
                case 'description':
                    $task->setDescription($v);
                    $update = true;
                    break;                      
                default:
                    break;
                }
            }
            if ($update) {
                $task->setUpdatedAt(time());
                $em->persist($task);
                $em->flush();
            }
        }
        return new JsonResponse($task->toArray());
    }
}
