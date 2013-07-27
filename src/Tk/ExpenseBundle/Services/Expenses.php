<?php
//src/Tk/ExpenseBundle/Services/Expenses.php

namespace Tk\ExpenseBundle\Services;

class Expenses {

	protected $em;

	public function __construct(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
	}

	public function getAllExpenses($member)
    {
    	$all_expenses_col = $member->getTGroup()->getExpenses();
        $all_expenses = array();

    	foreach($all_expenses_col as $expense){
    		$all_expenses[] = array($expense, $this->forYou($member, $expense));
    	}

    	return $all_expenses;
    }

    public function getMyExpenses($member)
    {
        $all_expenses = $member->getTGroup()->getExpenses();
        $my_expenses = array();

        foreach($all_expenses as $expense){
            if($expense->getOwner() == $member){
                $my_expenses[] = array($expense, $this->forYou($member, $expense));
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
    			$other_expenses[] = array($expense, $this->forYou($member, $expense));
    		}
    	}

    	return $other_expenses;
    }

    private function forYou($member, $expense)
    {
    	$members = $expense->getUsers()->toArray();
    	if(in_array($member, $members)){
    		return round(($expense->getAmount())/(sizeof($members)),2);
    	}else{
    		return 0;
    	}
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
                $positive[$balance[0]->getName()] = $balance[1]; 
            }
            elseif ($balance[1] < 0){
                $negative[$balance[0]->getName()] = $balance[1];
            }
            else{}
        }

        foreach($positive as $key1 => $value1){
            foreach($negative as $key2 => $value2){
                if ($value1 == -$value2){
                    $payments[] = array($key2, $value1, $key1);
                    $positive[$key1]= 0;
                    $negative[$key2]= 0;
                }
            }
        }

        reset($positive);
        reset($negative);

        arsort($positive);
        asort($negative);

        if (current($positive) <= 0.1) { $continue = false; } else { $continue = true; }

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

            if (current($positive) <= 0.2) { $continue = false; } else { $continue = true; }
        }

        return $payments;
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
}