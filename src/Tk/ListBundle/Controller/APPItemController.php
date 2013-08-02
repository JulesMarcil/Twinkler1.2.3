<?php
namespace Tk\ListBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\RestBundle\View\RouteRedirectView,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\ParamFetcherInterface;

use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Exclude;

use Tk\ListBundle\Entity\Item,
    Tk\ListBundle\Form\Lists;

class APPItemController extends Controller
{
    public function getAppItemsAction()
    {
        $response = array('message' => 'get method not usable, get Items through lists');

        $serializer = $this->container->get('serializer');
        $response = $serializer->serialize($response, 'json');
        return new Response($response);
    } // "get_app_items"     [GET] /items

    public function postAppItemsAction()
    {
        $data = $this->getRequest()->request->all();
        $list = $this->getDoctrine()->getRepository('TkListBundle:Lists')->find($data['list_id']);

        $Item = new Item();
        $Item->setName($data['name']);
        $Item->setStatus('incomplete');
        $Item->setList($list);

        $em = $this->getDoctrine()->getManager();
        $em->persist($Item);
        $em->flush();

        $serializer = $this->container->get('serializer');
        $response = $serializer->serialize($Item, 'json');
        return new Response($response);

    } // "post_app_items"    [POST] /app/items
}