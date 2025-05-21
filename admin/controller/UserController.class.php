<?php
class UserController extends NesoteController
{
	
	function validuser()
	{
		$username=$_COOKIE['a_username'];
		$password=$_COOKIE['a_password'];

		$db=new NesoteDALController();

		$no=$db->total("nesote_email_admin","username=? and password=? and status=?",[$username,$password,1]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }

	}

	function getsymbol()
	{
		return  htmlentities(" <any text> ");
	}
	function getuseridformat($id)
	{
		//echo $id;
		$db1=new NesoteDALController();
		$db1->select("nesote_liberyus_users");
		$db1->fields("username");
		$db1->where("id=?",[$id]);
		$result1=$db1->query();
		$row1=$db1->fetchRow($result1);

		
		return htmlentities("<".$row1[0].$this->getextension().">");
	
	}

	function getextension()
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='emailextension'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		if (stristr(trim((string) $row[0]),"@")!="") {
            return $row[0];
        } else {
            return htmlentities("@".$row[0]);
        }
	}

	function getusernameformat($id)
	{

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("username");
		$db->where("id=?",[$id]);
		$result1=$db->query();
		$row1=$db->fetchRow($result1);

		
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='emailextension'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		if (stristr(trim((string) $row[0]),"@")!="") {
            return htmlentities($row1[0].$row[0]);
        } else {
            return htmlentities($row1[0]."@".$row[0]);
        }
	}

	function getusername($id)
	{
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("username");
		$db->where("id=?",[$id]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		return $row[0];
	}
	function existusername($username)
	{
		$db=new NesoteDALController();

		$no=$db->total("nesote_liberyus_users","username=?",[$username]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }
	}
	function getuserid($username)
	{
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("id");
		$db->where("username=?",[$username]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		return $row[0];
	}


	function getgroupname($id)
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_contactgroup");
		$db->fields("name");
		$db->where("id=?",[$id]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		return $row[0];
	}

	function getfoldername($id,$tot,$i)
	{

		$db=new NesoteDALController();
		$db->select("nesote_email_contactgroup");
		$db->fields("name");
		$db->where("id=?",[$id]);
		$rs=$db->query();
		$rw=$db->fetchRow($rs);
		//		if($rw[0]!="")
		//		return $rw[0].",";
		//		else
		//		return $rw[0];

		if (($rw[0]!="")&&($tot-1>$i)) {
            return $rw[0].",";
        } else {
            return $rw[0];
        }

	}

	function getoperation($operation)
	{

		$db=new NesoteDALController();
		$db->select("nesote_email_client_logs");
		$db->fields("operation");
		$db->where("id=?",[$operation]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		return $row[0];
	}

	function existservername($servername,$id)
	{
		$db=new NesoteDALController();
		$no=$db->total("nesote_email_spamserver_settings","name=? and id=?",[$servername,$id]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }
	}

	function existeditservername($servername)
	{
		$db=new NesoteDALController();
		$no=$db->total("nesote_email_spamserver_settings","name=?",[$servername]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }
	}

	function existeditvalue($from,$subject,$body)
	{
		$db=new NesoteDALController();
		$no=$db->total("nesote_email_spam_settings","from_id=? and subject=? and body=? and fromflag=? and subjectflag=? and bodyflag=?",[$from,$subject,$body,$fromflag,$subjectflag,$bodyflag]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }
	}

	function existvalue($from,$subject,$body,$id)
	{

		$db=new NesoteDALController();
		$no=$db->total("nesote_email_spam_settings","from_id=? and subject=? and body=? and id=?",[$from,$subject,$body,$id]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }
	}

	function gettable($flag)
	{
		
		if ($flag==1) {
            //$status='Inbox';
            $table='nesote_email_inbox';
            //$field="to_list=?";
        } elseif ($flag==2) {
            //$status='Sent';
            $table='nesote_email_sent';
            //$field="from_list=?";
        } elseif ($flag==3) {
            //$status='Draft';
            $table='nesote_email_draft';
            //$field="from_list=?";
        } elseif ($flag==4) {
            //$status='Spam';
            $table='nesote_email_spam';
            //$field="to_list=?";
        } elseif ($flag==5) {
            //$status='Trash';
            $table='nesote_email_trash';
            //$field="to_list=?";
        }
		return $table;
	}
	function getname($firstname,$lastname)
	{

		return $firstname." ".$lastname;
	}
	function userviewAction()
	{
		if($this->validuser())
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?","portal_status");
			$rs=$db->query();
			$rslt=$db->fetchRow($rs);
			$this->setValue("portal_status",$rslt[0]);
			
			$portal_status=$rslt[0];
			$status=$this->getParam(1);
			if ($status==1) {
                $this->setValue("status","User has been activated successfully");
            } elseif ($status==2) {
                $this->setValue("status","User has been blocked successfully");
            } else
			{
				$this->setValue("status","");
			}
			
			$tot=$db->total("nesote_liberyus_users");
			$perpagesize=50;			
			$this->setValue("count",$perpagesize);
			$currentpage=1;
			if ($this->getParam(2)) {
                $currentpage=$this->getParam(2);
            }
			$paging= new Paging();
			$out=$paging->seoPage($tot,$perpagesize, $currentpage, $this->url("user/userview/0"),"paging",1,1,1,"top");
			$this->setValue("pagingtop",$out);
			$db->select("nesote_liberyus_users");
			$db->fields("id,name,status,username");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$db->order("joindate desc");
			$result=$db->query();//echo $db->getQuery();
			$row=$db->numRows($result);
			if ($row==0) {
                $this->setValue("empty","-No Users found-");
            } else {
                $this->setValue("empty","");
            }
			$this->setLoopValue("users",$result->getResult());
			$this->setRedirect("user/userview");


		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}


	}

	function userdetailAction()
	{


		if($this->validuser())
		{

			$id=$this->getParam(1);
			$db=new NesoteDALController();
			$db->select("nesote_liberyus_users");
			$db->fields("*");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$id=$db->fetchRow($result);
			$this->setValue("uid",$id[0]);
			$this->setLoopValue("users",$result->getResult());

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}
	function activateuserAction()
	{
		if($this->validuser())
		{


			$id=$this->getparam(1);

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 12) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }

			
			
			$db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?","portal_status");
			$rs=$db->query();
			$rslt=$db->fetchRow($rs);
			$portal_status=$rslt[0];
			
			$db->select("nesote_liberyus_users");
			$db->fields("status");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$status=$row[0];
			
			if($portal_status==0)
			{
			$db->update("nesote_liberyus_users");
			if ($status==0) {
                $db->set("status=?",1);
            } else {
                $db->set("status=?",0);
            }
			$db->where("id=?",$id);
			$result=$db->query();
			}
			else
			{
				$this->deleteuser_portal($id);
			}
			header("Location:".$this->url($this->getRedirect()));
			exit(0);
		}

		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}

	function deleteuser_portal($userid)
	{
		if($this->validuser())
		{
			

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $userid <= 12) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }
            $db=new NesoteDALController();
			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			
		
			$account_type=$settings->getValue("catchall_mail");
			if($account_type==1)//for catch all
			{
				$db->update("nesote_liberyus_users");		
			    $db->set("status=?",5);			
			    $db->where("id=?",$userid);
			    $result=$db->query();
//				$db->delete("nesote_liberyus_users");
//				$db->where("id=?",array($id));
//				$result=$db->query();
			}
			else// for individual
			{
				
				$automatic_account_creation=$settings->getValue("automatic_account_creation");
				if($automatic_account_creation==1)//for automatic account creation
				{
					//---api calling------

					
					$controlpanel=$settings->getValue("controlpanel");

					
					$db->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
					$db->fields("a.username,b.server_password");
					$db->where("a.id=? and a.id=b.userid",[$userid]);
					$result1=$db->query();
					$row1=$db->fetchRow($result1);
					$username=$row1[0];
					$password=base64_decode((string) $row1[1]);

					if ($controlpanel==1) {
                        $this->cpanelaction(3,$username,$password);
                        //3 for account deletion
                    } elseif ($controlpanel==2) {
                        $this->pleskaction(3,$username,$password);
                        //3 for account deletion
                    }


					
					$db->update("nesote_liberyus_users");		
				    $db->set("status=?",5);			
				    $db->where("id=?",$userid);
				    $result=$db->query();
				}
				else//for manully account creation
				{
					
					$db->update("nesote_liberyus_users");		
				    $db->set("status=?",5);			
				    $db->where("id=?",$userid);
				    $result=$db->query();
				}

			}
			        $db->update("nesote_liberyus_users");		
				    $db->set("status=?",5);			
				    $db->where("id=?",$userid);
				    $result=$db->query();
			
		}
		
	}
	function changepasswordAction()
	{

		if($this->validuser())
		{
			$msg='';
			$flag=1;
			$id=$this->getParam(1);
			$userpanel="";

			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			
			$db=new NesoteDALController();

			$userpanel=$settings->getValue("controlpanel");
			$this->setValue("controlpanel",$userpanel);

			$this->setValue("uid",$id);
			$username=$this->getusername($id);
			$this->setValue("username",$username);
			$msg=$this->getParam(2);

			if(isset($msg))
			{
				if ($msg=="e") {
                    $msg="All Fields are empty!!!";
                } elseif ($msg=="n") {
                    $msg="Please enter the new  password!!!";
                } elseif ($msg=="pequ") {
                    $msg="Password cannot be same as username";
                } elseif ($msg=="c") {
                    $msg="Please enter the  confirm password!!!";
                } elseif ($msg=="nc") {
                    $msg="New password and Confirm passwords are different!!";
                } elseif ($msg=="pcnt") {
                    $msg="Password srength is weak.Choose another one.";
                }
			}

			$this->setValue("msg",$msg);


			if($_POST !== [])

			{

				require(__DIR__ . "/script.inc.php");
				
				require($config_path."system.config.php");
				

				$uid=$_POST['uid'];

				if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $uid <= 12) {
                    header("Location:".$this->url("message/error/1023"));
                    exit(0);
                }

				
				$db->select("nesote_liberyus_users");
				$db->fields("*");
				$db->where("id=?",[$uid]);
				$result2=$db->query();//echo $db->getQuery();
				$id1=$db->fetchRow($result2);
				$uname=$id1[1];
				$password=$id1[2];
					
				//				$password1=$_POST['password1'];
				//				$cpassword=md5($password1);//echo $cpassword;//52f5972aee0aa1a446e711bd9407ab72
				$cnpassword=$_POST['cpassword'];

				if($cnpassword==$uname)
				{
					$flag=0;
					$msg="pequ";
					header("Location:".$this->url("user/changepassword/$uid/$msg"));
					exit(0);

				}
				$npassword=$_POST['npassword'];
				$server_password=base64_encode((string) $npassword);



				if ($cnpassword=="" && $npassword=="") {
                    $flag=0;
                    $msg="e";
                    header("Location:".$this->url("user/changepassword/$uid/$msg"));
                    exit(0);
                } elseif ($npassword=="") {
                    $flag=0;
                    $msg="n";
                    header("Location:".$this->url("user/changepassword/$uid/$msg"));
                    exit(0);
                } elseif ($cnpassword=="") {
                    $flag=0;
                    $msg="c";
                    header("Location:".$this->url("user/changepassword/$uid/$msg"));
                    exit(0);
                } elseif ($cnpassword!=$npassword) {
                    $flag=0;
                    $msg="nc";
                    header("Location:".$this->url("user/changepassword/$uid/$msg"));
                    exit(0);
                }
				$pwdcount=$_POST['pwdcnt'];
					
				if($pwdcount<2)
				{
					$flag=0;
					$msg="pcnt";
					header("Location:".$this->url("user/changepassword/$uid/$msg"));
					exit(0);
				}
                $cnpassword=md5((string) $npassword);
                //setcookie("password",$cnpassword,0,"/");
                $account_type=$settings->getValue("catchall_mail");
                if ($account_type==1) {
                    $db->update("nesote_liberyus_users");
                    $db->set("password=?",[$cnpassword]);
                    $db->where("username=? and password=?",[$uname,$password]);
                    $result=$db->query();
                    $db->update("nesote_email_usersettings");
                    $db->set("server_password=?",[$server_password]);
                    $db->where("userid=?",[$uid]);
                    $result=$db->query();
                    header("location:".$this->url("message/success/1093/1"));
                    exit(0);
                } elseif ($account_type==0) {
                    //						
                    $automatic_account_creation=$settings->getValue("automatic_account_creation");
                    if($automatic_account_creation==1)// for automatic account creation
						{
							// api calling

							
                        $controlpanel=$settings->getValue("controlpanel");
							if ($controlpanel==1) {
                            $this->cpanelaction(2,$uname,$npassword);
                            // 2 for change password
                        } elseif ($controlpanel==2) {
                            $this->pleskaction(2,$uname,$npassword);
                            // 2 for change password
                        }

							
							$db->update("nesote_liberyus_users");
							$db->set("password=?",[$cnpassword]);
							$db->where("username=? and password=?",[$uname,$password]);//echo $update->getQuery();exit;
							$result=$db->query();
							
							$db->update("nesote_email_usersettings");
							$db->set("server_password=?",[$server_password]);
							$db->where("userid=? ",[$uid]);//echo $update->getQuery();exit;
							$result=$db->query();

							header("location:".$this->url("message/success/1093/1"));
							exit(0);
						}
						else //manually
						{
							
							$db->update("nesote_liberyus_users");
							$db->set("password=?",[$cnpassword]);
							$db->where("username=? and password=?",[$uname,$password]);
							$result=$db->query();
							
                        $db->update("nesote_email_usersettings");
							$db->set("server_password=?",[$server_password]);
							$db->where("userid=? ",[$uid]);//echo $update->getQuery();exit;
							$result=$db->query();
							header("location:".$this->url("message/success/1093/1"));
							exit(0);
						}
                }

				if($flag==0)
				{
					header("Location:".$this->url("user/changepassword/$uid/$msg"));
					exit(0);
				}
				//$this->setValue("msg",$msg);
			}

		}
		else
		{
			header("location:".$this->url("index/index"));
			exit(0);
		}

	}

	function deleteuserAction()
	{

		if($this->validuser())
		{
			$id=$this->getParam(1);

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 12) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }
			$string=$this->getParam(1);
			$str=substr((string) $string,0,-1);

			if($str === '')
			{
				header("Location:".$this->url("message/error/1602"));
				exit(0);
			}
			$strr=explode(",",$str);$flag=0;
			$cnt=count($strr);
			
			
            $db=new NesoteDALController();
			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			
		
			$account_type=$settings->getValue("catchall_mail");
			for($i=0;$i<$cnt;$i++)
			{
			$id=$strr[$i];
			if($account_type==1)//for catch all
			{
				
				$db->delete("nesote_liberyus_users");
				$db->where("id=?",[$id]);
				$result=$db->query();
			}
			else// for individual
			{
				
				$automatic_account_creation=$settings->getValue("automatic_account_creation");
				if($automatic_account_creation==1)//for automatic account creation
				{
					//---api calling------

					
					$controlpanel=$settings->getValue("controlpanel");

					
					$db->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
					$db->fields("a.username,b.server_password");
					$db->where("a.id=? and a.id=b.userid",[$id]);
					$result1=$db->query();
					$row1=$db->fetchRow($result1);
					$username=$row1[0];
					$password=base64_decode((string) $row1[1]);

					if ($controlpanel==1) {
                        $this->cpanelaction(3,$username,$password);
                        //3 for account deletion
                    } elseif ($controlpanel==2) {
                        $this->pleskaction(3,$username,$password);
                        //3 for account deletion
                    }


					
					$db->delete("nesote_liberyus_users");
					$db->where("id=?",[$id]);
					$result=$db->query();
				}
				else//for manully account creation
				{
					
					$db->delete("nesote_liberyus_users");
					$db->where("id=?",[$id]);
					$result=$db->query();
				}

			}

			
			$db->delete("nesote_liberyus_users");
			$db->where("id=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_contacts");
			$db->where("addedby=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_contactgroup");
			$db->where("userid=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_emailfilters");
			$db->where("userid=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_usersettings");
			$db->where("userid=?",[$id]);
			$result=$db->query();


			
			$db->delete("nesote_email_whitelist_mail");
			$db->where("clientid=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_whitelist_server");
			$db->where("clientid=?",[$id]);
			$result=$db->query();



			
			$db->delete("nesote_image_display");
			$db->where("userid=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_blacklist_mail");
			$db->where("clientid=?",[$id]);
			$result=$db->query();

			
			$db->delete("nesote_email_blacklist_server");
			$db->where("clientid=?",[$id]);
			$result=$db->query();

			}

			header("Location:".$this->url("message/success/1077/1"));
			exit(0);
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}



	function mailtrackAction()
	{


		if($this->validuser())
		{
			
			$id=$this->getParam(1);
			$this->setValue("uid",$id);
			$flag=$this->getParam(2);
			$this->setValue("flag",$flag);

			$username=$this->getusername($id);
			$tablenumber=$this->tableid($username);
			if ($flag==1) {
                $status='Inbox';
                $table='nesote_email_inbox_'.$tablenumber;
            } elseif ($flag==2) {
                $status='Sent';
                $table='nesote_email_sent_'.$tablenumber;
            } elseif ($flag==3) {
                $status='Draft';
                $table='nesote_email_draft_'.$tablenumber;
            } elseif ($flag==4) {
                $status='Spam';
                $table='nesote_email_spam_'.$tablenumber;
            } elseif ($flag==5) {
                $status='Trash';
                $table='nesote_email_trash_'.$tablenumber;
            }

			$i=0;$maildetailfrom="";$maildetailto="";$maildetailcc="";$maildetail=[];$f=0;$t=0;$c=0;



			$db=new NesoteDALController();
			
			if ($flag==3) {
                $tot=$db->total("$table","userid=? and just_insert=?",[$id,0]);
            } else {
                $tot=$db->total("$table","userid=?",[$id]);
            }
			
		/*	$db->select("$table");
			$db->fields("*");
			if($flag==3)
			$db->where("userid=? and just_insert=?",array($id,0));
			else
			$db->where("userid=?",array($id));
			$db->order("time desc");
			$result2=$db->query();
			$tot=$db->numRows($result2);*/

			$perpagesize=50;
			$currentpage=1;
			//if($this->getParam(2))
			//$currentpage=$this->getParam(2);

			if (isset($_POST['pagenumber'])&&trim((string) $_POST['pagenumber']) !== "") {
                $currentpage=$_POST['pagenumber'];
            }
			$paging= new Paging();
			$out=$paging->page($tot,$perpagesize,"page",1,1,1,"","","",$_POST);
			$this->setValue("pagingtop",$out);

			
			$db->select("$table");
			$db->fields("*");
			if ($flag==3) {
                $db->where("userid=? and just_insert=?",[$id,0]);
            } else {
                $db->where("userid=?",[$id]);
            }
			$db->order("time desc");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result2=$db->query();
			//$num=$db->numRows($result2);

			//			while($id1=$db->fetchRow($result2))
			//			{
			//				$from=explode(",",$id1[2]);
			//				if(count($from)==0)
			//				$maildetail[$i][0]=$id1[2];
			//				else
			//				{
			//					for($k=0;$k<count($from);$k++)
			//					{
			//						$maildetailfrom.=$from[$k]."<br>";$f++;
			//					}
			//				}
			//
			//				$to=explode(",",$id1[3]);
			//				if(count($to)==0)
			//				$maildetailto[$i][1]=$id1[3];
			//				else
			//				{
			//					for($k=0;$k<count($to);$k++)
			//					{
			//						$maildetailto.=$to[$k]."<br>";$t++;
			//					}
			//				}
			//
			//				$cc=explode(",",$id1[4]);
			//
			//				if(count($cc)==0)
			//				$maildetail[$i][2]=$id1[4];
			//				else
			//				{
			//					for($k=0;$k<count($cc);$k++)
			//					{
			//						$maildetailcc.=$cc[$k]."<br>";$c++;
			//					}
			//				}
			//
			//
			//				$maildetail[$i][0]=$maildetailfrom;
			//				$maildetail[$i][1]=$maildetailto;
			//				$maildetail[$i][2]=$maildetailcc;
			//				$i++;
			//			}
			//
			//
			//			$p=$i;


			//		{loopstart:listings:100}
			//					<tr {if($listings % 2==1 )}  bgcolor="#E8E8E8"{endif}  bgcolor="#ffffff"  height="35" align="left" class="detail_tr">
			//						<td class="detail_td" width="15%">{if($listings[2]!="")}{$listings[2]} {else}&nbsp;{endif}</td>
			//						<td class="border_bottom" width="20%">{if($listings[3]!="")}{$listings[3]}{else}&nbsp;-&nbsp;{endif}</td>
			//						<td class="border_bottom" width="20%">{if($listings[4]!="")}{$listings[4]}{else}&nbsp;-&nbsp;{endif}</td>
			//						<td class="border_bottom" width="25%">{if($listings[6]!="")} {cfn:getsubject($listings[6])} {else}&nbsp;-&nbsp;{endif} </td>
			//						<td class="border_bottom" width="10%"> {fn:date("j/M/Y,g:i a",$listings[8])} </td>
			//						<td class="border_bottomright" width="10%"> <a href="javascript:confirmation({$listings[0]})" class="action" title="Delete Mail">Delete</a></td>
			//					  </tr>
			//					{loopend:listings}
			//print_r($maildetail);exit;
			$this->setValue("num",$tot);
			$this->setLoopValue("listings",$result2->getResult());
			//$this->setLoopValue("maildetails",$maildetail);
			$this->setValue("status",$status);



		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}
	function getsubject($subject)
	{
		return htmlentities((string) $subject,0,"UTF-8");
	}

	function getaddressformat($address)
	{

		$address=htmlspecialchars((string) $address);
		$count=strlen($address);$count--;
		$p=substr($address,$count,1);
		if ($p !== ",") {
            $address .= ",";
        }

		$str=explode(",",$address);
		$cont=count($str);
		return $cont > 2 ? $str[0].".........." : $str[0];
	}

	function getaddress($address)
	{


		$address=htmlspecialchars((string) $address);
		$count=strlen($address);$count--;
		$p=substr($address,$count,1);
		if ($p !== ",") {
            $address .= ",";
        }

		$str=explode(",",$address);
		$cont=count($str);
		return $cont > 2 ? $str[0]."..........".$hint : $str[0];

		//		$address=htmlspecialchars($address);
		//		$str=explode(",",$address);
		//		$cont=count($str);$cont--;
		//		$hint="";
		//
		//		$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: pink\">";
		//
		//		for($i=0;$i<$cont;$i++)
		//		{
		//			$hint.="<tr><td nowrap=\"nowrap\">$str[$i]</td></tr>";
		//		}
		//		$hint.="</table>";
		//		return $hint;
	}

	function viewdetailsAction()
	{
		$id=$this->getParam(1);$folderid=$this->getParam(2);$field=$this->getParam(3);
		$userid=$this->getParam(4);
		$username=$this->getusername($userid);
		$tablenumber=$this->tableid($username);
	$db1=new NesoteDALController();

		$hint="";

		$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: F1F1F1;padding-left: 20px;padding-right: 5px;\"><tr><td>&nbsp;</td></tr>";

		$i=0;
		if($folderid>=10)
		{
			if ($field==1) {
                $addrfield="b.to_list";
            } elseif ($field==2) {
                $addrfield="b.cc";
            }

		
			$db1->select(["a"=>"nesote_email_customfolder","b"=>"nesote_email_customfolder_mapping_$tablenumber"]);
			$db1->fields("$addrfield");
			$db1->where("a.id=b.folderid and b.folderid=? and b.id=?",[$folderid,$id]);
			$db1->order("b.time desc");
			$result=$db1->query();//echo $db1->getQuery();
			//$row=$db1->fetchRow($result);
			$num=$db1->numRows($result);
			$status=$row[0];

			$id1=$db1->fetchRow($result);
			$id1=htmlspecialchars((string) $id1[0]);
			$value=explode(",",$id1);
			if(count($value)==1)
			{
				//$maildetail[$i][0]=$id1[0];
				//$maildetailfrom=$id1;
				$hint.="<tr><td nowrap=\"nowrap\">$id1</td></tr>";
			}
			else {
                $counter = count($value);
                for($k=0;$k<$counter;$k++)
				{
					//$maildetailfrom.=$value[$k].",";//$f++;
					//$maildetailfrom=$maildetailfrom;
					$hint.="<tr><td nowrap=\"nowrap\">$value[$k]</td></tr>";
				}
            }

			//$address=$maildetailfrom;
			//$maildetail[$i][0]=$maildetailfrom;print_r($maildetail);

		}
		else
		{
			if ($field==1) {
                $addrfield="to_list";
            } elseif ($field==2) {
                $addrfield="cc";
            }
			$i=0;

			if ($folderid==1) {
                $table='nesote_email_inbox_'.$tablenumber;
            } elseif ($folderid==2) {
                $table='nesote_email_sent_'.$tablenumber;
            } elseif ($folderid==3) {
                $table='nesote_email_draft_'.$tablenumber;
            } elseif ($folderid==4) {
                $table='nesote_email_spam_'.$tablenumber;
            } elseif ($folderid==5) {
                $table='nesote_email_trash_'.$tablenumber;
            }

			
			$db1->select("$table");
			$db1->fields("$addrfield");
			if ($flag==3) {
                $db1->where("userid=? and just_insert=? and id=?",[$userid,0,$id]);
            } else {
                $db1->where("userid=? and id=?",[$userid,$id]);
            }
			$db1->order("time desc");
			$result2=$db1->query();//echo $db->getQuery();
			$id1=$db1->fetchRow($result2);

			$id1=htmlspecialchars((string) $id1[0]);
			$value=explode(",",$id1);
			if(count($value)==1)
			{
				$hint.="<tr><td nowrap=\"nowrap\">$id1</td></tr>";
			}
			else {
                $counter = count($value);
                for($k=0;$k<$counter;$k++)
				{
					
					$hint.="<tr><td nowrap=\"nowrap\">$value[$k]</td></tr>";
				}
            }



			
		}
		
		if ($field==1) {
            $field="TO";
        } elseif ($field==2) {
            $field="CC";
        }
		$this->setValue("field",$field);
		$hint.="<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr></table>";
		$this->setValue("address",$hint);


	}

	function statiticsAction()
	{
		if($this->validuser())
		{
			$id=$this->getParam(1);
			$this->setValue("uid",$id);
			$username=$this->getusername($id);
			$tablenumber=$this->tableid($username);
			
			$size=0;$inbox=0;$sent=0;$draft=0;$trash=0;$spam=0;

			$db=new NesoteDALController();
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result1=$db->query();
			$row1=$db->fetchRow($result1);
			if($row1!="")
			{
				$size+=$row1[12];

				$inbox=$row1[0];
			}

			
			$db->select("nesote_email_sent_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result2=$db->query();
			$row2=$db->fetchRow($result2);
			if($row2!="")
			{
				$size+=$row2[12];

				$sent=$row2[0];
			}


			
			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=? and just_insert=?",[$id,0]);
			$db->group("userid");
			$result3=$db->query();//echo $db->getQuery();
			$row3=$db->fetchRow($result3);
			if($row3!="")
			{
				$size+=$row3[12];

				$draft=$row3[0];
			}


			
			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result4=$db->query();
			$row4=$db->fetchRow($result4);
			if($row4!="")
			{
				$size+=$row4[12];

				$spam=$row4[0];
			}


			
			$db->select("nesote_email_trash_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result5=$db->query();
			$row5=$db->fetchRow($result5);
			if($row5!="")
			{
				$size+=$row5[12];

				$trash=$row5[0];
			}


			
			$db->select("nesote_email_customfolder a");
			$db->fields("a.id,a.name,b.from_list,b.to_list,b.cc,b.bcc,b.subject,b.body,b.time,b.status,b.readflag,b.starflag,sum(b.memorysize),count(b.id),b.folderid");
			$db->leftjoin("nesote_email_customfolder_mapping_$tablenumber b","a.id=b.folderid");
			$db->where("a.userid=? ",[$id]);
			$db->group("a.id");
			$result6=$db->query();
			$row6=$db->fetchRow($result6);
			$numfolder=$db->numRows($result6);
			$this->setLoopValue("custfolder",$result6->getResult());
			$this->setValue("numfolder",$numfolder);


		
			$db->select(["a"=>"nesote_email_customfolder","b"=>"nesote_email_customfolder_mapping_$tablenumber"]);
			$db->fields("a.id,a.name,b.from_list,b.to_list,b.cc,b.bcc,b.subject,b.body,b.time,b.status,b.readflag,b.starflag,sum(b.memorysize),count(b.id),b.folderid");
			$db->where("a.id=b.folderid and a.userid=?",[$id]);
			$db->order("a.name asc");
			$result7=$db->query();
			$row7=$db->fetchRow($result7);
			$size+=$row7[12];




			$size=round($size/1024,2);

			$this->setValue("inbox",$inbox);
			$this->setValue("sent",$sent);
			$this->setValue("draft",$draft);
			$this->setValue("spam",$spam);
			$this->setValue("trash",$trash);
			$this->setValue("size",$size);


		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function getsize($mailid,$folderid)
	{ 
		//$username=$_COOKIE['e_username'];
		//$tablenumber=$this->tableid($username);
		$size=0;
		$db=new NesoteDALController();
		$db->select("nesote_email_attachments");
		$db->fields("name");
		$db->where("mailid=? and folderid=?",[$mailid,$folderid]);
		$result=$db->query();
		while($row=$db->fetchRow($result))
		{
			$size+=filesize("attachments/$folderid/$mailid/$row[0]");
		}

		return $size;
	}

	function reservedmailsAction()
	{
		if($this->validuser())
		{
			$db=new NesoteDALController();

			$num=$db->total("nesote_email_reservedemail");
			$perpagesize=50;
			$currentpage=1;
			//if($this->getParam(2))
			//$currentpage=$this->getParam(2);

			if (isset($_POST['pagenumber'])&&trim((string) $_POST['pagenumber']) !== "") {
                $currentpage=$_POST['pagenumber'];
            }
			$paging= new Paging();
			$out=$paging->page($num,$perpagesize,"page",1,1,1,"","","",$_POST);
			$this->setValue("pagingtop",$out);


			
			$db->select("nesote_email_reservedemail");
			$db->fields("*");
			$db->order("name");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result=$db->query();
			$num=$db->numRows($result);


			$this->setValue("num",$num);
			$this->setLoopValue("reservedemails",$result->getResult());
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}
	function activateemailAction()
	{
		if($this->validuser())
		{
			$id=$this->getparam(1);

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 5) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }

			$db=new NesoteDALController();
			$db->select("nesote_email_reservedemail");
			$db->fields("status");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$status=$row[0];

			
			$db->update("nesote_email_reservedemail");
			if ($status==0) {
                $db->set("status=?",1);
            } else {
                $db->set("status=?",0);
            }
			$db->where("id=?",$id);
			$result=$db->query();



			header("Location:".$this->url("user/reservedmails"));
			exit(0);
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function deleteemailAction()
	{
		if($this->validuser())
		{
			$id=$this->getParam(1);

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 5) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }


			$db=new NesoteDALController();
			$db->delete("nesote_email_reservedemail");
			$db->where("id=?",[$id]);
			$result=$db->query();

			header("Location:".$this->url("message/success/1098/2"));
			exit(0);
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function newreservedmailAction()
	{
		if($this->validuser())
		{

			$db=new NesoteDALController();
            $db->select("nesote_email_settings");
            $db->fields("value");
            $db->where("name='min_usernamelength'");
            $result1=$db->query();
            $row1=$db->fetchRow($result1);
            $min_usernamelength=$row1[0];
            $this->setValue("min_usernamelength",$min_usernamelength);

            $msgs="Mail address  must be between {min_usernamelength} and 32 characters long.";
            $minlengtherr=str_replace('{min_usernamelength}',$min_usernamelength,$msgs);
            $this->setValue("minlengtherr",$minlengtherr);
			if($_POST !== [])
			{
				$emailacc=$_POST['email'];
				$emailacc1=$_POST['email'];
				
				$count=strlen((string) $emailacc1);
                if($count<$min_usernamelength)
                {
                    header("Location:".$this->url("message/error/1400"));
                    exit(0);

                }
				
			
				$emailacc .= $this->getextension();

				if(!$this->isEmail($emailacc))
				{
					header("Location:".$this->url("message/error/1085"));
					exit(0);
				}



				
				$no=$db->total("nesote_email_reservedemail","name=?",[$emailacc]);
				if($no==0)
				{
					$no=$db->total("nesote_liberyus_users","username=?",[$emailacc1]);
					if($no==0)
					{

					
						$db->insert("nesote_email_reservedemail");
						$db->fields("name,status");
						$db->values([$emailacc,1]);
						$result=$db->query();

						header("Location:".$this->url("message/success/1094/2"));
						exit(0);
					}
					else
					{
						header("Location:".$this->url("message/error/1128"));
						exit(0);
					}
				}

				else
				{
					header("Location:".$this->url("message/error/1000"));
					exit(0);
				}
			}

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function editmailAction()
	{
		if($this->validuser())
		{

			$id=$this->getParam(1);

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 5) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }
			$name="";
			$db=new NesoteDALController();
			$db->select("nesote_email_reservedemail");
			$db->fields("name");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$name=$row[0];//echo $name;
			$mail=explode("@",(string) $name);
			$this->setValue("mail",$mail[0]);
			$this->setValue("id",$id);
			$this->setValue("oldmail",$name);
			
			
            $db->select("nesote_email_settings");
            $db->fields("value");
            $db->where("name='min_usernamelength'");
            $result1=$db->query();
            $row1=$db->fetchRow($result1);
            $min_usernamelength=$row1[0];
            $this->setValue("min_usernamelength",$min_usernamelength);

            $msgs="Mail address  must be between {min_usernamelength} and 32 characters long.";
            $minlengtherr=str_replace('{min_usernamelength}',$min_usernamelength,$msgs);
            $this->setValue("minlengtherr",$minlengtherr);

			if($_POST !== [])
			{


				$email=$_POST['oldmail'];$id=$_POST['id'];

				$emailacc=$_POST['email'];$emailacc1=$_POST['email'];
				
				$count=strlen((string) $emailacc1);
                if($count<$min_usernamelength)
                {
                    header("Location:".$this->url("message/error/1400"));
                    exit(0);

                }
				
				
				$emailacc .= $this->getextension();

				if(!$this->isEmail($emailacc))
				{
					header("Location:".$this->url("message/error/1085"));
					exit(0);
				}

				$no = $emailacc == $email ? 0 : $db->total("nesote_email_reservedemail","name=?",[$emailacc]);

				if($no==0)
				{
					$no=$db->total("nesote_liberyus_users","username=?",[$emailacc1]);
					if($no==0)
					{
						
						$db->update("nesote_email_reservedemail");
						$db->set("name=?",[$emailacc]);
						$db->where("id=?",[$id]);
						$result=$db->query();

						header("Location:".$this->url("message/success/1095/2"));
						exit(0);
					}
					else
					{
						header("Location:".$this->url("message/error/1128"));
						exit(0);
					}
				}

				else
				{
					header("Location:".$this->url("message/error/1000"));
					exit(0);
				}
			}

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function userleftpanelAction()
	{
		if($this->validuser())
		{
			$id=$this->getParam(1);
			$this->setValue("uid",$id);
			$username=$this->getusername($id);
			$tablenumber=$this->tableid($username);
			$db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?","portal_status");
			$rs=$db->query();
			$rslt=$db->fetchRow($rs);
			$this->setValue("portal_status",$rslt[0]);

			$size=0;$inbox=0;$sent=0;$draft=0;$trash=0;$spam=0;

			$db=new NesoteDALController();
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result1=$db->query();
			$row1=$db->fetchRow($result1);
			if($row1!="")
			{
				//$size+=$row1[12];

				$inbox=$row1[0];
			}
			
			$db->select("nesote_email_sent_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result2=$db->query();
			$row2=$db->fetchRow($result2);
			if($row2!="")
			{
				//$size+=$row2[12];

				$sent=$row2[0];
			}
			
			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=? and just_insert=?",[$id,0]);
			$db->group("userid");
			$result3=$db->query();//echo $db->getQuery();
			$row3=$db->fetchRow($result3);
			if($row3!="")
			{
				//$size+=$row3[12];

				$draft=$row3[0];
			}

			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result4=$db->query();
			$row4=$db->fetchRow($result4);
			if($row4!="")
			{
				//$size+=$row4[12];

				$spam=$row4[0];
			}

			$db->select("nesote_email_trash_$tablenumber");
			$db->fields("count(id),userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,sum(memorysize)");
			$db->where("userid=?",[$id]);
			$db->group("userid");
			$result5=$db->query();
			$row5=$db->fetchRow($result5);
			if($row5!="")
			{
				//$size+=$row5[13];

				$trash=$row5[0];
			}

			$db->select("nesote_email_customfolder a");
			$db->fields("a.id,a.name,b.from_list,b.to_list,b.cc,b.bcc,b.subject,b.body,b.time,b.status,b.readflag,b.starflag,sum(b.memorysize),count(b.id),count(b.folderid)");
			$db->leftjoin("nesote_email_customfolder_mapping_$tablenumber b","a.id=b.folderid");
			$db->where("a.userid=? ",[$id]);
			$db->group("a.id");
			$result6=$db->query();
			$row6=$db->fetchRow($result6);
			$numfolder=$db->numRows($result6);
			$this->setLoopValue("customfolders",$result6->getResult());
			$this->setValue("numfolder",$numfolder);

			//			$db1=new NesoteDALController();
			//			$db1->select(array("a"=>"nesote_email_customfolder","b"=>"nesote_email_customfolder_mapping"));
			//			$db1->fields("a.id,a.name,b.from_list,b.to_list,b.cc,b.bcc,b.subject,b.body,b.time,b.status,b.readflag,b.starflag,sum(b.memorysize),count(b.id),b.folderid");
			//			$db1->where("a.id=b.folderid and a.userid=?",array($id));
			//			$db1->order("a.name asc");
			//			$result7=$db1->query();//echo $db->getQuery();
			//			$row7=$db1->fetchRow($result7);
			//$size+=$row7[12];


			//$size=($size/1024);

			$this->setValue("inbox",$inbox);
			$this->setValue("sent",$sent);
			$this->setValue("draft",$draft);
			$this->setValue("spam",$spam);
			$this->setValue("trash",$trash);
			//$this->setValue("size",$size);

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function folderAction()
	{
		if($this->validuser())
		{
			$id=$this->getParam(1);
			$folderid=$this->getParam(2);
			$this->setValue("flag",$folderid);
			$username=$this->getusername($id);
            $tablenumber=$this->tableid($username);
            
			$db1=new NesoteDALController();
			$db1->select(["a"=>"nesote_email_customfolder","b"=>"nesote_email_customfolder_mapping_$tablenumber"]);
			$db1->fields("a.name,b.from_list,b.to_list,b.cc,b.subject,b.time,b.id");
			$db1->where("a.id=b.folderid and b.folderid=?",[$folderid]);
			$db1->order("b.time desc");
			$result=$db1->query();
			$row=$db1->fetchRow($result);
			$tot=$db1->numRows($result);

			$perpagesize=50;
			$currentpage=1;
			//if($this->getParam(2))
			//$currentpage=$this->getParam(2);

			if (isset($_POST['pagenumber'])&&trim((string) $_POST['pagenumber']) !== "") {
                $currentpage=$_POST['pagenumber'];
            }
			$paging= new Paging();
			$out=$paging->page($tot,$perpagesize,"page",1,1,1,"","","",$_POST);
			$this->setValue("pagingtop",$out);

			
			$db1->select(["a"=>"nesote_email_customfolder","b"=>"nesote_email_customfolder_mapping_$tablenumber"]);
			$db1->fields("a.name,b.from_list,b.to_list,b.cc,b.subject,b.time,b.id");
			$db1->where("a.id=b.folderid and b.folderid=?",[$folderid]);
			$db1->order("b.time desc");
			$db1->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result=$db1->query();
			$row=$db1->fetchRow($result);
		//	echo $db1->getQuery();//$num=$db1->numRows($result);
			$status=$row[0];
			$this->setValue("num",$tot);


			$this->setValue("status",$status);
			$this->setLoopValue("listings",$result->getResult());
			$this->setValue("uid",$id);$this->setValue("folderid",$folderid);


		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function viewprofileAction()
	{
		if($this->validuser())
		{

			$id=$this->getParam(1);
			$db=new NesoteDALController();
			$db->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
			$db->fields("a.id,a.username,a.name,b.dateofbirth,a.joindate,b.country,b.time_zone,a.email,a.status");
			$db->where("a.id=? and a.id=b.userid",[$id]);
			$result=$db->query();//echo $db->getQuery();
			$id=$db->fetchRow($result);
			$this->setValue("uid",$id[0]);
			$this->setLoopValue("users",$result->getResult());

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function contactsAction()
	{
		if($this->validuser())
		{

			$flag=$this->getParam(1);// identify contacts/group 0 for contacts,. otherwise group;
			$userid=$this->getParam(2);
			if (!isset($userid)) {
                $userid="";
            }
			$this->setValue("flag",$flag);
			$this->setValue("userid",$userid);

			$db=new NesoteDALController();
			$db->select("nesote_email_contacts");
			$db->fields("*");
			if ($userid!="") {
                if ($flag!=0) {
                    $db->where("contactgroup=? and addedby=?",[$flag,$userid]);
                } else {
                    $db->where("addedby=?",[$userid]);
                }
            } elseif ($flag!=0) {
                $db->where("contactgroup=?",$flag);
            }
			$result=$db->query();//echo $db->getQuery();
			$num=$db->numRows($result);

			$this->setValue("num",$num);

			$perpagesize=50;
			$this->setValue("count",$perpagesize);
			$currentpage=1;
			//			if(isset($_POST['pagenumber'])&&trim($_POST['pagenumber'])!="")
			//			$currentpage=$_POST['pagenumber'];
			if ($this->getParam(3)) {
                $currentpage=$this->getParam(3);
            }
			$paging= new Paging();
			//$out=$paging->page($num,$perpagesize,"page",1,1,1,"","","",$_POST);
			$out=$paging->seoPage($num,$perpagesize, $currentpage, $this->url("user/contacts/0/$userid"),"paging",1,1,1,"top");
			$this->setValue("pagingtop",$out);

			
			$db->select("nesote_email_contacts");
			$db->fields("*");
			if ($userid!="") {
                if ($flag!=0) {
                    $db->where("contactgroup=? and addedby=?",[$flag,$userid]);
                } else {
                    $db->where("addedby=?",[$userid]);
                }
            } elseif ($flag!=0) {
                $db->where("contactgroup=?",$flag);
            }
			$db->order("mailid asc");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result1=$db->query();//echo $db->getQuery();
			$id=$db->fetchRow($result1);

			$this->setLoopValue("contacts",$result1->getResult());

			$extension=$this->getextension();
			$this->setValue("extension",$extension);

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}
	function deletecontactsACtion()
	{
		if($this->validuser())
		{
			$string=$this->getParam(1);
			$str=substr((string) $string,0,-1);

			if($str === '')
			{
				header("Location:".$this->url("message/error/1001"));
				exit(0);
			}
			$strr=explode(",",$str);$flag=0;
			if ($strr!="") {
                $cnt=count($strr);
                if ($cnt>1) {
                    for($i=0;$i<$cnt;$i++)
					{
						if ($strr[$cnt]<=2) {
                            $flag=1;
                        }break;
					}
                } elseif ($strr[0]<=2) {
                    $flag=1;
                }
            } elseif ($str<=2) {
                $flag=1;
            }

			if($flag==1)
			{
				//				if( $_SERVER['HTTP_HOST']=="www.libewebmaildemo.com" || $_SERVER['HTTP_HOST']=="libewebmaildemo.com" )
				//				{
				//					//if($id<=2)
				//					//{
				//					header("Location:".$this->url("message/error/1023"));
				//					exit(0);
				//					//}
				//				}

			}
			$db=new NesoteDALController();
			$db->delete("nesote_email_contacts");
			$db->where("id IN($str)");
			$result=$db->query();

			header("Location:".$this->url("message/success/1076/4"));
			exit(0);
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}

	function viewcontactdetailsAction()
	{
		if($this->validuser())
		{
			$flag="";
			$id=$this->getParam(1);
			$flag=$this->getParam(2);
			$userid=$this->getParam(3);
			if (!isset($userid)) {
                $userid="";
            }
			$this->setValue("flag",$flag);
			$this->setValue("id",$id);
			$this->setValue("userid",$userid);

			$db=new NesoteDALController();
			$db->select("nesote_email_contacts");
			$db->fields("*");
			if ($userid!="") {
                $db->where("id=? and addedby=?",[$id,$userid]);
            } else {
                $db->where("id=?",$id);
            }
			$db->order("contactgroup");
			$result=$db->query();
			$row=$db->fetchRow($result);
			$num=$db->numRows($result);
			$this->setValue("num",$num);
			$this->setValue("contactname",$row[1]);//echo $row[1];

			//			$select->select(array("a"=>"articles"));
			//			//$select->select("articles a");
			//			$select->fields("a.id,a.title,a.description,a.time,a.catid,a.image,c.name,a.authorid");
			//			$select->join(array("c"=>"category"),"a.catid=c.id");
			
			$db->select("nesote_email_contacts");
			$db->fields("*");
			//$db->where("mailid=? and contactgroup!=? and contactgroup!=?",array($row[1],0,$groupid)); //canot display the selected group
			if ($userid!="") {
                $db->where("mailid=? and addedby=? and contactgroup!=?",[$row[1],$userid,0]);
            } else {
                $db->where("mailid=? and contactgroup!=?",[$row[1],0]);
            }
			$result2=$db->query();//echo $db2->getQuery();
			//			$folderno=$db2->numRows($result1);//echo $row[1].$folderno;
			//			$this->setValue("folderno",$folderno);
			//			$this->setLoopValue("groupexist",$result1->getResult());
			$groupcount=$db->numRows($result2);
			$row2=$db->fetchRow($result2);
			$this->setValue("groupcount",$groupcount);//echo $groupcount;
			$this->setLoopValue("groups",$result2->getResult());


			//			$db1=new NesoteDALController();
			//			$db1->select(array("a"=>"nesote_email_contacts"));
			//			$db1->fields("b.name");
			//			$db1->join(array("b"=>"nesote_email_contactgroup"),"a.contactgroup=b.id");
			//			if($userid!="")
			//			$db1->where("b.id!=? and b.userid=?",array($row[3],$userid));
			//			else
			//			$db1->where("b.id!=?",array($row[3]));
			//			$db1->group("b.name");
			//			$result1=$db1->query();
			//			$groupcount=$db1->numRows($result1);
			//			$row1=$db1->fetchRow($result1);
			//			$this->setValue("groupcount",$groupcount);
			//			$this->setLoopValue("groups",$result1->getResult());

			$this->setLoopValue("contacts",$result->getResult());
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}

	function editcontactdetailsAction()
	{
		if($this->validuser())
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_contactgroup");
			$db->fields("id,name");
			$db->order("name asc");
			$result=$db->query();
			$row=$db->fetchRow($result);

			$this->setLoopValue("groups",$result->getResult());

			$id=$this->getParam(1);

			$this->setValue("id",$id);
			$userid=$this->getParam(2);
			if (!isset($userid)) {
                $userid="";
            }
			$this->setValue("userid",$userid);

			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 5) {
                header("Location:".$this->url("message/error/1023"));
                exit(0);
            }
			
			$db->select("nesote_email_contacts");
			$db->fields("*");
			if ($userid!="") {
                $db->where("id=? and addedby=?",[$id,$userid]);
            } else {
                $db->where("id=?",$id);
            }
			$result=$db->query();


			$this->setLoopValue("contacts",$result->getResult());
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}
	function deletemailAction()
	{
		if($this->validuser())
		{
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}
			$uid=$this->getParam(1);
			$flag=$this->getParam(2);
			$id=$this->getParam(3);
			$username=$this->getusername($uid);
			$tablenumber=$this->tableid($username);
			//echo $flag;
			$db=new NesoteDALController();
			if ($flag==1) {
                $db->select("nesote_email_inbox_$tablenumber");
            } elseif ($flag==2) {
                $db->select("nesote_email_sent_$tablenumber");
            } elseif ($flag==3) {
                $db->select("nesote_email_draft_$tablenumber");
            } elseif ($flag==4) {
                $db->select("nesote_email_spam_$tablenumber");
            } elseif ($flag==5) {
                $db->select("nesote_email_trash_$tablenumber");
            } elseif ($flag>=10) {
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db->fields("mail_references");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$reference=$db->fetchRow($result);
			$references=$reference[0];
			if ($flag==2) {
                $i=3;
            } elseif ($flag==3) {
                $i=2;
            } else {
                $i=$flag;
            }
			$match="<item><mailid>".$id."</mailid><folderid>".$i."</folderid></item>";
			$referencez=str_replace($match,"",$references);
			preg_match_all('/<item>(.+?)<\/item>/i',$referencez,$matches);
			$len=count($matches[1]);
			for($a=0;$a<$len;$a++)
			{
				preg_match('/<mailid>(.+?)<\/mailid>/i',$matches[1][$a],$mailid);
				preg_match('/<folderid>(.+?)<\/folderid>/i',$matches[1][$a],$folderid);
				
				if ($folderid[1]==1) {
                    $db->update("nesote_email_inbox_$tablenumber");
                } elseif ($folderid[1]==2) {
                    $db->update("nesote_email_draft_$tablenumber");
                } elseif ($folderid[1]==3) {
                    $db->update("nesote_email_sent_$tablenumber");
                } elseif ($folderid[1]==4) {
                    $db->update("nesote_email_spam_$tablenumber");
                } elseif ($folderid[1]==5) {
                    $db->update("nesote_email_trash_$tablenumber");
                } elseif ($folderid[1]>=10) {
                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->set("mail_references=?",$referencez);
				$db->where("id=?",[$mailid[1]]);
				$result1=$db->query();//echo $db1->getQuery();
			}
			
			if ($flag==1) {
                $db->delete("nesote_email_inbox_$tablenumber");
            } elseif ($flag==2) {
                $db->delete("nesote_email_sent_$tablenumber");
            } elseif ($flag==3) {
                $db->delete("nesote_email_draft_$tablenumber");
            } elseif ($flag==4) {
                $db->delete("nesote_email_spam_$tablenumber");
            } elseif ($flag==5) {
                $db->delete("nesote_email_trash_$tablenumber");
            } elseif ($flag>=10) {
                $db->delete("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db->where("id=?",[$id]);
			$result2=$db->query();
			
		    $db->select("nesote_email_attachments_$tablenumber");
			$db->fields("id,name");
			$db->where("folderid=? and mailid=? and userid=?",[$flag,$id,$uid]);
			$res=$db->query();
			while($result=$db->fetchRow($res))
				{
								unlink("attachments/".$flag."/".$tablenumber."/".$id."/".$result[1]);
								rmdir("attachments/".$flag."/".$tablenumber."/".$id);
								$db2->delete("nesote_email_attachments_$tablenumber");
								$db2->where("id=?",$result[0]);
								$db2->query();
				}
			
				
		  if($flag>=10)
           {
           header("Location:".$this->url("user/folder/$uid/$flag"));
                       exit(0);        
           }
           else
           {
           header("Location:".$this->url("user/mailtrack/$uid/$flag"));
                       exit(0);
           }

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function editcontactsAction()
	{
		$id=$this->getParam(1);
		//		if( $_SERVER['HTTP_HOST']=="www.libewebmaildemo.com" || $_SERVER['HTTP_HOST']=="libewebmaildemo.com" )
		//		{
		//			if($id<=5)
		//			{
		//				header("Location:".$this->url("message/error/1023"));
		//				exit(0);
		//			}
		//		}
		$userid=$this->getParam(2);
		if (!isset($userid)) {
            $userid="";
        }


		if($_POST !== [])
		{

			$mailid=$_POST['mailid'];
			$addedby=$this->getuserid($_POST['addedby']);
			$groupname=$_POST['groupname'];
			$fname=$_POST['fname'];
			$lname=$_POST['lname'];
			$dob2=$_POST['dob'];
			$title=$_POST['title'];
			$company=$_POST['company'];
			$phone=$_POST['phone'];
			$address=$_POST['address'];
			$website=$_POST['website'];

			$dob1=explode("/",(string) $dob2);
			$dob=mktime(0,0,0,$dob1[0],$dob1[1],$dob1[2]);


			$db=new NesoteDALController();
			$db->update("nesote_email_contacts");
			$db->set("mailid=?,addedby=?,contactgroup=?,firstname=?,lastname=?,date_of_birth=?,title=?,company=?,phone=?,address=?,website=?",[$mailid,$addedby,$groupname,$fname,$lname,$dob,$title,$company,$phone,$address,$website]);
			if ($userid!="") {
                $db->where("id=? and addedby=?",[$id,$userid]);
            } else {
                $db->where("id=?",$id);
            }
			$result1=$db->query();
			if ($userid!="") {
                header("Location:".$this->url("message/success/1075/4/$userid"));
            } else {
                header("Location:".$this->url("message/success/1075/4"));
            }
			exit(0);
		}
	}


	function groupsAction()
	{
		if($this->validuser())
		{

			$userid=$this->getParam(1);$tsr="";
			if (!isset($userid)) {
                $userid="";
            } else {
                $tsr="userid=$userid";
            }
			$this->setValue("userid",$userid);

			$db=new NesoteDALController();
            $num=$db->total("nesote_email_contactgroup","$tsr");
			$this->setValue("num",$num);

			$perpagesize=50;
			$this->setValue("count",$perpagesize);
			$currentpage=1;
			if ($this->getParam(2)) {
                $currentpage=$this->getParam(2);
            }
			//			if(isset($_POST['pagenumber'])&&trim($_POST['pagenumber'])!="")
			//			$currentpage=$_POST['pagenumber'];
			$paging= new Paging();
			$out=$paging->seoPage($num,$perpagesize, $currentpage, $this->url("user/groups/$userid"),"paging",1,1,1,"top");
			$this->setValue("pagingtop",$out);

			
			$db->select("nesote_email_contactgroup");
			$db->fields("*");
			if ($userid!="") {
                $db->where("userid=?",[$userid]);
            }
			$db->order("id asc");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result=$db->query();//echo $db->getQuery();
			$id=$db->fetchRow($result);

			$this->setLoopValue("groups",$result->getResult());

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}
	function editgroupAction()
	{
		if($this->validuser())
		{
			$id=$this->getParam(1);
			//			if( $_SERVER['HTTP_HOST']=="www.libewebmaildemo.com" || $_SERVER['HTTP_HOST']=="libewebmaildemo.com" )
			//			{
			//				if($id<=5)
			//				{
			//					header("Location:".$this->url("message/error/1023"));
			//					exit(0);
			//				}
			//			}
			$this->setValue("id",$id);
			$userid=$this->getParam(2);
			if (!isset($userid)) {
                $userid="";
            }
			$this->setValue("userid",$userid);

			$db=new NesoteDALController();
			$db->select("nesote_email_contactgroup");
			$db->fields("*");
			if ($userid!="") {
                $db->where("id=? and userid=?",[$id,$userid]);
            } else {
                $db->where("id=?",[$id]);
            }
			$result=$db->query();
			$row=$db->fetchRow($result);
			$this->setLoopValue("groups",$result->getResult());

			if($_POST !== [])
			{
				$groupname=$_POST['groupname'];$userid=$_POST['userid'];

				if($groupname!="")
				{
					if ($row[1]==$groupname) {
                        $groupname=$row[1];
                    } else
					{
						
						$db->select("nesote_email_contactgroup");
						$db->fields("*");
						$result=$db->query();
						while($row1=$db->fetchRow($result))
						{
							if($row1[1]==$groupname)
							{
								header("Location:".$this->url("message/error/1011"));
								exit(0);
							}

						}
					}
					
					$db->update("nesote_email_contactgroup");
					$db->set("name=?",[$groupname]);
					if ($userid!="") {
                        $db->where("id=? and userid=?",[$id,$userid]);
                    } else {
                        $db->where("id=?",[$id]);
                    }
					$db->query();//echo $db->getQuery();
					if ($userid!="") {
                        header("Location:".$this->url("message/success/1078/4/$userid"));
                    } else {
                        header("Location:".$this->url("message/success/1078/4/group"));
                    }//group for redirect to group
					exit(0);
				}
			}
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}


	function deletegroupsAction()
	{
		if($this->validuser())
		{

			$string=$this->getParam(1);

			$str=substr((string) $string,0,-1);

			if($str === '')
			{
				header("Location:".$this->url("message/error/1002"));
				exit(0);
			}
			$strr=explode(",",$str);$flag=0;
			if ($strr!="") {
                $cnt=count($strr);
                if ($cnt>1) {
                    for($i=0;$i<$cnt;$i++)
					{
						if ($strr[$cnt]<=2) {
                            $flag=1;
                        }break;
					}
                } elseif ($strr[0]<=2) {
                    $flag=1;
                }
            } elseif ($str<=2) {
                $flag=1;
            }

			if($flag==1)
			{
				//				if( $_SERVER['HTTP_HOST']=="www.libewebmaildemo.com" || $_SERVER['HTTP_HOST']=="libewebmaildemo.com" )
				//				{
				//					//if($id<=2)
				//					//{
				//					header("Location:".$this->url("message/error/1023"));
				//					exit(0);
				//					//}
				//				}

			}
			$db=new NesoteDALController();
			$db->delete("nesote_email_contactgroup");
			$db->where("id IN($str)");
			$result=$db->query();


			header("Location:".$this->url("message/success/1079/4/group"));//group for redirect to group;
			exit(0);


		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}
	function spammailsAction()
	{
		if($this->validuser())
		{
			$db=new NesoteDALController();
		
            $num=0;
		    for($i=1;$i<=100;$i++)
            $num+=$db->total("nesote_email_spam_$i");
			$this->setValue("num",$num);

			$perpagesize=50;
			$this->setValue("count",$perpagesize);
			$currentpage=1;if ($this->getParam(1)) {
                $currentpage=$this->getParam(1);
            }
			//			if(isset($_POST['pagenumber'])&&trim($_POST['pagenumber'])!="")
			//			$currentpage=$_POST['pagenumber'];
			$paging= new Paging();
			$out=$paging->seoPage($num,$perpagesize, $currentpage, $this->url("user/spammails"),"paging",1,1,1,"top");
			$this->setValue("pagingtop",$out);$mail=[];$j=0;

			 for($i=1;$i<=100;$i++)
			 {
				$db->select("nesote_email_spam_$i");
				$db->fields("*");
				$db->order("id asc");
				$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
				$result=$db->query();
				while($row=$db->fetchRow($result))
				{
				$mail[$j][0]=$row[0];
				$mail[$j][1]=$row[1];
				$mail[$j][2]=$row[2];
				$mail[$j][3]=$row[3];
				$mail[$j][4]=$row[4];
				$mail[$j][5]=$row[5];
				$mail[$j][6]=$row[6];
				$mail[$j][7]=$row[7];
				$mail[$j][8]=$row[8];
				$mail[$j][9]=$row[9];
				$mail[$j][10]=$row[10];
				$mail[$j][11]=$row[11];
				$mail[$j][12]=$row[12];
				$mail[$j][13]=$row[13];
				$mail[$j][14]=$row[14];
				$mail[$j][15]=$row[15];
				$mail[$j][16]=$row[16];
				$mail[$j][17]=$i;// table id
				$j++;
				}
			 }
			$this->setLoopValue("spammails",$mail);

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}
	function spamsettingsAction()
	{
		if($this->validuser())
		{
			$db=new NesoteDALController();
		    $num=$db->total("nesote_email_spam_settings");
			$this->setValue("num",$num);

			$perpagesize=50;
			$this->setValue("count",$perpagesize);
			$currentpage=1;
			if ($this->getParam(1)) {
                $currentpage=$this->getParam(1);
            }
			//			if(isset($_POST['pagenumber'])&&trim($_POST['pagenumber'])!="")
			//			$currentpage=$_POST['pagenumber'];
			$paging= new Paging();
			$out=$paging->seoPage($num,$perpagesize, $currentpage, $this->url("user/spamsettings"),"paging",1,1,1,"top");
			$this->setValue("pagingtopmail",$out);

			
			$db->select("nesote_email_spam_settings");
			$db->fields("*");
			$db->order("id desc");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result=$db->query();
			$id=$db->fetchRow($result);

			$this->setLoopValue("spams",$result->getResult());

			$db->select("nesote_email_spamserver_settings");
			$db->fields("*");
			$result1=$db->query();
			$num1=$db->numRows($result1);

			$this->setValue("num1",$num1);

			$perpagesize=50;
			$this->setValue("count1",$perpagesize);
			$currentpage=1;if ($this->getParam(1)) {
                $currentpage=$this->getParam(1);
            }
			//			if(isset($_POST['pagenumber'])&&trim($_POST['pagenumber'])!="")
			//			$currentpage=$_POST['pagenumber'];
			$paging= new Paging();
			$out=$paging->seoPage($num1,$perpagesize, $currentpage, $this->url("user/spamsettings"),"paging",1,1,1,"top");
			$this->setValue("pagingtopserver",$out);

			
			$db->select("nesote_email_spamserver_settings");
			$db->fields("*");
			$db->order("id asc");
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
			$result2=$db->query();
			$id=$db->fetchRow($result2);

			$this->setLoopValue("spamserver",$result2->getResult());

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}


	function newspamAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$flag1=$this->getParam(1);//echo $flag1;
		$this->setValue("flag",$flag1);
		if($_POST !== [])
		{

			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}

			$flag=$_POST['flag'];//echo $flag;
$db=new NesoteDALController();
			if ($flag==1) {
                $from=$_POST['from_id'];
                $fromflag = $from == "" ? 0 : $_POST['fromflag'];
                $subject=$_POST['subject'];
                $subjectflag = $subject == "" ? 0 : $_POST['subjectflag'];
                $body=$_POST['body'];
                $bodyflag = $body == "" ? 0 : $_POST['bodyflag'];
                if (($from=="")&&($subject=="")&&($body=="")) {
                    header("Location:".$this->url("message/error/1003"));
                    exit(0);
                } elseif (!$this->existeditvalue($from,$subject,$body)) {
                    $db->insert("nesote_email_spam_settings");
                    $db->fields("from_id,subject,body,fromflag,subjectflag,bodyflag");
                    $db->values([$from,$subject,$body,$fromflag,$subjectflag,$bodyflag]);
                    $result=$db->query();
                    header("Location:".$this->url("message/success/1096/6"));
                    exit(0);
                } else
					{
						header("Location:".$this->url("message/error/1008"));
						exit(0);
					}
            } elseif ($flag==2) {
                $servername=$_POST['servername'];
                //echo $servername;
                if ($servername=="") {
                    header("Location:".$this->url("message/error/1005"));
                    exit(0);
                } elseif (!$this->existeditservername($servername)) {
                    $isdomain=$this->checkDomain($servername);
                    //echo $isdomain;
                    $domain=explode("/",(string) $isdomain);
                    //print_r($domain);
                    if($domain[1]=="false")
						{

							$msg=$domain[0];
							if ($msg === "e") {
                            $ext=$domain[2];
                            header("Location:".$this->url("message/error/1080/$ext"));
                            exit(0);
                        } elseif ($msg === "b") {
                            header("Location:".$this->url("message/error/1081"));
                            exit(0);
                        } elseif ($msg === "s") {
                            header("Location:".$this->url("message/error/1082"));
                            exit(0);
                        } elseif ($msg === "sl") {
                            header("Location:".$this->url("message/error/1083"));
                            exit(0);
                        }
						}
						else
						{
							
							$db->insert("nesote_email_spamserver_settings");
							$db->fields("name");
							$db->values([$servername]);
							$result=$db->query();

							header("Location:".$this->url("message/success/1097/6"));
							exit(0);
						}
                } else
					{
						header("Location:".$this->url("message/error/1006"));
						exit(0);
					}
            }

		}

	}


	function deletespamsAction()
	{
		if($this->validuser())
		{
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}

			$string=$this->getParam(1);

			$str=substr((string) $string,0,-1);

			if($str === '')
			{
				header("Location:".$this->url("message/error/1004"));
				exit(0);
			}
			$strr=explode(",",$str);$flag=0;
			if ($strr!="") {
                $cnt=count($strr);
                if ($cnt>1) {
                    for($i=0;$i<$cnt;$i++)
					{
						if ($strr[$cnt]<=2) {
                            $flag=1;
                        }break;
					}
                } elseif ($strr[0]<=2) {
                    $flag=1;
                }
            } elseif ($str<=2) {
                $flag=1;
            }

			if ($flag == 1 && ($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com")) {
                //if($id<=2)
                //{
                header("Location:".$this->url("message/error/1023"));
                exit(0);
                //}
            }
			$db=new NesoteDALController();
			$db->delete("nesote_email_spam_settings");
			$db->where("id IN($str)");
			$result=$db->query();

			header("Location:".$this->url("message/success/1089/6"));
			exit(0);


		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}


	function editspamsettingsAction()
	{

		if($this->validuser())
		{
			$id=$this->getParam(1);
			//						if( $_SERVER['HTTP_HOST']=="www.libewebmaildemo.com" || $_SERVER['HTTP_HOST']=="libewebmaildemo.com" )
			//						{
			//							if($id<=5)
			//							{
			//								header("Location:".$this->url("message/error/1023"));
			//								exit(0);
			//							}
			//						}
			$this->setValue("id",$id);

			$db=new NesoteDALController();
			$db->select("nesote_email_spam_settings");
			$db->fields("*");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$this->setLoopValue("spams",$result->getResult());


			if($_POST !== [])
			{
				if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
				{

					header("Location:".$this->url("message/error/1023"));
					exit(0);
				}

				$id=$_POST['id'];

				$from_id=$_POST['from_id'];
				$fromflag = $from_id == "" ? 0 : $_POST['fromflag'];

				$subject=$_POST['subject'];
				$subjectflag = $subject == "" ? 0 : $_POST['subjectflag'];

				$body=$_POST['body'];
				$bodyflag = $body == "" ? 0 : $_POST['bodyflag'];


				if (($from_id=="")&& ($subject=="")&&($body=="")) {
                    header("Location:".$this->url("message/error/1003"));
                    exit(0);
                } elseif ($this->existvalue($from_id,$subject,$body,$id)) {
                    $db->update("nesote_email_spam_settings");
                    $db->set("from_id=?,subject=?,body=?,fromflag=?,subjectflag=?,bodyflag=?",[$from_id,$subject,$body,$fromflag,$subjectflag,$bodyflag]);
                    $db->where("id=?",$id);
                    $result1=$db->query();
                    //ECHO $db->getQuery();
                    header("Location:".$this->url("message/success/1090/6"));
                    exit(0);
                } elseif (!$this->existeditvalue($from_id,$subject,$body)) {
                    $db->update("nesote_email_spam_settings");
                    $db->set("from_id=?,subject=?,body=?",[$from_id,$subject,$body]);
                    $db->where("id=?",$id);
                    $result1=$db->query();
                    header("Location:".$this->url("message/success/1090/6"));
                    exit(0);
                } else
						{
							header("Location:".$this->url("message/error/1008"));
							exit(0);
						}
			}

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}

	function newspamserverAction()
	{
		if($this->validuser())
		{

			if($_POST !== [])
			{
				if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
				{

					header("Location:".$this->url("message/error/1023"));
					exit(0);
				}

				$servername=$_POST['servername'];
				if ($servername=="") {
                    header("Location:".$this->url("message/error/1005"));
                    exit(0);
                } elseif (!$this->existeditservername($servername)) {
                    $db=new NesoteDALController();
                    $db->insert("nesote_email_spamserver_settings");
                    $db->fields("name");
                    $db->values([$servername]);
                    $result=$db->query();
                    header("Location:".$this->url("user/spamserversettings"));
                    exit(0);
                } else
					{
						header("Location:".$this->url("message/error/1006"));
						exit(0);
					}

			}
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}
	function spamserversettingsAction()
	{
		if($this->validuser())
		{
		$db1=new NesoteDALController();
		
        $num1=$db1->total("nesote_email_spamserver_settings");
		$this->setValue("num1",$num1);

		$perpagesize=50;
		$this->setValue("count1",$perpagesize);
		$currentpage=1;if ($this->getParam(1)) {
            $currentpage=$this->getParam(1);
        }
		//			if(isset($_POST['pagenumber'])&&trim($_POST['pagenumber'])!="")
		//			$currentpage=$_POST['pagenumber'];
		$paging= new Paging();
		$out=$paging->seoPage($num1,$perpagesize, $currentpage, $this->url("user/spamsettings"),"paging",1,1,1,"top");
		$this->setValue("pagingtopserver",$out);

		
		$db1->select("nesote_email_spamserver_settings");
		$db1->fields("*");
		$db1->order("id asc");
		$db1->limit(($currentpage-1)*$perpagesize, $perpagesize);
		$result2=$db1->query();
		$id=$db1->fetchRow($result2);

		$this->setLoopValue("spamserver",$result2->getResult());

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
	}

	function deletespamserverACtion()
	{
		if($this->validuser())
		{

			$string=$this->getParam(1);

			$str=substr((string) $string,0,-1);


			if($str === '')
			{
				header("Location:".$this->url("message/error/1007"));
				exit(0);
			}
			$strr=explode(",",$str);$flag=0;
			if ($strr!="") {
                $cnt=count($strr);
                if ($cnt>1) {
                    for($i=0;$i<$cnt;$i++)
					{
						if ($strr[$cnt]<=2) {
                            $flag=1;
                        }break;
					}
                } elseif ($strr[0]<=2) {
                    $flag=1;
                }
            } elseif ($str<=2) {
                $flag=1;
            }

			if ($flag == 1 && ($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com")) {
                //if($id<=2)
                //{
                header("Location:".$this->url("message/error/1023"));
                exit(0);
                //}
            }


			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}

			$db=new NesoteDALController();
			$db->delete("nesote_email_spamserver_settings");
			$db->where("id IN($str)");
			$result=$db->query();

			header("Location:".$this->url("message/success/1091/6"));
			exit(0);


		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}



	function editspamserversettingsAction()
	{

		if($this->validuser())
		{
			$id=$this->getParam(1);
			/*if(($server=="www.libewebportal.com") || ($server=="libewebportal.com")|| ($server=="www.libewebmaildemo.com") || ($server=="libewebmaildemo.com"))
			{
				if($id<=5)
				{
					header("Location:".$this->url("message/error/1023"));
					exit(0);
				}
			}*/
			$this->setValue("id",$id);

			$db=new NesoteDALController();
			$db->select("nesote_email_spamserver_settings");
			$db->fields("*");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$this->setLoopValue("spamserver",$result->getResult());


			if($_POST !== [])
			{
				if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
				{

					header("Location:".$this->url("message/error/1023"));
					exit(0);
				}

				$id=$_POST['id'];
				$servername=$_POST['servername'];


				if ($servername=="") {
                    header("Location:".$this->url("message/error/1005"));
                    exit(0);
                } elseif ($this->existservername($servername,$id)) {
                    $isdomain=$this->checkDomain($servername);
                    //echo $isdomain;
                    $domain=explode("/",(string) $isdomain);
                    //print_r($domain);
                    if($domain[1]=="false")
						{

							$msg=$domain[0];
							if ($msg === "e") {
                            $ext=$domain[2];
                            header("Location:".$this->url("message/error/1080/$ext"));
                            exit(0);
                        } elseif ($msg === "b") {
                            header("Location:".$this->url("message/error/1081"));
                            exit(0);
                        } elseif ($msg === "s") {
                            header("Location:".$this->url("message/error/1082"));
                            exit(0);
                        } elseif ($msg === "sl") {
                            header("Location:".$this->url("message/error/1083"));
                            exit(0);
                        }
						}
						else
						{

							
							$db->update("nesote_email_spamserver_settings");
							$db->set("name=?",[$servername]);
							$db->where("id=?",$id);
							$result1=$db->query();

							header("Location:".$this->url("message/success/1092/6"));
							exit(0);
						}
                } elseif (!$this->existeditservername($servername)) {
                    $isdomain=$this->checkDomain($servername);
                    //echo $isdomain;
                    $domain=explode("/",(string) $isdomain);
                    //print_r($domain);
                    if($domain[1]=="false")
							{

								$msg=$domain[0];
								if ($msg === "e") {
                            $ext=$domain[2];
                            header("Location:".$this->url("message/error/1080/$ext"));
                            exit(0);
                        } elseif ($msg === "b") {
                            header("Location:".$this->url("message/error/1081"));
                            exit(0);
                        } elseif ($msg === "s") {
                            header("Location:".$this->url("message/error/1082"));
                            exit(0);
                        } elseif ($msg === "sl") {
                            header("Location:".$this->url("message/error/1083"));
                            exit(0);
                        }
							}
							else
							{

								
								$db->update("nesote_email_spamserver_settings");
								$db->set("name=?",[$servername]);
								$db->where("id=?",$id);
								$result1=$db->query();

								header("Location:".$this->url("message/success/1092/6"));
								exit(0);
							}
                } else
						{
							header("Location:".$this->url("message/error/1006"));
							exit(0);

						}
			}

		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}

	function deletespammailsACtion()
	{
		if($this->validuser())
		{
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}

			$string=$this->getParam(1);
			$str=substr((string) $string,0,-1);

			if($str === '')
			{
				header("Location:".$this->url("message/error/1004"));
				exit(0);
			}
			
			
			$strr=explode(",",$str);
			$db=new NesoteDALController();$cnt=count($strr);
			for($i=0;$i<$cnt;$i++)
					{
					$str1=explode("::",$strr[$i]);
					$db->delete("nesote_email_spam_$str1[1]");
					$db->where("id IN($str1[0])");
					$result=$db->query();//echo $db->getQuery();exit;
				    }
			header("Location:".$this->url("user/spammails"));
			exit(0);
			
			/*
			$strr=explode(",",$str);$flag=0;
			if($strr!="")
			{
				$cnt=count($strr);
				if($cnt>1)
				{
					for($i=0;$i<$cnt;$i++)
					{
						if($strr[$cnt]<=2)
						$flag=1;break;
					}

				}

				else if($strr[0]<=2)
				$flag=1;

			}
			else if($str<=2)
			$flag=1;

*/
		}
		else
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

	}

	function searchuserAction()
	{

		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$select=$this->getParam(1);
		$execute=$this->getParam(2);// 0 for time period search 1 for users name depend searching
		$db=new NesoteDALController();
		
		    $db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?","portal_status");
			$rs=$db->query();
			$rslt=$db->fetchRow($rs);
			$this->setValue("portal_status",$rslt[0]);
			
		if ($execute==0) {
            $this->setValue("time",$select);
            if ($select=='today') {
                $flag=86400;
            }
            if ($select=='lastweek') {
                $flag=604800;
            }
            if ($select=='lastmonth') {
                $flag=2678400;
            }
            if ($select=='lastyear') {
                $flag=31536000;
            }
            $time=time()-$flag;
            if ($flag==0) {
                $tot=$db->total("nesote_liberyus_users");
            } elseif ($flag==86400) {
                $tot=$db->total("nesote_liberyus_users",$time);
            } elseif ($flag==604800) {
                $tot=$db->total("nesote_liberyus_users",$time);
            } elseif ($flag==2678400) {
                $tot=$db->total("nesote_liberyus_users",$time);
            } elseif ($flag === 31536000) {
                $tot=$db->total("nesote_liberyus_users",$time);
            }
            $perpagesize=50;
            $this->setValue("count",$perpagesize);
            $currentpage=1;
            if (isset($_POST['pagenumber'])&&trim((string) $_POST['pagenumber']) !== "") {
                $currentpage=$_POST['pagenumber'];
            }
            $paging= new Paging();
            $out=$paging->page($tot,$perpagesize,"page",1,1,1,"","","",$_POST);
            $this->setValue("pagingtop",$out);
            //			if($this->getParam(2))
            //			$currentpage=$this->getParam(2);
            //			$paging= new Paging();
            //			$out=$paging->seoPage($tot,$perpagesize,"paging",1,1,1,"top",$currentpage,$this->url("user/searchuser/0"));
            //			$this->setValue("pagingtop",$out);
            $db->select("nesote_liberyus_users");
            $db->fields("id,name,status,username");
            if ($flag==86400) {
                $db->where("joindate>=?",[$time]);
            } elseif ($flag==604800) {
                $db->where("joindate>=?",[$time]);
            } elseif ($flag==2678400) {
                $db->where("joindate>=?",[$time]);
            } elseif ($flag === 31536000) {
                $db->where("joindate>=?",[$time]);
            }
            $db->limit(($currentpage-1)*$perpagesize, $perpagesize);
            $db->order("joindate desc");
            $result=$db->query();
            $row=$db->numRows($result);
            $tot=$row;
            if ($row==0) {
                $this->setValue("empty","-No Users found-");
            } else {
                $this->setValue("empty","");
            }
            $this->setLoopValue("users",$result->getResult());
            $this->setRedirect("user/searchuser");
        } elseif ($execute==1) {
            $this->setValue("searchuser",$select);
            $timeperiod=$this->getParam(3);
            $flag="";
            $length=strlen((string) $select);
            $at =strripos((string) $select,"@");
            $ext =substr((string) $select,$at,$length);
            //echo $ext;
            if (trim($ext) === trim((string) $this->getextension())) {
                $select=str_ireplace($ext,"",$select);
            }
            $msg="";
            $returnvalue="";
            $false="false";
            if ($timeperiod=='today') {
                $flag=time()-86400;
            }
            if ($timeperiod=='lastweek') {
                $flag=time()-604800;
            }
            if ($timeperiod=='lastmonth') {
                $flag=time()-2678400;
            }
            if ($timeperiod=='lastyear') {
                $flag=time()-31536000;
            }
            $perpagesize=50;
            $currentpage=1;
            $db->select("nesote_liberyus_users");
            $db->fields("*");
            if ($flag!="") {
                $db->where("joindate>=? and (username like '%$select%' or name like '%$select%' )",[$flag]);
            } else {
                $db->where("username like '%$select%' or name like '%$select%'");
            }
            $result=$db->query();
            //echo $db->getQuery();
            $tot=$db->numRows($result);
            if (isset($_POST['pagenumber'])&&trim((string) $_POST['pagenumber']) !== "") {
                $currentpage=$_POST['pagenumber'];
            }
            $paging= new Paging();
            $out=$paging->page($tot,$perpagesize,"page",1,1,1,"","","",$_POST);
            $this->setValue("pagingtop",$out);
            //$paging= new Paging();
            //$out=$paging->seoPage($tot,$perpagesize,"paging",1,1,1,"top",$currentpage,$this->url("user/searchuser/0"));
            //$this->setValue("pagingtop",$out);
            $db->select("nesote_liberyus_users");
            $db->fields("id,name,status,username");
            if ($flag!="") {
                $db->where("joindate>=? and (username like '%$select%' or name like '%$select%' )",[$flag]);
            } else {
                $db->where("username like '%$select%' or name like '%$select%' ");
            }
            $db->limit(($currentpage-1)*$perpagesize, $perpagesize);
            $db->order("name asc");
            $result=$db->query();
            $row=$db->numRows($result);
            $tot=$row;
            if ($row==0) {
                $this->setValue("empty","-No Users found-");
            } else {
                $this->setValue("empty","");
            }
            $this->setLoopValue("users",$result->getResult());
            $this->setRedirect("user/searchuser");
        }
	}

	function addnewuserAction()
	{

		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$chatsettings=new Settings('nesote_chat_settings');
		$chatsettings->loadValues();
		$userpanel="";

		$db=new NesoteDALController();

		$userpanel=$settings->getValue("controlpanel");
		$this->setValue("controlpanel",$userpanel);

		$min_usernamelength=$settings->getValue("min_usernamelength");
		$this->setValue("min_usernamelength",$min_usernamelength);
		
		$min_passwordlength=$settings->getValue("min_passwordlength");
		$this->setValue("min_passwordlength",$min_passwordlength);

		
		$db->select("nesote_email_time_zone");
		$db->fields("id,name,value");
		$result=$db->query();
		$this->setLoopValue("timezone",$result->getResult());

		if($_POST !== [])
		{

			require(__DIR__ . "/script.inc.php");
			
			require($config_path."system.config.php");
			


			$min_usernamelength=$settings->getValue("min_usernamelength");
			$this->setValue("min_usernamelength",$min_usernamelength);

			$username=$_POST['username'];

			if($username=="")
			{
				header("Location:".$this->url("message/error/1118"));
				exit(0);
			}
			$username=strtolower((string) $_POST['username']);
			$uname=$username.$this->getextension();//echo $uname;
            
		     if (preg_match ('/[^a-z0-9,_]/i', $username))
				{
				header("Location:".$this->url("message/error/1124"));
				exit(0);
				}

			$usernamelength=strlen($username);
			if($usernamelength<$min_usernamelength)
			{
				header("Location:".$this->url("message/error/1134/$min_usernamelength"));
				exit(0);
			}
			if($usernamelength>32)
			{
				header("Location:".$this->url("message/error/1065"));
				exit(0);
			}
			$password1=$_POST['password'];
			if($password1=="")
			{
				header("Location:".$this->url("message/error/1119"));
				exit(0);
			}
			if($password1==$username)
			{
				header("Location:".$this->url("message/error/1129"));
				exit(0);
			}
			$cpassword1=$_POST['cpassword'];
			if($cpassword1=="")
			{
				header("Location:".$this->url("message/error/1123"));
				exit(0);
			}
			if($password1!=$cpassword1)
			{
				header("Location:".$this->url("message/error/1120"));
				exit(0);
			}
			$server_password=base64_encode((string) $password1);
			$password=md5((string) $_POST['password']);

			$pwdcount=$_POST['pwdcnt'];

			if($pwdcount<2)
			{

				header("Location:".$this->url("message/error/1068"));
				exit(0);
			}


			$firstname=$_POST['firstname'];
			if($firstname=="")
			{
				header("Location:".$this->url("message/error/1121"));
				exit(0);
			}
			$lastname=$_POST['lastname'];
			if($lastname=="")
			{
				header("Location:".$this->url("message/error/1122"));
				exit(0);
			}

			$timezone=$_POST['time_zone'];
			if($timezone=="")
			{
				header("Location:".$this->url("message/error/1126"));
				exit(0);

			}

			$time=time();

			if($this->existusername($username))
			{
				header("Location:".$this->url("message/error/1010"));
				exit(0);
			}

			
			$db->select("nesote_email_reservedemail");
			$db->fields("name");
			$result=$db->query();
			while($row=$db->fetchRow($result))
			{
				if(trim((string) $row[0]) === trim($uname))
				{
					header("Location:".$this->url("message/error/1069"));
					exit(0);
				}
			}

            $name=$firstname." ".$lastname;
			
			$db->select("nesote_liberyus_users");
			$db->fields("username");
			$result1=$db->query();
			$row1=$db->fetchRow($result1);
			if(trim((string) $row1[0]) === trim($username))
			{
				header("Location:".$this->url("message/error/1010"));
				exit(0);
			}
			
			   $extension=$this->getextension();
				$extension1=substr((string) $extension,0,1);
				if ($extension1 === "@") {
                    $extension=substr((string) $extension,1,strlen((string) $extension));
                }

				$smtp_username="";

				$controlpanel=$settings->getValue("controlpanel");

				if ($controlpanel==1) {
                    $smtp_username=$username."+".$extension;
                } elseif ($controlpanel==2) {
                    $smtp_username=$username."@".$extension;
                }


				$account_type=$settings->getValue("catchall_mail");
				if($account_type==1)//for catch all
				{
					
					$db->insert("nesote_liberyus_users");
					$db->fields("username,password,name,joindate,status");
					$db->values([$username,$password,$name,time(),1]);
					$result=$db->query();
					$last=$db->lastInsert();
					$this->welcomemessage($last,$username);


					$mails_per_page=$settings->getValue("mails_per_page");
					if (($mails_per_page=="")|| ($mails_per_page==0)) {
                        $mails_per_page=20;
                    }


					$default_language=$settings->getValue("default_language");
					if ($default_language=="") {
                        $default_language='eng';
                    }


					$themes=$settings->getValue("themes");
					if ($themes==0) {
                        $themes=2;
                    }

					$display=$settings->getValue("display");
					if ($display==0) {
                        $display=1;
                    }

					$themes=$settings->getValue("themes");
					if ($themes==0) {
                        $themes=1;
                    }

					$display=$settings->getValue("display");
					if ($display==0) {
                        $display=1;
                    }

					$shortcuts=$settings->getValue("shortcuts");
					if ($shortcuts=="") {
                        $shortcuts=0;
                    }
					
				
               
               $db->select("nesote_email_calendar_settings");
               $db->fields("value");
               $db->where("name=?",\EMAIL_REMAINDER);
               $result=$db->query();
               $rs=$db->fetchRow($result);
               $email_remainder=$rs[0];
                       
               $db->select("nesote_email_calendar_settings");
               $db->fields("value");
               $db->where("name=?",\VIEW_EVENT);
               $result1=$db->query();
               $rs1=$db->fetchRow($result1);
               $view_event=$rs1[0];
			   
					
					$db->insert("nesote_email_usersettings");
					$db->fields("userid,lang_id,theme_id,display,mails_per_page,email_remainder,view_event,shortcuts,server_password,time_zone,smtp_username");
					$db->values([$last,$default_language,$themes,$display,$mails_per_page,$email_remainder,$view_event,$shortcuts,$server_password,$timezone,$smtp_username]);
					$result=$db->query();

					////Chat Start////////////

					$chathistory=$chatsettings->getValue("chat_history");



					$sounds=$chatsettings->getValue("default_chat_sound");


					$smileys=$chatsettings->getValue("chat_smiley");

					$deafault_chatwindow_size=$chatsettings->getValue("deafault_chatwindow_size");


					
					$db->insert("nesote_chat_users");
					$db->fields("userid,chat_status,chatwindowsize,chathistory,sounds,smileys");
					$db->values([$last,1,$deafault_chatwindow_size,$chathistory,$sounds,$smileys]);
					$result=$db->query();
					
					$attachments_path="../userdata";

					if(!is_dir("$attachments_path/$last"))

						{

							mkdir("$attachments_path/$last",0777);

						}


					/////////Chat End///////////

					header("Location:".$this->url("message/success/1070/1"));//1 for path creation in message/success controller
					exit(0);
				}
				else// for individual
				{

					$automatic_account_creation=$settings->getValue("automatic_account_creation");
					if($automatic_account_creation==1)//for automatic account creation
					{
						//---api calling------


						$controlpanel=$settings->getValue("controlpanel");

						if ($controlpanel==1) {
                            $exist_mail=$this->mailaccexist($username);
                            if($exist_mail!=0)
							{			
							header("Location:".$this->url("message/error/1010"));
							exit(0);
							}
                            $this->cpanelaction(1,$username,$password1);
                            //1 for account creation
                        } elseif ($controlpanel==2) {
                            $exist_mail=$this->pleskmailaccexist($username);
                            if($exist_mail=="ok")
							{			
							header("Location:".$this->url("message/error/1010"));
							exit(0);
							}
                            $this->pleskaction(1,$username,$password1);
                            //1 for account creation
                        }


						
						
						
						$db->insert("nesote_liberyus_users");
						$db->fields("username,password,name,joindate,status");
						$db->values([$username,$password,$name,time(),1]);
						$result=$db->query();
						$last=$db->lastInsert();

						$this->welcomemessage($last,$username);


						$mails_per_page=$settings->getValue("mails_per_page");
						if (($mails_per_page=="")|| ($mails_per_page==0)) {
                            $mails_per_page=20;
                        }


						$default_language=$settings->getValue("default_language");
						if ($default_language==""  || $default_language===0) {
                            $default_language='eng';
                        }


						$themes=$settings->getValue("themes");
						if ($themes==0) {
                            $themes=1;
                        }

						$display=$settings->getValue("display");
						if ($display==0) {
                            $display=1;
                        }
						
						$shortcuts=$settings->getValue("shortcuts");
					if ($shortcuts=="") {
                        $shortcuts=0;
                    }
					
				
               
               $db->select("nesote_email_calendar_settings");
               $db->fields("value");
               $db->where("name=?",\EMAIL_REMAINDER);
               $result=$db->query();
               $rs=$db->fetchRow($result);
               $email_remainder=$rs[0];
                       
               $db->select("nesote_email_calendar_settings");
               $db->fields("value");
               $db->where("name=?",\VIEW_EVENT);
               $result1=$db->query();
               $rs1=$db->fetchRow($result1);
               $view_event=$rs1[0];


						$db->insert("nesote_email_usersettings");
						$db->fields("userid,lang_id,theme_id,display,mails_per_page,email_remainder,view_event,shortcuts,server_password,time_zone,smtp_username");
						$db->values([$last,$default_language,$themes,$display,$mails_per_page,$email_remainder,$view_event,$shortcuts,$server_password,$timezone,$smtp_username]);
						$result=$db->query();

						////Chat Start////////////

						$chathistory=$chatsettings->getValue("chat_history");



						$sounds=$chatsettings->getValue("default_chat_sound");



						$smileys=$chatsettings->getValue("chat_smiley");
							

						$deafault_chatwindow_size=$chatsettings->getValue("deafault_chatwindow_size");


						
						$db->insert("nesote_chat_users");
						$db->fields("userid,chat_status,chatwindowsize,chathistory,sounds,smileys");
						$db->values([$last,1,$deafault_chatwindow_size,$chathistory,$sounds,$smileys]);
						$result=$db->query();
						
						$attachments_path="../userdata";

						if(!is_dir("$attachments_path/$last"))

							{

								mkdir("$attachments_path/$last",0777);

							}


							

						/////////Chat End///////////

						header("Location:".$this->url("message/success/1070/1"));//1 for path creation in message/success controller
						exit(0);
					}
					else//for manully account creation
					{
	
						$db->insert("nesote_liberyus_users");
						$db->fields("username,password,name,joindate,status");
						$db->values([$username,$password,$name,time(),1]);
						$result=$db->query();
						$last=$db->lastInsert();

						$this->welcomemessage($last,$username);

						$mails_per_page=$settings->getValue("mails_per_page");
						if (($mails_per_page=="")|| ($mails_per_page==0)) {
                            $mails_per_page=20;
                        }

						$default_language=$settings->getValue("default_language");
						if ($default_language==""  || $default_language===0) {
                            $default_language='eng';
                        }

						
						$themes=$settings->getValue("themes");
						if ($themes==0) {
                            $themes=1;
                        }

						$display=$settings->getValue("display");
						if ($display==0) {
                            $display=1;
                        }
						
						$shortcuts=$settings->getValue("shortcuts");
					if ($shortcuts=="") {
                        $shortcuts=0;
                    }
					
				
               
               $db->select("nesote_email_calendar_settings");
               $db->fields("value");
               $db->where("name=?",\EMAIL_REMAINDER);
               $result=$db->query();
               $rs=$db->fetchRow($result);
               $email_remainder=$rs[0];
                       
               $db->select("nesote_email_calendar_settings");
               $db->fields("value");
               $db->where("name=?",\VIEW_EVENT);
               $result1=$db->query();
               $rs1=$db->fetchRow($result1);
               $view_event=$rs1[0];

               

			
						$db->insert("nesote_email_usersettings");
						$db->fields("userid,lang_id,theme_id,display,mails_per_page,email_remainder,view_event,shortcuts,server_password,time_zone,smtp_username");
						$db->values([$last,$default_language,$themes,$display,$mails_per_page,$email_remainder,$view_event,$shortcuts,$server_password,$timezone,$smtp_username]);
						$result=$db->query();

						////Chat Start////////////
//					
						$chathistory=$chatsettings->getValue("chat_history");


						$sounds=$chatsettings->getValue("default_chat_sound");



						$smileys=$chatsettings->getValue("chat_smiley");



						$deafault_chatwindow_size=$chatsettings->getValue("deafault_chatwindow_size");


						
						$db->insert("nesote_chat_users");
						$db->fields("userid,chat_status,chatwindowsize,chathistory,sounds,smileys");
						$db->values([$last,1,$deafault_chatwindow_size,$chathistory,$sounds,$smileys]);
						$result=$db->query();
						
						$attachments_path="../userdata";

						if(!is_dir("$attachments_path/$last"))

							{

								mkdir("$attachments_path/$last",0777);

							}

						/////////Chat End///////////

						header("Location:".$this->url("message/success/1070/1"));//1 for path creation in message/success controller
						exit(0);
					}

				}

		}
	}

	function cpanelaction($execute,$username,$value)
	{

		include_once __DIR__ . '/../class/xmlapi.php';

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
			

		$ip=$settings->getValue("domain_ip");


		$root_pass=$settings->getValue("domain_password");


		$email_domain=$settings->getValue("domain_name");


		$domain_username=$settings->getValue("domain_username");


		$account = "cptest";
		$email_user = $username;
		$email_password = $value;
		$email_query = '10';
		$xmlapi = new xmlapi($ip);
		/* IF the port no is 2083 then uncomment the below sentence*/
		//$xmlapi->set_port(2083);
		$xmlapi->password_auth($domain_username,$root_pass);
		$xmlapi->set_output('xml');
		$email_quota=0;
		if ($execute==1) {
            $xmlapi->set_debug(1);
            //$arr = $xmlapi->api2_query($account, "Email", "addpop", array(domain=>$email_domain, email=>$email_user, password=>$email_password, quota=>0) );
            try {
				$arr = $xmlapi->api2_query($account, "Email", "addpop", [\DOMAIN=>$email_domain, \EMAIL=>$email_user, \PASSWORD=>$email_password, \QUOTA=>0] );

			}

			catch (Exception) {

				header("Location:".$this->url("message/error/1132"));
				exit(0);
			}
        } elseif ($execute==2) {
            try {
				$xmlapi->api1_query($account, "Email", "passwdpop", [$email_user, $value, $email_quota, $email_domain] );
			}
			catch (Exception) {

				header("Location:".$this->url("message/error/1132"));
				exit(0);
			}
        } elseif ($execute==3) {
            $xmlapi->set_debug(1);
            try {
				$arr = $xmlapi->api2_query($account, "Email", "delpop", [\DOMAIN=>$email_domain, \EMAIL=>$email_user, \PASSWORD=>$email_password] );
			}
			catch (Exception) {

				header("Location:".$this->url("message/error/1132"));
				exit(0);
			}
        }
	}

	function pleskaction($execute,$username,$value)
	{
		include_once __DIR__ . '/../class/mail_plesk.php';
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		

		$host=$settings->getValue("domain_name");


		$login=$settings->getValue("domain_username");


		$password=$settings->getValue("domain_password");


		$plesk_packetversion=$settings->getValue("plesk_packetversion");

		$plesk_domainid=$settings->getValue("plesk_domainid");

		if($execute==1)//for account creation
		{


			$create="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<create>
			<filter>
			<domain_id>$plesk_domainid</domain_id>
			<mailname>
			<name>$username</name>
			<mailbox>
			<enabled>true</enabled>
			</mailbox>
			<password>$value</password>
			<password_type>plain</password_type>

			</mailname>

			</filter>
			</create>
			</mail>
			</packet>
			";
			$action=$create;
		}

		if($execute==2)// for change password
		{


			$change="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<update>
			<set>
			<filter><domain_id>$plesk_domainid</domain_id>
			<mailname>
			<name>$username</name>
			<mailbox>
			<enabled>true</enabled>
			</mailbox>
			<password>$value</password>
			<password_type>plain</password_type>

			</mailname>
			</filter>
			</set>
			</update>
			</mail>
			</packet>
			";
			$action=$change;
		}

		if($execute==3)//for account deletion
		{


			$delete="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<remove>
			<filter>
			<domain_id>$plesk_domainid</domain_id>
			<name>$username</name>
			</filter>
			</remove>
			</mail>
			</packet>
			";
			$action=$delete;
		}

		$curl = curlInit($host, $login, $password);
		try {

			// echo GET_PROTOS;
			$response = sendRequest($curl, $action);//echo $response;exit;
			$responseXml = parseResponse($response);
			checkResponse($responseXml);
		} catch (ApiRequestException) {

			header("Location:".$this->url("message/error/1132"));
			exit(0);
		}
		// Explore the result
		foreach ($responseXml->xpath('/packet/domain/get/result') as $resultNode) {
			echo "Domain id: " . $resultNode->id . " ";
			echo $resultNode->data->gen_info->name . " (" .
			$resultNode->data->gen_info->dns_ip_address . ")\n";
		}

	}

	function welcomemessage($id,$username)
	{

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$tablenumber=$this->tableid($username);	
		$db=new NesoteDALController();

		$welcome_email=$settings->getValue("welcome_email_body");


		$welcome_subject=$settings->getValue("welcome_email_subject");


		$adminemail=$settings->getValue("adminemail");


		$mailid=$username.$this->getextension();
		//$subject=""

		
		$db->insert("nesote_email_inbox_$tablenumber");
		$db->fields("userid,from_list,to_list,subject,body,time,status");
		$db->values([$id,$adminemail,$mailid,$welcome_subject,$welcome_email,time(),1]);
		$db->query();
		$last=$db->lastInsert();

		$var=time().$id."1";
		$ext=$this->getextension();
		$message_id="<".md5($var).$ext.">";

		$mail_references="<references><item><mailid>$last</mailid><folderid>1</folderid></item></references>";
	$md5_references=md5($mail_references);
		
		$db->update("nesote_email_inbox_$tablenumber");
	$db->set("mail_references=?,md5_references=?,message_id=?",[$mail_references,$md5_references,$message_id]);
		$db->where("id=?",$last);
        $db->query();
	}

	function clientlogsAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$t=0;
		$time="";
		$operation=$_POST['operationtype'];

		$db=new NesoteDALController();
		$db->select(["a"=>"nesote_email_client_logs"]);
		$db->fields("DISTINCT(a.operation)");
		if ($operation!="") {
            $db->where("operation!=?",[$operation]);
        }
		$result=$db->query();//echo $db->getQuery();
		$this->setLoopValue("operation",$result->getresult());


		$operation=$_POST['operationtype'];//echo $operation;

		$optype="";
		if ($operation!="") {
            $optype='<option value="'.$operation.'" selected>'.$operation.'</option>';
        }

		$this->setValue('operationoption',$optype);
		if ($operation!="") {
            $this->setValue('selectoption',$operation);
        }

		$time=$_POST['timeperiod'];//echo $time;
		$this->setValue("time",$time);
		$conditionarray=[];
		$conditioncount=0;
		if ($_POST['operationtype']!="")
		{
			$condition .= "operation=? ";
			$conditionarray[$conditioncount++]=$_POST['operationtype'];
		}

		if ($_POST['timeperiod']!="")
		{

			$s=getdate(time());
			$t=mktime(0,0,0,$s[\MON],$s[\MDAY],$s[\YEAR]);

			$condition = $_POST['operationtype'] != "" ? $condition." AND time > ? " : $condition."time > ? ";
			$conditionarray[$conditioncount++] = $time == 86400 ? $t : $t-$_POST['timeperiod'];
		}



		$perpagesize=50;
		$currentpage=1;

		
		if ($_POST['operationtype']=="" && $_POST['timeperiod']=="") {
            $tot=$db->total("nesote_email_client_logs");
        } else {
            $tot=$db->total("nesote_email_client_logs",$condition,$conditionarray);
        }

		if (isset($_POST['pagenumber'])&&trim($_POST['pagenumber']) !== "") {
            $currentpage=$_POST['pagenumber'];
        }
		$paging= new Paging();
		$out=$paging->page($tot,$perpagesize,"page",1,1,1,"","","",$_POST);
		$this->setValue("pagingtop",$out);

		
		$db->select("nesote_email_client_logs");
		$db->fields("*");

		if ($_POST['operationtype']=="" && $_POST['timeperiod']=="") {
            $db->limit(($currentpage-1)*$perpagesize, $perpagesize);
        } else
		{
			$db->where($condition,$conditionarray);//echo $currentpage;
			$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
		}
		$db->order("time desc");
		$result=$db->query();//echo $db->getQuery();
		$db->fetchRow($result);//echo $row[3];
		$row1=$db->numRows($result);//echo $row1;
		if ($row1==0) {
            $this->setValue("empty","-No Logs found-");
        } else
		{

			$this->setValue("empty","");
			$this->setLoopValue("clientlogs",$result->getResult());
		}


	}

	function spamsAction()
	{

	}

	function checkDomain($nname)
	{

		$arr=[];

		$arr1 ="
.com,.net,.org,.biz,.coop,.info,.museum,.name,.pro,.edu,.gov,.int,.mil,.ac,.ad,.ae,.af,.ag,
.ai,.al,.am,.an,.ao,.aq,.ar,.as,.at,.au,.aw,.az,.ba,.bb,.bd,.be,.bf,.bg,.bh,.bi,.bj,.bm,
.bn,.bo,.br,.bs,.bt,.bv,.bw,.by,.bz,.ca,.cc,.cd,.cf,.cg,.ch,.ci,.ck,.cl,.cm,.cn,.co,.cr,
.cu,.cv,.cx,.cy,.cz,.de,.dj,.dk,.dm,.do,.dz,.ec,.ee,.eg,.eh,.er,.es,.et,.fi,.fj,.fk,.fm,
.fo,.fr,.ga,.gd,.ge,.gf,.gg,.gh,.gi,.gl,.gm,.gn,.gp,.gq,.gr,.gs,.gt,.gu,.gv,.gy,.hk,.hm,
.hn,.hr,.ht,.hu,.id,.ie,.il,.im,.in,.io,.iq,.ir,.is,.it,.je,.jm,.jo,.jp,.ke,.kg,.kh,.ki,
.km,.kn,.kp,.kr,.kw,.ky,.kz,.la,.lb,.lc,.li,.lk,.lr,.ls,.lt,.lu,.lv,.ly,.ma,.mc,.md,.mg,
.mh,.mk,.ml,.mm,.mn,.mo,.mp,.mq,.mr,.ms,.mt,.mu,.mv,.mw,.mx,.my,.mz,.na,.nc,.ne,.nf,.ng,
.ni,.nl,.no,.np,.nr,.nu,.nz,.om,.pa,.pe,.pf,.pg,.ph,.pk,.pl,.pm,.pn,.pr,.ps,.pt,.pw,.py,
.qa,.re,.ro,.rw,.ru,.sa,.sb,.sc,.sd,.se,.sg,.sh,.si,.sj,.sk,.sl,.sm,.sn,.so,.sr,.st,.sv,
.sy,.sz,.tc,.td,.tf,.tg,.th,.tj,.tk,.tm,.tn,.to,.tp,.tr,.tt,.tv,.tw,.tz,.ua,.ug,.uk,.um,
.us,.uy,.uz,.va,.vc,.ve,.vg,.vi,.vn,.vu,.ws,.wf,.ye,.yt,.yu,.za,.zm,.zw";//print_r($arr1);
		$arr=explode(",",$arr1);//print_r($arr);


		$mai=$nname;$dot=0;$length=0;$dname="";
		$val=true;
		$length=strlen((string) $mai);
		$dot =strripos((string) $mai,".");//echo $dot;
		$dname =substr((string) $mai,0,$dot);//echo $dname;

		$ext =substr((string) $mai,$dot,$length);//echo $ext;

		$msg="";$false="false";
		//alert(ext);

		if (($dot>2) && ($dot<57)) {
            $counter = count($arr);
            for($i=0; $i<$counter; $i++)
			{
				if(trim($ext) === trim($arr[$i]))
				{
					$val ="true";
					break;
				}

				else
				{
					$val ="false";
				}
			}
            //echo $val;
            if($val === "false")
			{

				$msg="e";
				$msg=str_replace('{extension}',$ext,$msg);
				return $msg."/".$false."/".$ext;
			}
			else
			{
				for($j=0; $j<strlen($dname); $j++)

				{
					$dh =substr($dname,$j,1);
					$hh = ord($dh);
					if((($hh > 47) && ($hh<59)) || (($hh > 64) && ($hh<91)) || (($hh > 96) && ($hh<123)) || ($hh==45) || ($hh==46))
					{
						if(($j==0 || $j==strlen($dname)-1) && ($hh == 45))

						{
							$msg="b";
							return $msg."/".$false;
						}
					}

					else	{
						$msg="s";
						return $msg."/".$false;
					}
				}
			}
        } else
		{
			$msg="sl";
			return $msg."/".$false;
		}



		return "true";
	}

	function isEmail($email)
	{
		$result =true;
		if(!preg_match("#^[_a-z0-9\\-]+(\\.[_a-z0-9\\-]+)*@[a-z0-9\\-]+(\\.[a-z0-9\\-]+)*(\\.[a-z]{2,4})\$#mi", (string) $email))
		{
			return false;
		}
		;
		return $result;
	}

	function getgroupcount($id)
	{
		$num=0;
		$db=new NesoteDALController();
		$db->select("nesote_email_contacts");
		$db->fields("*");
		$db->where("contactgroup=?",[$id]);
		$result=$db->query();
		$num=$db->numRows($result);
		if ($num==0) {
            return "No";
        } else {
            return $num;
        }
	}

	function getmode()
	{
		include(__DIR__ . "/script.inc.php");
		//global $config_path;
		include("{$config_path}system.config.php");
		//	global $mod_rewrite;
		$res = $mod_rewrite ? "?" : "&";
		return $res;

	}

	function getadmintime($a)
	{
		return $a;
		
	}
	function getusertime($a)
	{
		//time
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
				
		$position=$settings->getValue("time_zone_postion");

		$hour=$settings->getValue("time_zone_hour");

		$min=$settings->getValue("time_zone_mint");

		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=$a-$diff;
		
		return  date("jS, F Y,g:i a",$ts);
		
	}

	function gettimezone($id)
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_time_zone");
		$db->fields("*");
		$db->where("id=?",[$id]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		return "(GMT".$row[2].") ".$row[1];
	}
	function tableid($username)
    {
		$user_name=$username;
		include(__DIR__ . "/../config.php");
		$number=$cluster_factor;
		
		$user_name=trim((string) $user_name);
		$mdsuser_name=md5($user_name);
		$mdsuser_name=str_replace("a","",$mdsuser_name);
		$mdsuser_name=str_replace("b","",$mdsuser_name);
		$mdsuser_name=str_replace("c","",$mdsuser_name);
		$mdsuser_name=str_replace("d","",$mdsuser_name);
		$mdsuser_name=str_replace("e","",$mdsuser_name);
		$mdsuser_name=str_replace("f","",$mdsuser_name);
		
		$digits=substr($mdsuser_name,-6);
		
		$modlusnumber=$digits % $number;
		$modlusnumber += 1;
		$numbers[$modlusnumber]++;
		return $modlusnumber;
    }
	
	function mailaccexist($username)// username->uu@domain.com format
		{

			$extension=$this->getextension();
			$username .= $extension;
			include_once __DIR__ . '/../class/xmlapi.php';

			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();

			$ip=$settings->getValue("domain_ip");

			$root_pass=$settings->getValue("domain_password");

			$email_domain=$settings->getValue("domain_name");

			$domain_username=$settings->getValue("domain_username");

			$account = "cptest";

			$email_query = '10';
			$xmlapi = new xmlapi($ip);
			/* IF the port no is 2083 then uncomment the below sentence*/
			//$xmlapi->set_port(2083);
			$xmlapi->password_auth($domain_username,$root_pass);
			$xmlapi->set_output('json');

			$xmlapi->set_debug(1);
			$arr = $xmlapi->api2_query($account,"Email", "listpopswithdisk", [\DOMAIN=>$email_domain] );

			$json_o=json_decode((string) $arr);
			$e_arr=[];$i=0;
			foreach($json_o->cpanelresult->data as $p)
			{
				if ($username==$p->login) {
                    $i++;
                }
			}
			return $i;
		}

function pleskmailaccexist($username)
	{
		include_once __DIR__ . '/../class/mail_plesk.php';
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$host=$settings->getValue("domain_name");
		$login=$settings->getValue("domain_username");
		$password=$settings->getValue("domain_password");
		$plesk_packetversion=$settings->getValue("plesk_packetversion");
		$plesk_domainid=$settings->getValue("plesk_domainid");
		$action = '<packet version="1.6.0.0"><mail><get_info><filter><domain_id>'.$plesk_domainid.'</domain_id><name>'.$username.'</name></filter></get_info></mail></packet>';

		$curl = curlInit($host, $login, $password);
		try {

			// echo GET_PROTOS;
			$response = sendRequest($curl, $action);
			preg_match('/<status>(.+?)<\/status>/i',$response,$folderArray);
			return $folderArray[1];
			
		} catch (ApiRequestException) {

			header("Location:".$this->url("message/error/1132"));
			exit(0);
		}
		// Explore the result
		foreach ($responseXml->xpath('/packet/domain/get/result') as $resultNode) {
			echo "Domain id: " . $resultNode->id . " ";
			echo $resultNode->data->gen_info->name . " (" .
			$resultNode->data->gen_info->dns_ip_address . ")\n";
		}

	}
};
?>