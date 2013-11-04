<?php
//src/Tk/ExpenseBundle/Services/Expenses.php

namespace Tk\ExpenseBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class Expenses {

    protected $doctrine;
	protected $em;

	public function __construct(Doctrine $doctrine)
	{
        $this->doctrine = $doctrine;
		$this->em = $doctrine->getManager();
	}

	public function getAllExpenses($member)
    {
    	$all_expenses_col = $member->getTGroup()->getExpenses();
        $all_expenses = array();

    	foreach($all_expenses_col as $expense){
            if ($expense->getType() == 'expense'){                            
    		  $all_expenses[] = array($expense, $this->youGet($member, $expense));
            }
    	}

    	return $all_expenses;
    }

    public function getAllPaybacks($member)
    {
        $all_expenses_col = $member->getTGroup()->getExpenses();
        $all_expenses = array();

        foreach($all_expenses_col as $expense){
            if ($expense->getType() == 'payback'){ 
                $all_expenses[] = $expense;
            }
        }

        return $all_expenses;
    }

    public function getMyExpenses($member)
    {
        $all_expenses = $member->getTGroup()->getExpenses();
        $my_expenses = array();

        foreach($all_expenses as $expense){
            if($expense->getOwner() == $member){
                $my_expenses[] = array($expense, $this->youGet($member, $expense));
            }else{}
        }

        return $my_expenses;
    }
    
    public function getOtherExpenses($member)
    {
    	$all_expenses = $member->getTGroup()->getExpenses();

    	$other_expenses = array();
    	foreach($all_expenses as $expense){
    		if ($expense->getOwner() == $member){
    		}else{
    			$other_expenses[] = array($expense, $this->youGet($member, $expense));
    		}
    	}

    	return $other_expenses;
    }

    public function forYou($member, $expense)
    {
    	$members = $expense->getUsers()->toArray();
    	if(in_array($member, $members)){
    		return round(($expense->getAmount())/(sizeof($members)),2);
    	}else{
    		return 0;
    	}
    }

    public function youGet($member, $expense)
    {
        $forYou = $this->forYou($member, $expense);
        if ($expense->getOwner() == $member) {
            $paid = $expense->getAmount();
        } else {
            $paid = 0;
        }

        return round($paid - $forYou, 2);
    }

    public function getTotalPaid($group)
    {
        $all_expenses = $group->getExpenses();

    	$sum = 0;
    	foreach($all_expenses as $expense){
    		$sum += $expense->getAmount();
    	}

    	return $sum;
    }

    public function getTotalPaidByMe($member)
    {
        $sum = 0;
        foreach($this->getMyExpenses($member) as $expense){
            $sum += $expense[0]->getAmount();
        }

        return $sum;
    }

    public function getTotalSupposedPaid($member)
    {
        $all_expenses = $member->getTGroup()->getExpenses();

        $sum = 0;
        foreach($all_expenses as $expense){
            $sum += $this->forYou($member, $expense);
        }

        return $sum;
    }

    public function getTotalPaidForMe($member)
    {
    	$sum = 0;
    	$other_expenses = $this->getOtherExpenses($member);
    	foreach($other_expenses as $expense){
    		$sum += $expense[1];
    	}

    	return $sum;
    }

    public function getCurrentDebts($group)
    {
        $balances = $this->getBalances($group);

        $payments = array();
        $positive = array();
        $negative = array();

        foreach($balances as $balance){

            if ($balance[1] > 0){
                $positive[$balance[0]->getId()] = $balance[1]; 
            }
            elseif ($balance[1] < 0){
                $negative[$balance[0]->getId()] = $balance[1];
            }
            else{}
        }

        foreach($positive as $key1 => $value1){
            foreach($negative as $key2 => $value2){
                if ($value1 == -$value2){
                    $payments[] = array($key2, $value1, $key1);
                    $positive[$key1]= 0;
                    $negative[$key2]= 0;
                    $value1 = 0;
                }
            }
        }

        reset($positive);
        reset($negative);

        arsort($positive);
        asort($negative);

        if (current($positive) <= 0.02*sizeof($balances)) { $continue = false; } else { $continue = true; }

        while ($continue){

            reset($positive);
            reset($negative);

            $cp = current($positive);
            $cn = current($negative);
            $kp = key($positive);
            $kn = key($negative);
            $min = round(min($cp,-$cn),2);

            $payments[] = array($kn, $min, $kp);

            $positive[$kp] -= $min;
            $negative[$kn] += $min;

            arsort($positive);
            asort($negative);
            reset($positive);
            reset($negative);

            if (current($positive) <= 0.02*sizeof($balances)) { $continue = false; } else { $continue = true; }
        }

        $repo = $this->doctrine->getRepository('TkUserBundle:Member');
        $debts = array();

        foreach($payments as $key => $value){
            $member1 = $repo->find($value[0]);
            $member2 = $repo->find($value[2]);
            $debts[] = array($member1, $value[1], $member2); 
        }

        return $debts;
    }

    private function getBalances($group)
    {
        $all_members = $group->getMembers();

        $balances = array();
        foreach($all_members as $member){
            $balances[] = array($member, $member->getBalance());
        }
        return $balances;
    }

    public function getMyCurrentDebts($member){
        
        $debts = $this->getCurrentDebts($member->getTGroup());
        $my_debts = array();
        foreach($debts as $debt){
            if ($debt[0] == $member){
                $my_debts[] = $debt;
            }
        }
        return $my_debts;
    }
}