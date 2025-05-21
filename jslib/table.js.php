<?php 
if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
    ob_start("ob_gzhandler");
} else {
    ob_start();
}
header("Content-type: text/javascript;");

?>
//document.write("<script src='resultSet.js' type='text/javascript'></script>");
//include('resultSet.js');
var tableNames="";
function isArrayAvailable(tblname)//Checks whether the object is already created or not. Returns 1 if created else 0
{
	var names=new Array(1000);
	names=tableNames.split(",");
	var result=0;
	for(var i=0;i<(names.length);i++)
		{
		if(names[i]==tblname)
		result=1;
		}
	return result;
}
Table.prototype.constructor=Table;
function Table(tblname,fields,total,buffer)//Creates an instance of the class Table
{
	this.nam=tblname;
	tableNames=tableNames+","+tblname;
	this.buff=200;
	if(buffer)
	this.buff=buffer;
	this.fieldz=new Array(100);
	this.fieldz=fields;
	this.tot=total;
	this.totl=parseInt(this.tot)+parseInt(this.buff);
	this.fbuff=this.buff/2;
	this.lbuff=this.buff/2;
	this.totl1=this.fbuff;
	this.totl2=this.totl-this.lbuff;
	this.totl2=this.totl2+parseInt(1);
	this.nam=new Array(this.totl);
	this.updateString="";
	this.deleteString="";
	var p=0;var as=new Array(this.totl);
	for(var elem=0;elem<this.totl;elem++)
	{
		if(this.nam[elem])
		{
			as[p]=elem;
			p=p+1;
		}
	}
	this.positions=new Array(p);
	for(var i=0;i<p;i++)
	{
		this.positions[i]=as[i];
	}
}

Table.prototype.insertToposition=function(tblname,position,values)
{
	this.valuez=new Array(100);
	this.valuez=values;
	var positionToinsert=parseInt(this.fbuff)+parseInt(position);
	this.nam[positionToinsert]=this.valuez;
}
Table.prototype.sort=function(tblname,pagIndx,values,sortfld,ordr) //sort an  array 
{

	var tot_no=this.totl2;
	var strt=0;
	this.order="desc";
		for(var i=11;i<=tot_no;i++)//sort 
		{
			
			for(var j=i+1;j<=tot_no;j++)
			{
				
				if(this.order=="asc")
				{
					if(this.nam[i]>this.nam[j])
					{
						var temp=this.nam[i];
						this.nam[i]=this.nam[j];
						this.nam[j]=temp;
					}
				}
				else if(this.order=="desc")
				{
				
					
					if(this.nam[i][11]<this.nam[j][11])
					{
						var temp=this.nam[i];
						this.nam[i]=this.nam[j];
						this.nam[j]=temp;
					}
				}
			}
		}
}

