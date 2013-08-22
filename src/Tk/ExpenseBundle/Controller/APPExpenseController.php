<?php
namespace Tk\ExpenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\RestBundle\View\RouteRedirectView,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\ParamFetcherInterface;

use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Exclude;

use Tk\ExpenseBundle\Entity\Expense,
    Tk\ExpenseBundle\Form\ExpenseType,
    Tk\GroupBundle\Entity\TGroup,
    Tk\UserBundle\Entity\Member;

class APPExpenseController extends Controller
{
    public function optionsExpensesAction()
    {} // "options_expenses" [OPTIONS] /expenses

    public function getExpensesAction()
    {
        $data = $this->getRequest()->query->all();
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
        $group = $member->getTGroup();
        $expenses = $group->getExpenses();

        $response_array = array();

        foreach($expenses as $expense){
            $members = array();
            foreach($expense->getUsers() as $m){
                $members[] = array('id' => $m->getId(), 'name' => $m->getName(), 'picturePath' => $m->getPicturePath());
            }
            $response_item = array(
                'name' => $expense->getName(),
                'amount' => $expense->getAmount(),
                'owner' => array('id' => $expense->getOwner()->getId(), 'name' => $expense->getOwner()->getName(), 'picturePath' => $expense->getOwner()->getPicturePath()),
                'date' => $expense->getDate()->getTimestamp(),
                'members' => $members,
                'active' => $expense->getActive(),
                'author' => $expense->getAuthor()->getName(),
                'addedDate' => $expense->getAddedDate()->getTimestamp(),
                'share' => $this->container->get('tk_expense.expenses')->forYou($member, $expense),
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
    {
        $data = $this->getRequest()->request->all();
        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($data['currentGroupId']);
        $member_repo = $this->getDoctrine()->getRepository('TkUserBundle:Member');
        $owner = $member_repo->find($data['owner_id']);
        $author = $member_repo->find($data['author_id']);

        $expense = new Expense();
        $expense->setAmount($data['amount']);
        $expense->setName($data['name']);
        $expense->setAddedDate(new \DateTime('now'));
        $expense->setDate(new \Datetime('today'));
        $expense->setActive(true);
        $expense->setAuthor($author);
        $expense->setOwner($owner);
        $expense->setGroup($group);

        foreach($data['member_ids'] as $id) {
            $member = $member_repo->find($id);
            $expense->addUser($member);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($expense);
        $em->flush();

        return new JsonResponse(array('message' => 'expense ('.$expense->getId().' : '.$expense->getName().') added successfully'));
    } // "post_expenses"    [POST] /expenses

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