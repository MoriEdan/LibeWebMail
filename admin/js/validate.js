function textAreaLimiter(fld, cntfld, lmt)
{
if (fld.value.length > lmt) 
fld.value = fld.value.substring(0, lmt);
else 
cntfld.innerHTML = lmt - fld.value.length;
}