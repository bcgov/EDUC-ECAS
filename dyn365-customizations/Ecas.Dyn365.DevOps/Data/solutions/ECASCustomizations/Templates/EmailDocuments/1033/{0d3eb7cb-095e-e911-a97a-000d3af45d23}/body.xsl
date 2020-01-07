<?xml version="1.0" ?><xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"><xsl:output method="text" indent="no"/><xsl:template match="/data"><![CDATA[<div><font face="Tahoma, Verdana, Arial" size=2 style="display:inline;">Hi ]]><xsl:choose><xsl:when test="educ_assignment/educ_contact/@name"><xsl:value-of select="educ_assignment/educ_contact/@name" /></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose><![CDATA[&#160;</font></div><div><font face="Tahoma, Verdana, Arial" size=2 style="display:inline;"><br></font></div><div><font class=keyboardFocusClass face="Tahoma, Verdana, Arial" size=2 style="display:inline;"><span style="margin:0px;color:black;line-height:107%;font-family:&quot;Calibri&quot;,sans-serif;font-size:11pt;">We are pleased to let you know that you have
been selected to attend our upcoming ]]><xsl:choose><xsl:when test="educ_assignment/educ_session/@name"><xsl:value-of select="educ_assignment/educ_session/@name" /></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose><![CDATA[ as a
]]><xsl:choose><xsl:when test="educ_assignment/educ_role/@name"><xsl:value-of select="educ_assignment/educ_role/@name" /></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose><![CDATA[. Please login to</span><span style="margin:0px;padding:0cm;border:1pt windowtext;"><font face="Times New Roman" size=3> the <span style="margin:0px;color:black;line-height:107%;font-family:&quot;Calibri&quot;,sans-serif;font-size:11pt;"><a href="https://www.bbc.com/news"><span style="margin:0px;padding:0cm;border:1pt windowtext;color:darkblue;"><span style="margin:0px;">ECAS Contractors Portal</span></span></a> and accept or
decline the invitation for this session within the next 7 business days.</span></font></span></font></div><div><font class=keyboardFocusClass face="Tahoma, Verdana, Arial" size=2 style="display:inline;"><b></b><i></i><u></u><sub></sub><sup></sup><strike></strike><br></font></div><div><font class=keyboardFocusClass face="Tahoma, Verdana, Arial" size=2 style="display:inline;">

<p style="margin:0px 0px 10.66px;"><span style="margin:0px;color:black;"><font face=Calibri size=3>If you accept you can expect a contract email being
sent 7 to 10 business days later.</font></span></p>

<p style="margin:0px 0px 10.66px;"><span style="margin:0px;color:black;"><font face=Calibri size=3>&#160;</font></span></p>

<p style="margin:0px 0px 10.66px;"><span style="margin:0px;color:black;"><font face=Calibri size=3>Thank You for you participation in this important
event.</font></span></p>

<p style="margin:0px 0px 10.66px;"><span style="margin:0px;color:black;"><font face=Calibri size=3>&#160;</font></span></p>

<p style="margin:0px 0px 10.66px;"><span style="margin:0px;color:black;"><font face=Calibri size=3>Yours Truly</font></span></p>

<p style="margin:0px 0px 10.66px;"><span style="margin:0px;color:black;"><font face=Calibri size=3>The ECAS administration team.</font></span></p>

<b></b><i></i><u></u><sub></sub><sup></sup><strike></strike><font face=Calibri></font><font size=3></font><font face="Times New Roman"></font><br></font></div>]]></xsl:template></xsl:stylesheet>