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
    Tk\ListBundle\Form\ItemType;

class ItemsController extends Controller
{
    /**
     * Gets the Item repository
     *
     * @return Doctrine\Common\Persistence\AbstractManagerRegistry
     */
    private function getItemRepository()
    {
        return $this->getDoctrine()->getRepository('TkListBundle:Item');
    }

    private function getListAction()
    {
        $list_id = $this->get('session')->get('list_id');
        return $this->getDoctrine()->getRepository('TkListBundle:Lists')->find($list_id);   
    }

    public function optionsItemsAction()
    {} // "options_items" [OPTIONS] /items

    public function getItemsAction()
    {
        $list = $this->getListAction();
        $items = $list->getItems();        

        $serializer = $this->container->get('serializer');
        $response = $serializer->serialize($items, 'json');
        return new Response($response); 
    } // "get_items"     [GET] /items

    public function newItemsAction()
    {} // "new_items"     [GET] /items/new

    public function postItemsAction()
    {
        $data = $this->getRequest()->request->all();
        $list = $this->getListAction();

        $item = new Item();
        $item->setName($data['name']);
        $item->setStatus($data['status']);
        $item->setList($list);

        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        $serializer = $this->container->get('serializer');
        $response = $serializer->serialize($item, 'json');
        return new Response($response);

    } // "post_items"    [POST] /items

    public function patchItemsAction()
    {} // "patch_items"   [PATCH] /items

    public function getItemAction($slug)
    {
        $item = $this->getDoctrine()->getRepository('TkListBundle:Item')->find($slug);
        $data = array('id' => $item->getId(), 'name' => $item->getName(), 'status' => $item->getStatus());

        $serializer = $this->container->get('serializer');
        $response = $serializer->serialize($item, 'json');
        return new Response($response);

    } // "get_item"      [GET] /items/{slug}

    public function editItemAction($slug)
    {} // "edit_item"     [GET] /items/{slug}/edit

    public function putItemAction($slug)
    {
        $item = $this->getItemRepository()->find($slug);
        if (!$item) {
            throw $this->createNotFoundException();
        }

        $data = $this->getRequest()->request->all();

        $item->setName($data['name']);
        $item->setStatus($data['status']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($item));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
         
    } // "put_item"      [PUT] /items/{slug}

    public function patchItemAction($slug)
    {} // "patch_item"    [PATCH] /items/{slug}

    public function lockItemAction($slug)
    {} // "lock_item"     [PATCH] /items/{slug}/lock

    public function banItemAction($slug)
    {} // "ban_item"      [PATCH] /items/{slug}/ban

    public function removeItemAction($slug)
    {} // "remove_item"   [GET] /items/{slug}/remove

    public function deleteItemAction($slug)
    {
        $item = $this->getItemRepository()->find($slug);
        if (!$item) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($item));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    } // "delete_item"   [DELETE] /items/{slug}

    public function getItemCommentsAction($slug)
    {} // "get_item_comments"    [GET] /items/{slug}/comments

    public function newItemCommentsAction($slug)
    {} // "new_item_comments"    [GET] /items/{slug}/comments/new

    public function postItemCommentsAction($slug)
    {} // "post_item_comments"   [POST] /items/{slug}/comments

    public function getItemCommentAction($slug, $id)
    {} // "get_item_comment"     [GET] /items/{slug}/comments/{id}

    public function editItemCommentAction($slug, $id)
    {} // "edit_item_comment"    [GET] /items/{slug}/comments/{id}/edit

    public function putItemCommentAction($slug, $id)
    {} // "put_item_comment"     [PUT] /items/{slug}/comments/{id}

    public function postItemCommentVoteAction($slug, $id)
    {} // "post_item_comment_vote" [POST] /items/{slug}/comments/{id}/vote

    public function removeItemCommentAction($slug, $id)
    {} // "remove_item_comment"  [GET] /items/{slug}/comments/{id}/remove

    public function deleteItemCommentAction($slug, $id)
    {} // "delete_item_comment"  [DELETE] /items/{slug}/comments/{id}

    public function linkItemAction($slug)
    {} // "link_item_friend"     [LINK] /items/{slug}

    public function unlinkItemAction($slug)
    {} // "link_item_friend"     [UNLINK] /items/{slug}
}