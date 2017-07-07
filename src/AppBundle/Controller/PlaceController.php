<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\PlaceType;
use AppBundle\Entity\Place;

class PlaceController extends Controller
{


  /**
  * @Rest\View()
  * @Rest\Get("/places/{id}")
  */
  public function getPlaceAction($id, Request $request)
  {
    $place = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Place')
                ->find($id);
          /* @var $place Place */

    if (empty($place))
    {
        return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }

    /*
    $formatted = [
      'id' => $place->getId(),
      'name' => $place->getName(),
      'address' => $place->getAddress(),
    ];

    return new JsonResponse($formatted);
    */
    return $place;

  }

  /**
  * @Rest\View()
  * @Rest\Get("/places")
  */
  public function getPlacesAction(Request $request)
  {
    $places = $this->get('doctrine.orm.entity_manager')
                  ->getRepository('AppBundle:Place')
                  ->findAll();
          /* @var $places Place[] */

    // No need to format anything because we have the serializer
    /*$formatted = [];
    foreach($places as $place)
    {
      $formatted[] = [
        'id' => $place->getId(),
        'name' => $place->getName(),
        'address' => $place->getAddress(),
      ];
    }*/


    // Création d'une vue FOSRestBundle
    // No need to specify that this is json cause we force format by default in config.yml
    /*
    $view = View::create($places);
    $view->setFormat('json');*/

    return $places;

  }

  /**
  * @Rest\View(statusCode=Response::HTTP_CREATED)
  * @Rest\Post("/places")
  */
 public function postPlacesAction(Request $request)
 {
     $place = new Place();
     $form = $this->createForm(PlaceType::class, $place);

     $form->submit($request->request->all()); // Validation des données

     if ($form->isValid()) {
         $em = $this->get('doctrine.orm.entity_manager');
         $em->persist($place);
         $em->flush();
         return $place;
     } else {
         return $form;
     }
 }

 /**
 * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
 * @Rest\Delete("/places/{id}")
 */
 public function deletePlaceAction(Request $request)
 {
    $em = $this->get('doctrine.orm.entity_manager');
    $place = $em->getRepository('AppBundle:Place')
                ->find($request->get('id'));
        /* @var $place place */

   if ($place)
    {
        $em->remove($place);
        $em->flush();
    }
 }

 /**
 * @Rest\View()
 * @Rest\Put("/places/{id}")
 */
 public function updatePlaceAction(Request $request)
 {
    return $this->updatePlace($request, true);
 }

 /**
 * @Rest\View()
 * @Rest\Patch("/places/{id}")
 */
 public function patchPlaceAction(Request $request)
 {
    return $this->updatePlace($request, false);
 }

 private function updatePlace(Request $request, $clearMissing)
 {
   $place = $this->get('doctrine.orm.entity_manager')
               ->getRepository('AppBundle:Place')
               ->find($request->get('id'));
             /* @Var $place Place */

   if (empty($place))
   {
       return  \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
   }

   $form = $this->createForm(PlaceType::class, $place);

   // le paramètre false dit à symfony de garder les valeurs dans notre
   // entité si l'utilisateur n'en fournit pas une dans sa requête
   $form->submit($request->request->all(), $clearMissing);

   if ($form->isValid())
   {
       $em = $this->get('doctrine.orm.entity_manager');
       // l'entité vient de la base, donc le merge n'est pas nécessaire.
       // il est utilisé juste par soucis de clarté
       $em->merge($place);
       $em->flush();
       return $place;
   } else {
       return $form;
   }

 }

}

?>
