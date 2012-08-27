<?php
/* 	
	Problem Description:  
	Write a class in PHP that implements a stack. This data structure should
    support the typical operations on a stack (insert and delete). In
    addition, it should support a method that returns the smallest element
    in the stack. This last operation can be done in O(1).

	Written by: Anthony Su
	Date: 4/21/12
	Implementation:
		1. Create a stack of nodes
			a.The stack is actually a combination of a stack and a linked list(ordered from smallest to greatest)
				i. this implementation allows us to return the smallest value in O(1) by referencing the list_head
				ii. this implementation allows us to also pop the node off the stack in O(1) by referencing stack_head
		2. Node is effectively a struct that stores its value, and pointers to the list(next) and stack(smaller,larger)
		3. auxiliary functions print_list and print_stack have been written 
			
*/
	class Node {
		public $value;
		public $next; //next in stack
		public $smaller;
		public $larger;
	}
	
	class Stack {

		function __construct() {
			$this->stack_head = null;
			$this->list_head = null;
		}
		
		public function push($num){
		//insert
		//push onto stack
			$n_node = new Node();
			$n_node->value = $num;
			
			if($this->get_shead() == null){
			
				$n_node->next = null;
				$this->stack_head = $n_node;
				
			} else  {
			
				$temp_node = $this->stack_head;
				$n_node->next = $temp_node;
				$this->stack_head = $n_node;
				
			}
			
		//push onto ordered list
			if($this->get_lhead() == null) {//unitialized list
			
				$n_node->smaller = null;
				$n_node->larger = null;
				$this->list_head = $n_node;
				
			} else {
			
				if($n_node->value < $this->get_lhead()->value) {//insert at top
					$temp = $this->get_lhead();
					$temp->smaller = $n_node;
					
					$n_node->smaller = null;
					$n_node->larger = $temp;
					$this->list_head = $n_node;
					
				} else {// insert at middle
				
						$cur_node = $this->get_lhead();//node before insertion
						$next_node = $this->get_lhead()->larger;//node after insertion
					
						while($next_node != null){
					
							if($n_node->value < $next_node->value) {
							
								$cur_node->larger = $n_node;
								$n_node->smaller = $cur_node;
								$n_node->larger = $next_node;
								break;
							}

							//iterate to next node if not inserted
							$cur_node = $next_node;
							$next_node = $next_node->larger;//move to larger node
					
						}
						
						if($next_node == null) {//insert at end
						
							$cur_node->larger = $n_node;
							$n_node->smaller = $cur_node;
							$n_node->larger = null;
						}
				}
			}
		}
		
		public function pop() {
		
		
			if($this->get_shead() != null) {
				//delete
				
				$pop_node = $this->get_shead();
				
				//update list
		
			
				if($this->get_shead() == $this->get_lhead()) {
					
					if($this->get_lhead()!= null) {
					
						$this->list_head = $this->get_lhead()->larger;
							//changes list_head and set its smaller value to null
						if($this->get_lhead() != null) {
							$this->get_lhead()->smaller = null;
						}
					}
				
				} else { //set list values accordingly by using stack pointer to reference
					   //use stack reference instead of iterating through list to find node in list

					if($this->get_shead()->larger != null) {
						$this->get_shead()->larger->smaller = $this->get_shead()->smaller;
					}
				
					if($this->stack_head->smaller != null) {
						$this->get_shead()->smaller->larger = $this->get_shead()->larger;
					}
				}
				
				//pop
				if($this->get_shead()!= null) {
					$this->stack_head = $this->get_shead()->next;
				}
				
				return $pop_node;
			}
		}
		
		
		public function print_stack() {
		//auxiliary function for printing stack
			$node = $this->get_shead();
			$i = 0;
			echo "/*---STACK---*/"."<br/>";
			while($node != null)
			{
				echo "[".$i."]: ".$node->value."<br />";
				$node = $node->next;
				$i++;
			}
			echo "/*---end STACK---*/"."<br/>";
		}
		
		public function print_list() {
		//auxiliary function for printing list
			$node = $this->get_lhead();
			$i = 0;
			echo "/*---LIST---*/"."<br/>";
			while($node != null)
			{
				echo "[".$i."]: ".$node->value."<br />";
				$node = $node->larger;
				$i++;
			}
			echo "/*---end LIST---*/"."<br/>";
		}
		
		public function get_shead() {
			return $this->stack_head;
		}
		
		public function get_lhead() {
			return $this->list_head;
		}
		
		public function smallest() {
			if($this->get_lhead()!= null ) {
				return $this->list_head->value;
			}
		}
		
		function __destruct() {
			//reset pointers
			$this->stack_head = null;
			$this->list_head = null;
		}
		
	  	private $stack_head;
		private $list_head;//ordered list from smallest to largest
		
	}
	

	
	/*
	
	//Uncomment to run Test
	
	$numStack = new Stack();
	$numStack->push(4);
	$numStack->push(5);
	$numStack->push(3);
	$numStack->push(5);
	$numStack->push(5);
	$numStack->push(2);
	$numStack->push(4);
	$numStack->push(5);
	$numStack->push(5);
	$numStack->push(1);
	
	$numStack->print_stack();
	$numStack->print_list();
	
	echo $numStack->smallest() ."<br />"; //1
	assert ($numStack->smallest() == 1);
	
	$numStack->pop();
	echo $numStack->smallest() ."<br />";  //2
	assert($numStack->smallest() == 2);
	
	$numStack->print_stack(); 

	
	$numStack->pop();
	$numStack->pop();
	$numStack->pop();
	$numStack->pop();
	$numStack->pop();
	echo $numStack->smallest() ."<br />"; //3
	assert($numStack->smallest() == 3);
	$numStack->print_stack();
	$numStack->print_list();
	
	//pop off until only one value left
	$numStack->pop();
	$numStack->pop();
	$numStack->pop();
	echo $numStack->smallest() ."<br />"; //4
	assert($numStack->smallest() == 4);
	$numStack->print_stack();
	$numStack->print_list();
	
	$numStack->pop();
	echo $numStack->get_shead();
	echo $numStack->get_lhead();
	
	assert($numStack->get_shead() == null);
	assert($numStack->get_lhead() == null);
	
	//both should be null
	if($numStack->get_shead() == null)
	{
		echo "null _shead" . "<br/>";
	}
	
	if($numStack->get_lhead() == null) {
		echo "null_lhead" . "<br/>";
	}

	//both should be empty
	$numStack->print_stack();
	$numStack->print_list();
	
	*/
	
?>