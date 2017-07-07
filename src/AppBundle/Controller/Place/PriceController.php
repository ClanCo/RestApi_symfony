<?php
namespace AppBundle\Controller\Place;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations

class PriceController extends Controller
{

  /**
  * @Rest\View()
  * @Rest\Get("places/{id}/prices")
  */
  public function getPricesAction(Request $request)
  {
      $places = $this->get('doctrine.orm.entity_manager')
                  ->getRepository('AppBundle:Place')
                  ->find($request->get($id)); // L'identifiant en tant que paramétre n'est plus nécessaire
            /* @var $place Place */

      if (empty($place)) {
          return $this->placeNotFound();
      }

      return $place->getPrices();

  }

  /**
  * @Rest\View(statusCode=Response::HTTP_CREATED)
  * @Rest\Post("/places/{id}/prices")
  */
  public function postPricesAction(Request $request)
  {

  }

}

?>
