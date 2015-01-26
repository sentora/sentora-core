<?php
namespace Dryden\example;

use Dryden\example\RunAbstract;
use Dryden\example\RunInterface;

class RunClass extends RunAbstract implements RunInterface {

	public function RunClassTest(){
		return 'RunClassTest:true';
	}	

}
