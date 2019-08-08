Import-Module .\Microsoft.Xrm.Data.Powershell\2.8.0\Microsoft.Xrm.Data.PowerShell.psd1
Import-Module .\Adoxio.Dynamics.DevOps\0.8.0\Adoxio.Dynamics.DevOps.psd1
.\Export.ps1 -CrmConnectionName OnlineDev-LCLBPortal -ExportSettings LCLBPortalsCustomizations -Actions Solutions
#.\Export.ps1 -CrmConnectionName OnlineDev-LCLBPortal -ExportSettings LCLBPortalsCustomizations -Actions Expand-CrmSolutions
#.\Export.ps1 -CrmConnectionName OnlineDev-LCLBPortal -ExportSettings LCLBPortalsData -Actions Edit-CrmSchemaFile
# Use Dynamics 365 SDK Configuration Migration Tool with the Schema File generated to export data and stored it to DevOps\Temp\Export\LCLBPortal as LCLBPortalData.zip

