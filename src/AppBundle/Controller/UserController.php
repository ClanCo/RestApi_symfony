<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;

class UserController extends Controller
{

  /**
  * @Rest\View()
  * @Rest\Get("/users")
  */
  public function getUsersAction(Request $request)
  {
      $users = $this->get('doctrine.orm.entity_manager')
                  ->getRepository('AppBundle:User')
                  ->findAll();
            /* @var $users User[] */
      /*
        $formatted = [];
        foreach ($users as $user) {
          $formatted[] = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
          ];
        }*/

        return $users;

  }

  /**
  * @Rest\View()
  * @Rest\Get("/users/{id}")
  */
  public function getUserAction($id ,Request $request)
  {

      $user = $this->get('doctrine.orm.entity_manager')
                  ->getRepository("AppBundle:User")
                  ->find($request->get('id'));
              /* @var $user User */


    /*
      if(empty($user))
      {
        return new JsonResponse(['message' => "user not found"], Response::HTTP_NOT_FOUND);
      }

      $formatted = [
        'id' => $user->getId(),
        'firstname' => $user->getFirstname(),
        'lastname' => $user->getLastname(),
        'email' => $user->getEmail(),
      ];*/

      return $user;

  }

  /**
  * @Rest\View(statusCode=Response::HTTP_CREATED)
  * @Rest\Post("/users")
  */

  public function postUsersAction(Request $request)
  {
    $user = new User();
    $form = $this->createForm(UserType::class, $user);

    $form->submit($request->request->all());



    if($form->isValid()) {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();

        return $user;
    } else {

      return $form;
    }

  }

  /**
  * @Rest\View("statusCode=Response::HTTP_NO_CONTENT")
  * @Rest\DELETE("/users/{id}")
  */
  public function deleteUserAction(Request $request)
  {
      $em = $this->get('doctrine.orm.entity_manager');
      $user = $em->getRepository('AppBundle:User')
                ->find($request->get('id'));
                /* @Var $user User */
      if ($user)
      {
        $em->remove($user);
        $em->flush();
      }
  }

/**
* @Rest\View()
* @Rest\Put("/users/{id}")
*/
public function updateUserAction(Request $request)
{
    return $this->updateUser($request, true);
}

/**
* @Rest\View()
* @Rest\Patch("/users/{id}")
*/
public function patchUserAction(Request $request)
{
    return $this->updateUser($request, false);
}

private function updateUser(Request $request, $clearMissing)
{
    $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
          /* @Var $user User */

    if (empty($user))
    {
        return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }


    $form = $this->createForm(UserType::class, $user);

    // le paramètre false dit à symfony de garder les valeurs dans notre
    // entité si l'utilisateur n'en fournit pas une dans sa requête
    $form->submit($request->request->all(), $clearMissing);

    if ($form->isValid())
    {
        $em = $this->get('doctrine.orm.entity_manager');
        // L'entité vient de la base, donc le merge n'est pas nécessaire
        // il est utilisé juste par soucis de clarté
        $em->merge($user);
        $em->flush();

        return $user;

    } else {

      return $form;

    }

}

}
 ?>
