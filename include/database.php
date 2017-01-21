<?php
/*
<Secret Blog>
Copyright (C) 2012-2017 太陽部落格站長 Secret <http://gdsecret.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, version 3.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Also add information on how to contact you by electronic and paper mail.

  If your software can interact with users remotely through a computer
network, you should also make sure that it provides a way for users to
get its source.  For example, if your program is a web application, its
interface could display a "Source" link that leads users to an archive
of the code.  There are many ways you could offer source, and different
solutions will be better for different programs; see section 13 for the
specific requirements.

  You should also get your employer (if you work as a programmer) or school,
if any, to sign a "copyright disclaimer" for the program, if necessary.
For more information on this, and how to apply and follow the GNU AGPL, see
<http://www.gnu.org/licenses/>.
*/

class Database {
	private $conn;
	private $addr;
	private $user;
	private $pass;
	private $db;

	public function __construct($addr,$user,$pass,$db){
		$this->addr = $addr;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;

		$this->conn = new mysqli($addr,$user,$pass,$db);
		
		if($this->conn->connect_error !== null){
			throw new Exception($this->conn->connect_error);
		}
	}
	
	private function reconnect(){
		$this->conn = new mysqli($this->addr,$this->user,$this->pass,$this->db);
	}

	private function checkConn(){
		return $this->conn->ping();
	}

	public function query($query,$data = array()){
		if(!$this->checkConn()) $this->reconnect();
		
		foreach($data as $k=>$d){
			$data[$k] = $this->conn->real_escape_string($d);
		}
		
		$result = $this->conn->query(vsprintf($query,$data));
		
		if($result === false){
			throw new Exception($this->conn->error);
		}
		
		return $result;
	}
};