<?php
namespace Tk\ExpenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\RestBundle\View\RouteRedirectView,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\ParamFetcherInterface;

use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Exclude;

use Tk\ExpenseBundle\Entity\Expense,
    Tk\ExpenseBundle\Form\ExpenseType,
    Tk\GroupBundle\Entity\TGroup;

class ExpensesController extends Controller
{
    public function optionsExpensesAction()
    {} // "options_expenses" [OPTIONS] /expenses

    public function getExpensesAction()
    {
        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find(6);
        $expenses = $group->getExpenses();

        $response_array = array();

        foreach($expenses as $expense){
            $members = array();
            foreach($expense->getUsers() as $member){
                $members[] = array('id' => $member->getId(), 'name' => $member->getName());
            }
            $response_item = array(
                'name' => $expense->getName(),
                'amount' => $expense->getAmount(),
                'owner' => array('id' => $expense->getOwner()->getId(), 'name' => $expense->getOwner()->getName()),
                'date' => $expense->getDate()->getTimestamp(),
                'members' => $members,
                'active' => $expense->getActive(),
                'author' => $expense->getAuthor()->getName(),
                'addedDate' => $expense->getAddedDate()->getTimestamp(),
                );
            $response_array[] = $response_item;
        }

        $serializer = $this->container->get('serializer');
        $response = $serializer->serialize($response_array, 'json');
        return new Response($response); 

    } // "get_expenses"     [GET] /expenses

    public function newExpensesAction()
    {} // "new_expenses"     [GET] /expenses/new

    public function postExpensesAction()
    {} // "post_expenses"    [POST] /expenses

    public function patchExpensesAction()
    {} // "patch_expenses"   [PATCH] /expenses

    public function getExpenseAction($slug)
    {} // "get_expense"      [GET] /expenses/{slug}

    public function editExpenseAction($slug)
    {} // "edit_expense"     [GET] /expenses/{slug}/edit

    public function putExpenseAction($slug)
    {} // "put_expense"      [PUT] /expenses/{slug}

    public function patchExpenseAction($slug)
    {} // "patch_expense"    [PATCH] /expenses/{slug}

    public function lockExpenseAction($slug)
    {} // "lock_expense"     [PATCH] /expenses/{slug}/lock

    public function banExpenseAction($slug)
    {} // "ban_expense"      [PATCH] /expenses/{slug}/ban

    public function removeExpenseAction($slug)
    {} // "remove_expense"   [GET] /expenses/{slug}/remove

    public function deleteExpenseAction($slug)
    {} // "delete_expense"   [DELETE] /expenses/{slug}

    public function getExpenseCommentsAction($slug)
    {} // "get_expense_comments"    [GET] /expenses/{slug}/comments

    public function newExpenseCommentsAction($slug)
    {} // "new_expense_comments"    [GET] /expenses/{slug}/comments/new

    public function postExpenseCommentsAction($slug)
    {} // "post_expense_comments"   [POST] /expenses/{slug}/comments

    public function getExpenseCommentAction($slug, $id)
    {} // "get_expense_comment"     [GET] /expenses/{slug}/comments/{id}

    public function editExpenseCommentAction($slug, $id)
    {} // "edit_expense_comment"    [GET] /expenses/{slug}/comments/{id}/edit

    public function putExpenseCommentAction($slug, $id)
    {} // "put_expense_comment"     [PUT] /expenses/{slug}/comments/{id}

    public function postExpenseCommentVoteAction($slug, $id)
    {} // "post_expense_comment_vote" [POST] /expenses/{slug}/comments/{id}/vote

    public function removeExpenseCommentAction($slug, $id)
    {} // "remove_expense_comment"  [GET] /expenses/{slug}/comments/{id}/remove

    public function deleteExpenseCommentAction($slug, $id)
    {} // "delete_expense_comment"  [DELETE] /expenses/{slug}/comments/{id}

    public function linkExpenseAction($slug)
    {} // "link_expense_friend"     [LINK] /expenses/{slug}

    public function unlinkExpenseAction($slug)
    {} // "link_expense_friend"     [UNLINK] /expenses/{slug}
}