Table.prototype.insert=function(tblname,pagIndx,values,sortfld,ordr)//Inserts an element into the array first 3 patameters are mandatory.values(array)
{
	this.nam1=tblname;
	this.valuez=new Array(100);
	this.valuez=values;
	this.srtfld="id";
	if(sortfld)
	this.srtfld=sortfld;
	this.order="asc";
	if(ordr)
	this.order=ordr;
	this.pageIndex=0;
	if(pagIndx)
	this.pageIndex=pagIndx;
	this.sortIndex=0;
	for(var j=0;j<this.fieldz.length;j++)
	{
		if(this.fieldz[j]==this.srtfld)
		this.sortIndex=j;
	}

	for(var j=0;j<this.fieldz.length;j++)
	{
		if(this.fieldz[j]=="id")
		this.idIndex=j;
	}
	if(this.pageIndex==1)//insert starting position
	{
		this.nam[this.totl1]=this.valuez;
		this.totl1--;
	}
	else if(this.pageIndex==2)//insert after first few
	{
		var strt=this.totl1+1;var positionToinsert=this.totl2;//alert(strt);//alert(this.totl2);
		
		for(var i=strt;i<=this.totl2;i++)
		{
			if(this.nam[i])
			{
			var aa=i;
			}
			else
			{
				positionToinsert=i;
				break;
			}
		}
	
		this.nam[positionToinsert]=this.valuez;
		for(var i=strt;i<=positionToinsert;i++)//sort after insertion
		{
			for(var j=i+1;j<=positionToinsert;j++)
			{
				if(this.order=="asc")
				{
					if(this.nam[i][this.sortIndex]>this.nam[j][this.sortIndex])
					{
						var temp=this.nam[i];
						this.nam[i]=this.nam[j];
						this.nam[j]=temp;
					}
				}
				else if(this.order=="desc")
				{
					if(this.nam[i][this.sortIndex]<this.nam[j][this.sortIndex])
					{//alert(temp);
						var temp=this.nam[i];
						this.nam[i]=this.nam[j];
						this.nam[j]=temp;
					}
				}
			}
		}
	}
	else if(this.pageIndex==3)//insert before last few
	{
		var strt=this.totl2;
		for(var i=strt;i>this.totl1;i--)
		{
			if(this.nam[i])
			var aa=i;
			else
			{
				var positionToinsert=i;
				break;
			}
		}
		this.nam[positionToinsert]=this.valuez;
		for(var i=strt;i>=positionToinsert;i--)//sort after insertion
		{
			for(var j=i-1;j>=positionToinsert;j--)
			{
				if(this.order=="asc")
				{
					if(this.nam[j][this.sortIndex]>this.nam[i][this.sortIndex])
					{
						var temp=this.nam[i];
						this.nam[i]=this.nam[j];
						this.nam[j]=temp;
					}
				}
				else if(this.order=="desc")
				{
					if(this.nam[j][this.sortIndex]<this.nam[i][this.sortIndex])
					{
						var temp=this.nam[i];
						this.nam[i]=this.nam[j];
						this.nam[j]=temp;
					}
				}
			}
		}
	}
	else if(this.pageIndex==4)//insert at end
	{
		this.nam[this.totl2]=this.valuez;
		this.totl2++;
	}
}

Table.prototype.sortnew=function(a,b,c,d,e){this.srtfld=d;for(var j=0;j<this.fieldz.length;j++){if(this.fieldz[j]==this.srtfld)this.Index=j;}var f=this.totl2;var g=0;for(var h=11;h<f;h++){for(var i=h+1;i<f;i++){if(this.order=="desc"){if(this.nam[i]=="" && this.nam[i]!="undefined" && this.nam[h]=="" && this.nam[h]!="undefined"){if(this.nam[h][this.sortIndex]<this.nam[i][this.sortIndex]){var j=this.nam[h];this.nam[h]=this.nam[i];this.nam[i]=j}}}}}};
Table.prototype.update=function(tblname,fields,values)//Updates an array entry with the given values(array) in the corresponding fields(array). 
{
	this.updateString="update ";
	this.fieldarr=new Array(100);
	this.fieldarr=fields;
	this.fieldvarr=new Array(100);
	this.fieldvarr=values;
	if(this.fieldarr.length!=this.fieldvarr.length)
	{
		//alert("Number of fields and values don't match");
		this.updateString="";
	}
}



Table.prototype.deleteEntry=function(tblname)//Deletes an entry from the array
{
	this.deleteString="delete "+tblname;
}


