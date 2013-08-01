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

use Tk\ListBundle\Entity\Lists;

class JSONListController extends Controller
{
    public function optionsListsAction()
    {} // "options_lists" [OPTIONS] /lists

    public function getListsAction()
    {
    	$group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find(6);
    	$lists = $group->getLists();

    	$response_array = array();

    	foreach($lists as $list) {
    		$items = $list->getItems();
    		$items_array = array();
    		foreach($items as $item){
    			$items_array[] = array('id' => $item->getId(), 'name' => $item->getName(), 'status' => $item->getStatus());
    		}
    		$response_array[] = array('name' => $list->getName(), 'items' => $items_array);
    	}

    	$serializer = $this->container->get('serializer');
        $response = $serializer->serialize($response_array, 'json');
        return new Response($response); 

    } // "get_lists"     [GET] /lists

    public function newListsAction()
    {} // "new_lists"     [GET] /lists/new

    public function postListsAction()
    {} // "post_lists"    [POST] /lists

    public function patchListsAction()
    {} // "patch_lists"   [PATCH] /lists

    public function getListAction($slug)
    {} // "get_list"      [GET] /lists/{slug}

    public function editListAction($slug)
    {} // "edit_list"     [GET] /lists/{slug}/edit

    public function putListAction($slug)
    {} // "put_list"      [PUT] /lists/{slug}

    public function patchListAction($slug)
    {} // "patch_list"    [PATCH] /lists/{slug}

    public function lockListAction($slug)
    {} // "lock_list"     [PATCH] /lists/{slug}/lock

    public function banListAction($slug)
    {} // "ban_list"      [PATCH] /lists/{slug}/ban

    public function removeListAction($slug)
    {} // "remove_list"   [GET] /lists/{slug}/remove

    public function deleteListAction($slug)
    {} // "delete_list"   [DELETE] /lists/{slug}

    public function getListCommentsAction($slug)
    {} // "get_list_comments"    [GET] /lists/{slug}/comments

    public function newListCommentsAction($slug)
    {} // "new_list_comments"    [GET] /lists/{slug}/comments/new

    public function postListCommentsAction($slug)
    {} // "post_list_comments"   [POST] /lists/{slug}/comments

    public function getListCommentAction($slug, $id)
    {} // "get_list_comment"     [GET] /lists/{slug}/comments/{id}

    public function editListCommentAction($slug, $id)
    {} // "edit_list_comment"    [GET] /lists/{slug}/comments/{id}/edit

    public function putListCommentAction($slug, $id)
    {} // "put_list_comment"     [PUT] /lists/{slug}/comments/{id}

    public function postListCommentVoteAction($slug, $id)
    {} // "post_list_comment_vote" [POST] /lists/{slug}/comments/{id}/vote

    public function removeListCommentAction($slug, $id)
    {} // "remove_list_comment"  [GET] /lists/{slug}/comments/{id}/remove

    public function deleteListCommentAction($slug, $id)
    {} // "delete_list_comment"  [DELETE] /lists/{slug}/comments/{id}

    public function linkListAction($slug)
    {} // "link_list_friend"     [LINK] /lists/{slug}

    public function unlinkListAction($slug)
    {} // "link_list_friend"     [UNLINK] /lists/{slug}
}