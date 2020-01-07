Import-Module Microsoft.Xrm.Data.Powershell
$Global:conn = Get-CrmConnection -InteractiveMode
 
# get all template records
$templates = Get-CrmRecords `
    -EntityLogicalName documenttemplate `
    -Fields name,documenttype,content
 
# loop through the templates 
ForEach($t in $templates.CrmRecords) 
{
  # figure out file extension
  if($t.documenttype -eq 'Microsoft Excel') { 
    $ext = '.xlsx'
  } 
  else {
    $ext = '.docx'
  } 
     
  $filename = 'c:\temp\Cannabis\' + $t.name + $ext
  write-host $filename
 
  # decode and dump file content 
  $bytes = [convert]::FromBase64String($t.content)
  [io.file]::WriteAllBytes($filename, $bytes)
}