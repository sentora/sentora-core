<?php
namespace Dryden\example;

use Dryden\example\RunAbstract;

class RunClass extends RunAbstract implements RunInterface {

	public function RunClassTest(){
		return 'RunClassTest:true';
	}	

}
