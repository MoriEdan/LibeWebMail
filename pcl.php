<?php 

$url="";

$host=$_SERVER['HTTP_HOST'];
$script_name=$_SERVER['SCRIPT_NAME'];

$script_name=substr((string) $script_name,0,strrpos((string) $script_name,"/"));

//echo $script_name;
//exit;

$url="http://".$host.$script_name;

$url .= "/index.php?page=";

//echo $url;
//exit;

?>

<pcl>
	<metadetails>
				<myaccounturl><?php echo $url."mail/mailbox" ?></myaccounturl>
	</metadetails>
	<pclcontent>
				<pclitem>
						<pclitemtitle></pclitemtitle>
						<pclitemurl></pclitemurl>
						<pclitemfields></pclitemfields>
						<item>
							<itemcontent>
								<pcltitle></pcltitle>
								<pcldescription></pcldescription>
								<pclimage></pclimage>
								<pclurl></pclurl>
							</itemcontent>
					    </item>	
					   
				</pclitem>
				
				
	</pclcontent>



</pcl>
	
