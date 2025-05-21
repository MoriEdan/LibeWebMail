ResultSet.prototype.constructor=ResultSet;
function ResultSet()
{
	this.results=new Array(10000);
	this.resultindex=0;
}

ResultSet.prototype.add=function(arry,num) 
{
	var i=0;
	while(i<num)
	{																
		this.results[i]=arry[i];
		i++;
	}
}

ResultSet.prototype.fetchRow=function() 
{
	if(this.results.length==0)
	{		
		return false;
	}
	if((this.resultindex)<(this.results.length))
	{			
		var value=this.results[this.resultindex];
		this.resultindex=this.resultindex+1;
		return value;
	}
	else
	{
		return false;
	}
}