Table.prototype.where=function(condition,fields)//Condition for updation or deletion. Condition format(fieldname=?) and fields must be an array.
{ // alert(condition+"...."+fields);
	this.condition=condition;
	//this.condnvlue=fields;
	this.conditionValue=new Array(100);
	this.condnFld=new Array(100);
	var p=0;var as=new Array(this.totl);
	for(var elem=0;elem<this.totl;elem++)
	{
		if(this.nam[elem])
		{
			as[p]=elem;
			p=p+1;
		}
	}
	this.positions=new Array(p);
	for(var i=0;i<p;i++)
	{
		this.positions[i]=as[i];
	}

	var xx=this.totl-p;
	this.positions.splice(p,xx);
	this.condnFld=this.condition.split("and");
	//this.conditionValue=this.condnvlue.split(",");//alert(this.conditionValue);
	this.conditionValue=fields;//alert(fields);
	if((this.condnFld.length!=0)&&(this.condnFld.length==this.conditionValue.length))
	{
		for(var l=0;l<this.condnFld.length;l++)
		{
			var pos=0;var incr=0;
			var condn=new Array(100);
			var condn1=new Array(100);
			this.condnFld[l]=this.condnFld[l].substr(0,this.condnFld[l].length-1);
			condn[l]=this.condnFld[l].substr(this.condnFld[l].length-1,1);
			condn1[l]=this.condnFld[l].substr(this.condnFld[l].length-2,1);
			if((condn1[l]=="<")||(condn1[l]==">")||(condn1[l]=="!"))
			{
				condn[l]=condn1[l]+condn[l];
				this.condnFld[l]=this.condnFld[l].substr(0,this.condnFld[l].length-2);
			}
			else
			{
				condn[l]=condn[l];
				this.condnFld[l]=this.condnFld[l].substr(0,this.condnFld[l].length-1);
			}
			for(var j=0;j<(this.fieldz.length);j++)
			{
				if(this.fieldz[j]==this.condnFld[l])
				{
					pos=j;
				}
			}
			var no=this.positions.length-1;
			for(var x=no;x>=0;x--)
			{
				var temps=this.positions[x];
				var temp=new Array(100);
				temp=this.nam[temps];
				if(condn[l]=="=")
				{
					if(temp[pos]!=this.conditionValue[l])
					{
						this.positions.splice(x,1);
					}
				}
				else if(condn[l]=="!=")
				{
					if(temp[pos]==this.conditionValue[l])
					{
						this.positions.splice(x,1);
					}
				}
				else if(condn[l]=="<=")
				{
					if(temp[pos]>this.conditionValue[l])
					{
						this.positions.splice(x,1);
					}
				}
				else if(condn[l]=="<")
				{
					if(temp[pos]>=this.conditionValue[l])
					{
						this.positions.splice(x,1);
					}
				}
				else if(condn[l]==">=")
				{	
					if(temp[pos]<this.conditionValue[l])
					{
						this.positions.splice(x,1);
					}
				}
				else if(condn[l]==">")
				{
					if(temp[pos]<=this.conditionValue[l])
					{
						this.positions.splice(x,1);
					}
				}
			}
		}
	
	}
}


Table.prototype.truncate=function()//deletes the entire array
{
	this.nam="";
}


Table.prototype.action=function()//Have to add after each "where" statement.
{
//alert(this.positions.length);
	if(this.updateString!="")
	{
		for(var i=0;i<(this.fieldarr.length);i++)
		{
			for(var j=0;j<(this.fieldz.length);j++)
			{
				if(this.fieldz[j]==this.fieldarr[i])
				{
					for(var x=0;x<(this.positions.length);x++)
					{
						var temp=new Array(100);
						temp=this.nam[this.positions[x]];
						temp[j]=this.fieldvarr[i];
						this.nam[this.positions[x]]=temp;
					}
				}
			}
		}
			
		this.updateString="";
	}
	if(this.deleteString!="")
	{
		
		for(var x=0;x<(this.positions.length);x++)
		{
			var position=this.positions[x];
			this.nam.splice(position,1);
			this.totl=this.totl-1;
			this.totl2=this.totl2-1;
		}
		this.deleteString="";
	}
	var p=0;var as=new Array(this.totl);
	for(var elem=0;elem<this.totl;elem++)
	{
		if(this.nam[elem])
		{
			as[p]=elem;
			p=p+1;
		}
	}
	this.positions=new Array(p);
	for(var i=0;i<p;i++)
	{
		this.positions[i]=as[i];
	}
}



Table.prototype.isPageAvailable=function(tblname,pageno,pagesize)//Checks whether the mentioned page is available in the array. Returns 1 if available, else 0.
{
	var available=0;
	this.pageSize=pagesize;
	var start=(pageno-1)*pagesize+1+this.totl1;
	var end=pageno*pagesize+this.totl1;
	if(end>=this.totl2)
	end=this.totl2; 
	var z=end-start+1;
	for(var j=start;j<=end;j++)
	{
		if(this.nam[j])
		{
			available=available+1;
		}
	}
	if(available!=0)
	return 1;
	else
	return 0;
}


