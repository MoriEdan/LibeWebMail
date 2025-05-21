<?php
class ThemesController extends NesoteController
{


	function newAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

			}

	function storeinfoAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		

		$name=$_POST['theme'];
		$content=$_POST['style'];

		if(($name=="")||($content==""))
		{
			header("Location:".$this->url("message/error/1013"));
			exit(0);
		}
		$db= new NesoteDALController();
		$tot=$db->total("nesote_email_themes","name=?",[$name]);
		if($tot!=0)
		{
			header("Location:".$this->url("message/error/1055"));
			exit(0);
		}

		$mdl=$this->modelInstance("nesote_email_themes");
		$mdl->setName($name);
		$mdl->setStyle($content);
		$mdl->setStatus(1);
		$mdl->save();

		header("Location:".$this->url("themes/manage/1"));
		exit(0);
	}

	function manageAction()
	{

		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		$msg=$this->getParam(1);$msg1="";
		if ($msg==1) {
            $this->setValue("msg","New theme has been added successfully.");
        }
		if ($msg==2) {
            $this->setValue("msg","Theme details has been updated successfully.");
        }
		if ($msg==3) {
            $this->setValue("msg","Theme has been deleted successfully.");
        }
		if ($msg==4) {
            $this->setValue("msg","Theme has been disabled successfully.");
        }
		if ($msg==5) {
            $this->setValue("msg","Theme has been enabled successfully.");
        }

		$db= new NesoteDALController();
		$db->select("nesote_email_themes");
		$db->fields("*");
		$db->order("name");
		$result=$db->query();//echo $db->getQuery();
		$num=$db->numRows($result);
		if ($num==0) {
            $msg1="No Themes";
        }
		$this->setValue("msg1",$msg1);
		$this->setLoopValue("themes",$result->getResult());
	}

	function editAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}


		$id=$this->getParam(1);
		$this->setValue("id",$id);
		/*if(($server=="www.libewebportal.com") || ($server=="libewebportal.com")|| ($server=="www.libewebmaildemo.com") || ($server=="libewebmaildemo.com"))
		{
			//if($id<3)
			//{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			//}
		}*/

		$db= new NesoteDALController();
		$db->select("nesote_email_themes");
		$db->fields("*");
		$db->where("id=?",$id);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$this->setValue("name",$row[1]);
		$this->setValue("style",$row[2]);
	}

	function storeeditinfoAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		

		$id=$this->getParam(1);
		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{
			//if($id<3)
			//{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			//}
		}

		$theme=$_POST['theme'];
		$style=$_POST['style'];

		if(($theme=="")||($style==""))
		{
			header("Location:".$this->url("message/error/1013"));
			exit(0);
		}
		$db= new NesoteDALController();
		$tot=$db->total("nesote_email_themes","name=? and id!=?",[$theme,$id]);
		if($tot!=0)
		{
			header("Location:".$this->url("message/error/1055"));
			exit(0);
		}

		$mdl=$this->modelInstance("nesote_email_themes");
		$mdl->load($id);
		$mdl->setName($theme);
		$mdl->setStyle($style);
		$mdl->update();
		header("Location:".$this->url("themes/manage/2"));
		exit(0);
	}

	function deleteAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		
		$id=$this->getParam(1);
		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{
			//if($id<3)
			//{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			//}
		}

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$themes=$settings->getValue("themes");

		if($themes==$id)
		{
			header("Location:".$this->url("message/error/1057"));
			exit(0);
		}
		else
		{
			$db= new NesoteDALController();
			$db->delete("nesote_email_themes");
			$db->where("id=?",$id);
			$db->query();
			header("Location:".$this->url("themes/manage/3"));
			exit(0);

		}

	}


	function enableAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		
		$id=$this->getParam(1);
		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{
			//if($id<3)
			//{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			//}
		}

		$mdl=$this->modelInstance("nesote_email_themes");
		$mdl->load("$id");
		$mdl->setStatus("1");
		$mdl->update();
		header("Location:".$this->url("themes/manage/5"));
		exit(0);
	}

	function disableAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		
		$id=$this->getParam(1);
		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{
			//if($id<3)
			//{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			//}
		}

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$themes=$settings->getValue("themes");

		if($themes==$id)
		{
			header("Location:".$this->url("message/error/1056"));
			exit(0);
		}

		$mdl=$this->modelInstance("nesote_email_themes");
		$mdl->load("$id");
		$mdl->setStatus("0");
		$mdl->update();
		header("Location:".$this->url("themes/manage/4"));
		exit(0);

	}

	function settingsAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		

		$db= new NesoteDALController();
		$db->select("nesote_email_themes");
		$db->fields("id,name");
		$db->where("status=?",1);
		$result=$db->query();
		$this->setLoopValue("themes",$result->getResult());

		$this->loadLibrary("settings");
		$set=new settings("nesote_email_settings");
		$set->loadValues();
		$override_themes=$set->getValue("override_themes");
		$themes=$set->getValue("themes");
		$this->setValue("themes",$themes);
		$this->setValue("override_themes",$override_themes);
	}

	function settingsprocessAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$db= new NesoteDALController();
		
		$themes=$_POST['themes'];
		$override_themes=$_POST['override_themes'];
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}
		$override_themes = $override_themes == "on" ? 1 : 0;
		$db->update("nesote_email_settings");
		$db->set("value=?",[$themes]);
		$db->where("name='themes'");
		$db->query();
		$db->set("value=?",[$override_themes]);
		$db->where("name='override_themes'");
		$db->query();
		//echo $db->getQuery();
		header("Location:".$this->url("message/success/1020/3"));
		exit(0);
	}

	function adscodeAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		$this->loadLibrary("settings");
		$set=new settings("nesote_email_settings");
		$set->loadValues();
		$leftads_code=$set->getValue("leftads_code");
		$rightads_code=$set->getValue("rightads_code");
		$this->setValue("leftads_code",$leftads_code);
		$this->setValue("rightads_code",$rightads_code);

	}

	function adscodeprocessAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		$leftads_code=$_POST['leftads_code'];
		$rightads_code=$_POST['rightads_code'];

		$db= new NesoteDALController();
		$db->update("nesote_email_settings");
		$db->set("value=?",[$leftads_code]);
		$db->where("name='leftads_code'");
		$db->query();
		$db->set("value=?",[$rightads_code]);
		$db->where("name='rightads_code'");
		$db->query();
		//echo $db->getQuery();
		header("Location:".$this->url("message/success/1060/3"));
		exit(0);
	}


	function analyticscodeAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		

		$this->loadLibrary("settings");
		$set=new settings("nesote_email_settings");
		$set->loadValues();
		$analystics_code=$set->getValue("analystics_code");
		$this->setValue("analystics_code",$analystics_code);
	}

	function analyticscodeprocessAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		$analystics_code=trim((string) $_POST['analystics_code']);

		$db=new NesoteDALController();
		$db->update("nesote_email_settings");
		$db->set("value=?",[$analystics_code]);
		$db->where("name='analystics_code'");
		$db->query();

		header("Location:".$this->url("message/success/1020/3"));
		exit(0);
	}



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

	
}
?>