Table.prototype.isItemAvailable=function(tblname,id)//Checks whether the mentioned page is available in the array. Returns 1 if available, else 0.
{
	var available=0;
	for(var j=0;j<this.totl;j++)
	{
		if(this.nam[j])
		{
			if(this.nam[j][0]==id)
			{
				available=j;
			}
		}
		
	}
	
	return available;
	
}


Table.prototype.isItemAvailableindex=function(tblname,index,value)//Checks whether the mentioned page is available in the array. Returns 1 if available, else 0.
{
	var available=0;var fieldPosition=-1;
	for(var j=0;j<this.totl;j++)
	{
		if(this.nam[j])
		{
			for(var k=0;k<this.fieldz.length;k++)
			{
				if(this.fieldz[k]==index)
				fieldPosition=k;
			}
			if(fieldPosition>=0)
			{
				if(this.nam[j][fieldPosition]==value)
				{
					available=j;
				}
			}
		}
		
	}
	
	return available;
	
}


Table.prototype.getResultPage=function(tblname,pageno,pagesize)//Used to collect the result pages. Must be followed by the function "fetchRow".
{
	this.pageSize=pagesize;
	var start=(pageno-1)*pagesize+parseInt(1)+parseInt(this.totl1);
	var end=pageno*pagesize+this.totl1;
	if(end>this.totl2)
	end=this.totl2;
	var k=0;
	var resultset=new Array(this.totl);
	//alert(this.nam[51]);
	//alert(end);
	for(var j=start;j<=end;j++,k++)
	{//alert(this.nam[j]);
			resultset[k]=this.nam[j];
	}
	this.resultSet=new Array(k);
	for(var i=0;i<k;i++)
	{
//alert(resultset[i]);
		this.resultSet[i]=resultset[i];
	}
	rsset=new ResultSet();
	rsset.add(this.resultSet,k);
	return rsset;
}


Table.prototype.getResultItembyIndex=function(tblname,index)//Used to collect the result pages. Must be followed by the function "fetchRow".
{
	return this.nam[index];
}



Table.prototype.getResultItem=function(tblname,id)//Used to collect the result pages. Must be followed by the function "fetchRow".
{
	var strt=parseInt(this.totl1)+parseInt(1);
	this.resultSet=new Array(1);
	for(var j=strt;j<=this.totl2;j++)
	{
		if(this.nam[j][0]==id)
		{
			this.resultSet[0]=this.nam[j];
			break;
		}
	}
	rsset=new ResultSet();
	rsset.add(this.resultSet,1);
	return rsset;
}
Table.prototype.getResultItemM=function(tblname,id,pageno,pagesize)//Used to collect the result pages. Must be followed by the function "fetchRow".
{

    var strt=(pageno-1)*pagesize+parseInt(this.totl1)+parseInt(1);
    this.resultSet=new Array(1);
    for(var j=strt;j<=this.totl2;j++)
    {
        if(this.nam[j][0]==id)
        {
            this.resultSet[0]=this.nam[j];
            break;
        }
    }
    rsset=new ResultSet();
    rsset.add(this.resultSet,1);
    return rsset;
}



Table.prototype.fetchRow=function(obj)//Followed by the function "getResultPage" to get the results in a better format.
{
	return obj.fetchRow();
}





ResultSet.prototype.constructor=ResultSet;
function ResultSet()//Constuctor of the resultset class.
{
	this.results=new Array(10000);
	this.resultindex=0;
}

ResultSet.prototype.add=function(arry,num)//Adding a result to the formatted array results.
{
	var i=0;
	while(i<num)
	{	
									
		this.results[i]=arry[i];
		i++;
	}
	i=i-1;
}

ResultSet.prototype.fetchRow=function()//Gives the final results of "fetchRow" function in "Table" class.
{

	if(this.results.length==0)
	{	
		return false;
	}
	if((this.resultindex)<(this.results.length))
	{	
		var value=this.results[this.resultindex];	
		//alert(this.resultindex);	//alert(value);
		this.resultindex=this.resultindex+1;
		
		return value;
	}
	else
	{
		return false;
	}
}

